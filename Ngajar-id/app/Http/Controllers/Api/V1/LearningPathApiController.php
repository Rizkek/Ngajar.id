<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;

class LearningPathApiController extends Controller
{
    use ApiResponse;

    /**
     * GET /api/v1/learning-paths-api
     * List all learning paths
     */
    public function index(Request $request)
    {
        try {
            $page = $request->input('page', 1);
            $perPage = $request->input('per_page', 10);
            $sortBy = $request->input('sort_by', 'newest'); // newest, popular

            $paths = DB::table('learning_paths')
                ->join('users', 'learning_paths.created_by', '=', 'users.user_id')
                ->leftJoin('learning_path_courses', 'learning_paths.id', '=', 'learning_path_courses.learning_path_id')
                ->select(
                    'learning_paths.id',
                    'learning_paths.judul',
                    'learning_paths.deskripsi',
                    'learning_paths.icon',
                    'learning_paths.level',
                    'learning_paths.duration_weeks',
                    'users.name as creator_name',
                    DB::raw('COUNT(DISTINCT learning_path_courses.kelas_id) as course_count'),
                    'learning_paths.created_at'
                )
                ->where('learning_paths.status', 'active')
                ->whereNull('learning_paths.deleted_at')
                ->groupBy('learning_paths.id', 'learning_paths.judul', 'learning_paths.deskripsi',
                    'learning_paths.icon', 'learning_paths.level', 'learning_paths.duration_weeks',
                    'users.name', 'learning_paths.created_at');

            // Sorting
            switch ($sortBy) {
                case 'popular':
                    $paths->orderBy('course_count', 'desc');
                    break;
                default: // newest
                    $paths->orderBy('learning_paths.created_at', 'desc');
            }

            $total = $paths->count();
            $paths = $paths->paginate($perPage, ['*'], 'page', $page);

            return $this->successWithPagination(
                $paths->items(),
                'Learning paths retrieved',
                $total,
                $perPage,
                $page
            );

        } catch (\Exception $e) {
            return $this->error('Failed to retrieve learning paths: ' . $e->getMessage(), 400);
        }
    }

    /**
     * GET /api/v1/learning-paths-api/{id}
     * Get learning path detail
     */
    public function show($pathId, Request $request)
    {
        try {
            $path = DB::table('learning_paths')
                ->join('users', 'learning_paths.created_by', '=', 'users.user_id')
                ->where('learning_paths.id', $pathId)
                ->select(
                    'learning_paths.id',
                    'learning_paths.judul',
                    'learning_paths.deskripsi',
                    'learning_paths.icon',
                    'learning_paths.level',
                    'learning_paths.duration_weeks',
                    'users.name as creator_name',
                    'learning_paths.created_at'
                )
                ->first();

            if (!$path) {
                return $this->error('Learning path not found', 404);
            }

            // Get courses in this path
            $courses = DB::table('learning_path_courses')
                ->join('kelas', 'learning_path_courses.kelas_id', '=', 'kelas.kelas_id')
                ->where('learning_path_courses.learning_path_id', $pathId)
                ->select(
                    'kelas.kelas_id',
                    'kelas.judul',
                    'kelas.harga',
                    'kelas.level',
                    'learning_path_courses.sequence'
                )
                ->orderBy('learning_path_courses.sequence', 'asc')
                ->get();

            $path->courses = $courses;
            $path->total_courses = count($courses);

            return $this->success($path, 'Learning path retrieved');

        } catch (\Exception $e) {
            return $this->error('Failed to retrieve learning path: ' . $e->getMessage(), 400);
        }
    }

