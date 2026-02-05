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

        // Ambil daftar kelas yang sedang diikuti user (status aktif)
        $kelasYangDiikuti = $user->kelasIkuti()
            ->with(['pengajar:user_id,name', 'materi'])
            ->where('status', 'aktif')
            ->get();

        // Kumpulkan semua materi dari kelas-kelas tersebut
        $materiList = [];
        foreach ($kelasYangDiikuti as $kelas) {
            foreach ($kelas->materi as $materi) {
                $materiList[] = [
                    'judul' => $materi->judul,
                    'kelas' => $kelas->judul,
                    'tipe' => $materi->tipe,
                    'file_url' => $materi->file_url,
                ];
            }
        }

        // Ambil daftar Modul Belajar (marketplace)
        // Ambil daftar Modul Belajar (marketplace) - Cached for 10 minutes
        $modulList = Cache::remember('modul_list_dashboard', 600, function () use ($user) {
            return Modul::with('pembuat:user_id,name')
                ->select('modul_id', 'judul', 'deskripsi', 'tipe', 'token_harga', 'dibuat_oleh')
                ->latest()
                ->limit(10)
                ->get();
        });

        // Map data modul (perlu dilakukan di luar cache jika tergantung user, TAPI 'sudah_dibeli' tergantung user)
        // Jadi kita cache raw data modulnya, lalu map status pembeliannya
        $modulList = $modulList->map(function ($modul) use ($user) {
            return [
                'modul_id' => $modul->modul_id,
                'judul' => $modul->judul,
                'deskripsi' => $modul->deskripsi,
                'tipe' => $modul->tipe,
                'harga' => $modul->token_harga,
                'sudah_dibeli' => $user->modulDimiliki->contains('modul_id', $modul->modul_id),
            ];
        });

        // Ambil saldo token
        $tokenBalance = $user->getSaldoToken();

        $data = [
            'kelas_count' => $kelasYangDiikuti->count(),
            'materiList' => $materiList,
            'modulList' => $modulList,
            'token_balance' => $tokenBalance,
        ];

        // Respons API
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => $data,
            ]);
        }

        // Tampilan web
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
            'leaderboard' => $leaderboard
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

        $kelasList = $user->kelasIkuti()
            ->with(['pengajar:user_id,name,email', 'materi'])
            ->withPivot('tanggal_daftar')
            ->latest()
            ->get()
            ->map(function ($kelas) {
                return [
                    'kelas_id' => $kelas->kelas_id,
                    'judul' => $kelas->judul,
                    'deskripsi' => $kelas->deskripsi,
                    'status' => $kelas->status,
                    'pengajar_name' => $kelas->pengajar->name ?? 'N/A',
                    'pengajar_email' => $kelas->pengajar->email ?? 'N/A',
                    'total_materi' => $kelas->materi->count(),
                    'tanggal_daftar' => $kelas->pivot->tanggal_daftar,
                ];
            });

        return view('murid.kelas', ['kelasList' => $kelasList]);
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

        $kelasYangDiikuti = $user->kelasIkuti()
            ->with(['materi', 'pengajar:user_id,name'])
            ->where('status', 'aktif')
            ->get();

        $materiList = [];
        foreach ($kelasYangDiikuti as $kelas) {
            foreach ($kelas->materi as $materi) {
                $materiList[] = [
                    'materi_id' => $materi->materi_id,
                    'judul' => $materi->judul,
                    'deskripsi' => $materi->deskripsi,
                    'tipe' => $materi->tipe,
                    'kelas_judul' => $kelas->judul,
                    'pengajar_name' => $kelas->pengajar->name ?? 'N/A',
                    'file_url' => $materi->file_url,
                ];
            }
        }

        return view('murid.materi', ['materiList' => $materiList]);
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
            ->with('materi')
            ->get();

        $materiList = [];
        foreach ($kelasAjar as $kelas) {
            foreach ($kelas->materi as $materi) {
                $materiList[] = [
                    'materi_id' => $materi->materi_id,
                    'judul' => $materi->judul,
                    'deskripsi' => $materi->deskripsi,
                    'tipe' => $materi->tipe,
                    'kelas_judul' => $kelas->judul,
                    'kelas_id' => $kelas->kelas_id,
                    'file_url' => $materi->file_url,
                ];
            }
        }

        return view('pengajar.materi', ['materiList' => $materiList]);
    }
}
