<?php

namespace App\Services\Course;

use App\Models\Course;
use App\Models\User;
use App\Models\TokenLog;
use Exception;
use Illuminate\Support\Facades\DB;
use App\Services\Core\GamificationService;

class EnrollmentService
{
    /**
     * @var GamificationService
     */
    protected $gamificationService;

    public function __construct(GamificationService $gamificationService)
    {
        $this->gamificationService = $gamificationService;
    }

    /**
     * Enroll user in a class with robust business rules (roles, token payment, beasiswa)
     */
    public function enrollUser(User $user, Course $kelas): array
    {
        // 1. Cek apakah sudah terdaftar
        if ($user->kelasIkuti()->where('kelas_peserta.kelas_id', $kelas->kelas_id)->exists()) {
            throw new Exception('Anda sudah terdaftar di kelas ini.');
        }

        // 2. Cek Role
        if (!$user->isMurid()) {
            throw new Exception('Hanya akun Murid yang bisa mendaftar kelas.');
        }

        // 3. Logic Pembayaran Token
        $harga = $kelas->harga_token ?? 0;

        // Bypass pembayaran jika user adalah penerima beasiswa
        if ($user->hasBeasiswa()) {
            $harga = 0;
        }

        $enrollmentData = [];

        DB::beginTransaction();
        try {
            if ($harga > 0) {
                // Cek saldo token user
                $userToken = $user->token;

                if (!$userToken || !$userToken->cukup($harga)) {
                    $currentBalance = $userToken->jumlah ?? 0;
                    throw new Exception("Token tidak mencukupi. Harga kelas: {$harga} Token. Saldo Anda: {$currentBalance}");
                }

                // Potong Token
                $userToken->kurang($harga);

                // Catat Log
                TokenLog::create([
                    'user_id' => $user->user_id,
                    'jumlah' => $harga,
                    'aksi' => 'kurang',
                    'tipe' => 'pembelian_kelas',
                    'keterangan' => "Membeli akses kelas: {$kelas->judul}",
                    'tanggal' => now(),
                ]);

                $enrollmentData['message'] = "Berhasil membeli kelas seharga {$harga} Token!";
                $enrollmentData['cost'] = $harga;
            } else {
                $enrollmentData['message'] = $user->hasBeasiswa() 
                    ? "Fasilitas Beasiswa: Berhasil bergabung secara GRATIS!" 
                    : "Berhasil bergabung ke kelas gratis!";
                $enrollmentData['cost'] = 0;
            }

            // Enroll User
            $user->kelasIkuti()->attach($kelas->kelas_id, [
                'tanggal_daftar' => now(),
                'progress' => 0,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Increment denormalized student count
            $kelas->increment('total_siswa');

            // Gamification: Award 100 XP for enrollment (via GamificationService)
            $gamificationResult = $this->gamificationService->awardXp($user, 100, 'class_enrollment');
            
            DB::commit();

            $enrollmentData['success'] = true;
            $enrollmentData['kelas_id'] = $kelas->kelas_id;
            $enrollmentData['xp_earned'] = 100;
            $enrollmentData['gamification'] = $gamificationResult;

            return $enrollmentData;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Unenroll user from class
     */
    public function unenrollUser(User $user, Course $kelas): bool
    {
        $detached = $user->kelasIkuti()->detach($kelas->kelas_id);
        if ($detached > 0) {
            $kelas->decrement('total_siswa');
            return true;
        }
        return false;
    }
}


