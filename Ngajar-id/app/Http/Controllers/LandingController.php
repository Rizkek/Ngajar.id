<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Modul;
use App\Models\Donasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class LandingController extends Controller
{
    /**
     * Tampilkan halaman landing aplikasi
     */
    public function index()
    {
        // Statistik untuk hero/impact section (Cache 10 menit)
        $stats = Cache::remember('landing_stats', 600, function () {
            return [
                'pelajar_active' => User::murid()->aktif()->count(),
                'relawan_active' => User::pengajar()->aktif()->count(),
                'modul_count' => Modul::count(),
                'total_donasi' => Donasi::sum('jumlah'),
                'rating' => '4.9', // Hardcoded karena belum ada tabel reviews
            ];
        });

        // Program unggulan (ambil 3 kelas dengan peserta terbanyak) - Cache 10 menit
        $featured_programs = Cache::remember('landing_featured_programs', 600, function () {
            return \App\Models\Kelas::with('pengajar')
                ->where('status', 'aktif')
                ->withCount('peserta')
                ->orderBy('peserta_count', 'desc')
                ->take(3)
                ->get();
        });

        // Relawan (ambil 4 pengajar secara acak) - Cache 10 menit
        $volunteers = Cache::remember('landing_volunteers', 600, function () {
            return User::pengajar()
                ->aktif()
                ->inRandomOrder()
                ->take(4)
                ->get();
        });

        // Progress donasi
        $donation_target = 200000000; // 200 Juta Target
        $donation_progress = ($stats['total_donasi'] / $donation_target) * 100;

        return view('welcome', compact(
            'stats',
            'featured_programs',
            'volunteers',
            'donation_target',
            'donation_progress'
        ));
    }
}
