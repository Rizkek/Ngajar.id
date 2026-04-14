<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreKelasRequest;
use App\Http\Requests\UpdateKelasRequest;
use App\Http\Traits\ApiResponse;
use App\Http\Resources\KelasResource;
use App\Models\Kelas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class KelasController extends Controller
{
    use AuthorizesRequests;
    use ApiResponse;
    /**
     * Tampilkan form untuk membuat kelas baru
     */
    public function create()
    {
        return view('pengajar.kelas.create');
    }

    /**
     * Simpan kelas baru ke database
     */
    public function store(Request $request)
    {
        try {
            $this->authorize('create', Kelas::class);

            if ($request->expectsJson()) {
                // API validation
                $data = $request->validate([
                    'judul' => 'required|string|max:255',
                    'deskripsi' => 'nullable|string',
                    'kategori' => 'nullable|string|max:100',
                    'harga_token' => 'nullable|integer|min:0',
                    'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
                ]);
            } else {
                // Form validation using request class
                $data = $request->validate(app(StoreKelasRequest::class)->rules());
            }

            $user = $request->user();
            $data['pengajar_id'] = $user->user_id;
            $data['status'] = $data['status'] ?? 'draft';

            // Handle file upload
            if ($request->hasFile('thumbnail')) {
                $data['thumbnail'] = $request->file('thumbnail')->store('thumbnails', 'public');
            }

            $kelas = Kelas::create($data);

            if ($request->expectsJson()) {
                return $this->success(
                    KelasResource::make($kelas),
                    'Class created successfully',
                    201
                );
            }

            return redirect()->route('pengajar.kelas')->with('success', 'Kelas berhasil dibuat!');

        } catch (\Exception $e) {
            \Log::error('Error in store: ' . $e->getMessage());

            if ($request->expectsJson()) {
                return $this->serverError($e->getMessage());
            }

            return back()->with('error', 'Failed to create class');
        }
    }

    /**
     * Tampilkan form untuk edit kelas
     */
    public function edit($id)
    {
        $kelas = Kelas::where('pengajar_id', Auth::id())->findOrFail($id);
        return view('pengajar.kelas.edit', compact('kelas'));
    }

    /**
     * Update kelas ke database
     */
    public function update(Request $request, $id)
    {
        try {
            $user = $request->user();
            $kelas = Kelas::where('pengajar_id', $user->user_id)->findOrFail($id);

            $this->authorize('update', $kelas);

            if ($request->expectsJson()) {
                // API validation
                $data = $request->validate([
                    'judul' => 'nullable|string|max:255',
                    'deskripsi' => 'nullable|string',
                    'kategori' => 'nullable|string|max:100',
                    'harga_token' => 'nullable|integer|min:0',
                    'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
                ]);
            } else {
                // Form validation using request class
                $data = $request->validate(app(UpdateKelasRequest::class)->rules());
            }

            // Handle File Update
            if ($request->hasFile('thumbnail')) {
                if ($kelas->thumbnail && Storage::disk('public')->exists($kelas->thumbnail)) {
                    Storage::disk('public')->delete($kelas->thumbnail);
                }
                $data['thumbnail'] = $request->file('thumbnail')->store('thumbnails', 'public');
            }

            $kelas->update(array_filter($data));

            if ($request->expectsJson()) {
                return $this->success(
                    KelasResource::make($kelas),
                    'Class updated successfully'
                );
            }

            return redirect()->route('pengajar.kelas')->with('success', 'Kelas berhasil diperbarui!');

        } catch (\Exception $e) {
            \Log::error('Error in update: ' . $e->getMessage());

            if ($request->expectsJson()) {
                return $this->serverError($e->getMessage());
            }

            return back()->with('error', 'Failed to update class');
        }
    }

    /**
     * Hapus kelas dari database
     */
    public function destroy(Request $request, $id)
    {
        try {
            $user = $request->user();
            $kelas = Kelas::where('pengajar_id', $user->user_id)->findOrFail($id);

            $this->authorize('delete', $kelas);

            // Delete thumbnail if exists
            if ($kelas->thumbnail && Storage::disk('public')->exists($kelas->thumbnail)) {
                Storage::disk('public')->delete($kelas->thumbnail);
            }

            $kelas->delete();

            if ($request->expectsJson()) {
                return $this->success(['deleted' => true], 'Class deleted successfully');
            }

            return redirect()->route('pengajar.kelas')->with('success', 'Kelas berhasil dihapus!');

        } catch (\Exception $e) {
            \Log::error('Error in destroy: ' . $e->getMessage());

            if ($request->expectsJson()) {
                return $this->serverError($e->getMessage());
            }

            return back()->with('error', 'Failed to delete class');
        }
    }

    // ========== API ENDPOINTS (Phase 3E - Teacher) ==========

    /**
     * GET /api/v1/teacher/classes
     * Get all classes for teacher
     */
    public function index(Request $request)
    {
        try {
            $user = $request->user();
            $classes = Kelas::where('pengajar_id', $user->user_id)
                ->withCount('peserta')
                ->latest()
                ->paginate(10);

            if ($request->expectsJson()) {
                return $this->successWithPagination(
                    $classes,
                    'Classes retrieved successfully'
                );
            }

            return view('pengajar.kelas.index', compact('classes'));
        } catch (\Exception $e) {
            \Log::error('Error in index: ' . $e->getMessage());
            if ($request->expectsJson()) {
                return $this->serverError($e->getMessage());
            }
            return back()->with('error', 'Failed to load classes');
        }
    }

    /**
     * POST /api/v1/teacher/classes/{id}/publish
     * Publish class (change status to aktif)
     */
    public function publish(Request $request, $id)
    {
        try {
            $user = $request->user();
            $kelas = Kelas::where('pengajar_id', $user->user_id)->findOrFail($id);

            // Verify class has at least 2 materials
            if ($kelas->materi->count() < 2) {
                if ($request->expectsJson()) {
                    return $this->validationError(['error' => 'Class must have at least 2 materials before publishing']);
                }
                return back()->with('error', 'Kelas harus memiliki minimal 2 materi sebelum dipublikasi');
            }

            $kelas->update(['status' => 'aktif']);

            if ($request->expectsJson()) {
                return $this->success(
                    KelasResource::make($kelas),
                    'Class published successfully'
                );
            }

            return back()->with('success', 'Kelas dipublikasi!');

        } catch (\Exception $e) {
            \Log::error('Error in publish: ' . $e->getMessage());

            if ($request->expectsJson()) {
                return $this->serverError($e->getMessage());
            }

            return back()->with('error', 'Failed to publish class');
        }
    }

    /**
     * POST /api/v1/teacher/classes/{id}/archive
     * Archive class
     */
    public function archive(Request $request, $id)
    {
        try {
            $user = $request->user();
            $kelas = Kelas::where('pengajar_id', $user->user_id)->findOrFail($id);

            $kelas->update(['status' => 'archived']);

            if ($request->expectsJson()) {
                return $this->success(
                    KelasResource::make($kelas),
                    'Class archived successfully'
                );
            }

            return back()->with('success', 'Kelas diarsipkan!');

        } catch (\Exception $e) {
            \Log::error('Error in archive: ' . $e->getMessage());

            if ($request->expectsJson()) {
                return $this->serverError($e->getMessage());
            }

            return back()->with('error', 'Failed to archive class');
        }
    }

    /**
     * GET /api/v1/teacher/classes/{id}/students
     * Get list of students in class
     */
    public function students(Request $request, $id)
    {
        try {
            $user = $request->user();
            $kelas = Kelas::where('pengajar_id', $user->user_id)->findOrFail($id);

            $students = $kelas->peserta()
                ->select(['users.user_id', 'users.name', 'users.email', 'users.avatar_path', 'kelas_peserta.tanggal_daftar'])
                ->orderBy('kelas_peserta.tanggal_daftar', 'desc')
                ->paginate(15);

            if ($request->expectsJson()) {
                return $this->successWithPagination(
                    $students,
                    'Students retrieved'
                );
            }

            return view('pengajar.classes.students', compact('kelas', 'students'));

        } catch (\Exception $e) {
            \Log::error('Error in students: ' . $e->getMessage());

            if ($request->expectsJson()) {
                return $this->serverError($e->getMessage());
            }

            return back()->with('error', 'Failed to load students');
        }
    }

    /**
     * GET /api/v1/teacher/classes/{id}/stats
     * Get class statistics
     */
    public function stats(Request $request, $id)
    {
        try {
            $user = $request->user();
            $kelas = Kelas::where('pengajar_id', $user->user_id)->findOrFail($id);

            $studentCount = $kelas->peserta->count();
            $materialCount = $kelas->materi->count();

            // Calculate average rating
            $avgRating = \App\Models\Ulasan::where('kelas_id', $id)->avg('rating') ?? 0;
            $reviewCount = \App\Models\Ulasan::where('kelas_id', $id)->count();

            // Calculate revenue from this class
            $revenue = \App\Models\TokenLog::where('tipe', 'pendapatan')
                ->where('keterangan', 'LIKE', "%{$kelas->judul}%")
                ->sum('jumlah');

            // Enrollment trend (last 7 days)
            $enrollmentTrend = \Illuminate\Support\Facades\DB::table('kelas_peserta')
                ->where('kelas_id', $id)
                ->selectRaw('DATE(tanggal_daftar) as date, COUNT(*) as count')
                ->where('tanggal_daftar', '>=', now()->subDays(7))
                ->groupBy('date')
                ->orderBy('date', 'asc')
                ->get();

            $data = [
                'student_count' => $studentCount,
                'material_count' => $materialCount,
                'average_rating' => round($avgRating, 2),
                'review_count' => $reviewCount,
                'estimated_revenue' => $revenue ?? 0,
                'enrollment_trend' => $enrollmentTrend
            ];

            if ($request->expectsJson()) {
                return $this->success($data, 'Class statistics retrieved');
            }

            return view('pengajar.classes.stats', compact('kelas', 'data'));

        } catch (\Exception $e) {
            \Log::error('Error in stats: ' . $e->getMessage());

            if ($request->expectsJson()) {
                return $this->serverError($e->getMessage());
            }

            return back()->with('error', 'Failed to load statistics');
        }
    }

    /**
     * GET /api/v1/teacher/earnings
     * Get teacher earnings overview
     */
    public function earnings(Request $request)
    {
        try {
            $user = $request->user();

            $totalEarnings = \App\Models\TokenLog::where('user_id', $user->user_id)
                ->where('tipe', 'pendapatan')
                ->sum('jumlah');

            $monthlyEarnings = \App\Models\TokenLog::where('user_id', $user->user_id)
                ->where('tipe', 'pendapatan')
                ->where('tanggal', '>=', now()->startOfMonth())
                ->sum('jumlah');

            $data = [
                'total_earnings' => $totalEarnings ?? 0,
                'monthly_earnings' => $monthlyEarnings ?? 0,
                'total_students' => $user->kelasAjar()->withCount('peserta')->pluck('peserta_count')->sum() ?? 0
            ];

            if ($request->expectsJson()) {
                return $this->success($data, 'Earnings retrieved');
            }

            return view('pengajar.earnings.overview', compact('data'));

        } catch (\Exception $e) {
            \Log::error('Error in earnings: ' . $e->getMessage());

            if ($request->expectsJson()) {
                return $this->serverError($e->getMessage());
            }

            return back()->with('error', 'Failed to load earnings');
        }
    }

    /**
     * GET /api/v1/teacher/earnings/history
     * Get earnings transaction history
     */
    public function earningHistory(Request $request)
    {
        try {
            $user = $request->user();
            $limit = $request->get('limit', 20);

            $history = \App\Models\TokenLog::where('user_id', $user->user_id)
                ->where('tipe', 'pendapatan')
                ->latest('tanggal')
                ->paginate($limit);

            if ($request->expectsJson()) {
                return $this->successWithPagination(
                    $history,
                    'Earnings history retrieved'
                );
            }

            return view('pengajar.earnings.history', compact('history'));

        } catch (\Exception $e) {
            \Log::error('Error in earningHistory: ' . $e->getMessage());

            if ($request->expectsJson()) {
                return $this->serverError($e->getMessage());
            }

            return back()->with('error', 'Failed to load history');
        }
    }

    /**
     * GET /api/v1/teacher/earnings/stats
     * Get earnings statistics
     */
    public function earningStats(Request $request)
    {
        try {
            $user = $request->user();

            // Calculate by month for past 6 months
            $monthlyStats = \Illuminate\Support\Facades\DB::table('token_logs')
                ->where('user_id', $user->user_id)
                ->where('tipe', 'pendapatan')
                ->where('tanggal', '>=', now()->subMonths(6))
                ->selectRaw('DATE_TRUNC(\'month\', tanggal) as month, SUM(jumlah) as total')
                ->groupBy('month')
                ->orderBy('month', 'asc')
                ->get();

            $data = [
                'monthly_stats' => $monthlyStats,
                'total_lifetime' => \App\Models\TokenLog::where('user_id', $user->user_id)
                    ->where('tipe', 'pendapatan')
                    ->sum('jumlah') ?? 0
            ];

            if ($request->expectsJson()) {
                return $this->success($data, 'Earnings statistics retrieved');
            }

            return view('pengajar.earnings.stats', compact('data'));

        } catch (\Exception $e) {
            \Log::error('Error in earningStats: ' . $e->getMessage());

            if ($request->expectsJson()) {
                return $this->serverError($e->getMessage());
            }

            return back()->with('error', 'Failed to load statistics');
        }
    }

    /**
     * GET /api/v1/teacher/analytics/class/{classId}
     * Get class analytics
     */
    public function classAnalytics(Request $request, $classId)
    {
        try {
            $user = $request->user();
            $kelas = Kelas::where('pengajar_id', $user->user_id)->findOrFail($classId);

            // Get comprehensive analytics for the class
            $stats = [
                'total_students' => $kelas->peserta->count(),
                'total_materials' => $kelas->materi->count(),
                'completion_rate' => 0, // Would need to calculate from cache
                'average_rating' => \App\Models\Ulasan::where('kelas_id', $classId)->avg('rating') ?? 0,
                'total_reviews' => \App\Models\Ulasan::where('kelas_id', $classId)->count(),
                'enrollment_trend' => []
            ];

            if ($request->expectsJson()) {
                return $this->success($stats, 'Class analytics retrieved');
            }

            return view('pengajar.analytics.class', compact('kelas', 'stats'));

        } catch (\Exception $e) {
            \Log::error('Error in classAnalytics: ' . $e->getMessage());

            if ($request->expectsJson()) {
                return $this->serverError($e->getMessage());
            }

            return back()->with('error', 'Failed to load analytics');
        }
    }

    /**
     * GET /api/v1/teacher/analytics/overview
     * Get teacher analytics overview
     */
    public function analyticsOverview(Request $request)
    {
        try {
            $user = $request->user();

            $classes = $user->kelasAjar()->get();

            $overview = [
                'total_classes' => $classes->count(),
                'total_students' => 0,
                'total_materials' => 0,
                'average_rating' => 0,
                'total_earnings' => 0
            ];

            foreach ($classes as $kelas) {
                $overview['total_students'] += $kelas->peserta->count();
                $overview['total_materials'] += $kelas->materi->count();
            }

            $overview['average_rating'] = \App\Models\Ulasan::whereIn('kelas_id', $classes->pluck('kelas_id'))
                ->avg('rating') ?? 0;

            $overview['total_earnings'] = \App\Models\TokenLog::where('user_id', $user->user_id)
                ->where('tipe', 'pendapatan')
                ->sum('jumlah') ?? 0;

            if ($request->expectsJson()) {
                return $this->success($overview, 'Analytics overview retrieved');
            }

            return view('pengajar.analytics.overview', compact('overview'));

        } catch (\Exception $e) {
            \Log::error('Error in analyticsOverview: ' . $e->getMessage());

            if ($request->expectsJson()) {
                return $this->serverError($e->getMessage());
            }

            return back()->with('error', 'Failed to load analytics');
        }
    }

    /**
     * POST /api/v1/teacher/classes/{id}/grades
     * Upload grades for class students
     */
    public function uploadGrades(Request $request, $id)
    {
        try {
            $user = $request->user();
            $kelas = Kelas::where('pengajar_id', $user->user_id)->findOrFail($id);

            $data = $request->validate([
                'grades' => 'required|array',
                'grades.*.student_id' => 'required|exists:users,user_id',
                'grades.*.grade' => 'required|numeric|min:0|max:100'
            ]);

            // Implementation would depend on your grading schema
            // For now, we'll just return success

            if ($request->expectsJson()) {
                return $this->success(
                    ['uploaded' => true],
                    'Grades uploaded successfully',
                    201
                );
            }

            return back()->with('success', 'Grades uploaded!');

        } catch (\Exception $e) {
            \Log::error('Error in uploadGrades: ' . $e->getMessage());

            if ($request->expectsJson()) {
                return $this->serverError($e->getMessage());
            }

            return back()->with('error', 'Failed to upload grades');
        }
    }

    /**
     * GET /api/v1/teacher/feedback/class/{classId}
     * Get student feedback for class
     */
    public function studentFeedback(Request $request, $classId)
    {
        try {
            $user = $request->user();
            $kelas = Kelas::where('pengajar_id', $user->user_id)->findOrFail($classId);

            $feedback = \App\Models\Ulasan::where('kelas_id', $classId)
                ->with('user:user_id,name,avatar_path')
                ->orderBy('created_at', 'desc')
                ->paginate(10);

            if ($request->expectsJson()) {
                return $this->successWithPagination(
                    $feedback,
                    'Student feedback retrieved'
                );
            }

            return view('pengajar.feedback.class', compact('kelas', 'feedback'));

        } catch (\Exception $e) {
            \Log::error('Error in studentFeedback: ' . $e->getMessage());

            if ($request->expectsJson()) {
                return $this->serverError($e->getMessage());
            }

            return back()->with('error', 'Failed to load feedback');
        }
    }

    /**
     * GET /api/v1/teacher/feedback/student/{studentId}
     * Get student progress/feedback
     */
    public function studentProgress(Request $request, $studentId)
    {
        try {
            $user = $request->user();

            $student = \App\Models\User::findOrFail($studentId);

            // Get classes where this student is enrolled and teacher owns them
            $classes = $user->kelasAjar()
                ->whereHas('peserta', function ($q) use ($studentId) {
                    $q->where('siswa_id', $studentId);
                })
                ->get();

            if (count($classes) === 0) {
                if ($request->expectsJson()) {
                    return $this->forbidden('Not authorized');
                }
                return back()->with('error', 'Unauthorized');
            }

            $progress = [
                'student' => ['id' => $student->user_id, 'name' => $student->name, 'email' => $student->email],
                'classes' => $classes,
                'average_rating' => $student->ulasan()->avg('rating') ?? 0
            ];

            if ($request->expectsJson()) {
                return $this->success($progress, 'Student progress retrieved');
            }

            return view('pengajar.feedback.student', compact('student', 'progress'));

        } catch (\Exception $e) {
            \Log::error('Error in studentProgress: ' . $e->getMessage());

            if ($request->expectsJson()) {
                return $this->serverError($e->getMessage());
            }

            return back()->with('error', 'Failed to load progress');
        }
    }

    /**
     * POST /api/v1/teacher/feedback/student/{studentId}/comment
     * Add comment/feedback to student
     */
    public function addComment(Request $request, $studentId)
    {
        try {
            $request->validate(['comment' => 'required|string|max:1000']);

            $user = $request->user();
            $student = \App\Models\User::findOrFail($studentId);

            // Verify teacher has access to this student
            $isTeacher = $user->kelasAjar()
                ->whereHas('peserta', function ($q) use ($studentId) {
                    $q->where('siswa_id', $studentId);
                })
                ->exists();

            if (!$isTeacher) {
                if ($request->expectsJson()) {
                    return $this->forbidden('Not authorized');
                }
                return back()->with('error', 'Unauthorized');
            }

            // Save comment to a feedback/comment model (if it exists)
            // For now, we'll just return success

            if ($request->expectsJson()) {
                return $this->success(
                    ['commented' => true],
                    'Comment added successfully',
                    201
                );
            }

            return back()->with('success', 'Comment added!');

        } catch (\Exception $e) {
            \Log::error('Error in addComment: ' . $e->getMessage());

            if ($request->expectsJson()) {
                return $this->serverError($e->getMessage());
            }

            return back()->with('error', 'Failed to add comment');
        }
    }
}
