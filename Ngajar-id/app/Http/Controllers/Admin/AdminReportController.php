<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Admin\ReportService;

class AdminReportController extends Controller
{

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

            return view('admin.laporan.donasi', compact('data', 'stats'));
        } catch (\Exception $e) {

            return back()->with('error', $e->getMessage());
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
            return back()->with('error', $e->getMessage());
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

            return view('admin.laporan.revenue', compact('data', 'stats'));
        } catch (\Exception $e) {

            return back()->with('error', $e->getMessage());
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
            return back()->with('error', $e->getMessage());
        }
    }

    /** GET /admin/reports/users */
    public function usersReport(Request $request)
    {
        try {
            ['stats' => $stats, 'byRole' => $byRole] = $this->reportService->getUsersReport();

            return redirect()->route('admin.laporan.donasi')->with('info', 'Laporan Users sedang dalam pengembangan.');
        } catch (\Exception $e) {

            return back()->with('error', $e->getMessage());
        }
    }

    /** GET /admin/reports/classes */
    public function classesReport(Request $request)
    {
        try {
            ['stats' => $stats, 'classes' => $classes] = $this->reportService->getClassesReport();

            return redirect()->route('admin.laporan.donasi')->with('info', 'Laporan Kelas sedang dalam pengembangan.');
        } catch (\Exception $e) {

            return back()->with('error', $e->getMessage());
        }
    }

    /** GET /admin/reports/engagement */
    public function engagementReport(Request $request)
    {
        try {
            $stats = $this->reportService->getEngagementReport();

            return redirect()->route('admin.laporan.donasi')->with('info', 'Laporan Engagement sedang dalam pengembangan.');
        } catch (\Exception $e) {

            return back()->with('error', $e->getMessage());
        }
    }
}
