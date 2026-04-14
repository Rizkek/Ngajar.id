<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Kelas;
use App\Models\Materi;
use App\Models\User;
use App\Http\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class TeacherCourseController extends Controller
{
    use ApiResponse;

    /**
     * GET /api/v1/teacher/kelas
     * Get all courses created by the current teacher
     */
    public function index(Request $request)
    {
        try {
            $teacherId = auth()->id();

            $courses = Kelas::where('pengajar_id', $teacherId)
                ->with([
                    'peserta' => function($q) {
                        $q->count();
                    },
                    'materi' => function($q) {
                        $q->select('materi_id', 'kelas_id', 'judul');
                    }
                ])
                ->select('kelas_id', 'judul', 'deskripsi', 'kategori', 'status', 'created_at')
                ->paginate($request->input('per_page', 10));

            // Add stats to each course
            $courses->load([
                'peserta' => function($q) use ($teacherId) {
                    // Get enrollment counts
                }
            ]);

            $coursesData = $courses->map(function($c) use ($teacherId) {
                $totalPeserta = DB::table('kelas_peserta')->where('kelas_id', $c->kelas_id)->count();
                $completedPeserta = DB::table('kelas_peserta')
                    ->where('kelas_id', $c->kelas_id)
                    ->where('status', 'completed')
                    ->count();
                $avgProgress = DB::table('kelas_peserta')
                    ->where('kelas_id', $c->kelas_id)
                    ->avg('progress');

                return [
                    'kelas_id' => $c->kelas_id,
                    'judul' => $c->judul,
                    'deskripsi' => $c->deskripsi,
                    'kategori' => $c->kategori,
                    'status' => $c->status,
                    'created_at' => $c->created_at,
                    'total_peserta' => $totalPeserta,
                    'completed_peserta' => $completedPeserta,
                    'avg_progress' => round($avgProgress ?? 0, 2),
                    'total_materi' => $c->materi()->count(),
                ];
            });

            return $this->successWithPagination(
                $coursesData->toArray(),
                'My courses retrieved successfully',
                $courses->total(),
                $courses->per_page(),
                $courses->current_page()
            );

        } catch (\Exception $e) {
            return $this->error('Failed to retrieve courses: ' . $e->getMessage(), 400);
        }
    }

    /**
     * POST /api/v1/teacher/kelas
     * Create a new course
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'judul' => 'required|string|max:255',
                'deskripsi' => 'required|string|max:1000',
                'kategori' => 'required|string|in:Programming,Data Science,Teknologi,Business,Design,Other',
                'status' => 'sometimes|in:draft,aktif,archived',
            ]);

            if ($validator->fails()) {
                return $this->error('Validation failed: ' . implode(', ', $validator->errors()->all()), 400);
            }

            $course = Kelas::create([
                'pengajar_id' => auth()->id(),
                'judul' => $request->judul,
                'deskripsi' => $request->deskripsi,
                'kategori' => $request->kategori,
                'status' => $request->input('status', 'draft'),
            ]);

            // Award XP for creating course
            auth()->user()->update([
                'xp' => (auth()->user()->xp ?? 0) + 500,
            ]);

            if ($request->expectsJson()) {
                return $this->success([
                    'kelas_id' => $course->kelas_id,
                    'judul' => $course->judul,
                    'status' => $course->status,
                    'xp_earned' => 500,
                ], 'Course created successfully', 201);
            }

            return redirect()->back()->with('success', 'Course created successfully');

        } catch (\Exception $e) {
            return $this->error('Failed to create course: ' . $e->getMessage(), 400);
        }
    }

    /**
     * GET /api/v1/teacher/kelas/{id}
     * Get course details with students and materials
     */
    public function show($id, Request $request)
    {
        try {
            $course = Kelas::findOrFail($id);

            // Check ownership
            if ($course->pengajar_id !== auth()->id()) {
                return $this->error('Not authorized to view this course', 403);
            }

            $course->load([
                'materi:materi_id,kelas_id,judul,tipe,created_at',
            ]);

            $totalPeserta = DB::table('kelas_peserta')->where('kelas_id', $id)->count();
            $completedPeserta = DB::table('kelas_peserta')
                ->where('kelas_id', $id)
                ->where('status', 'completed')
                ->count();

            if ($request->expectsJson()) {
                return $this->success([
                    'kelas_id' => $course->kelas_id,
                    'judul' => $course->judul,
                    'deskripsi' => $course->deskripsi,
                    'kategori' => $course->kategori,
                    'status' => $course->status,
                    'created_at' => $course->created_at,
                    'total_peserta' => $totalPeserta,
                    'completed_peserta' => $completedPeserta,
                    'materials' => $course->materi,
                ], 'Course detail retrieved successfully');
            }

            return view('teacher.kelas.show', compact('course'));

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->error('Course not found', 404);
        } catch (\Exception $e) {
            return $this->error('Failed to retrieve course: ' . $e->getMessage(), 400);
        }
    }

    /**
     * PUT /api/v1/teacher/kelas/{id}
     * Update course details
     */
    public function update($id, Request $request)
    {
        try {
            $course = Kelas::findOrFail($id);

            // Check ownership
            if ($course->pengajar_id !== auth()->id()) {
                return $this->error('Not authorized to edit this course', 403);
            }

            $validator = Validator::make($request->all(), [
                'judul' => 'sometimes|string|max:255',
                'deskripsi' => 'sometimes|string|max:1000',
                'kategori' => 'sometimes|string|in:Programming,Data Science,Teknologi,Business,Design,Other',
                'status' => 'sometimes|in:draft,aktif,archived',
            ]);

            if ($validator->fails()) {
                return $this->error('Validation failed: ' . implode(', ', $validator->errors()->all()), 400);
            }

            $course->update($request->only(['judul', 'deskripsi', 'kategori', 'status']));

            if ($request->expectsJson()) {
                return $this->success($course, 'Course updated successfully');
            }

            return redirect()->back()->with('success', 'Course updated successfully');

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->error('Course not found', 404);
        } catch (\Exception $e) {
            return $this->error('Failed to update course: ' . $e->getMessage(), 400);
        }
    }

    /**
     * DELETE /api/v1/teacher/kelas/{id}
     * Delete a course (only if no students enrolled)
     */
    public function destroy($id, Request $request)
    {
        try {
            $course = Kelas::findOrFail($id);

            // Check ownership
            if ($course->pengajar_id !== auth()->id()) {
                return $this->error('Not authorized to delete this course', 403);
            }

            // Check if has enrolled students
            $enrollmentCount = DB::table('kelas_peserta')->where('kelas_id', $id)->count();
            if ($enrollmentCount > 0 && !$request->input('force')) {
                return $this->error('Cannot delete course with enrolled students. Use force=true to delete anyway.', 400);
            }

            $course->delete();

            if ($request->expectsJson()) {
                return $this->success(['kelas_id' => $id], 'Course deleted successfully');
            }

            return redirect()->back()->with('success', 'Course deleted successfully');

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->error('Course not found', 404);
        } catch (\Exception $e) {
            return $this->error('Failed to delete course: ' . $e->getMessage(), 400);
        }
    }

    /**
     * GET /api/v1/teacher/kelas/{id}/students
     * Get all students enrolled in the course with their progress
     */
    public function getStudents($id, Request $request)
    {
        try {
            $course = Kelas::findOrFail($id);

            // Check ownership
            if ($course->pengajar_id !== auth()->id()) {
                return $this->error('Not authorized to view this course', 403);
            }

            $students = DB::table('kelas_peserta')
                ->join('users', 'kelas_peserta.siswa_id', '=', 'users.user_id')
                ->where('kelas_peserta.kelas_id', $id)
                ->select(
                    'users.user_id',
                    'users.name',
                    'users.email',
                    'kelas_peserta.progress',
                    'kelas_peserta.status',
                    'kelas_peserta.tanggal_daftar',
                    'kelas_peserta.completion_date',
                    DB::raw('(SELECT COUNT(*) FROM material_progress WHERE material_progress.student_id = users.user_id AND material_progress.is_completed = true AND material_progress.materi_id IN (SELECT materi_id FROM materi WHERE kelas_id = ' . $id . ')) as materi_completed'),
                    DB::raw('(SELECT COUNT(*) FROM materi WHERE materi.kelas_id = ' . $id . ') as total_materi')
                )
                ->paginate($request->input('per_page', 20));

            return $this->successWithPagination(
                $students->items(),
                'Course students retrieved successfully',
                $students->total(),
                $students->per_page(),
                $students->current_page()
            );

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->error('Course not found', 404);
        } catch (\Exception $e) {
            return $this->error('Failed to retrieve students: ' . $e->getMessage(), 400);
        }
    }

    /**
     * GET /api/v1/teacher/kelas/{id}/materi
     * Get all materials in a course
     */
    public function getMaterials($id, Request $request)
    {
        try {
            $course = Kelas::findOrFail($id);

            // Check ownership
            if ($course->pengajar_id !== auth()->id()) {
                return $this->error('Not authorized to view this course', 403);
            }

            $materials = Materi::where('kelas_id', $id)
                ->select('materi_id', 'judul', 'deskripsi', 'tipe', 'created_at')
                ->paginate($request->input('per_page', 20));

            return $this->successWithPagination(
                $materials->items(),
                'Course materials retrieved successfully',
                $materials->total(),
                $materials->per_page(),
                $materials->current_page()
            );

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->error('Course not found', 404);
        } catch (\Exception $e) {
            return $this->error('Failed to retrieve materials: ' . $e->getMessage(), 400);
        }
    }

    /**
     * POST /api/v1/teacher/kelas/{id}/materi
     * Add a new material to the course
     */
    public function addMaterial($id, Request $request)
    {
        try {
            $course = Kelas::findOrFail($id);

            // Check ownership
            if ($course->pengajar_id !== auth()->id()) {
                return $this->error('Not authorized to edit this course', 403);
            }

            $validator = Validator::make($request->all(), [
                'judul' => 'required|string|max:255',
                'deskripsi' => 'required|string|max:1000',
                'tipe' => 'required|in:video,artikel,pdf,quiz,assignment',
            ]);

            if ($validator->fails()) {
                return $this->error('Validation failed: ' . implode(', ', $validator->errors()->all()), 400);
            }

            $material = Materi::create([
                'kelas_id' => $id,
                'judul' => $request->judul,
                'deskripsi' => $request->deskripsi,
                'tipe' => $request->tipe,
                'status' => 'aktif',
            ]);

            // Award XP for creating material
            auth()->user()->update([
                'xp' => (auth()->user()->xp ?? 0) + 100,
            ]);

            if ($request->expectsJson()) {
                return $this->success([
                    'materi_id' => $material->materi_id,
                    'kelas_id' => $material->kelas_id,
                    'judul' => $material->judul,
                    'tipe' => $material->tipe,
                    'xp_earned' => 100,
                ], 'Material created successfully', 201);
            }

            return redirect()->back()->with('success', 'Material created successfully');

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->error('Course not found', 404);
        } catch (\Exception $e) {
            return $this->error('Failed to create material: ' . $e->getMessage(), 400);
        }
    }

    /**
     * GET /api/v1/teacher/dashboard
     * Get teacher dashboard stats
     */
    public function dashboard(Request $request)
    {
        try {
            $teacherId = auth()->id();

            $totalCourses = Kelas::where('pengajar_id', $teacherId)->count();
            $totalStudents = DB::table('kelas_peserta')
                ->whereIn('kelas_id', function($q) use ($teacherId) {
                    $q->select('kelas_id')->from('kelas')->where('pengajar_id', $teacherId);
                })
                ->count();

            $totalCompleted = DB::table('kelas_peserta')
                ->where('status', 'completed')
                ->whereIn('kelas_id', function($q) use ($teacherId) {
                    $q->select('kelas_id')->from('kelas')->where('pengajar_id', $teacherId);
                })
                ->count();

            $avgProgress = DB::table('kelas_peserta')
                ->whereIn('kelas_id', function($q) use ($teacherId) {
                    $q->select('kelas_id')->from('kelas')->where('pengajar_id', $teacherId);
                })
                ->avg('progress');

            $totalXP = auth()->user()->xp ?? 0;
            $level = auth()->user()->level ?? 1;

            return $this->success([
                'total_courses' => $totalCourses,
                'total_students' => $totalStudents,
                'total_completed' => $totalCompleted,
                'avg_progress' => round($avgProgress ?? 0, 2),
                'xp' => $totalXP,
                'level' => $level,
            ], 'Teacher dashboard retrieved successfully');

        } catch (\Exception $e) {
            return $this->error('Failed to retrieve dashboard: ' . $e->getMessage(), 400);
        }
    }
}
