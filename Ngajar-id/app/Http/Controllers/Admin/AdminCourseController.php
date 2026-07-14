<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\KelasResource;
use Illuminate\Http\Request;
use App\Services\Admin\ClassModerationService;

class AdminCourseController extends Controller
{

    protected $moderationService;

    public function __construct(ClassModerationService $moderationService)
    {
        $this->moderationService = $moderationService;
    }

    /** GET /admin/classes */
    public function index(Request $request)
    {
        try {
            $data = $this->moderationService->listClasses(
                $request->only(['status', 'kategori', 'search']),
                $request->get('per_page', 15)
            );

            $stats = [
                'total' => \App\Models\Course::count(),
                'aktif' => \App\Models\Course::where('status', 'aktif')->count(),
                'selesai' => \App\Models\Course::whereIn('status', ['arsip', 'selesai'])->count(),
                'ditolak' => \App\Models\Course::where('status', 'ditolak')->count(),
            ];

            return view('admin.courses.index', compact('data', 'stats'));
        } catch (\Exception $e) {

            return back()->with('error', $e->getMessage());
        }
    }

    /** GET /admin/classes/{id} */
    public function show(Request $request, $id)
    {
        try {
            $kelas = $this->moderationService->getClass($id);

            return view('admin.courses.show', compact('kelas'));
        } catch (\Exception $e) {

            return back()->with('error', $e->getMessage());
        }
    }

    /** POST /admin/classes/{id}/approve */
    public function approve(Request $request, $id)
    {
        try {
            $kelas = $this->moderationService->approveClass($id);

            return back()->with('success', 'Class approved successfully');
        } catch (\Exception $e) {

            return back()->with('error', $e->getMessage());
        }
    }

    /** POST /admin/classes/{id}/reject */
    public function reject(Request $request, $id)
    {
        try {
            $validated = $request->validate(['reason' => 'nullable|string|max:500']);
            $kelas = $this->moderationService->rejectClass($id, $validated['reason'] ?? null);

            return back()->with('success', 'Class rejected successfully');
        } catch (\Exception $e) {

            return back()->with('error', $e->getMessage());
        }
    }

    /** POST /admin/classes/{id}/archive */
    public function archive(Request $request, $id)
    {
        try {
            $kelas = $this->moderationService->archiveClass($id);

            return back()->with('success', 'Class archived successfully');
        } catch (\Exception $e) {

            return back()->with('error', $e->getMessage());
        }
    }

    /** DELETE /admin/classes/{id} */
    public function destroy(Request $request, $id)
    {
        try {
            $this->moderationService->deleteClass($id);

            return back()->with('success', 'Class deleted successfully');
        } catch (\Exception $e) {

            return back()->with('error', $e->getMessage());
        }
    }

    /** POST /admin/classes/{id}/flag */
    public function flag(Request $request, $id)
    {
        try {
            $validated = $request->validate(['reason' => 'required|string|max:500']);
            $kelas = $this->moderationService->flagClass($id, $validated['reason']);

            return back()->with('success', 'Class flagged for review');
        } catch (\Exception $e) {

            return back()->with('error', $e->getMessage());
        }
    }
}

