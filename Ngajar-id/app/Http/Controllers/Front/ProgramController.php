<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;

use App\Models\Course;
use App\Http\Traits\ApiResponse;
use App\Http\Resources\KelasResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProgramController extends Controller
{
    use ApiResponse;
    /**
     * Tampilkan katalog kelas yang aktif
     * API: GET /api/v1/programs
     * Web: GET /programs
     */
    public function index(Request $request)
    {
        try {
            // Build query
            $query = Course::with(['pengajar', 'materi', 'peserta'])
                ->where('status', 'aktif');

            // Filter by search
            if ($request->has('search') && $request->search) {
                $searchTerm = $request->search;
                $query->where(function ($q) use ($searchTerm) {
                    $q->where('judul', 'ILIKE', "%{$searchTerm}%")
                        ->orWhere('deskripsi', 'ILIKE', "%{$searchTerm}%");
                });
            }

            // Filter by category
            if ($request->has('kategori') && $request->kategori) {
                $query->where('kategori', $request->kategori);
            }

            // Get results
            $limit = $request->get('limit', 12);
            $programs = $query->latest()->paginate($limit);

            // Support both web & API
            if ($request->expectsJson()) {
                return $this->successWithPagination(
                    KelasResource::collection($programs),
                    'Programs retrieved successfully'
                );
            }

            return view('programs', compact('programs'));

        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return $this->serverError($e->getMessage());
            }
            return redirect()->back()->with('error', 'Failed to load programs: ' . $e->getMessage());
        }
    }

    /**
     * Get single program/class details
     * API: GET /api/v1/programs/{id}
     */
    public function show($id)
    {
        try {
            $program = Course::with(['pengajar', 'materi', 'peserta'])
                ->findOrFail($id);

            return $this->success(
                new KelasResource($program),
                'Program details retrieved successfully'
            );

        } catch (\Exception $e) {
            return $this->notFound('Program not found');
        }
    }

    /**
     * Get program reviews
     * API: GET /api/v1/programs/{id}/reviews
     */
    public function reviews($id)
    {
        try {
            $program = Course::findOrFail($id);

            $reviews = $program->ulasan()
                ->with('siswa')
                ->paginate(10);

            if ($reviews->isEmpty()) {
                return $this->successWithPagination(
                    collect(),
                    'No reviews yet for this program'
                );
            }

            return $this->successWithPagination(
                $reviews->map(function ($review) {
                    return [
                        'id' => $review->id,
                        'rating' => $review->rating,
                        'comment' => $review->komentar,
                        'author' => $review->siswa->nama ?? 'Anonymous',
                        'created_at' => $review->created_at?->toIso8601String(),
                    ];
                }),
                'Program reviews retrieved successfully'
            );

        } catch (\Exception $e) {
            return $this->notFound('Program not found');
        }
    }

    /**
     * Get program materials/lessons
     * API: GET /api/v1/programs/{id}/materials
     */
    public function materials($id)
    {
        try {
            $program = Course::findOrFail($id);

            $materials = $program->materi()
                ->paginate(15);

            if ($materials->isEmpty()) {
                return $this->successWithPagination(
                    collect(),
                    'No materials yet for this program'
                );
            }

            return $this->successWithPagination(
                $materials->map(function ($material) {
                    return [
                        'id' => $material->id,
                        'title' => $material->judul,
                        'description' => $material->deskripsi ?? null,
                        'type' => $material->tipe ?? 'video',
                        'duration' => $material->durasi ?? null,
                        'is_free' => !$material->premium,
                        'is_premium' => (bool) $material->premium,
                        'order' => $material->urutan ?? null,
                    ];
                }),
                'Program materials retrieved successfully'
            );

        } catch (\Exception $e) {
            return $this->notFound('Program not found');
        }
    }

    /**
     * Proses murid bergabung ke kelas
     */
    public function join($kelasId)
    {
        // Cek login
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu untuk mendaftar kelas.');
        }

        $user = Auth::user();

        // Cek role (hanya murid yang bisa gabung)
        if (!$user->isMurid()) {
            return redirect()->back()->with('error', 'Hanya akun Murid yang bisa mendaftar kelas.');
        }

        $kelas = Course::findOrFail($kelasId);

        // Cek apakah sudah terdaftar
        $isRegistered = $kelas->peserta()->where('kelas_peserta.siswa_id', $user->user_id)->exists();

        if ($isRegistered) {
            return redirect()->route('student.kelas')->with('info', 'Anda sudah terdaftar di kelas ini.');
        }

        // Logic Pembayaran Token
        $harga = $kelas->harga_token ?? 0;

        // Bypass pembayaran jika user adalah penerima beasiswa
        if ($user->hasBeasiswa()) {
            $harga = 0;
        }

        if ($harga > 0) {
            // Cek saldo token user
            $userToken = $user->token;

            if (!$userToken || !$userToken->cukup($harga)) {
                return redirect()->route('topup.create')
                    ->with('error', "Token tidak mencukupi. Harga kelas: {$harga} Token. Saldo Anda: " . ($userToken->jumlah ?? 0));
            }

            try {
                \Illuminate\Support\Facades\DB::transaction(function () use ($user, $kelas, $userToken, $harga) {
                    $userToken->kurang($harga);

                    \App\Models\TokenLog::create([
                        'user_id' => $user->user_id,
                        'jumlah' => $harga,
                        'aksi' => 'kurang',
                        'tipe' => 'pembelian_kelas',
                        'keterangan' => "Membeli akses kelas: {$kelas->judul}",
                        'tanggal' => now(),
                    ]);

                    $kelas->peserta()->attach($user->user_id, ['tanggal_daftar' => now()]);
                });
            } catch (\Exception $e) {
                return back()->with('error', 'Terjadi kesalahan saat memproses pembayaran token: ' . $e->getMessage());
            }

            return redirect()->route('student.kelas')->with('success', "Berhasil bergabung ke kelas seharga {$harga} Token! Selamat belajar.");

        } else {
            // Jika Gratis (atau Beasiswa)
            $kelas->peserta()->attach($user->user_id, ['tanggal_daftar' => now()]);

            $msg = $user->hasBeasiswa() ? "Fasilitas Beasiswa: Berhasil bergabung secara GRATIS!" : "Berhasil bergabung ke kelas gratis! Selamat belajar.";

            return redirect()->route('student.kelas')->with('success', $msg);
        }
    }
}




