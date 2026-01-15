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

        $data = [
            'stats' => $stats,
            'kelasList' => $kelasList,
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
}
