<?php

namespace App\Http\Controllers\Api\V1\Teacher;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreKelasRequest;
use App\Http\Requests\UpdateKelasRequest;
use App\Http\Traits\ApiResponse;
use App\Http\Resources\KelasResource;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

// The Services
use App\Services\Teacher\ClassManagementService;
use App\Services\Teacher\StudentManagementService;
use App\Services\Teacher\EarningService;
use App\Services\Teacher\AnalyticsService;

class TeacherClassController extends Controller
{
    use AuthorizesRequests;
    use ApiResponse;

    protected $classService;
    protected $studentService;
    protected $earningService;
    protected $analyticsService;

    public function __construct(
        ClassManagementService $classService,
        StudentManagementService $studentService,
        EarningService $earningService,
        AnalyticsService $analyticsService
    ) {
        $this->classService = $classService;
        $this->studentService = $studentService;
        $this->earningService = $earningService;
        $this->analyticsService = $analyticsService;
    }

    /**
     * Simpan kelas baru ke database
     */
    public function store(Request $request)
    {
        try {
            $this->authorize('create', Course::class);

            $data = $request->validate([
                'judul' => 'required|string|max:255',
                'deskripsi' => 'nullable|string',
                'kategori' => 'nullable|string|max:100',
                'harga_token' => 'nullable|integer|min:0',
                'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
            ]);

            if ($request->hasFile('thumbnail')) {
                $data['thumbnail'] = $request->file('thumbnail')->store('thumbnails', 'public');
            }

            $kelas = $this->classService->createClass($request->user() ?? Auth::user(), $data);

            return $this->success(KelasResource::make($kelas), 'Class created successfully', 201);
        } catch (\Exception $e) {
            \Log::error('Error creating class: ' . $e->getMessage());
            return $this->serverError($e->getMessage());
        }
    }

    /**
     * Update kelas ke database
     */
    public function update(Request $request, $id)
    {
        try {
            $user = $request->user() ?? Auth::user();
            $kelas = Course::where('pengajar_id', $user->user_id)->findOrFail($id);

            $this->authorize('update', $kelas);

            $data = $request->validate([
                'judul' => 'nullable|string|max:255',
                'deskripsi' => 'nullable|string',
                'kategori' => 'nullable|string|max:100',
                'harga_token' => 'nullable|integer|min:0',
                'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
            ]);

            if ($request->hasFile('thumbnail')) {
                if ($kelas->thumbnail && Storage::disk('public')->exists($kelas->thumbnail)) {
                    Storage::disk('public')->delete($kelas->thumbnail);
                }
                $data['thumbnail'] = $request->file('thumbnail')->store('thumbnails', 'public');
            }

            $kelas = $this->classService->updateClass($user, $id, array_filter($data));

            return $this->success(KelasResource::make($kelas), 'Class updated successfully');
        } catch (\Exception $e) {
            \Log::error('Error updating class: ' . $e->getMessage());
            return $this->serverError($e->getMessage());
        }
    }

    /**
     * Hapus kelas dari database
     */
    public function destroy(Request $request, $id)
    {
        try {
            $user = $request->user() ?? Auth::user();
            $kelas = Course::where('pengajar_id', $user->user_id)->findOrFail($id);

            $this->authorize('delete', $kelas);

            if ($kelas->thumbnail && Storage::disk('public')->exists($kelas->thumbnail)) {
                Storage::disk('public')->delete($kelas->thumbnail);
            }

            $this->classService->deleteClass($user, $id);

            return $this->success(['deleted' => true], 'Class deleted successfully');
        } catch (\Exception $e) {
            \Log::error('Error deleting class: ' . $e->getMessage());
            return $this->serverError($e->getMessage());
        }
    }

    // ========== API ENDPOINTS (Phase 3E - Teacher) ==========

    public function index(Request $request)
    {
        try {
            $classes = $this->classService->getTeacherClasses($request->user() ?? Auth::user());
            return $this->successWithPagination($classes, 'Classes retrieved successfully');
        } catch (\Exception $e) {
            \Log::error('Error fetching classes: ' . $e->getMessage());
            return $this->serverError($e->getMessage());
        }
    }

