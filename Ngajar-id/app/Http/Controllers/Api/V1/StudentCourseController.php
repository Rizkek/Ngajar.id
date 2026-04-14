<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Kelas;
use App\Models\Materi;
use App\Models\User;
use App\Http\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;

class StudentCourseController extends Controller
{
    use ApiResponse;

    /**
     * GET /api/v1/kelas
     * Browse all available courses with search, filter, and pagination
     */
    public function index(Request $request)
    {
        try {
            $query = Kelas::where('status', 'aktif');

            // Search by title or description
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('judul', 'like', "%{$search}%")
                      ->orWhere('deskripsi', 'like', "%{$search}%");
                });
            }

            // Filter by category
            if ($request->filled('kategori')) {
                $query->where('kategori', $request->kategori);
            }

            // Filter by instructor (pengajar_id)
            if ($request->filled('pengajar_id')) {
                $query->where('pengajar_id', $request->pengajar_id);
            }

            // Pagination
            $perPage = $request->input('per_page', 10);
            $courses = $query->with([
                'pengajar:user_id,name,xp,level',
                'peserta:user_id',
                'materi:materi_id,kelas_id,judul',
            ])
            ->paginate($perPage);

            // Add enrollment status for authenticated user
            if (auth()->check()) {
                $userId = auth()->id();
                $courses->load([
                    'peserta' => function($q) use ($userId) {
                        $q->where('siswa_id', $userId);
                    }
                ]);
            }

            return $this->successWithPagination(
                $courses->items(),
                'Courses retrieved successfully',
                $courses->total(),
                $courses->per_page(),
                $courses->current_page()
            );

        } catch (\Exception $e) {
            return $this->error('Failed to retrieve courses: ' . $e->getMessage(), 400);
        }
    }

    /**
     * GET /api/v1/kelas/{id}
     * Get course detail with materials and enrollment info
     */
    public function show($id, Request $request)
    {
        try {
            $course = Kelas::with([
                'pengajar:user_id,name,bio,xp,level',
                'materi:materi_id,kelas_id,judul,deskripsi,tipe,created_at',
                'peserta:siswa_id,user_id' => function($q) {
                    $q->count();
                }
            ])->findOrFail($id);

            // Get materials with student progress if authenticated
            $materials = $course->materi;

            if (auth()->check()) {
                $userId = auth()->id();

                // Check if enrolled
                $enrollment = DB::table('kelas_peserta')
                    ->where('kelas_id', $id)
                    ->where('siswa_id', $userId)
                    ->first(['progress', 'status', 'completion_date']);

                // Get material progress
                $materialProgress = DB::table('material_progress')
                    ->where('student_id', $userId)
                    ->whereIn('materi_id', $materials->pluck('materi_id'))
                    ->get()
                    ->keyBy('materi_id');

                $materials = $materials->map(function($m) use ($materialProgress) {
                    $progress = $materialProgress[$m->materi_id] ?? null;
                    return [
                        'materi_id' => $m->materi_id,
                        'judul' => $m->judul,
                        'deskripsi' => $m->deskripsi,
                        'tipe' => $m->tipe,
                        'created_at' => $m->created_at,
                        'is_completed' => $progress?->is_completed ?? false,
                        'completed_at' => $progress?->completed_at,
                    ];
                });

                return $this->success([
                    'course' => [
                        'kelas_id' => $course->kelas_id,
                        'judul' => $course->judul,
                        'deskripsi' => $course->deskripsi,
                        'kategori' => $course->kategori,
                        'status' => $course->status,
                        'pengajar' => $course->pengajar,
                        'total_peserta' => $course->peserta()->count(),
                        'total_materi' => $course->materi()->count(),
                    ],
                    'materials' => $materials,
                    'user_enrollment' => $enrollment ? [
                        'status' => $enrollment->status,
                        'progress' => $enrollment->progress,
                        'completion_date' => $enrollment->completion_date,
                    ] : null,
                ], 'Course detail retrieved successfully');
            }

            return $this->success([
                'course' => $course,
                'materials' => $materials,
            ], 'Course detail retrieved successfully');

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->error('Course not found', 404);
        } catch (\Exception $e) {
            return $this->error('Failed to retrieve course: ' . $e->getMessage(), 400);
        }
    }

    /**
     * POST /api/v1/kelas/{id}/enroll
     * Enroll current user to a course
     */
    public function enroll($id, Request $request)
    {
        try {
            $user = auth()->user();

            // Check course exists
            $course = Kelas::findOrFail($id);

            // Check if already enrolled
            $existing = DB::table('kelas_peserta')
                ->where('kelas_id', $id)
                ->where('siswa_id', $user->user_id)
                ->first();

            if ($existing) {
                return $this->error('Already enrolled to this course', 400);
            }

            // Enroll user
            DB::table('kelas_peserta')->insert([
                'kelas_id' => $id,
                'siswa_id' => $user->user_id,
                'tanggal_daftar' => now(),
                'progress' => 0,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Award 100 XP for enrollment
            $user->update([
                'xp' => ($user->xp ?? 0) + 100,
                'level' => max(1, intdiv(($user->xp ?? 0) + 100, 2000) + 1),
            ]);

            if ($request->expectsJson()) {
                return $this->success([
                    'kelas_id' => $id,
                    'user_id' => $user->user_id,
                    'status' => 'active',
                    'xp_earned' => 100,
                ], 'Successfully enrolled to course', 201);
            }

            return redirect()->back()->with('success', 'Successfully enrolled to course');

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->error('Course not found', 404);
        } catch (\Exception $e) {
            return $this->error('Failed to enroll: ' . $e->getMessage(), 400);
        }
    }

    /**
     * GET /api/v1/my-courses
     * Get authenticated user's enrolled courses with progress
     */
    public function myCourses(Request $request)
    {
        try {
            $userId = auth()->id();

            $courses = DB::table('kelas_peserta')
                ->join('kelas', 'kelas_peserta.kelas_id', '=', 'kelas.kelas_id')
                ->join('users', 'kelas.pengajar_id', '=', 'users.user_id')
                ->where('kelas_peserta.siswa_id', $userId)
                ->select(
                    'kelas.kelas_id',
                    'kelas.judul',
                    'kelas.deskripsi',
                    'kelas.kategori',
                    'users.name as pengajar_name',
                    'kelas_peserta.progress',
                    'kelas_peserta.status',
                    'kelas_peserta.tanggal_daftar',
                    'kelas_peserta.completion_date',
                    DB::raw('(SELECT COUNT(*) FROM materi WHERE materi.kelas_id = kelas.kelas_id) as total_materi'),
                    DB::raw('(SELECT COUNT(*) FROM material_progress WHERE material_progress.student_id = ' . $userId . ' AND material_progress.is_completed = true AND material_progress.materi_id IN (SELECT materi_id FROM materi WHERE kelas_id = kelas.kelas_id)) as materi_completed')
                )
                ->paginate($request->input('per_page', 10));

            return $this->successWithPagination(
                $courses->items(),
                'My courses retrieved successfully',
                $courses->total(),
                $courses->per_page(),
                $courses->current_page()
            );

        } catch (\Exception $e) {
            return $this->error('Failed to retrieve courses: ' . $e->getMessage(), 400);
        }
    }
}
