<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Modul;
use App\Models\Donasi;
use Illuminate\Http\Request;

class LandingController extends Controller
{
    /**
     * Show the application landing page.
     */
    public function index()
    {
        // 1. Stats for Hero/Impact Section
        $stats = [
            'pelajar_active' => User::murid()->aktif()->count(),
            'relawan_active' => User::pengajar()->aktif()->count(),
            'modul_count' => Modul::count(),
            'total_donasi' => Donasi::sum('jumlah'),
            'rating' => '4.9', // Hardcoded for now as we don't have reviews table yet
        ];

        // 2. Featured Programs (Ambil 3 Modul Premium/Terbaru sebagai contoh program)
        // Idealnya nanti ambil dari table 'kelas' yang populer
        $featured_programs = \App\Models\Kelas::with('pengajar')
            ->where('status', 'aktif')
            ->withCount('peserta')
            ->orderBy('peserta_count', 'desc')
            ->take(3)
            ->get();

        // 3. Meet the Volunteers (Ambil 3 Pengajar Random)
        $volunteers = User::pengajar()
            ->aktif()
            ->inRandomOrder()
            ->take(4)
            ->get();

        // 4. Donation Progress
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
