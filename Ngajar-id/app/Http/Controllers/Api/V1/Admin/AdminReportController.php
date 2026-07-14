<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponse;
use Illuminate\Http\Request;
use App\Services\Admin\ReportService;

class AdminReportController extends Controller
{
    use ApiResponse;

    protected $reportService;

    public function __construct(ReportService $reportService)
    {
        $this->reportService = $reportService;
    }

    /** GET /admin/reports/donations */
    public function donasiIndex(Request $request)
    {
        try {
            ['data' => $data, 'stats' => $stats] = $this->reportService->getDonationReport(
                $request->only(['status', 'metode', 'start_date', 'end_date', 'search']),
                $request->get('per_page', 20)
            );

            return $this->successWithPagination(
                    $data->map(fn($d) => [
                        'id' => $d->id,
                        'name' => $d->nama,
                        'email' => $d->email,
                        'amount' => $d->jumlah,
                        'status' => $d->status,
                        'date' => $d->tanggal?->toIso8601String(),
                    ]),
                    'Donation report retrieved successfully',
                    $stats
                );
            
        } catch (\Exception $e) {
            return $this->serverError($e->getMessage());
            
        }
    }

    /** GET /admin/reports/donations/export */
    public function donasiExport(Request $request)
    {
        try {
            $content = $this->reportService->exportDonationCsv(
                $request->only(['start_date', 'end_date'])
            );
            $filename = 'donation-report-' . now()->format('Y-m-d-His') . '.csv';
            return response($content, 200, [
                'Content-Type' => 'text/csv; charset=utf-8',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ]);
        } catch (\Exception $e) {
            
        }
    }

    /** GET /admin/reports/revenue */
    public function revenueIndex(Request $request)
    {
        try {
            ['data' => $data, 'stats' => $stats] = $this->reportService->getRevenueReport(
                $request->only(['start_date', 'end_date']),
                $request->get('per_page', 20)
            );

            return $this->successWithPagination(
                    $data->map(fn($t) => [
                        'id' => $t->topup_id,
                        'user' => $t->user->name ?? 'N/A',
                        'email' => $t->user->email ?? 'N/A',
                        'tokens' => $t->jumlah_token,
                        'amount' => $t->harga,
                        'date' => $t->tanggal?->toIso8601String(),
                    ]),
                    'Revenue report retrieved successfully',
                    $stats
                );
            
        } catch (\Exception $e) {
            return $this->serverError($e->getMessage());
            
        }
    }

    /** GET /admin/reports/revenue/export */
    public function revenueExport(Request $request)
    {
        try {
            $content = $this->reportService->exportRevenueCsv(
                $request->only(['start_date', 'end_date'])
            );
            $filename = 'revenue-report-' . now()->format('Y-m-d-His') . '.csv';
            return response($content, 200, [
                'Content-Type' => 'text/csv; charset=utf-8',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ]);
        } catch (\Exception $e) {
            
        }
    }

    /** GET /admin/reports/users */
    public function usersReport(Request $request)
    {
        try {
            ['stats' => $stats, 'byRole' => $byRole] = $this->reportService->getUsersReport();

            return $this->success(['stats' => $stats, 'by_role' => $byRole], 'Users report retrieved successfully');
            
        } catch (\Exception $e) {
            return $this->serverError($e->getMessage());
            
        }
    }

    /** GET /admin/reports/classes */
    public function classesReport(Request $request)
    {
        try {
            ['stats' => $stats, 'classes' => $classes] = $this->reportService->getClassesReport();

            return $this->success([
                    'stats' => $stats,
                    'top_classes' => $classes->map(fn($k) => [
                        'id' => $k->id,
                        'title' => $k->judul,
                        'teacher' => $k->pengajar->name,
                        'students' => $k->peserta_count,
                        'materials' => $k->materi_count,
                    ]),
                ], 'Classes report retrieved successfully');
            
        } catch (\Exception $e) {
            return $this->serverError($e->getMessage());
            
        }
    }

    /** GET /admin/reports/engagement */
    public function engagementReport(Request $request)
    {
        try {
            $stats = $this->reportService->getEngagementReport();

            return $this->success($stats, 'Engagement report retrieved successfully');
            
        } catch (\Exception $e) {
            return $this->serverError($e->getMessage());
            
        }
    }
}