    public function publish(Request $request, $id)
    {
        try {
            $kelas = $this->classService->publishClass($request->user(), $id);
            return $this->success(KelasResource::make($kelas), 'Class published');
        } catch (\Exception $e) {
            return $this->serverError($e->getMessage());
        }
    }

    public function archive(Request $request, $id)
    {
        try {
            $kelas = $this->classService->archiveClass($request->user(), $id);
            return $this->success(KelasResource::make($kelas), 'Class archived');
        } catch (\Exception $e) {
            return $this->serverError($e->getMessage());
        }
    }

    public function students(Request $request, $id)
    {
        try {
            $students = $this->studentService->getClassStudents($request->user(), $id);
            return $this->successWithPagination($students, 'Students retrieved successfully');
        } catch (\Exception $e) {
            return $this->serverError($e->getMessage());
        }
    }

    public function stats(Request $request, $id)
    {
        return $this->classAnalytics($request, $id);
    }

    public function earnings(Request $request)
    {
        try {
            $stats = $this->earningService->getEarningStats($request->user());
            return $this->success($stats, 'Earnings retrieved');
        } catch (\Exception $e) {
            return $this->serverError($e->getMessage());
        }
    }

    public function earningHistory(Request $request)
    {
        try {
            $history = $this->earningService->getEarningHistory($request->user());
            return $this->successWithPagination($history, 'Earning history retrieved');
        } catch (\Exception $e) {
            return $this->serverError($e->getMessage());
        }
    }

    public function earningStats(Request $request)
    {
        try {
            $data = $this->earningService->getEarningStats($request->user());
            return $this->success($data, 'Earnings statistics retrieved');
        } catch (\Exception $e) {
            return $this->serverError($e->getMessage());
        }
    }

    public function classAnalytics(Request $request, $classId)
    {
        try {
            $data = $this->analyticsService->getClassAnalytics($request->user(), $classId);
            return $this->success($data, 'Class analytics retrieved');
        } catch (\Exception $e) {
            return $this->serverError($e->getMessage());
        }
    }

    public function analyticsOverview(Request $request)
    {
        try {
            $data = $this->analyticsService->getOverviewAnalytics($request->user());
            return $this->success($data, 'Overview retrieved');
        } catch (\Exception $e) {
            return $this->serverError($e->getMessage());
        }
    }

    public function uploadGrades(Request $request, $id)
    {
        try {
            $request->validate([
                'student_id' => 'required|exists:users,user_id',
                'grade' => 'required|numeric|min:0|max:100'
            ]);
            
            $this->studentService->uploadGrades($request->user(), $id, $request->student_id, ['grade' => $request->grade]);

            return $this->success(['grade' => $request->grade], 'Grades uploaded successfully', 201);
        } catch (\Exception $e) {
            return $this->serverError($e->getMessage());
        }
    }

    public function studentFeedback(Request $request, $classId)
    {
        try {
            $request->validate([
                'student_id' => 'required|exists:users,user_id',
                'feedback' => 'required|string|max:500'
            ]);
            
            $this->studentService->addComment($request->user(), $classId, $request->student_id, $request->feedback);

            return $this->success(['feedback' => $request->feedback], 'Feedback provided', 201);
        } catch (\Exception $e) {
            return $this->serverError($e->getMessage());
        }
    }

    public function studentProgress(Request $request, $studentId)
    {
        try {
            $request->validate(['class_id' => 'required|exists:kelas,kelas_id']);
            $progress = $this->studentService->getStudentProgress($request->user(), $request->class_id, $studentId);

            return $this->success($progress, 'Student progress retrieved');
        } catch (\Exception $e) {
            return $this->serverError($e->getMessage());
        }
    }

    public function addComment(Request $request, $studentId)
    {
        try {
            $request->validate([
                'class_id' => 'required|exists:kelas,kelas_id',
                'comment' => 'required|string|max:1000'
            ]);

            $this->studentService->addComment($request->user(), $request->class_id, $studentId, $request->comment);

            return $this->success(['comment' => $request->comment], 'Comment added successfully', 201);
        } catch (\Exception $e) {
            return $this->serverError($e->getMessage());
        }
    }
}



