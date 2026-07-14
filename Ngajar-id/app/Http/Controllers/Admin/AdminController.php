<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\User;
use App\Models\Course;
use App\Models\Donation;
use App\Models\Module;
use Illuminate\Http\Request;

class AdminController extends Controller
{

    public function index(Request $request)
    {
        try {
            // Statistik utama
            $totalMurid = User::murid()->count();
            $totalPengajar = User::pengajar()->count();
            $totalDonasi = Donation::sum('jumlah');
            $totalKelas = Course::count();
            $totalModul = Module::count();

            // Tren pertumbuhan 6 bulan terakhir
            $userGrowthData = $this->getUserGrowthData();
            $donationTrendData = $this->getDonationTrendData();

            // Aktivitas terbaru (gabungan user dan donasi)
            $recentActivity = $this->getRecentActivity();

            // Data terbaru
            $latestUsers = User::latest()->limit(5)->get();
            $latestDonations = Donation::orderBy('tanggal', 'desc')->limit(5)->get();

            // Hitung persentase pertumbuhan bulan ini vs bulan lalu
            $muridGrowth = $this->calculateMonthlyGrowth(User::murid());
            $pengajarGrowth = $this->calculateMonthlyGrowth(User::pengajar());

            $data = compact(
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
            );

            return view('admin.index', $data);

        } catch (\Exception $e) {
            \Log::error('Error in index: ' . $e->getMessage());

            return back()->with('error', 'Failed to load dashboard');
        }
    }

    private function getUserGrowthData()
    {
        $months = [];
        $muridData = [];
        $pengajarData = [];

        $sixMonthsAgo = now()->subMonths(5)->startOfMonth();

        // 1. Get base counts before 6 months ago (2 queries)
        $baseCountMurid = User::murid()->where('created_at', '<', $sixMonthsAgo)->count();
        $baseCountPengajar = User::pengajar()->where('created_at', '<', $sixMonthsAgo)->count();

        // 2. Aggregate monthly growth for the last 6 months (1 query)
        $monthlyCounts = User::whereIn('role', ['murid', 'pengajar'])
            ->where('created_at', '>=', $sixMonthsAgo)
            ->selectRaw('role, EXTRACT(YEAR FROM created_at) as year, EXTRACT(MONTH FROM created_at) as month, count(*) as total')
            ->groupBy('role', \Illuminate\Support\Facades\DB::raw('EXTRACT(YEAR FROM created_at)'), \Illuminate\Support\Facades\DB::raw('EXTRACT(MONTH FROM created_at)'))
            ->get();

        $growthMap = [];
        foreach ($monthlyCounts as $data) {
            $key = $data->year . '-' . str_pad($data->month, 2, '0', STR_PAD_LEFT);
            $growthMap[$key][$data->role] = $data->total;
        }

        $currentMuridTotal = $baseCountMurid;
        $currentPengajarTotal = $baseCountPengajar;

        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $key = $date->format('Y-m');
            $months[] = $date->format('M Y');
            
            $currentMuridTotal += $growthMap[$key]['murid'] ?? 0;
            $currentPengajarTotal += $growthMap[$key]['pengajar'] ?? 0;

            $muridData[] = $currentMuridTotal;
            $pengajarData[] = $currentPengajarTotal;
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

        $sixMonthsAgo = now()->subMonths(5)->startOfMonth();

        // Single query for aggregation
        $monthlyDonations = Donation::where('tanggal', '>=', $sixMonthsAgo)
            ->selectRaw('EXTRACT(YEAR FROM tanggal) as year, EXTRACT(MONTH FROM tanggal) as month, sum(jumlah) as total')
            ->groupBy(\Illuminate\Support\Facades\DB::raw('EXTRACT(YEAR FROM tanggal)'), \Illuminate\Support\Facades\DB::raw('EXTRACT(MONTH FROM tanggal)'))
            ->get();

        $donationMap = [];
        foreach ($monthlyDonations as $data) {
            $key = $data->year . '-' . str_pad($data->month, 2, '0', STR_PAD_LEFT);
            $donationMap[$key] = $data->total;
        }

        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $key = $date->format('Y-m');
            
            $months[] = $date->format('M Y');
            $amounts[] = $donationMap[$key] ?? 0;
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
        $donations = Donation::orderBy('tanggal', 'desc')->take(5)->get()->map(function ($donation) {
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
        // Clone query to avoid mutating the original builder instance
        $currentMonth = (clone $query)->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        $lastMonth = (clone $query)->whereMonth('created_at', now()->subMonth()->month)
            ->whereYear('created_at', now()->subMonth()->year)
            ->count();

        if ($lastMonth == 0) {
            return $currentMonth > 0 ? 100 : 0;
        }

        return round((($currentMonth - $lastMonth) / $lastMonth) * 100, 1);
    }
}


