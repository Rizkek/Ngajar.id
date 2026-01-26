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
        // Statistik utama
        $totalMurid = User::murid()->count();
        $totalPengajar = User::pengajar()->count();
        $totalDonasi = Donasi::sum('jumlah');
        $totalKelas = Kelas::count();
        $totalModul = Modul::count();

        // Tren pertumbuhan 6 bulan terakhir
        $userGrowthData = $this->getUserGrowthData();
        $donationTrendData = $this->getDonationTrendData();

        // Aktivitas terbaru (gabungan user dan donasi)
        $recentActivity = $this->getRecentActivity();

        // Data terbaru
        $latestUsers = User::latest()->limit(5)->get();
        $latestDonations = Donasi::orderBy('tanggal', 'desc')->limit(5)->get();

        // Hitung persentase pertumbuhan bulan ini vs bulan lalu
        $muridGrowth = $this->calculateMonthlyGrowth(User::murid());
        $pengajarGrowth = $this->calculateMonthlyGrowth(User::pengajar());

        return view('admin.index', compact(
            'totalMurid',
            'totalPengajar',
            'totalDonasi',
            'totalKelas',
            'totalModul',
            'latestUsers',
            'latestDonations',
            'userGrowthData',
            'donationTrendData',
            'recentActivity',
            'muridGrowth',
            'pengajarGrowth'
        ));
    }

    private function getUserGrowthData()
    {
        $months = [];
        $muridData = [];
        $pengajarData = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i)->endOfMonth(); // Ambil tanggal akhir bulan
            $months[] = $date->format('M Y');

            // Hitung kumulatif (Total user sampai tanggal tersebut)
            $muridData[] = User::murid()
                ->where('created_at', '<=', $date)
                ->count();

            $pengajarData[] = User::pengajar()
                ->where('created_at', '<=', $date)
                ->count();
        }

        return [
            'labels' => $months,
            'murid' => $muridData,
            'pengajar' => $pengajarData
        ];
    }

    private function getDonationTrendData()
    {
        $months = [];
        $amounts = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $months[] = $date->format('M Y');

            $amounts[] = Donasi::whereYear('tanggal', $date->year)
                ->whereMonth('tanggal', $date->month)
                ->sum('jumlah');
        }

        return [
            'labels' => $months,
            'amounts' => $amounts
        ];
    }

    private function getRecentActivity()
    {
        // Ambil user terbaru
        $users = User::latest()->take(5)->get()->map(function ($user) {
            return [
                'type' => 'user',
                'icon' => 'person_add',
                'color' => 'blue',
                'message' => "{$user->name} bergabung sebagai " . ucfirst($user->role),
                'time' => $user->created_at
            ];
        });

        // Ambil donasi terbaru
        $donations = Donasi::orderBy('tanggal', 'desc')->take(5)->get()->map(function ($donation) {
            return [
                'type' => 'donation',
                'icon' => 'volunteer_activism',
                'color' => 'green',
                'message' => ($donation->nama ?: 'Hamba Allah') . " berdonasi Rp " . number_format($donation->jumlah, 0, ',', '.'),
                'time' => $donation->tanggal
            ];
        });

        // Gabung dan urutkan berdasarkan waktu
        return $users->concat($donations)->sortByDesc('time')->take(10)->values();
    }

    private function calculateMonthlyGrowth($query)
    {
        $currentMonth = $query->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        $lastMonth = $query->whereMonth('created_at', now()->subMonth()->month)
            ->whereYear('created_at', now()->subMonth()->year)
            ->count();

        if ($lastMonth == 0) {
            return $currentMonth > 0 ? 100 : 0;
        }

        return round((($currentMonth - $lastMonth) / $lastMonth) * 100, 1);
    }
}
