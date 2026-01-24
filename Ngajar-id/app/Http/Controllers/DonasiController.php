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

        // 2. Donation History (Latest 10)
        $riwayat_donasi = Donasi::latest('tanggal')
            ->take(10)
            ->get();

        return view('donasi', compact('total_donasi', 'riwayat_donasi'));
    }

    /**
     * Handle incoming donation (Mockup / Future implementation)
     */
    public function store(Request $request)
    {
        // Logic for payment gateway callback or manual input would go here
    }
}
