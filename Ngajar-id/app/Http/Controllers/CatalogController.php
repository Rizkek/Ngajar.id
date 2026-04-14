<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Http\Traits\ApiResponse;
use App\Http\Resources\KelasResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CatalogController extends Controller
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

        $query = Kelas::with(['pengajar'])
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

        return view('murid.katalog.index', compact('allKelas', 'enrolledKelasIds'));
    }

    /**
     * Proses gabung kelas (Enrollment).
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function join($id)
    {
        $kelas = Kelas::findOrFail($id);
        $user = Auth::user();

        // 1. Cek apakah sudah terdaftar
        if ($user->kelasIkuti()->where('kelas_peserta.kelas_id', $id)->exists()) {
            return redirect()->route('belajar.show', ['kelas_id' => $id])
                ->with('info', 'Anda sudah terdaftar di kelas ini.');
        }

        // 2. Cek Role
        if (!$user->isMurid()) {
            return back()->with('error', 'Hanya akun Murid yang bisa mendaftar kelas.');
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
                return redirect()->route('topup.create') // Asumsi ada route ini atau ke halaman donasi/topup
                    ->with('error', "Token tidak mencukupi. Harga kelas: {$harga} Token. Saldo Anda: " . ($userToken->jumlah ?? 0));
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
                return back()->with('error', 'Terjadi kesalahan saat memproses pembayaran token: ' . $e->getMessage());
            }

            return redirect()->route('belajar.show', ['kelas_id' => $id])
                ->with('success', "Berhasil membeli kelas seharga {$harga} Token!");

        } else {
            // Jika Gratis (atau Beasiswa)
            $user->kelasIkuti()->attach($id, ['tanggal_daftar' => now()]);

            $msg = $user->hasBeasiswa() ? "Fasilitas Beasiswa: Berhasil bergabung secara GRATIS!" : "Berhasil bergabung ke kelas gratis!";

            return redirect()->route('belajar.show', ['kelas_id' => $id])
                ->with('success', $msg);
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

            if ($request->expectsJson()) {
                return $this->successWithPagination(
                    KelasResource::collection($classes),
                    'My classes retrieved successfully'
                );
            }

            return view('murid.kelas.index', compact('classes'));

        } catch (\Exception $e) {
            \Log::error('Error in myClasses: ' . $e->getMessage());

            if ($request->expectsJson()) {
                return $this->serverError($e->getMessage());
            }

            return back()->with('error', 'Failed to load classes: ' . $e->getMessage());
        }
    }

    /**
     * GET /api/v1/student/classes/{id}
     * Get class details
     */
    public function show(Request $request, $id)
    {
        try {
            $kelas = Kelas::with(['pengajar:user_id,name', 'materi', 'peserta'])
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

            if ($request->expectsJson()) {
                return $this->success($data, 'Class details retrieved successfully');
            }

            return view('murid.kelas.show', compact('kelas', 'isEnrolled'));

        } catch (\Exception $e) {
            \Log::error('Error in show: ' . $e->getMessage());

            if ($request->expectsJson()) {
                return $this->notFound('Class not found');
            }

            return back()->with('error', 'Class not found');
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
            $kelas = Kelas::findOrFail($id);

            // 1. Cek apakah sudah terdaftar
            if ($user->kelasIkuti()->where('kelas_peserta.kelas_id', $id)->exists()) {
                if ($request->expectsJson()) {
                    return $this->validationError(['error' => 'Already enrolled in this class']);
                }
                return redirect()->route('belajar.show', ['kelas_id' => $id])
                    ->with('info', 'Anda sudah terdaftar di kelas ini.');
            }

            // 2. Cek Role
            if (!$user->isMurid()) {
                if ($request->expectsJson()) {
                    return $this->forbidden('Only student accounts can enroll');
                }
                return back()->with('error', 'Hanya akun Murid yang bisa mendaftar kelas.');
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
                    return redirect()->route('topup.create')
                        ->with('error', "Token tidak mencukupi. Harga kelas: {$harga} Token. Saldo Anda: " . $currentBalance);
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

                return redirect()->route('belajar.show', ['kelas_id' => $id])
                    ->with('success', "Berhasil membeli kelas seharga {$harga} Token!");

            } else {
                // Jika Gratis (atau Beasiswa)
                $user->kelasIkuti()->attach($id, ['tanggal_daftar' => now()]);

                $msg = $user->hasBeasiswa() ? "Fasilitas Beasiswa: Berhasil bergabung secara GRATIS!" : "Berhasil bergabung ke kelas gratis!";

                if ($request->expectsJson()) {
                    return $this->success(
                        ['enrollment' => KelasResource::make($kelas)],
                        $msg,
                        201
                    );
                }

                return redirect()->route('belajar.show', ['kelas_id' => $id])
                    ->with('success', $msg);
            }

        } catch (\Exception $e) {
            \Log::error('Error in enroll: ' . $e->getMessage());

            if ($request->expectsJson()) {
                return $this->serverError($e->getMessage());
            }

            return back()->with('error', 'Enrollment failed: ' . $e->getMessage());
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

            if ($request->expectsJson()) {
                return $this->successWithPagination(
                    collect($saved->items()),
                    'Saved classes retrieved successfully'
                );
            }

            return view('murid.saved', compact('saved'));

        } catch (\Exception $e) {
            \Log::error('Error in savedClasses: ' . $e->getMessage());

            if ($request->expectsJson()) {
                return $this->serverError($e->getMessage());
            }

            return back()->with('error', 'Failed to load saved classes');
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
            $kelas = Kelas::findOrFail($id);

            // Check if already saved
            $exists = \Illuminate\Support\Facades\DB::table('kelas_saved')
                ->where('user_id', $user->user_id)
                ->where('kelas_id', $id)
                ->exists();

            if ($exists) {
                if ($request->expectsJson()) {
                    return $this->validationError(['error' => 'Class already saved']);
                }
                return back()->with('info', 'Kelas sudah disimpan');
            }

            // Save class
            \Illuminate\Support\Facades\DB::table('kelas_saved')->insert([
                'user_id' => $user->user_id,
                'kelas_id' => $id,
                'saved_at' => now()
            ]);

            if ($request->expectsJson()) {
                return $this->success(
                    ['class' => KelasResource::make($kelas)],
                    'Class saved successfully',
                    201
                );
            }

            return back()->with('success', 'Kelas disimpan ke wishlist');

        } catch (\Exception $e) {
            \Log::error('Error in saveClass: ' . $e->getMessage());

            if ($request->expectsJson()) {
                return $this->serverError($e->getMessage());
            }

            return back()->with('error', 'Failed to save class');
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
                if ($request->expectsJson()) {
                    return $this->notFound('Saved class not found');
                }
                return back()->with('info', 'Kelas tidak ditemukan di wishlist');
            }

            if ($request->expectsJson()) {
                return $this->success(
                    ['deleted' => true],
                    'Class removed from wishlist'
                );
            }

            return back()->with('success', 'Kelas dihapus dari wishlist');

        } catch (\Exception $e) {
            \Log::error('Error in unsaveClass: ' . $e->getMessage());

            if ($request->expectsJson()) {
                return $this->serverError($e->getMessage());
            }

            return back()->with('error', 'Failed to remove class from wishlist');
        }
    }
}

