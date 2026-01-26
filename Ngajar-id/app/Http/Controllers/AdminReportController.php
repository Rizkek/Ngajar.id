<?php

namespace App\Http\Controllers;

use App\Models\Donasi;
use App\Models\Topup;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AdminReportController extends Controller
{
    // ==========================================
    // LAPORAN DONASI
    // ==========================================

    public function donasiIndex(Request $request)
    {
        $query = Donasi::query();

        // Date Filter
        if ($request->start_date && $request->end_date) {
            $startDate = Carbon::parse($request->start_date)->startOfDay();
            $endDate = Carbon::parse($request->end_date)->endOfDay();
            $query->whereBetween('tanggal', [$startDate, $endDate]);
        }

        // Stats
        $totalDonasi = $query->sum('jumlah');
        $countDonasi = $query->count();
        $avgDonasi = $countDonasi > 0 ? $totalDonasi / $countDonasi : 0;

        $donations = $query->latest('tanggal')->paginate(20)->withQueryString();

        return view('admin.laporan.donasi', compact('donations', 'totalDonasi', 'countDonasi', 'avgDonasi'));
    }

    public function donasiExport(Request $request)
    {
        $query = Donasi::query();

        if ($request->start_date && $request->end_date) {
            $startDate = Carbon::parse($request->start_date)->startOfDay();
            $endDate = Carbon::parse($request->end_date)->endOfDay();
            $query->whereBetween('tanggal', [$startDate, $endDate]);
        }

        $donations = $query->oldest('tanggal')->get();

        $filename = "laporan-donasi-" . date('Y-m-d') . ".csv";
        $handle = fopen('php://memory', 'w');

        // Header
        fputcsv($handle, ['ID', 'Nama Donatur', 'Jumlah (Rp)', 'Tanggal', 'Keterangan']);

        foreach ($donations as $donasi) {
            fputcsv($handle, [
                $donasi->id,
                $donasi->nama ?: 'Hamba Allah',
                $donasi->jumlah,
                $donasi->tanggal->format('Y-m-d H:i:s'),
                'Donasi via Platform'
            ]);
        }

        fseek($handle, 0);

        return response()->stream(
            function () use ($handle) {
                fpassthru($handle);
                fclose($handle);
            },
            200,
            [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ]
        );
    }

    // ==========================================
    // REVENUE REPORT (TOKEN SALES)
    // ==========================================

    public function revenueIndex(Request $request)
    {
        $query = Topup::with('user');

        // Date Filter
        if ($request->start_date && $request->end_date) {
            $startDate = Carbon::parse($request->start_date)->startOfDay();
            $endDate = Carbon::parse($request->end_date)->endOfDay();
            $query->whereBetween('tanggal', [$startDate, $endDate]);
        }

        // Stats
        $totalRevenue = $query->sum('harga');
        $totalTokenSold = $query->sum('jumlah_token');
        $countTransactions = $query->count();

        $topups = $query->latest('tanggal')->paginate(20)->withQueryString();

        // Chart Data (Daily Revenue) for selected period (or last 30 days)
        $chartStartDate = $request->start_date ? Carbon::parse($request->start_date) : now()->subDays(30);
        $chartEndDate = $request->end_date ? Carbon::parse($request->end_date) : now();

        $chartData = $this->getRevenueChartData($chartStartDate, $chartEndDate);

        return view('admin.laporan.revenue', compact('topups', 'totalRevenue', 'totalTokenSold', 'countTransactions', 'chartData'));
    }

    public function revenueExport(Request $request)
    {
        $query = Topup::with('user');

        if ($request->start_date && $request->end_date) {
            $startDate = Carbon::parse($request->start_date)->startOfDay();
            $endDate = Carbon::parse($request->end_date)->endOfDay();
            $query->whereBetween('tanggal', [$startDate, $endDate]);
        }

        $topups = $query->oldest('tanggal')->get();

        $filename = "laporan-revenue-" . date('Y-m-d') . ".csv";
        $handle = fopen('php://memory', 'w');

        // Header
        fputcsv($handle, ['ID', 'User', 'Email', 'Jumlah Token', 'Total Bayar (Rp)', 'Tanggal']);

        foreach ($topups as $topup) {
            fputcsv($handle, [
                $topup->topup_id,
                $topup->user->name ?? 'Deleted User',
                $topup->user->email ?? '-',
                $topup->jumlah_token,
                $topup->harga,
                $topup->tanggal->format('Y-m-d H:i:s'),
            ]);
        }

        fseek($handle, 0);

        return response()->stream(
            function () use ($handle) {
                fpassthru($handle);
                fclose($handle);
            },
            200,
            [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ]
        );
    }

    private function getRevenueChartData($startDate, $endDate)
    {
        $labels = [];
        $data = [];

        $period = new \DatePeriod(
            $startDate->startOfDay(),
            new \DateInterval('P1D'),
            $endDate->endOfDay()
        );

        foreach ($period as $date) {
            $labels[] = $date->format('d M');
            $revenue = Topup::whereDate('tanggal', $date)->sum('harga');
            $data[] = $revenue;
        }

        return [
            'labels' => $labels,
            'data' => $data
        ];
    }
}
