<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Kelas;
use App\Models\Donasi;
use App\Models\Modul;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index()
    {
        // Statistik Utama
        $totalMurid = User::murid()->count();
        $totalPengajar = User::pengajar()->count();
        $totalDonasi = Donasi::sum('jumlah');
        $totalKelas = Kelas::count();

        // Data Terbaru
        $latestUsers = User::latest()->limit(5)->get();
        $latestDonations = Donasi::latest()->limit(5)->get();

        return view('admin.index', compact(
            'totalMurid',
            'totalPengajar',
            'totalDonasi',
            'totalKelas',
            'latestUsers',
            'latestDonations'
        ));
    }
}
