<?php

namespace App\Services\Admin;

use App\Models\Donation;
use App\Models\Topup;
use App\Models\User;
use App\Models\Course;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReportService
{
    /**
     * Get donation report with optional filters.
     */
    public function getDonationReport(array $filters = [], int $perPage = 20): array
    {
        $query = Donation::query();

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['metode'])) {
            $query->where('metode_pembayaran', $filters['metode']);
        }

        if (!empty($filters['start_date'])) {
            $query->whereDate('tanggal', '>=', $filters['start_date']);
        }

        if (!empty($filters['end_date'])) {
            $query->whereDate('tanggal', '<=', $filters['end_date']);
        }

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('nomor_transaksi', 'like', "%{$search}%");
            });
        }

        // Clone query for stats before paginating
        $statsQuery = clone $query;
        $stats = [
            'total' => $statsQuery->sum('jumlah'),
            'count' => $statsQuery->count(),
            'average' => $statsQuery->count() > 0
                ? $statsQuery->sum('jumlah') / $statsQuery->count()
                : 0,
        ];

        $data = $query->latest('tanggal')->paginate($perPage);

        return compact('data', 'stats');
    }

    /**
     * Export donations as CSV string.
     */
    public function exportDonationCsv(array $filters = []): string
    {
        $query = Donation::query();

        if (!empty($filters['start_date'])) {
            $query->whereDate('tanggal', '>=', $filters['start_date']);
        }

        if (!empty($filters['end_date'])) {
            $query->whereDate('tanggal', '<=', $filters['end_date']);
        }

        $donations = $query->get();

        $handle = fopen('php://memory', 'w');
        fputcsv($handle, ['ID', 'Name', 'Email', 'Amount', 'Status', 'Date']);

        foreach ($donations as $donasi) {
            fputcsv($handle, [
                $donasi->id,
                $donasi->nama,
                $donasi->email,
                $donasi->jumlah,
                $donasi->status,
                $donasi->tanggal?->format('Y-m-d H:i:s'),
            ]);
        }

        rewind($handle);
        $content = stream_get_contents($handle);
        fclose($handle);

        return $content;
    }

    /**
     * Get revenue report (token topups).
     */
    public function getRevenueReport(array $filters = [], int $perPage = 20): array
    {
        $query = Topup::with('user');

        if (!empty($filters['start_date'])) {
            $query->whereDate('tanggal', '>=', $filters['start_date']);
        }

        if (!empty($filters['end_date'])) {
            $query->whereDate('tanggal', '<=', $filters['end_date']);
        }

        $statsQuery = clone $query;
        $stats = [
            'total_revenue' => $statsQuery->sum('harga'),
            'total_tokens' => $statsQuery->sum('jumlah_token'),
            'transaction_count' => $statsQuery->count(),
        ];

        $data = $query->latest('tanggal')->paginate($perPage);

        return compact('data', 'stats');
    }

    /**
     * Export revenue as CSV string.
     */
    public function exportRevenueCsv(array $filters = []): string
    {
        $query = Topup::with('user');

        if (!empty($filters['start_date'])) {
            $query->whereDate('tanggal', '>=', $filters['start_date']);
        }

        if (!empty($filters['end_date'])) {
            $query->whereDate('tanggal', '<=', $filters['end_date']);
        }

        $topups = $query->get();

        $handle = fopen('php://memory', 'w');
        fputcsv($handle, ['ID', 'User', 'Email', 'Tokens', 'Amount', 'Date']);

        foreach ($topups as $topup) {
            fputcsv($handle, [
                $topup->topup_id,
                $topup->user->name ?? 'N/A',
                $topup->user->email ?? 'N/A',
                $topup->jumlah_token,
                $topup->harga,
                $topup->tanggal?->format('Y-m-d H:i:s'),
            ]);
        }

        rewind($handle);
        $content = stream_get_contents($handle);
        fclose($handle);

        return $content;
    }

    /**
     * Get summary user stats report.
     */
    public function getUsersReport(): array
    {
        $stats = [
            'total_users' => User::count(),
            'total_students' => User::where('role', 'murid')->count(),
            'total_teachers' => User::where('role', 'pengajar')->count(),
            'total_admins' => User::where('role', 'admin')->count(),
            'active_users' => User::where('status', 'aktif')->count(),
            'inactive_users' => User::where('status', 'nonaktif')->count(),
        ];

        $byRole = User::groupBy('role')
            ->selectRaw('role, count(*) as total')
            ->get()
            ->pluck('total', 'role');

        return compact('stats', 'byRole');
    }

    /**
     * Get summary classes stats report.
     */
    public function getClassesReport(): array
    {
        $totalClasses = Course::count();
        $enrolled = $totalClasses > 0 ? Course::withCount('peserta')->get()->sum('peserta_count') : 0;

        $stats = [
            'total_classes' => $totalClasses,
            'active_classes' => Course::where('status', 'aktif')->count(),
            'archived_classes' => Course::where('status', 'selesai')->count(),
            'rejected_classes' => Course::where('status', 'ditolak')->count(),
            'total_students_enrolled' => $enrolled,
            'avg_students_per_class' => $totalClasses > 0 ? round($enrolled / $totalClasses, 1) : 0,
        ];

        $classes = Course::with('pengajar')
            ->withCount('peserta', 'materi')
            ->latest()
            ->limit(20)
            ->get();

        return compact('stats', 'classes');
    }

    /**
     * Get engagement/activity report.
     */
    public function getEngagementReport(): array
    {
        return [
            'total_users_this_month' => User::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),
            'active_users_this_week' => User::where('status', 'aktif')
                ->where('updated_at', '>=', now()->subWeek())
                ->count(),
            'new_classes_this_month' => Course::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),
            'most_popular_class' => Course::withCount('peserta')
                ->orderByDesc('peserta_count')
                ->first()?->judul ?? 'N/A',
            'total_donations_this_month' => Donation::whereMonth('tanggal', now()->month)
                ->whereYear('tanggal', now()->year)
                ->where('status', 'settlement')
                ->sum('jumlah'),
        ];
    }
}


