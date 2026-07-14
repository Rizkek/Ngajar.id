<?php

namespace App\Http\Controllers\Api\V1\Student;

use App\Http\Controllers\Controller;

use App\Models\Course;
use App\Http\Traits\ApiResponse;
use App\Http\Resources\KelasResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CatalogApiController extends Controller
{
    use ApiResponse;
    /**
     * Tampilkan semua kelas yang tersedia (Katalog).
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $search = $request->query('q');

        // Ambil kelas aktif, exclude yang sudah diikuti user (opsional, tapi bagus buat UX)
        // Atau tampilkan status "Sudah Bergabung"

        $query = Course::with(['pengajar'])
            ->where('status', 'aktif')
            ->orderBy('created_at', 'desc');

        if ($search) {
            $query->where('judul', 'ILIKE', "%{$search}%")
                ->orWhere('deskripsi', 'ILIKE', "%{$search}%");
        }

        $allKelas = $query->paginate(9);

        // Map data untuk view
        // Kita perlu tahu user sudah join kelas mana aja
        $user = Auth::user();
        $enrolledKelasIds = $user ? $user->kelasIkuti()->pluck('kelas_peserta.kelas_id')->toArray() : [];

    }

    /**
     * Proses gabung kelas (Enrollment).
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function join($id)
    {
        $kelas = Course::findOrFail($id);
        $user = Auth::user();

        // 1. Cek apakah sudah terdaftar
        if ($user->kelasIkuti()->where('kelas_peserta.kelas_id', $id)->exists()) {

        }

        // 2. Cek Role
        if (!$user->isMurid()) {

        }

        // 3. Logic Pembayaran Token
        $harga = $kelas->harga_token ?? 0;

        // Bypass pembayaran jika user adalah penerima beasiswa
        if ($user->hasBeasiswa()) {
            $harga = 0;
        }

        if ($harga > 0) {
            // Cek saldo token user
            $userToken = $user->token;

            if (!$userToken || !$userToken->cukup($harga)) {

            }

            // Gunakan Transaction untuk atomic operation
            try {
                \Illuminate\Support\Facades\DB::transaction(function () use ($user, $kelas, $userToken, $harga) {
                    // Potong Token
                    $userToken->kurang($harga);

                    // Catat Log
                    \App\Models\TokenLog::create([
                        'user_id' => $user->user_id,
                        'jumlah' => $harga,
                        'aksi' => 'kurang',
                        'tipe' => 'pembelian_kelas',
                        'keterangan' => "Membeli akses kelas: {$kelas->judul}",
                        'tanggal' => now(),
                    ]);

                    // Enroll User
                    $user->kelasIkuti()->attach($kelas->kelas_id, ['tanggal_daftar' => now()]);
                });
            } catch (\Exception $e) {

            }

        } else {
            // Jika Gratis (atau Beasiswa)
            $user->kelasIkuti()->attach($id, ['tanggal_daftar' => now()]);

            $msg = $user->hasBeasiswa() ? "Fasilitas Beasiswa: Berhasil bergabung secara GRATIS!" : "Berhasil bergabung ke kelas gratis!";

        }
    }

    /**
     * GET /api/v1/student/classes
     * Get user's enrolled classes
     */
    public function myClasses(Request $request)
    {
        try {
            $user = $request->user();
            $search = $request->get('q');
            $kategori = $request->get('kategori');

            $query = $user->kelasIkuti()
                ->with('pengajar:user_id,name')
                ->where('status', 'aktif');

            if ($search) {
                $query->where('judul', 'ILIKE', "%{$search}%")
                    ->orWhere('deskripsi', 'ILIKE', "%{$search}%");
            }

            if ($kategori) {
                $query->where('kategori', $kategori);
            }

            $classes = $query->paginate(10);

            return $this->successWithPagination(
                    KelasResource::collection($classes),
                    'My classes retrieved successfully'
                );

        } catch (\Exception $e) {
            \Log::error('Error in myClasses: ' . $e->getMessage());

            return $this->serverError($e->getMessage());

        }
    }

    /**
     * GET /api/v1/student/classes/{id}
     * Get class details
     */
    public function show(Request $request, $id)
    {
        try {
            $kelas = Course::with(['pengajar:user_id,name', 'materi', 'peserta'])
                ->findOrFail($id);

            // Check if user is enrolled
            $user = $request->user();
            $isEnrolled = $user ? $user->kelasIkuti()->where('kelas_id', $id)->exists() : false;

            $data = [
                'class' => KelasResource::make($kelas),
                'is_enrolled' => $isEnrolled,
                'material_count' => $kelas->materi->count(),
                'student_count' => $kelas->peserta->count()
            ];

            return $this->success($data, 'Class details retrieved successfully');

        } catch (\Exception $e) {
            \Log::error('Error in show: ' . $e->getMessage());

            return $this->notFound('Class not found');

        }
    }

    /**
     * POST /api/v1/student/classes/{id}/enroll
     * Enroll student in a class
     */
    public function enroll(Request $request, $id)
    {
        try {
            $user = $request->user();
            $kelas = Course::findOrFail($id);

            // 1. Cek apakah sudah terdaftar
            if ($user->kelasIkuti()->where('kelas_peserta.kelas_id', $id)->exists()) {
                return $this->validationError(['error' => 'Already enrolled in this class']);

            }

            // 2. Cek Role
            if (!$user->isMurid()) {
                return $this->forbidden('Only student accounts can enroll');

            }

            // 3. Logic Pembayaran Token
            $harga = $kelas->harga_token ?? 0;

            // Bypass pembayaran jika user adalah penerima beasiswa
            if ($user->hasBeasiswa()) {
                $harga = 0;
            }

            if ($harga > 0) {
                // Cek saldo token user
                $userToken = $user->token;

                if (!$userToken || !$userToken->cukup($harga)) {
                    $currentBalance = $userToken->jumlah ?? 0;
                    if ($request->expectsJson()) {
                        return $this->validationError(
                            ['error' => "Insufficient token balance. Required: {$harga}, Balance: {$currentBalance}"]
                        );
                    }

                }

                // Gunakan Transaction untuk atomic operation
                \Illuminate\Support\Facades\DB::transaction(function () use ($user, $kelas, $userToken, $harga) {
                    // Potong Token
                    $userToken->kurang($harga);

                    // Catat Log
                    \App\Models\TokenLog::create([
                        'user_id' => $user->user_id,
                        'jumlah' => $harga,
                        'aksi' => 'kurang',
                        'tipe' => 'pembelian_kelas',
                        'keterangan' => "Membeli akses kelas: {$kelas->judul}",
                        'tanggal' => now(),
                    ]);

                    // Enroll User
                    $user->kelasIkuti()->attach($kelas->kelas_id, ['tanggal_daftar' => now()]);
                });

                if ($request->expectsJson()) {
                    return $this->success(
                        ['enrollment' => KelasResource::make($kelas)],
                        "Successfully enrolled in class! Cost: {$harga} Token",
                        201
                    );
                }

            } else {
                // Jika Gratis (atau Beasiswa)
                $user->kelasIkuti()->attach($id, ['tanggal_daftar' => now()]);

                $msg = $user->hasBeasiswa() ? "Fasilitas Beasiswa: Berhasil bergabung secara GRATIS!" : "Berhasil bergabung ke kelas gratis!";

                return $this->success(
                        ['enrollment' => KelasResource::make($kelas)],
                        $msg,
                        201
                    );

            }

        } catch (\Exception $e) {
            \Log::error('Error in enroll: ' . $e->getMessage());

            return $this->serverError($e->getMessage());

        }
    }

    /**
     * GET /api/v1/student/saved
     * Get user's saved/wishlist classes
     */
    public function savedClasses(Request $request)
    {
        try {
            $user = $request->user();

            $saved = \Illuminate\Support\Facades\DB::table('kelas_saved')
                ->join('kelas', 'kelas_saved.kelas_id', '=', 'kelas.kelas_id')
                ->join('users as u', 'kelas.pengajar_id', '=', 'u.user_id')
                ->select(['kelas.*', 'u.name as pengajar_name', 'u.user_id as pengajar_id'])
                ->where('kelas_saved.user_id', $user->user_id)
                ->where('kelas.status', 'aktif')
                ->paginate(10);

            return $this->successWithPagination(
                    collect($saved->items()),
                    'Saved classes retrieved successfully'
                );

        } catch (\Exception $e) {
            \Log::error('Error in savedClasses: ' . $e->getMessage());

            return $this->serverError($e->getMessage());

        }
    }

    /**
     * POST /api/v1/student/saved/classes/{id}
     * Save a class to wishlist
     */
    public function saveClass(Request $request, $id)
    {
        try {
            $user = $request->user();
            $kelas = Course::findOrFail($id);

            // Check if already saved
            $exists = \Illuminate\Support\Facades\DB::table('kelas_saved')
                ->where('user_id', $user->user_id)
                ->where('kelas_id', $id)
                ->exists();

            if ($exists) {
                return $this->validationError(['error' => 'Class already saved']);

            }

            // Save class
            \Illuminate\Support\Facades\DB::table('kelas_saved')->insert([
                'user_id' => $user->user_id,
                'kelas_id' => $id,
                'saved_at' => now()
            ]);

            return $this->success(
                    ['class' => KelasResource::make($kelas)],
                    'Class saved successfully',
                    201
                );

        } catch (\Exception $e) {
            \Log::error('Error in saveClass: ' . $e->getMessage());

            return $this->serverError($e->getMessage());

        }
    }

    /**
     * DELETE /api/v1/student/saved/classes/{id}
     * Remove class from wishlist
     */
    public function unsaveClass(Request $request, $id)
    {
        try {
            $user = $request->user();

            $deleted = \Illuminate\Support\Facades\DB::table('kelas_saved')
                ->where('user_id', $user->user_id)
                ->where('kelas_id', $id)
                ->delete();

            if ($deleted === 0) {
                return $this->notFound('Saved class not found');

            }

            return $this->success(
                    ['deleted' => true],
                    'Class removed from wishlist'
                );

        } catch (\Exception $e) {
            \Log::error('Error in unsaveClass: ' . $e->getMessage());

            return $this->serverError($e->getMessage());

        }
    }
}



