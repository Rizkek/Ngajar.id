<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;

use App\Models\Course;
use App\Http\Traits\ApiResponse;
use App\Http\Resources\KelasResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\Course\EnrollmentService;

class CatalogController extends Controller
{
    use ApiResponse;
    /**
     * Tampilkan semua kelas yang tersedia (Katalog).
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $search = $request->query('q');

        // Ambil kelas aktif, exclude yang sudah diikuti user (opsional, tapi bagus buat UX)
        // Atau tampilkan status "Sudah Bergabung"

        $query = Course::with(['pengajar'])
            ->where('status', 'aktif')
            ->orderBy('created_at', 'desc');

        if ($search) {
            $query->where('judul', 'ILIKE', "%{$search}%")
                ->orWhere('deskripsi', 'ILIKE', "%{$search}%");
        }

        $allKelas = $query->paginate(9);

        // Map data untuk view
        // Kita perlu tahu user sudah join kelas mana aja
        $user = Auth::user();
        $enrolledKelasIds = $user ? $user->kelasIkuti()->pluck('kelas_peserta.kelas_id')->toArray() : [];

        return view('student.catalog.index', compact('allKelas', 'enrolledKelasIds'));
    }

    /**
     * Proses gabung kelas (Enrollment).
     *
     * @param int $id
     * @param EnrollmentService $enrollmentService
     * @return \Illuminate\Http\RedirectResponse
     */
    public function join($id, EnrollmentService $enrollmentService)
    {
        $kelas = Course::findOrFail($id);
        $user = Auth::user();

        try {
            $result = $enrollmentService->enrollUser($user, $kelas);
            
            if (isset($result['cost']) && $result['cost'] > 0) {
                return redirect()->route('belajar.show', ['kelas_id' => $id])
                    ->with('success', $result['message']);
            }
            
            return redirect()->route('belajar.show', ['kelas_id' => $id])
                ->with('success', $result['message']);

        } catch (\Exception $e) {
            if (str_contains($e->getMessage(), 'Token tidak mencukupi')) {
                return redirect()->route('topup.create')->with('error', $e->getMessage());
            }
            
            if (str_contains($e->getMessage(), 'Anda sudah terdaftar')) {
                return redirect()->route('belajar.show', ['kelas_id' => $id])
                    ->with('info', $e->getMessage());
            }
            
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * GET /api/v1/student/classes
     * Get user's enrolled classes
     */
    public function myClasses(Request $request)
    {
        try {
            $user = $request->user();
            $search = $request->get('q');
            $kategori = $request->get('kategori');

            $query = $user->kelasIkuti()
                ->with('pengajar:user_id,name')
                ->where('status', 'aktif');

            if ($search) {
                $query->where('judul', 'ILIKE', "%{$search}%")
                    ->orWhere('deskripsi', 'ILIKE', "%{$search}%");
            }

            if ($kategori) {
                $query->where('kategori', $kategori);
            }

            $classes = $query->paginate(10);

            if ($request->expectsJson()) {
                return $this->successWithPagination(
                    KelasResource::collection($classes),
                    'My classes retrieved successfully'
                );
            }

            return view('student.courses.index', compact('classes'));

        } catch (\Exception $e) {
            \Log::error('Error in myClasses: ' . $e->getMessage());

            if ($request->expectsJson()) {
                return $this->serverError($e->getMessage());
            }

            return back()->with('error', 'Failed to load classes: ' . $e->getMessage());
        }
    }

    /**
     * GET /api/v1/student/classes/{id}
     * Get class details
     */
    public function show(Request $request, $id)
    {
        try {
            $kelas = Course::with(['pengajar:user_id,name', 'materi', 'peserta'])
                ->findOrFail($id);

            // Check if user is enrolled
            $user = $request->user();
            $isEnrolled = $user ? $user->kelasIkuti()->where('kelas_id', $id)->exists() : false;

            $data = [
                'class' => KelasResource::make($kelas),
                'is_enrolled' => $isEnrolled,
                'material_count' => $kelas->materi->count(),
                'student_count' => $kelas->peserta->count()
            ];

            if ($request->expectsJson()) {
                return $this->success($data, 'Class details retrieved successfully');
            }

            return redirect()->route('student.katalog')->with('info', 'Halaman detail kelas sedang dalam pengembangan.');

        } catch (\Exception $e) {
            \Log::error('Error in show: ' . $e->getMessage());

            if ($request->expectsJson()) {
                return $this->notFound('Class not found');
            }

            return back()->with('error', 'Class not found');
        }
    }

    /**
     * POST /api/v1/student/classes/{id}/enroll
     * Enroll student in a class
     */
    public function enroll(Request $request, $id, EnrollmentService $enrollmentService)
    {
        try {
            $user = $request->user();
            $kelas = Course::findOrFail($id);

            $result = $enrollmentService->enrollUser($user, $kelas);

            if ($request->expectsJson()) {
                return $this->success(
                    ['enrollment' => KelasResource::make($kelas)],
                    $result['message'],
                    201
                );
            }

            return redirect()->route('belajar.show', ['kelas_id' => $id])
                ->with('success', $result['message']);

        } catch (\Exception $e) {
            $msg = $e->getMessage();
            
            if ($request->expectsJson()) {
                if (str_contains($msg, 'Anda sudah terdaftar')) {
                    return $this->validationError(['error' => 'Already enrolled in this class']);
                }
                if (str_contains($msg, 'Hanya akun Murid')) {
                    return $this->forbidden('Only student accounts can enroll');
                }
                if (str_contains($msg, 'Token tidak mencukupi')) {
                    return $this->validationError(['error' => $msg]);
                }
                return $this->error('Failed to enroll: ' . $msg, 400);
            }

            if (str_contains($msg, 'Anda sudah terdaftar')) {
                return redirect()->route('belajar.show', ['kelas_id' => $id])
                    ->with('info', $msg);
            }
            if (str_contains($msg, 'Token tidak mencukupi')) {
                return redirect()->route('topup.create')
                    ->with('error', $msg);
            }
            return back()->with('error', $msg);
        }
    }

    /**
     * GET /api/v1/student/saved
     * Get user's saved/wishlist classes
     */
    public function savedClasses(Request $request)
    {
        try {
            $user = $request->user();

            $saved = \Illuminate\Support\Facades\DB::table('kelas_saved')
                ->join('kelas', 'kelas_saved.kelas_id', '=', 'kelas.kelas_id')
                ->join('users as u', 'kelas.pengajar_id', '=', 'u.user_id')
                ->select(['kelas.*', 'u.name as pengajar_name', 'u.user_id as pengajar_id'])
                ->where('kelas_saved.user_id', $user->user_id)
                ->where('kelas.status', 'aktif')
                ->paginate(10);

            if ($request->expectsJson()) {
                return $this->successWithPagination(
                    collect($saved->items()),
                    'Saved classes retrieved successfully'
                );
            }

            return view('student.saved', compact('saved'));

        } catch (\Exception $e) {
            \Log::error('Error in savedClasses: ' . $e->getMessage());

            if ($request->expectsJson()) {
                return $this->serverError($e->getMessage());
            }

            return back()->with('error', 'Failed to load saved classes');
        }
    }

    /**
     * POST /api/v1/student/saved/classes/{id}
     * Save a class to wishlist
     */
    public function saveClass(Request $request, $id)
    {
        try {
            $user = $request->user();
            $kelas = Course::findOrFail($id);

            // Check if already saved
            $exists = \Illuminate\Support\Facades\DB::table('kelas_saved')
                ->where('user_id', $user->user_id)
                ->where('kelas_id', $id)
                ->exists();

            if ($exists) {
                if ($request->expectsJson()) {
                    return $this->validationError(['error' => 'Class already saved']);
                }
                return back()->with('info', 'Kelas sudah disimpan');
            }

            // Save class
            \Illuminate\Support\Facades\DB::table('kelas_saved')->insert([
                'user_id' => $user->user_id,
                'kelas_id' => $id,
                'saved_at' => now()
            ]);

            if ($request->expectsJson()) {
                return $this->success(
                    ['class' => KelasResource::make($kelas)],
                    'Class saved successfully',
                    201
                );
            }

            return back()->with('success', 'Kelas disimpan ke wishlist');

        } catch (\Exception $e) {
            \Log::error('Error in saveClass: ' . $e->getMessage());

            if ($request->expectsJson()) {
                return $this->serverError($e->getMessage());
            }

            return back()->with('error', 'Failed to save class');
        }
    }

    /**
     * DELETE /api/v1/student/saved/classes/{id}
     * Remove class from wishlist
     */
    public function unsaveClass(Request $request, $id)
    {
        try {
            $user = $request->user();

            $deleted = \Illuminate\Support\Facades\DB::table('kelas_saved')
                ->where('user_id', $user->user_id)
                ->where('kelas_id', $id)
                ->delete();

            if ($deleted === 0) {
                if ($request->expectsJson()) {
                    return $this->notFound('Saved class not found');
                }
                return back()->with('info', 'Kelas tidak ditemukan di wishlist');
            }

            if ($request->expectsJson()) {
                return $this->success(
                    ['deleted' => true],
                    'Class removed from wishlist'
                );
            }

            return back()->with('success', 'Kelas dihapus dari wishlist');

        } catch (\Exception $e) {
            \Log::error('Error in unsaveClass: ' . $e->getMessage());

            if ($request->expectsJson()) {
                return $this->serverError($e->getMessage());
            }

            return back()->with('error', 'Failed to remove class from wishlist');
        }
    }
}





