<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponse;
use App\Http\Resources\KelasResource;
use Illuminate\Http\Request;
use App\Services\Admin\ClassModerationService;

class AdminCourseController extends Controller
{
    use ApiResponse;

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

            return $this->successWithPagination(KelasResource::collection($data), 'Classes retrieved successfully');
            
        } catch (\Exception $e) {
            return $this->serverError($e->getMessage());
            
        }
    }

    /** GET /admin/classes/{id} */
    public function show(Request $request, $id)
    {
        try {
            $kelas = $this->moderationService->getClass($id);
            return $this->success(new KelasResource($kelas), 'Class retrieved successfully');
            
        } catch (\Exception $e) {
            return $this->serverError($e->getMessage());
            
        }
    }

    /** POST /admin/classes/{id}/approve */
    public function approve(Request $request, $id)
    {
        try {
            $kelas = $this->moderationService->approveClass($id);
            return $this->success(new KelasResource($kelas), 'Class approved successfully');
            
        } catch (\Exception $e) {
            return $this->serverError($e->getMessage());
            
        }
    }

    /** POST /admin/classes/{id}/reject */
    public function reject(Request $request, $id)
    {
        try {
            $validated = $request->validate(['reason' => 'nullable|string|max:500']);
            $kelas = $this->moderationService->rejectClass($id, $validated['reason'] ?? null);
            return $this->success(new KelasResource($kelas), 'Class rejected successfully');
            
        } catch (\Exception $e) {
            return $this->serverError($e->getMessage());
            
        }
    }

    /** POST /admin/classes/{id}/archive */
    public function archive(Request $request, $id)
    {
        try {
            $kelas = $this->moderationService->archiveClass($id);
            return $this->success(new KelasResource($kelas), 'Class archived successfully');
            
        } catch (\Exception $e) {
            return $this->serverError($e->getMessage());
            
        }
    }

    /** DELETE /admin/classes/{id} */
    public function destroy(Request $request, $id)
    {
        try {
            $this->moderationService->deleteClass($id);
            return $this->success(null, 'Class deleted successfully');
            
        } catch (\Exception $e) {
            return $this->serverError($e->getMessage());
            
        }
    }

    /** POST /admin/classes/{id}/flag */
    public function flag(Request $request, $id)
    {
        try {
            $validated = $request->validate(['reason' => 'required|string|max:500']);
            $kelas = $this->moderationService->flagClass($id, $validated['reason']);
            return $this->success(new KelasResource($kelas), 'Class flagged for review');
            
        } catch (\Exception $e) {
            return $this->serverError($e->getMessage());
            
        }
    }
}


