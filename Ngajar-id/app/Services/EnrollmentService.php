<?php

namespace App\Services;

use App\Models\Kelas;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;

class EnrollmentService
{
    /**
     * Enroll user in a class
     */
    public function enrollUser(User $user, Kelas $kelas): array
    {
        // Check if already enrolled
        if ($user->kelasIkuti()->where('kelas_id', $kelas->kelas_id)->exists()) {
            throw new Exception('User already enrolled in this class', 409);
        }

        // Check class availability
        if ($kelas->status !== 'aktif') {
            throw new Exception('Class is not available', 422);
        }

        // Enroll user
        DB::beginTransaction();
        try {
            $user->kelasIkuti()->attach($kelas->kelas_id, [
                'tanggal_daftar' => now(),
                'status' => 'aktif',
            ]);

            // Award XP for enrollment
            app(GamificationService::class)->awardXp($user, 50, 'class_enrollment');

            DB::commit();

            return [
                'success' => true,
                'message' => 'Successfully enrolled',
                'class_id' => $kelas->kelas_id,
            ];
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Unenroll user from class
     */
    public function unenrollUser(User $user, Kelas $kelas): bool
    {
        return $user->kelasIkuti()->detach($kelas->kelas_id) > 0;
    }

    /**
     * Get user progress in class
     */
    public function getUserClassProgress(User $user, Kelas $kelas): array
    {
        $totalMaterials = $kelas->materi()->count();

        $completedMaterials = DB::table('ulasan_materi')
            ->where('siswa_id', $user->user_id)
            ->where('materi_id', '!=', null)
            ->join('materi', 'ulasan_materi.materi_id', '=', 'materi.materi_id')
            ->where('materi.kelas_id', $kelas->kelas_id)
            ->where('ulasan_materi.nilai', '>=', 60)
            ->distinct('ulasan_materi.materi_id')
            ->count();

        $progress = $totalMaterials > 0 ? ($completedMaterials / $totalMaterials) * 100 : 0;

        return [
            'total_materials' => $totalMaterials,
            'completed_materials' => $completedMaterials,
            'progress_percentage' => round($progress, 2),
            'is_completed' => $progress === 100,
        ];
    }

    /**
     * Mark material as completed
     */
    public function completeMaterial(User $user, int $materiId, float $score): bool
    {
        return DB::table('ulasan_materi')
            ->updateOrCreate(
                [
                    'siswa_id' => $user->user_id,
                    'materi_id' => $materiId,
                ],
                [
                    'nilai' => $score,
                    'waktu_selesai' => now(),
                ]
            );
    }

    /**
     * Get all enrolled classes for user
     */
    public function getEnrolledClasses(User $user, int $perPage = 15)
    {
        return $user->kelasIkuti()
            ->with(['pengajar:user_id,name', 'kategori'])
            ->where('status', 'aktif')
            ->paginate($perPage);
    }
}
