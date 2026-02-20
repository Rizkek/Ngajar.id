<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Models\Modul;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    /**
     * Tampilkan Dashboard untuk Role MURID
     * Menampilkan ringkasan kelas, materi terbaru, modul rekomendasi, dan saldo token.
     * 
     * @param Request $request
     * @return \Illuminate\Contracts\View\View|\Illuminate\Http\JsonResponse
     */
    public function muridDashboard(Request $request)
    {
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
        $catalogQuery = Kelas::with('pengajar:user_id,name') // Optimize eager load
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
        $totalPerCategory = Kelas::selectRaw('kategori, count(*) as count')
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
            return response()->json(['success' => true, 'data' => $data]);
        }

        return view('murid.index', $data);
    }

    /**
     * Tampilkan Dashboard untuk Role PENGAJAR
     * Menampilkan statistik mengajar, gamifikasi (level/badge), dan leaderboard.
     * 
     * @param Request $request
     * @return \Illuminate\Contracts\View\View|\Illuminate\Http\JsonResponse
     */
    public function pengajarDashboard(Request $request)
    {
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
        $poin = ($stats['total_kelas'] * 50) + ($stats['total_materi'] * 10) + ($stats['total_siswa'] * 2);

        if ($poin >= 1000) {
            $level = 'Legenda Ngajar.ID';
            $badgeColor = 'purple';
        } elseif ($poin >= 500) {
            $level = 'Pahlawan Pendidikan';
            $badgeColor = 'amber';
        } elseif ($poin >= 100) {
            $level = 'Relawan Bersemi';
            $badgeColor = 'teal';
        } else {
            $level = 'Relawan Tunas';
            $badgeColor = 'slate';
        }

        // Data Leaderboard (Papan Peringkat) Pengajar Terbaik
        // (Simulasi data untuk demo, seharusnya query database)
        $leaderboard = collect([
            ['name' => 'Budi Santoso', 'poin' => 1250, 'avatar' => 'https://ui-avatars.com/api/?name=Budi+Santoso&background=random'],
            ['name' => 'Siti Aminah', 'poin' => 980, 'avatar' => 'https://ui-avatars.com/api/?name=Siti+Aminah&background=random'],
            ['name' => 'Rizky Fadillah', 'poin' => 850, 'avatar' => 'https://ui-avatars.com/api/?name=Rizky+Fadillah&background=random'],
            ['name' => $user->name, 'poin' => $poin, 'avatar' => 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&background=random'] // User saat ini
        ])->sortByDesc('poin')->values();

        $gamification = [
            'poin' => $poin,
            'level' => $level,
            'badge_color' => $badgeColor,
            'next_target' => $poin < 100 ? 100 : ($poin < 500 ? 500 : ($poin < 1000 ? 1000 : $poin * 2)),
            'points_needed' => ($poin < 100 ? 100 : ($poin < 500 ? 500 : ($poin < 1000 ? 1000 : $poin * 2))) - $poin
        ];

        $data = [
            'stats' => $stats,
            'kelasList' => $kelasList,
            'gamification' => $gamification,
            'leaderboard' => $leaderboard,
            'recentEarnings' => $recentEarnings
        ];

        // Respons API
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => $data,
            ]);
        }

        // Tampilan web
        return view('pengajar.index', $data);
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
        $catalogQuery = Kelas::with('pengajar')
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

        return view('murid.kelas.index', compact('myKelas', 'catalogKelas', 'enrolledKelasIds'));
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

        return view('murid.materi', compact('kelasMateri', 'unlockedMateriIds', 'hasBeasiswa'));
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

        return view('murid.sertifikat', compact('sertifikatPath'));
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

        return view('pengajar.kelas', ['kelasList' => $kelasList]);
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

        return view('pengajar.materi', compact('kelasAjar'));
    }

    /**
     * Proses Pembelian Materi Premium
     */
    public function beliMateri(Request $request, $id)
    {
        $user = $request->user();
        $materi = \App\Models\Materi::findOrFail($id);

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

        // 3. Transaksi (DB Transaction)
        try {
            \Illuminate\Support\Facades\DB::transaction(function () use ($user, $materi) {
                // Kurangi Token dari Murid
                $token = $user->token()->lockForUpdate()->first();
                if (!$token) {
                    throw new \Exception('Token wallet tidak ditemukan. Silakan hubungi admin.');
                }

                $token->decrement('jumlah', $materi->harga_token);

                // Catat Log Token Murid (Pengurangan)
                \App\Models\TokenLog::create([
                    'user_id' => $user->user_id,
                    'jumlah' => -$materi->harga_token,
                    'tipe' => 'penggunaan',
                    'keterangan' => 'Membeli materi: ' . $materi->judul
                ]);

                // Transfer Token ke Pengajar (80% dari harga)
                $kelas = $materi->kelas;
                if ($kelas && $kelas->pengajar_id) {
                    $pengajarShare = (int) ($materi->harga_token * 0.8); // 80% untuk pengajar
                    $adminCommission = $materi->harga_token - $pengajarShare; // 20% untuk admin

                    // Tambah token ke pengajar
                    $pengajarToken = \App\Models\Token::firstOrCreate(
                        ['user_id' => $kelas->pengajar_id],
                        ['jumlah' => 0, 'last_update' => now()]
                    );
                    $pengajarToken->increment('jumlah', $pengajarShare);
                    $pengajarToken->update(['last_update' => now()]);

                    // Log token pengajar (Penambahan)
                    \App\Models\TokenLog::create([
                        'user_id' => $kelas->pengajar_id,
                        'jumlah' => $pengajarShare,
                        'tipe' => 'pendapatan',
                        'keterangan' => 'Penjualan materi: ' . $materi->judul . ' (80%)'
                    ]);

                    // Log komisi admin (untuk tracking)
                    \App\Models\TokenLog::create([
                        'user_id' => 1, // Assuming admin user_id is 1, adjust if needed
                        'jumlah' => $adminCommission,
                        'tipe' => 'komisi',
                        'keterangan' => 'Komisi penjualan materi: ' . $materi->judul . ' (20%)'
                    ]);
                }

                // Insert Akses Materi
                \Illuminate\Support\Facades\DB::table('materi_akses')->insert([
                    'user_id' => $user->user_id,
                    'materi_id' => $materi->materi_id,
                    'unlocked_at' => now(),
                ]);
            });

            return back()->with('success', 'Pembelian berhasil! Materi kini dapat diakses.');

        } catch (\Exception $e) {
            \Log::error('Error in beliMateri: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat memproses transaksi: ' . $e->getMessage());
        }
    }
}