    /**
     * POST /api/v1/learning-paths-api
     * Create learning path (admin only)
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'judul' => 'required|string|max:255',
                'deskripsi' => 'required|string',
                'level' => 'required|in:beginner,intermediate,advanced',
                'duration_weeks' => 'required|integer|min:1',
                'icon' => 'nullable|string',
                'courses' => 'nullable|array',
            ]);

            $pathId = DB::table('learning_paths')->insertGetId([
                'judul' => $validated['judul'],
                'deskripsi' => $validated['deskripsi'],
                'level' => $validated['level'],
                'duration_weeks' => $validated['duration_weeks'],
                'icon' => $validated['icon'] ?? null,
                'created_by' => auth()->id(),
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Attach courses if provided
            if (!empty($validated['courses'])) {
                foreach ($validated['courses'] as $index => $courseId) {
                    DB::table('learning_path_courses')->insert([
                        'learning_path_id' => $pathId,
                        'kelas_id' => $courseId,
                        'sequence' => $index + 1,
                        'created_at' => now(),
                    ]);
                }
            }

            $path = DB::table('learning_paths')->where('id', $pathId)->first();

            return $this->success($path, 'Learning path created successfully', 201);

        } catch (\Exception $e) {
            return $this->error('Failed to create learning path: ' . $e->getMessage(), 400);
        }
    }

    /**
     * PUT /api/v1/learning-paths-api/{id}
     * Update learning path
     */
    public function update($pathId, Request $request)
    {
        try {
            $validated = $request->validate([
                'judul' => 'nullable|string|max:255',
                'deskripsi' => 'nullable|string',
                'level' => 'nullable|in:beginner,intermediate,advanced',
                'duration_weeks' => 'nullable|integer|min:1',
                'icon' => 'nullable|string',
            ]);

            DB::table('learning_paths')
                ->where('id', $pathId)
                ->update(array_merge($validated, ['updated_at' => now()]));

            $path = DB::table('learning_paths')->where('id', $pathId)->first();

            return $this->success($path, 'Learning path updated successfully');

        } catch (\Exception $e) {
            return $this->error('Failed to update learning path: ' . $e->getMessage(), 400);
        }
    }

    /**
     * DELETE /api/v1/learning-paths-api/{id}
     * Delete learning path
     */
    public function destroy($pathId, Request $request)
    {
        try {
            DB::table('learning_paths')
                ->where('id', $pathId)
                ->update(['deleted_at' => now()]);

            return $this->success(null, 'Learning path deleted successfully');

        } catch (\Exception $e) {
            return $this->error('Failed to delete learning path: ' . $e->getMessage(), 400);
        }
    }

