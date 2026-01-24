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
        $totalModul = Modul::count();

        // Growth Trends (Last 6 Months)
        $userGrowthData = $this->getUserGrowthData();
        $donationTrendData = $this->getDonationTrendData();

        // Recent Activity (Combined Users + Donations)
        $recentActivity = $this->getRecentActivity();

        // Data Terbaru
        $latestUsers = User::latest()->limit(5)->get();
        $latestDonations = Donasi::orderBy('tanggal', 'desc')->limit(5)->get();

        // Calculate growth percentage (comparing this month vs last month)
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
            $date = now()->subMonths($i);
            $months[] = $date->format('M Y');

            $muridData[] = User::murid()
                ->whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();

            $pengajarData[] = User::pengajar()
                ->whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
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
        // Get recent users
        $users = User::latest()->take(5)->get()->map(function ($user) {
            return [
                'type' => 'user',
                'icon' => 'person_add',
                'color' => 'blue',
                'message' => "{$user->name} bergabung sebagai " . ucfirst($user->role),
                'time' => $user->created_at
            ];
        });

        // Get recent donations
        $donations = Donasi::orderBy('tanggal', 'desc')->take(5)->get()->map(function ($donation) {
            return [
                'type' => 'donation',
                'icon' => 'volunteer_activism',
                'color' => 'green',
                'message' => ($donation->nama ?: 'Hamba Allah') . " berdonasi Rp " . number_format($donation->jumlah, 0, ',', '.'),
                'time' => $donation->tanggal
            ];
        });

        // Merge and sort by time
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
