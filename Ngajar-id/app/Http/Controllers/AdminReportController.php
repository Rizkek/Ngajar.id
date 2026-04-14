<?php

namespace App\Http\Controllers;

use App\Models\Donasi;
use App\Models\Topup;
use App\Models\User;
use App\Models\Kelas;
use App\Http\Traits\ApiResponse;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AdminReportController extends Controller
{
    use ApiResponse;

    /**
     * Donation report
     * GET /admin/reports/donations
     */
    public function donasiIndex(Request $request)
    {
        try {
            $query = Donasi::query();

            // Date filter
            if ($request->has('start_date') && $request->start_date) {
                $query->whereDate('tanggal', '>=', $request->start_date);
            }
            if ($request->has('end_date') && $request->end_date) {
                $query->whereDate('tanggal', '<=', $request->end_date);
            }

            $data = $query->latest('tanggal')->paginate($request->get('per_page', 20));

            $stats = [
                'total' => $query->sum('jumlah'),
                'count' => $query->count(),
                'average' => $query->count() > 0 ? $query->sum('jumlah') / $query->count() : 0,
            ];

            if ($request->expectsJson()) {
                return $this->successWithPagination(
                    $data->map(fn($d) => $this->formatDonation($d)),
                    'Donation report retrieved successfully',
                    $stats
                );
            }

            return view('admin.reports.donations', compact('data', 'stats'));
        } catch (\Exception $e) {
            \Log::error('AdminReportController@donasiIndex: ' . $e->getMessage());
            if ($request->expectsJson()) {
                return $this->serverError($e->getMessage());
            }
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Export donation report as CSV
     * GET /admin/reports/donations/export
     */
    public function donasiExport(Request $request)
    {
        try {
            $query = Donasi::query();

            if ($request->has('start_date') && $request->start_date) {
                $query->whereDate('tanggal', '>=', $request->start_date);
            }
            if ($request->has('end_date') && $request->end_date) {
                $query->whereDate('tanggal', '<=', $request->end_date);
            }

            $donations = $query->get();

            $filename = "donation-report-" . now()->format('Y-m-d-His') . ".csv";
            $handle = fopen('php://memory', 'w');

            fputcsv($handle, ['ID', 'Name', 'Email', 'Amount', 'Status', 'Date']);

            foreach ($donations as $donasi) {
                fputcsv($handle, [
                    $donasi->id,
                    $donasi->nama,
                    $donasi->email,
                    $donasi->jumlah,
                    $donasi->status,
                    $donasi->tanggal->format('Y-m-d H:i:s'),
                ]);
            }

            rewind($handle);
            $content = stream_get_contents($handle);
            fclose($handle);

            return response($content, 200, [
                'Content-Type' => 'text/csv; charset=utf-8',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ]);
        } catch (\Exception $e) {
            \Log::error('AdminReportController@donasiExport: ' . $e->getMessage());
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Revenue report (token sales)
     * GET /admin/reports/revenue
     */
    public function revenueIndex(Request $request)
    {
        try {
            $query = Topup::with('user');

            // Date filter
            if ($request->has('start_date') && $request->start_date) {
                $query->whereDate('tanggal', '>=', $request->start_date);
            }
            if ($request->has('end_date') && $request->end_date) {
                $query->whereDate('tanggal', '<=', $request->end_date);
            }

            $data = $query->latest('tanggal')->paginate($request->get('per_page', 20));

            $stats = [
                'total_revenue' => $query->sum('harga'),
                'total_tokens' => $query->sum('jumlah_token'),
                'transaction_count' => $query->count(),
            ];

            if ($request->expectsJson()) {
                return $this->successWithPagination(
                    $data->map(fn($t) => $this->formatTopup($t)),
                    'Revenue report retrieved successfully',
                    $stats
                );
            }

            return view('admin.reports.revenue', compact('data', 'stats'));
        } catch (\Exception $e) {
            \Log::error('AdminReportController@revenueIndex: ' . $e->getMessage());
            if ($request->expectsJson()) {
                return $this->serverError($e->getMessage());
            }
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Export revenue report as CSV
     * GET /admin/reports/revenue/export
     */
    public function revenueExport(Request $request)
    {
        try {
            $query = Topup::with('user');

            if ($request->has('start_date') && $request->start_date) {
                $query->whereDate('tanggal', '>=', $request->start_date);
            }
            if ($request->has('end_date') && $request->end_date) {
                $query->whereDate('tanggal', '<=', $request->end_date);
            }

            $topups = $query->get();

            $filename = "revenue-report-" . now()->format('Y-m-d-His') . ".csv";
            $handle = fopen('php://memory', 'w');

            fputcsv($handle, ['ID', 'User', 'Email', 'Tokens', 'Amount', 'Date']);

            foreach ($topups as $topup) {
                fputcsv($handle, [
                    $topup->topup_id,
                    $topup->user->name ?? 'N/A',
                    $topup->user->email ?? 'N/A',
                    $topup->jumlah_token,
                    $topup->harga,
                    $topup->tanggal->format('Y-m-d H:i:s'),
                ]);
            }

            rewind($handle);
            $content = stream_get_contents($handle);
            fclose($handle);

            return response($content, 200, [
                'Content-Type' => 'text/csv; charset=utf-8',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ]);
        } catch (\Exception $e) {
            \Log::error('AdminReportController@revenueExport: ' . $e->getMessage());
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Users report
     * GET /admin/reports/users
     */
    public function usersReport(Request $request)
    {
        try {
            $stats = [
                'total_users' => User::count(),
                'total_students' => User::where('role', 'murid')->count(),
                'total_teachers' => User::where('role', 'pengajar')->count(),
                'total_admins' => User::where('role', 'admin')->count(),
                'active_users' => User::where('status', 'aktif')->count(),
                'inactive_users' => User::where('status', 'nonaktif')->count(),
            ];

            $users = User::groupBy('role')
                ->selectRaw('role, count(*) as total')
                ->get();

            if ($request->expectsJson()) {
                return $this->success(
                    [
                        'stats' => $stats,
                        'by_role' => $users->pluck('total', 'role'),
                    ],
                    'Users report retrieved successfully'
                );
            }

            return view('admin.reports.users', compact('stats', 'users'));
        } catch (\Exception $e) {
            \Log::error('AdminReportController@usersReport: ' . $e->getMessage());
            if ($request->expectsJson()) {
                return $this->serverError($e->getMessage());
            }
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Classes report
     * GET /admin/reports/classes
     */
    public function classesReport(Request $request)
    {
        try {
            $stats = [
                'total_classes' => Kelas::count(),
                'active_classes' => Kelas::where('status', 'aktif')->count(),
                'archived_classes' => Kelas::where('status', 'selesai')->count(),
                'rejected_classes' => Kelas::where('status', 'ditolak')->count(),
                'total_students_enrolled' => Kelas::count() > 0 ? Kelas::withCount('peserta')->get()->sum('peserta_count') : 0,
                'avg_students_per_class' => Kelas::count() > 0 ? (Kelas::withCount('peserta')->get()->sum('peserta_count') / Kelas::count()) : 0,
            ];

            $classes = Kelas::with('pengajar')
                ->withCount('peserta', 'materi')
                ->latest()
                ->limit(20)
                ->get();

            if ($request->expectsJson()) {
                return $this->success(
                    [
                        'stats' => $stats,
                        'top_classes' => $classes->map(fn($k) => [
                            'id' => $k->id,
                            'title' => $k->judul,
                            'teacher' => $k->pengajar->name,
                            'students' => $k->peserta_count,
                            'materials' => $k->materi_count,
                        ]),
                    ],
                    'Classes report retrieved successfully'
                );
            }

            return view('admin.reports.classes', compact('stats', 'classes'));
        } catch (\Exception $e) {
            \Log::error('AdminReportController@classesReport: ' . $e->getMessage());
            if ($request->expectsJson()) {
                return $this->serverError($e->getMessage());
            }
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Engagement report
     * GET /admin/reports/engagement
     */
    public function engagementReport(Request $request)
    {
        try {
            $stats = [
                'total_users_this_month' => User::whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year)
                    ->count(),
                'active_users_this_week' => User::where('status', 'aktif')
                    ->where('updated_at', '>=', now()->subWeek())
                    ->count(),
                'new_classes_this_month' => Kelas::whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year)
                    ->count(),
                'most_popular_class' => Kelas::withCount('peserta')
                    ->orderByDesc('peserta_count')
                    ->first()?->judul ?? 'N/A',
                'total_donations_this_month' => Donasi::whereMonth('tanggal', now()->month)
                    ->whereYear('tanggal', now()->year)
                    ->where('status', 'settlement')
                    ->sum('jumlah'),
            ];

            if ($request->expectsJson()) {
                return $this->success($stats, 'Engagement report retrieved successfully');
            }

            return view('admin.reports.engagement', compact('stats'));
        } catch (\Exception $e) {
            \Log::error('AdminReportController@engagementReport: ' . $e->getMessage());
            if ($request->expectsJson()) {
                return $this->serverError($e->getMessage());
            }
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Format donation for API response
     */
    private function formatDonation($donasi)
    {
        return [
            'id' => $donasi->id,
            'name' => $donasi->nama,
            'email' => $donasi->email,
            'amount' => $donasi->jumlah,
            'status' => $donasi->status,
            'date' => $donasi->tanggal?->toIso8601String(),
        ];
    }

    /**
     * Format topup for API response
     */
    private function formatTopup($topup)
    {
        return [
            'id' => $topup->topup_id,
            'user' => $topup->user->name ?? 'N/A',
            'email' => $topup->user->email ?? 'N/A',
            'tokens' => $topup->jumlah_token,
            'amount' => $topup->harga,
            'date' => $topup->tanggal?->toIso8601String(),
        ];
    }
}
