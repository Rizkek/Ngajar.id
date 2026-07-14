<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;

use App\Models\Course;
use App\Models\Module;
use App\Http\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class TeacherDashboardController extends Controller
{
    use ApiResponse;
    /**
     * Tampilkan Dashboard untuk Role MURID
     * Menampilkan ringkasan kelas, materi terbaru, modul rekomendasi, dan saldo token.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\View|\Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function muridDashboard(Request $request)
    {
        try {
            $user = $request->user();
            $kategori = $request->get('kategori'); // Filter kategori dari query string

            // 1. Gamification Stats & Token
            $userStats = [
                'xp' => $user->xp ?? 0,
                'level' => $user->level ?? 1,
                'token_balance' => $user->getSaldoToken(),
                'total_kelas' => $user->kelasIkuti()->count(),
                'xp_next_level' => ($user->level ?? 1) * 1000 // Contoh logic target XP
            ];

            // 2. Last Accessed Class (Untuk fitur "Lanjutkan Belajar")
            $lastClass = $user->kelasIkuti()
                ->with([
                    'materi' => function ($q) {
                        $q->select('materi_id', 'kelas_id', 'judul', 'created_at')
                            ->orderBy('created_at', 'asc')
                            ->limit(1);
                    },
                    'pengajar:user_id,name'
                ])
                ->orderByPivot('updated_at', 'desc') // Use updated_at for last access
                ->first();

            // 3. Kelas yang sedang diikuti (My Classes) - dengan kategori
            $myClasses = $user->kelasIkuti()
                ->with('pengajar:user_id,name')
                ->where('status', 'aktif')
                ->when($kategori, function ($q) use ($kategori) {
                    $q->where('kategori', $kategori);
                })
                ->take(20) // Limit loading
                ->get();

            // 4. Rekomendasi Kelas berdasarkan kategori (Yang belum diikuti)
            $catalogQuery = Course::with('pengajar:user_id,name') // Optimize eager load
                ->whereDoesntHave('peserta', function ($q) use ($user) {
                    $q->where('siswa_id', $user->user_id);
                })
                ->where('status', 'aktif');

            // Filter by kategori if specified
            if ($kategori) {
                $catalogQuery->where('kategori', $kategori);
            }

            $recommendedClasses = $catalogQuery
                ->inRandomOrder()
                ->limit(6)
                ->get();

            // 5. & 6. Optimized Category Stats (Replace N+1 Queries)
            $totalPerCategory = Course::selectRaw('kategori, count(*) as count')
                ->where('status', 'aktif')
                ->whereNotNull('kategori')
                ->where('kategori', '!=', '')
                ->groupBy('kategori')
                ->pluck('count', 'kategori');

            $enrolledPerCategory = \Illuminate\Support\Facades\DB::table('kelas')
                ->join('kelas_peserta', 'kelas.kelas_id', '=', 'kelas_peserta.kelas_id')
                ->where('kelas_peserta.siswa_id', $user->user_id)
                ->whereNotNull('kategori')
                ->selectRaw('kelas.kategori, count(*) as count')
                ->groupBy('kelas.kategori')
                ->pluck('count', 'kategori');

            $availableCategories = $totalPerCategory->keys();

            $categoryStats = [];
            foreach ($availableCategories as $cat) {
                $categoryStats[$cat] = [
                    'total' => $totalPerCategory[$cat] ?? 0,
                    'enrolled' => $enrolledPerCategory[$cat] ?? 0
                ];
            }

            // 7. Activity Chart simulasi
            $activityChart = [
                'labels' => ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'],
                'data' => [rand(1, 5), rand(3, 8), rand(2, 6), rand(5, 10), rand(4, 9), rand(8, 12), rand(1, 4)]
            ];

            $data = [
                'userStats' => $userStats,
                'lastClass' => $lastClass,
                'myClasses' => $myClasses,
                'recommendedClasses' => $recommendedClasses,
                'availableCategories' => $availableCategories,
                'categoryStats' => $categoryStats,
                'selectedKategori' => $kategori,
                'activityChart' => $activityChart
            ];

            if ($request->expectsJson()) {
                return $this->success($data, 'Dashboard loaded successfully');
            }

            return view('student.index', $data);

        } catch (\Exception $e) {
            \Log::error('Error in muridDashboard: ' . $e->getMessage());

            if ($request->expectsJson()) {
                return $this->serverError($e->getMessage());
            }

            return back()->with('error', 'Failed to load dashboard: ' . $e->getMessage());
        }
    }

    /**
     * Tampilkan Dashboard untuk Role PENGAJAR
     * Menampilkan statistik mengajar, gamifikasi (level/badge), dan leaderboard.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\View|\Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function pengajarDashboard(Request $request)
    {
        try {
            $user = $request->user();

            // Ambil daftar kelas yang diajar oleh user ini
            $kelasList = $user->kelasAjar()
                ->withCount('peserta')
                ->with('materi:materi_id,kelas_id,judul')
                ->latest()
                ->get()
                ->map(function ($kelas) {
                    return [
                        'kelas_id' => $kelas->kelas_id,
                        'judul' => $kelas->judul,
                        'status' => $kelas->status,
                        'total_siswa' => $kelas->peserta_count,
                        'total_materi' => $kelas->materi->count(),
                    ];
                });

            // Statistik
            $stats = [
                'total_kelas' => $kelasList->count(),
                'total_materi' => $kelasList->sum('total_materi'),
                'total_siswa' => $kelasList->sum('total_siswa'),
            ];

            // Token Statistics
            $tokenBalance = $user->getSaldoToken();
            $tokenEarnings = \App\Models\TokenLog::where('user_id', $user->user_id)
                ->where('tipe', 'pendapatan')
                ->sum('jumlah');
            $recentEarnings = \App\Models\TokenLog::where('user_id', $user->user_id)
                ->where('tipe', 'pendapatan')
                ->latest('tanggal')
                ->take(5)
                ->get();

            $stats['token_balance'] = $tokenBalance;
            $stats['token_earnings'] = $tokenEarnings;

            // Logika Gamifikasi: Hitung Poin & Level Pengajar
            $gamificationService = app(\App\Services\Core\GamificationService::class);
            $gamification = $gamificationService->getTeacherGamificationStats($user, $stats);

            // Data Leaderboard (Papan Peringkat) Pengajar Terbaik
            $leaderboard = $gamificationService->getLeaderboard($user, $gamification['poin']);

            $data = [
                'stats' => $stats,
                'kelasList' => $kelasList,
                'gamification' => $gamification,
                'leaderboard' => $leaderboard,
                'recentEarnings' => $recentEarnings
            ];

            // Respons API
            if ($request->expectsJson()) {
                return $this->success($data, 'Dashboard loaded successfully');
            }

            // Tampilan web
            return view('teacher.index', $data);

        } catch (\Exception $e) {
            \Log::error('Error in pengajarDashboard: ' . $e->getMessage());

            if ($request->expectsJson()) {
                return $this->serverError($e->getMessage());
            }

            return back()->with('error', 'Failed to load dashboard: ' . $e->getMessage());
        }
    }

    /**
     * Halaman 'Kelas Saya' untuk Murid
     * Menampilkan detail semua kelas yang diikuti.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\View
     */
    public function muridKelas(Request $request)
    {
        $user = $request->user();
        $search = $request->query('q');
        $kategori = $request->query('kategori');

        // 1. Ambil Kelas Saya (My Classes)
        $myKelas = $user->kelasIkuti()
            ->with(['pengajar:user_id,name', 'materi'])
            ->withCount('materi') // Ensure this relation count works or use withCount(['materi'])
            ->latest('kelas_peserta.created_at')
            ->get();

        $enrolledKelasIds = $myKelas->pluck('kelas_id')->toArray();

        // 2. Ambil Katalog (Catalog) - Exclude logic handled in view or query
        // Note: Katalog sebaiknya tidak meng-exclude yang sudah diambil supaya user tetap bisa cari,
        // tapi tombolnya jadi "Sudah Bergabung" (handled in view)
        $catalogQuery = Course::with('pengajar')
            ->where('status', 'aktif')
            ->latest();

        if ($search) {
            $catalogQuery->where(function ($q) use ($search) {
                $q->where('judul', 'ILIKE', "%{$search}%")
                    ->orWhere('deskripsi', 'ILIKE', "%{$search}%");
            });
        }

        if ($kategori) {
            $catalogQuery->where('kategori', $kategori);
        }

        $catalogKelas = $catalogQuery->paginate(9);

        return view('student.courses.index', compact('myKelas', 'catalogKelas', 'enrolledKelasIds'));
    }

    /**
     * Halaman 'Materi Saya' untuk Murid
     * Menampilkan semua materi pembelajaran dari kelas yang diikuti.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\View
     */
    public function muridMateri(Request $request)
    {
        $user = $request->user();

        // Ambil kelas yang diikuti beserta materinya
        // Logic ini sudah otomatis grouping karena kita kirim object Kelas
        $kelasMateri = $user->kelasIkuti()
            ->with(['materi', 'pengajar:user_id,name'])
            ->where('status', 'aktif')
            ->get();

        // Ambil ID materi yang sudah dibeli/unlocked oleh user
        $unlockedMateriIds = \Illuminate\Support\Facades\DB::table('materi_akses')
            ->where('user_id', $user->user_id)
            ->pluck('materi_id')
            ->toArray();

        // Cek status beasiswa
        $hasBeasiswa = $user->hasBeasiswa();

        return view('student.materi', compact('kelasMateri', 'unlockedMateriIds', 'hasBeasiswa'));
    }

    /**
     * Halaman 'Sertifikat Saya' untuk Murid
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\View
     */
    public function muridSertifikat(Request $request)
    {
        $user = $request->user();

        // Ambil Sertifikat dari Learning Path yang sudah selesai
        $sertifikatPath = $user->learningPathsEnrolled()
            ->wherePivotNotNull('completed_at')
            ->with(['creator'])
            ->get();

        // TODO: Tambahkan sertifikat dari Kelas standalone jika ada fitur tersebut

        return view('student.sertifikat', compact('sertifikatPath'));
    }

    /**
     * Halaman 'Kelola Kelas' untuk Pengajar
     * Daftar kelas yang dibuat oleh pengajar.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\View
     */
    public function pengajarKelas(Request $request)
    {
        $user = $request->user();

        $kelasList = $user->kelasAjar()
            ->withCount('peserta')
            ->with('materi')
            ->latest()
            ->get()
            ->map(function ($kelas) {
                return [
                    'kelas_id' => $kelas->kelas_id,
                    'judul' => $kelas->judul,
                    'deskripsi' => $kelas->deskripsi,
                    'status' => $kelas->status,
                    'total_siswa' => $kelas->peserta_count,
                    'total_materi' => $kelas->materi->count(),
                    'created_at' => $kelas->created_at,
                ];
            });

        return view('teacher.kelas', ['kelasList' => $kelasList]);
    }

    /**
     * Halaman 'Kelola Materi' untuk Pengajar
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\View
     */
    public function pengajarMateri(Request $request)
    {
        $user = $request->user();

        $kelasAjar = $user->kelasAjar()
            ->with([
                'materi' => function ($q) {
                    $q->orderBy('created_at', 'desc');
                }
            ])
            ->latest()
            ->get();

        return view('teacher.materi', compact('kelasAjar'));
    }

    /**
     * Proses Pembelian Materi Premium
     */
    public function beliMateri(Request $request, $id)
    {
        $user = $request->user();
        $materi = \App\Models\Lesson::findOrFail($id);

        // 1. Validasi
        if (!$materi->is_premium) {
            return back()->with('success', 'Materi ini gratis, silakan langsung dibuka.');
        }

        if ($materi->isUnlockedBy($user)) {
            return back()->with('info', 'Anda sudah memiliki akses ke materi ini.');
        }

        // 2. Cek Saldo
        $saldo = $user->getSaldoToken();
        if ($saldo < $materi->harga_token) {
            return back()->with('error', 'Saldo Token tidak cukup! Silakan top up token Anda.');
        }

        // 3. Transaksi (DB Transaction via Service)
        try {
            $transactionService = app(\App\Services\Payment\TokenTransactionService::class);
            $transactionService->purchaseMaterial($user, $materi);

            return back()->with('success', 'Pembelian berhasil! Materi kini dapat diakses.');

        } catch (\Exception $e) {
            \Log::error('Error in beliMateri: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat memproses transaksi: ' . $e->getMessage());
        }
    }

    public function downloadSertifikatStub()
    {
        return back()->with('info', 'Fitur download sertifikat akan segera tersedia!');
    }
}




