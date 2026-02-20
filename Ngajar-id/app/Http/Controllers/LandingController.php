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



        // Relawan (ambil 4 pengajar secara acak) - Cache 10 menit
        $volunteers = Cache::remember('landing_volunteers', 600, function () {
            return User::pengajar()
                ->aktif()
                ->inRandomOrder()
                ->take(4)
                ->get();
        });

        return view('welcome', compact(
            'stats',
            'volunteers'
        ));
    }
}
