<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;

use App\Models\LearningPath;
use App\Models\UserPathProgress;
use App\Http\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LearningPathController extends Controller
{
    use ApiResponse;
    /**
     * Browse/Explore Learning Paths
     * API: GET /api/v1/learning-paths?kategori=programming&level=beginner&sort=popular
     * Web: GET /learning-paths
     * Supports both Web & API
     */
    public function index(Request $request)
    {
        try {
            $kategori = $request->get('kategori');
            $level = $request->get('level');
            $sort = $request->get('sort', 'popular'); // popular, newest, title

            $query = LearningPath::with(['creator', 'kelas'])
                ->where('is_active', true);

            // Filter by kategori
            if ($kategori) {
                $query->where('kategori', $kategori);
            }

            // Filter by level
            if ($level) {
                $query->where('level', $level);
            }

            // Sorting
            switch ($sort) {
                case 'newest':
                    $query->latest();
                    break;
                case 'title':
                    $query->orderBy('judul');
                    break;
                case 'popular':
                default:
                    $query->orderBy('total_enrolled', 'desc');
                    break;
            }

            $limit = $request->get('limit', 12);
            $paths = $query->paginate($limit);

            // Support both web & API
            if ($request->expectsJson()) {
                return $this->successWithPagination(
                    $paths->map(function ($path) {
                        return [
                            'id' => $path->path_id,
                            'title' => $path->judul,
                            'description' => $path->deskripsi,
                            'category' => $path->kategori,
                            'level' => $path->level,
                            'price_token' => $path->harga_token,
                            'is_free' => (bool) $path->is_free,
                            'total_courses' => $path->kelas->count(),
                            'total_enrolled' => $path->total_enrolled,
                            'created_by' => $path->creator->name ?? null,
                            'image' => $path->gambar_url ?? null,
                        ];
                    }),
                    'Learning paths retrieved successfully'
                );
            }

            // Get available categories and levels
            $categories = LearningPath::select('kategori')
                ->whereNotNull('kategori')
                ->where('kategori', '!=', '')
                ->distinct()
                ->pluck('kategori');

            $levels = ['Beginner', 'Intermediate', 'Advanced'];

            return view('learning-paths.index', compact('paths', 'categories', 'levels', 'kategori', 'level', 'sort'));

        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return $this->serverError($e->getMessage());
            }
            return redirect()->back()->with('error', 'Failed to load learning paths: ' . $e->getMessage());
        }
    }

    /**
     * Show Learning Path Detail
     * API: GET /api/v1/learning-paths/{id}
     * Web: GET /learning-paths/{id}
     */
    public function show($pathId, Request $request)
    {
        try {
            $path = LearningPath::with(['creator', 'kelas.pengajar'])
                ->findOrFail($pathId);

            $user = auth()->user();
            $progress = null;
            $isEnrolled = false;

            if ($user) {
                $isEnrolled = $path->isEnrolledBy($user);
                $progress = $path->getProgressFor($user);
            }

            // Support both web & API
            if ($request->expectsJson()) {
                return $this->success([
                    'id' => $path->path_id,
                    'title' => $path->judul,
                    'description' => $path->deskripsi,
                    'category' => $path->kategori,
                    'level' => $path->level,
                    'price_token' => $path->harga_token,
                    'is_free' => (bool) $path->is_free,
                    'total_courses' => $path->kelas->count(),
                    'total_enrolled' => $path->total_enrolled,
                    'created_by' => $path->creator->name ?? null,
                    'image' => $path->gambar_url ?? null,
                    'courses' => $path->kelas->map(function ($kelas) {
                        return [
                            'id' => $kelas->kelas_id,
                            'title' => $kelas->judul,
                            'teacher' => $kelas->pengajar->name ?? null,
                        ];
                    }),
                    'is_enrolled' => $isEnrolled,
                    'progress' => $progress ? $progress->progress_percentage : 0,
                ], 'Learning path details retrieved successfully');
            }

            return view('learning-paths.show', compact('path', 'isEnrolled', 'progress'));

        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return $this->notFound('Learning path not found');
            }
            return redirect()->back()->with('error', 'Learning path not found');
        }
    }

    /**
     * Get courses in a learning path
     * API: GET /api/v1/learning-paths/{id}/courses
     */
    public function courses($pathId, Request $request)
    {
        try {
            $path = LearningPath::findOrFail($pathId);

            $courses = $path->kelas()
                ->with('pengajar')
                ->paginate($request->get('limit', 10));

            if ($courses->isEmpty()) {
                return $this->successWithPagination(
                    collect(),
                    'No courses in this learning path'
                );
            }

            return $this->successWithPagination(
                $courses->map(function ($course) {
                    return [
                        'id' => $course->kelas_id,
                        'title' => $course->judul,
                        'description' => $course->deskripsi,
                        'teacher' => $course->pengajar->name ?? null,
                        'price_token' => $course->harga_token,
                        'rating' => $course->rating ?? null,
                        'student_count' => $course->peserta->count(),
                    ];
                }),
                'Learning path courses retrieved successfully'
            );

        } catch (\Exception $e) {
            return $this->notFound('Learning path not found');
        }
    }

    /**
     * Enroll user to a learning path
     * API: POST /api/v1/learning-paths/{id}/enroll
     * Web: POST /learning-paths/{id}/enroll
     * Supports both Web & API
     */
    public function enroll(Request $request, $pathId)
    {
        try {
            $user = $request->user();

            // 0. Cek Role
            if (!$user->isMurid()) {
                if ($request->expectsJson()) {
                    return $this->forbidden('Only student accounts can enroll in learning paths');
                }
                return back()->with('error', 'Hanya akun Murid yang bisa mendaftar learning path.');
            }

            $path = LearningPath::findOrFail($pathId);

            // 1. Check if already enrolled
            if ($path->isEnrolledBy($user)) {
                if ($request->expectsJson()) {
                    return $this->success(null, 'Already enrolled in this learning path', 200);
                }
                return back()->with('info', 'Anda sudah terdaftar di learning path ini.');
            }

            // 2. Logic Pembayaran Token
            $harga = $path->harga_token ?? 0;

            // Bypass jika free atau beasiswa
            if ($path->is_free || $user->hasBeasiswa()) {
                $harga = 0;
            }

            if ($harga > 0) {
                // Cek saldo
                $userToken = $user->token;
                if (!$userToken || !$userToken->cukup($harga)) {
                    if ($request->expectsJson()) {
                        return $this->validationError([
                            'tokens' => ["Insufficient tokens. Required: {$harga}, Available: " . ($userToken->jumlah ?? 0)]
                        ]);
                    }
                    return redirect()->route('topup.create')
                        ->with('error', "Token tidak mencukupi untuk daftar Learning Path ini ({$harga} Token). Saldo Anda: " . ($userToken->jumlah ?? 0));
                }
            }

            // Perform enrollment in transaction
            DB::transaction(function () use ($user, $path, $harga) {
                // Potong Token jika berbayar
                if ($harga > 0) {
                    $user->token->kurang($harga);

                    // Catat Log
                    \App\Models\TokenLog::create([
                        'user_id' => $user->user_id,
                        'jumlah' => $harga,
                        'aksi' => 'kurang',
                        'tipe' => 'pembelian_path',
                        'keterangan' => "Membeli Learning Path: {$path->judul}",
                        'tanggal' => now(),
                    ]);
                }

                // Create progress record (Enrollment)
                UserPathProgress::create([
                    'user_id' => $user->user_id,
                    'path_id' => $path->path_id,
                    'progress_percentage' => 0,
                    'started_at' => now(),
                ]);

                // Increment total enrolled
                $path->increment('total_enrolled');

                // Auto-enroll to ALL classes in this path
                $kelasIds = $path->kelas()->pluck('kelas.kelas_id');
                $existingEnrollments = $user->kelasIkuti()->whereIn('kelas_peserta.kelas_id', $kelasIds)->pluck('kelas_peserta.kelas_id')->toArray();
                $newKelasIds = $kelasIds->diff($existingEnrollments);

                if ($newKelasIds->isNotEmpty()) {
                    $user->kelasIkuti()->attach($newKelasIds, ['tanggal_daftar' => now()]);
                }
            });

            // Response
            $successMsg = $harga > 0
                ? "Berhasil membeli akses Learning Path seharga {$harga} Token!"
                : ($user->hasBeasiswa() ? "Fasilitas Beasiswa: Berhasil mendaftar GRATIS!" : "Berhasil mendaftar gratis!");

            if ($request->expectsJson()) {
                return $this->success([
                    'path_id' => $path->path_id,
                    'path_title' => $path->judul,
                    'enrollment_date' => now()->toIso8601String(),
                    'courses_count' => $path->kelas->count(),
                    'tokens_spent' => $harga,
                ], $successMsg, 201);
            }

            return redirect()->route('learning-paths.show', $pathId)
                ->with('success', $successMsg);

        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return $this->serverError($e->getMessage());
            }
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * My Learning Paths (User's enrolled paths)
     * API: GET /api/v1/student/learning-paths
     * Web: GET /learning-paths/my-paths
     */
    public function myPaths(Request $request)
    {
        try {
            $user = $request->user();

            $enrolledPaths = $user->learningPathsEnrolled()
                ->with(['kelas'])
                ->withPivot('progress_percentage', 'started_at', 'completed_at')
                ->get();

            // Separate ongoing and completed
            $ongoingPaths = $enrolledPaths->filter(function ($path) {
                return $path->pivot->completed_at === null;
            });

            $completedPaths = $enrolledPaths->filter(function ($path) {
                return $path->pivot->completed_at !== null;
            });

            // Support both web & API
            if ($request->expectsJson()) {
                return $this->success([
                    'ongoing' => $ongoingPaths->map(function ($path) {
                        return [
                            'id' => $path->path_id,
                            'title' => $path->judul,
                            'progress' => $path->pivot->progress_percentage,
                            'started_at' => $path->pivot->started_at?->toIso8601String(),
                            'courses_count' => $path->kelas->count(),
                        ];
                    })->values(),
                    'completed' => $completedPaths->map(function ($path) {
                        return [
                            'id' => $path->path_id,
                            'title' => $path->judul,
                            'completed_at' => $path->pivot->completed_at?->toIso8601String(),
                            'courses_count' => $path->kelas->count(),
                        ];
                    })->values(),
                ], 'My learning paths retrieved successfully');
            }

            return view('learning-paths.my-paths', compact('ongoingPaths', 'completedPaths'));

        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return $this->serverError($e->getMessage());
            }
            return redirect()->back()->with('error', 'Failed to load learning paths');
        }
    }

    /**
     * Mark a class as completed in a path
     * API: POST /api/v1/student/learning-paths/{pathId}/complete-class
     */
    public function markKelasCompleted(Request $request, $pathId, $kelasId)
    {
        try {
            $user = $request->user();

            $progress = UserPathProgress::where('user_id', $user->user_id)
                ->where('path_id', $pathId)
                ->firstOrFail();

            $progress->markKelasCompleted($kelasId);

            return $this->success([
                'progress_percentage' => $progress->progress_percentage,
                'is_completed' => $progress->isCompleted(),
            ], 'Class marked as completed successfully');

        } catch (\Exception $e) {
            return $this->notFound('Learning path not found');
        }
    }

    /**
     * Set current class user is taking
     * API: POST /api/v1/student/learning-paths/{pathId}/set-current-class
     */
    public function setCurrentKelas(Request $request, $pathId, $kelasId)
    {
        try {
            $user = $request->user();

            $progress = UserPathProgress::where('user_id', $user->user_id)
                ->where('path_id', $pathId)
                ->firstOrFail();

            $progress->setCurrentKelas($kelasId);

            return $this->success(null, 'Current class updated successfully');

        } catch (\Exception $e) {
            return $this->notFound('Learning path not found');
        }
    }

    /**
     * Download certificate (if path completed)
     * API: GET /api/v1/student/learning-paths/{pathId}/certificate/download
     */
    public function downloadCertificate($pathId, Request $request)
    {
        try {
            $user = auth()->user();
            $path = LearningPath::findOrFail($pathId);

            $progress = UserPathProgress::where('user_id', $user->user_id)
                ->where('path_id', $pathId)
                ->firstOrFail();

            if (!$progress->isCompleted()) {
                if ($request->expectsJson()) {
                    return $this->forbidden('Learning path not yet completed');
                }
                return back()->with('error', 'Anda belum menyelesaikan learning path ini.');
            }

            // Support both API & Web
            if ($request->expectsJson()) {
                // TODO: Generate actual signed URL for PDF download
                return $this->success([
                    'certificate_available' => true,
                    'path_title' => $path->judul,
                    'completed_at' => $progress->completed_at?->toIso8601String(),
                    'download_url' => route('learning-paths.certificate.download', $pathId),
                    'message' => 'Certificate is ready for download',
                ], 'Certificate information retrieved');
            }

            // Web: Show certificate view
            return view('learning-paths.certificate', compact('path', 'progress', 'user'));

        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return $this->notFound('Learning path or certificate not found');
            }
            return redirect()->back()->with('error', 'Learning path not found');
        }
    }
}
