<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Models\Modul;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Get dashboard data for Murid (student)
     * 
     * @param Request $request
     * @return \Illuminate\Contracts\View\View|\Illuminate\Http\JsonResponse
     */
    public function muridDashboard(Request $request)
    {
        $user = $request->user();

        // Get enrolled classes with their materials
        $kelasYangDiikuti = $user->kelasIkuti()
            ->with(['pengajar:user_id,name', 'materi'])
            ->where('status', 'aktif')
            ->get();

        // Get available materials from enrolled classes
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

        // Get available modules
        $modulList = Modul::with('pembuat:user_id,name')
            ->select('modul_id', 'judul', 'deskripsi', 'tipe', 'token_harga', 'dibuat_oleh')
            ->latest()
            ->limit(10)
            ->get()
            ->map(function ($modul) use ($user) {
                return [
                    'modul_id' => $modul->modul_id,
                    'judul' => $modul->judul,
                    'deskripsi' => $modul->deskripsi,
                    'tipe' => $modul->tipe,
                    'harga' => $modul->token_harga,
                    'sudah_dibeli' => $user->modulDimiliki->contains('modul_id', $modul->modul_id),
                ];
            });

        // Get token balance
        $tokenBalance = $user->getSaldoToken();

        $data = [
            'kelas_count' => $kelasYangDiikuti->count(),
            'materiList' => $materiList,
            'modulList' => $modulList,
            'token_balance' => $tokenBalance,
        ];

        // API response
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => $data,
            ]);
        }

        // Web view
        return view('murid.index', $data);
    }

    /**
     * Get dashboard data for Pengajar (teacher)
     * 
     * @param Request $request
     * @return \Illuminate\Contracts\View\View|\Illuminate\Http\JsonResponse
     */
    public function pengajarDashboard(Request $request)
    {
        $user = $request->user();

        // Get teacher's classes with students count
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

        // Stats
        $stats = [
            'total_kelas' => $kelasList->count(),
            'total_materi' => $user->kelasAjar()->withCount('materi')->get()->sum('materi_count'),
            'total_siswa' => $kelasList->sum('total_siswa'),
        ];

        // Gamification Logic
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

        $gamification = [
            'poin' => $poin,
            'level' => $level,
            'badge_color' => $badgeColor,
            'next_target' => $poin < 100 ? 100 : ($poin < 500 ? 500 : ($poin < 1000 ? 1000 : $poin * 2)),
        ];

        $data = [
            'stats' => $stats,
            'kelasList' => $kelasList,
            'gamification' => $gamification,
        ];

        // API response
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => $data,
            ]);
        }

        // Web view
        return view('pengajar.index', $data);
    }

    /**
     * Get class list for Murid
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
     * Get material list for Murid
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
     * Get class list for Pengajar
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
     * Get material list for Pengajar
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
