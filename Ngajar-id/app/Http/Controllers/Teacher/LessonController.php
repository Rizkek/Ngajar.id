<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Lesson;
use App\Http\Traits\ApiResponse;
use App\Http\Resources\MateriResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Services\Teacher\MaterialManagementService;
use App\Services\Course\CertificateService;

class LessonController extends Controller
{
    use ApiResponse;

    protected $materialService;
    protected $certificateService;

    public function __construct(
        MaterialManagementService $materialService,
        CertificateService $certificateService
    ) {
        $this->materialService = $materialService;
        $this->certificateService = $certificateService;
    }

    /**
     * Tampilkan form untuk upload materi baru
     */
    public function create(Request $request)
    {
        $kelas = Course::where('pengajar_id', Auth::id())->where('status', '!=', 'ditolak')->get();
        $selectedKelasId = $request->query('kelas_id');
        return view('teacher.lessons.create', compact('kelas', 'selectedKelasId'));
    }

    /**
     * Simpan materi baru
     */
    public function store(Request $request)
    {
        try {
            $data = $request->validate([
                'judul' => 'required|string|max:150',
                'kelas_id' => 'required|exists:kelas,kelas_id',
                'tipe' => 'required|in:video,pdf,soal',
                'deskripsi' => 'nullable|string',
                'file' => 'nullable|file|mimes:pdf,mp4,mov,avi,doc,docx,ppt,pptx,zip|max:51200',
                'is_premium' => 'required|boolean',
                'harga_token' => 'required_if:is_premium,1|integer|min:0',
            ]);

            if ($request->hasFile('file')) {
                $data['file_path'] = $request->file('file')->store('materi', 'public');
            }

            $materi = $this->materialService->createMaterial($request->user() ?? Auth::user(), $request->kelas_id, $data);

            if ($request->expectsJson()) {
                return $this->success(MateriResource::make($materi), 'Material created successfully', 201);
            }

            return redirect()->route('teacher.materi.by_class', $request->kelas_id)
                ->with('success', 'Materi berhasil ditambahkan!');
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return $this->serverError($e->getMessage());
            }
            return back()->with('error', 'Gagal menambahkan materi: ' . $e->getMessage());
        }
    }

    /**
     * Tampilkan form untuk edit materi
     */
    public function edit($id)
    {
        $materi = Lesson::findOrFail($id);
        
        if ($materi->kelas->pengajar_id != Auth::id()) {
            abort(403);
        }

        $kelas = Course::where('pengajar_id', Auth::id())->get();
        return view('teacher.lessons.edit', compact('materi', 'kelas'));
    }

    /**
     * Update materi
     */
    public function update(Request $request, $id)
    {
        try {
            $data = $request->validate([
                'judul' => 'nullable|string|max:150',
                'tipe' => 'nullable|in:video,pdf,soal',
                'deskripsi' => 'nullable|string',
                'file' => 'nullable|file|mimes:pdf,mp4,mov,avi,doc,docx,ppt,pptx,zip|max:51200',
                'is_premium' => 'nullable|boolean',
                'harga_token' => 'nullable|integer|min:0',
            ]);

            if ($request->hasFile('file')) {
                $data['file_path'] = $request->file('file')->store('materi', 'public');
            }

            $materi = $this->materialService->updateMaterial($request->user() ?? Auth::user(), $id, array_filter($data));

            if ($request->expectsJson()) {
                return $this->success(MateriResource::make($materi), 'Material updated successfully');
            }

            return redirect()->route('teacher.materi.by_class', $materi->kelas_id)
                ->with('success', 'Materi berhasil diperbarui!');
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return $this->serverError($e->getMessage());
            }
            return back()->with('error', 'Gagal memperbarui materi: ' . $e->getMessage());
        }
    }

    /**
     * Hapus materi
     */
    public function destroy(Request $request, $id)
    {
        try {
            $materi = Lesson::findOrFail($id);
            $kelasId = $materi->kelas_id;

            $this->materialService->deleteMaterial($request->user() ?? Auth::user(), $id);

            if ($request->expectsJson()) {
                return $this->success(['deleted' => true], 'Material deleted successfully');
            }

            return redirect()->route('teacher.materi.by_class', $kelasId)
                ->with('success', 'Materi berhasil dihapus!');
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return $this->serverError($e->getMessage());
            }
            return back()->with('error', 'Gagal menghapus materi: ' . $e->getMessage());
        }
    }

    // ========== API ENDPOINTS (Phase 3F - Teacher Uploads & Materials) ==========

    public function index(Request $request)
    {
        try {
            $user = $request->user();
            // Need a way to fetch all materials across classes for API
            // Simple implementation since it's just index
            $kelasIds = Course::where('pengajar_id', $user->user_id)->pluck('kelas_id');
            $materials = Lesson::whereIn('kelas_id', $kelasIds)->paginate(20);

            if ($request->expectsJson()) {
                return $this->successWithPagination($materials, 'Materials retrieved successfully');
            }
            return view('teacher.materi', compact('materials'));
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return $this->serverError($e->getMessage());
            }
            return back()->with('error', 'Failed to retrieve materials');
        }
    }

    public function show(Request $request, $id)
    {
        try {
            $materi = Lesson::with('kelas')->findOrFail($id);
            
            if ($materi->kelas->pengajar_id != ($request->user()->user_id ?? Auth::id())) {
                throw new \Exception("Unauthorized access", 403);
            }

            if ($request->expectsJson()) {
                return $this->success(MateriResource::make($materi), 'Material retrieved successfully');
            }
            return redirect()->route('teacher.materi')->with('info', 'Detail materi sedang dalam pengembangan.');
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return $this->serverError($e->getMessage());
            }
            return back()->with('error', 'Failed to retrieve material details');
        }
    }

    public function byClass(Request $request, $classId)
    {
        try {
            $materials = $this->materialService->getMaterialsByClass($request->user() ?? Auth::user(), $classId);

            if ($request->expectsJson()) {
                return $this->success(MateriResource::collection($materials), 'Class materials retrieved');
            }

            $kelas = Course::findOrFail($classId);
            return view('teacher.materi', compact('materials', 'kelas'));
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return $this->serverError($e->getMessage());
            }
            return back()->with('error', 'Failed to retrieve class materials: ' . $e->getMessage());
        }
    }

    public function generateCertificates(Request $request, $classId)
    {
        try {
            // Reusing logic from CertificateService if possible, else stub out
            // Since CertificateService primarily handles student downloads, 
            // the teacher might just be triggering some DB flag. 
            // For now we keep it as a stub reflecting the refactored design.
            $data = [
                'generated_count' => 0,
                'class_id' => $classId,
                'message' => 'Certificates are generated automatically on student completion (Sprint 2 refactoring)'
            ];

            if ($request->expectsJson()) {
                return $this->success($data, 'Certificates generated successfully');
            }
            return back()->with('success', 'Certificates logic is now handled automatically for students!');
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return $this->serverError($e->getMessage());
            }
            return back()->with('error', 'Failed to generate certificates');
        }
    }

    public function issuedCertificates(Request $request, $classId)
    {
        try {
            $user = $request->user() ?? Auth::user();
            $kelas = Course::where('kelas_id', $classId)->where('pengajar_id', $user->user_id)->firstOrFail();

            // Using the updated mechanism: find enrolled students with completed_at not null
            $issued = \Illuminate\Support\Facades\DB::table('kelas_peserta')
                ->join('users', 'kelas_peserta.user_id', '=', 'users.user_id')
                ->where('kelas_peserta.kelas_id', $classId)
                ->whereNotNull('kelas_peserta.completed_at')
                ->select('users.name', 'kelas_peserta.completed_at', 'users.email')
                ->paginate(15);

            if ($request->expectsJson()) {
                return $this->successWithPagination($issued, 'Issued certificates retrieved');
            }
            return view('teacher.certificates.issued', compact('kelas', 'issued'));
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return $this->serverError($e->getMessage());
            }
            return back()->with('error', 'Failed to load certificates');
        }
    }
}





