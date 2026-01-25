<?php

namespace App\Http\Controllers;

use App\Models\Donasi;
use Illuminate\Http\Request;

class DonasiController extends Controller
{
    /**
     * Display the donation page with history.
     */
    public function index()
    {
        // 1. Total Donations
        $total_donasi = Donasi::sum('jumlah');

        // 2. Donation Stats & Targets
        $target_donasi = 50000000; // Rp 50.000.000 target
        $progress_percentage = $total_donasi > 0 ? min(($total_donasi / $target_donasi) * 100, 100) : 0;
        $donatur_count = Donasi::count();

        // 3. Donation History (Latest 10)
        $riwayat_donasi = Donasi::latest('tanggal')
            ->take(10)
            ->get();

        return view('donasi', compact('total_donasi', 'riwayat_donasi', 'target_donasi', 'progress_percentage', 'donatur_count'));
    }

    /**
     * Handle incoming donation (Mockup / Future implementation)
     */
    public function store(Request $request)
    {
        // Logic for payment gateway callback or manual input would go here
    }
}