    /**
     * POST /api/v1/learning-paths-api/{id}/enroll
     * Enroll user in entire learning path
     */
    public function enroll($pathId, Request $request)
    {
        try {
            $userId = auth()->id();

            $path = DB::table('learning_paths')->where('id', $pathId)->first();

            if (!$path) {
                return $this->error('Learning path not found', 404);
            }

            // Get courses in path
            $courses = DB::table('learning_path_courses')
                ->where('learning_path_id', $pathId)
                ->pluck('kelas_id');

            $enrolled = [];
            $failed = [];

            foreach ($courses as $courseId) {
                try {
                    // Check if already enrolled
                    $existing = DB::table('kelas_peserta')
                        ->where('siswa_id', $userId)
                        ->where('kelas_id', $courseId)
                        ->exists();

                    if (!$existing) {
                        DB::table('kelas_peserta')->insert([
                            'siswa_id' => $userId,
                            'kelas_id' => $courseId,
                            'tanggal_daftar' => now(),
                            'progress' => 0,
                            'status' => 'active',
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);

                        // Award XP for enrolling in course
                        DB::table('users')
                            ->where('user_id', $userId)
                            ->increment('xp', 100);

                        $enrolled[] = $courseId;
                    }
                } catch (\Exception $e) {
                    $failed[] = ['course_id' => $courseId, 'error' => $e->getMessage()];
                }
            }

            return $this->success([
                'learning_path_id' => $pathId,
                'enrolled_courses' => $enrolled,
                'failed_enrollments' => $failed,
                'total_courses' => count($courses),
                'successfully_enrolled' => count($enrolled)
            ], 'Enrolled in learning path successfully', 201);

        } catch (\Exception $e) {
            return $this->error('Failed to enroll in learning path: ' . $e->getMessage(), 400);
        }
    }

    /**
     * GET /api/v1/learning-paths-api/{id}/progress
     * Get user's progress in learning path
     */
    public function progress($pathId, Request $request)
    {
        try {
            $userId = auth()->id();

            $path = DB::table('learning_paths')->where('id', $pathId)->first();

            if (!$path) {
                return $this->error('Learning path not found', 404);
            }

            // Get courses and user progress
            $courses = DB::table('learning_path_courses')
                ->join('kelas', 'learning_path_courses.kelas_id', '=', 'kelas.kelas_id')
                ->leftJoin('kelas_peserta', function ($join) use ($userId) {
                    $join->on('learning_path_courses.kelas_id', '=', 'kelas_peserta.kelas_id')
                        ->where('kelas_peserta.siswa_id', '=', $userId);
                })
                ->where('learning_path_courses.learning_path_id', $pathId)
                ->select(
                    'kelas.kelas_id',
                    'kelas.judul',
                    'learning_path_courses.sequence',
                    'kelas_peserta.progress',
                    'kelas_peserta.status',
                    DB::raw('CASE WHEN kelas_peserta.siswa_id IS NOT NULL THEN true ELSE false END as enrolled')
                )
                ->orderBy('learning_path_courses.sequence', 'asc')
                ->get();

            $totalCourses = count($courses);
            $completedCourses = $courses->where('status', 'completed')->count();
            $enrolledCourses = $courses->where('enrolled', true)->count();

            $overallProgress = $totalCourses > 0 ? round(($completedCourses / $totalCourses) * 100) : 0;

            return $this->success([
                'learning_path_id' => $pathId,
                'learning_path_title' => $path->judul,
                'courses' => $courses,
                'overall_progress' => $overallProgress,
                'completed_courses' => $completedCourses,
                'enrolled_courses' => $enrolledCourses,
                'total_courses' => $totalCourses,
                'is_completed' => $completedCourses === $totalCourses
            ], 'Learning path progress retrieved');

        } catch (\Exception $e) {
            return $this->error('Failed to get progress: ' . $e->getMessage(), 400);
        }
    }

    /**
     * GET /api/v1/learning-paths-api/my
     * Get user's enrolled learning paths
     */
    public function myPaths(Request $request)
    {
        try {
            $userId = auth()->id();
            $page = $request->input('page', 1);
            $perPage = $request->input('per_page', 10);

            $paths = DB::table('learning_paths')
                ->join('learning_path_courses', 'learning_paths.id', '=', 'learning_path_courses.learning_path_id')
                ->join('kelas_peserta', function ($join) use ($userId) {
                    $join->on('learning_path_courses.kelas_id', '=', 'kelas_peserta.kelas_id')
                        ->where('kelas_peserta.siswa_id', '=', $userId);
                })
                ->distinct()
                ->select(
                    'learning_paths.id',
                    'learning_paths.judul',
                    'learning_paths.deskripsi',
                    'learning_paths.level',
                    DB::raw('COUNT(DISTINCT learning_path_courses.kelas_id) as total_courses'),
                    DB::raw('COUNT(DISTINCT CASE WHEN kelas_peserta.status = "completed" THEN learning_path_courses.kelas_id END) as completed_courses')
                )
                ->where('learning_paths.status', 'active')
                ->groupBy('learning_paths.id', 'learning_paths.judul', 'learning_paths.deskripsi', 'learning_paths.level')
                ->paginate($perPage, ['*'], 'page', $page);

            return $this->successWithPagination(
                $paths->items(),
                'Your learning paths retrieved',
                $paths->total(),
                $perPage,
                $page
            );

        } catch (\Exception $e) {
            return $this->error('Failed to retrieve learning paths: ' . $e->getMessage(), 400);
        }
    }

    /**
     * POST /api/v1/learning-paths-api/{id}/attach-courses
     * Attach courses to learning path
     */
    public function attachCourses($pathId, Request $request)
    {
        try {
            $validated = $request->validate([
                'course_ids' => 'required|array',
                'course_ids.*' => 'exists:kelas,kelas_id',
            ]);

            $path = DB::table('learning_paths')->where('id', $pathId)->first();

            if (!$path) {
                return $this->error('Learning path not found', 404);
            }

            // Remove existing courses
            DB::table('learning_path_courses')->where('learning_path_id', $pathId)->delete();

            // Attach new courses
            foreach ($validated['course_ids'] as $index => $courseId) {
                DB::table('learning_path_courses')->insert([
                    'learning_path_id' => $pathId,
                    'kelas_id' => $courseId,
                    'sequence' => $index + 1,
                    'created_at' => now(),
                ]);
            }

            return $this->success(null, 'Courses attached to learning path successfully');

        } catch (\Exception $e) {
            return $this->error('Failed to attach courses: ' . $e->getMessage(), 400);
        }
    }
}
