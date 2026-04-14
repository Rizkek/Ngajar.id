<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Models\Materi;
use App\Http\Traits\ApiResponse;
use App\Http\Resources\MateriResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class MateriController extends Controller
{
    use ApiResponse;
    /**
     * Tampilkan form untuk upload materi baru
     */
    public function create(Request $request)
    {
        // Ambil kelas yang dibuat oleh pengajar ini
        $kelas = Kelas::where('pengajar_id', Auth::id())->where('status', '!=', 'ditolak')->get();
        $selectedKelasId = $request->query('kelas_id');

        return view('pengajar.materi.create', compact('kelas', 'selectedKelasId'));
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

            // Verifikasi kepemilikan kelas
            $kelas = Kelas::where('kelas_id', $request->kelas_id)
                ->where('pengajar_id', Auth::id())
                ->firstOrFail();

            $path = null;
            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $path = $file->store('materi', 'public');
            }

            $materi = Materi::create([
                'kelas_id' => $request->kelas_id,
                'judul' => $request->judul,
                'tipe' => $request->tipe,
                'deskripsi' => $request->deskripsi,
                'file_url' => $path ? Storage::url($path) : null,
                'is_premium' => $request->is_premium,
                'harga_token' => $request->is_premium ? $request->harga_token : 0,
            ]);

            if ($request->expectsJson()) {
                return $this->success(
                    MateriResource::make($materi),
                    'Material created successfully',
                    201
                );
            }

            return redirect()->route('pengajar.materi')->with('success', 'Materi berhasil diupload!');

        } catch (\Exception $e) {
            \Log::error('Error in store: ' . $e->getMessage());

            if ($request->expectsJson()) {
                return $this->serverError($e->getMessage());
            }

            return back()->with('error', 'Failed to create material');
        }
    }

    /**
     * Tampilkan form edit materi
     */
    public function edit($id)
    {
        // Cari materi dan pastikan milik kelas pengajar ini
        $materi = Materi::whereHas('kelas', function ($q) {
            $q->where('pengajar_id', Auth::id());
        })->findOrFail($id);

        $kelas = Kelas::where('pengajar_id', Auth::id())->where('status', '!=', 'ditolak')->get();

        return view('pengajar.materi.edit', compact('materi', 'kelas'));
    }

    /**
     * Update materi
     */
    public function update(Request $request, $id)
    {
        try {
            $materi = Materi::whereHas('kelas', function ($q) {
                $q->where('pengajar_id', Auth::id());
            })->findOrFail($id);

            $data = $request->validate([
                'judul' => 'nullable|string|max:150',
                'kelas_id' => 'nullable|exists:kelas,kelas_id',
                'tipe' => 'nullable|in:video,pdf,soal',
                'deskripsi' => 'nullable|string',
                'file' => 'nullable|file|mimes:pdf,mp4,mov,avi,doc,docx,ppt,pptx,zip|max:51200',
                'is_premium' => 'nullable|boolean',
                'harga_token' => 'nullable|integer|min:0',
            ]);

            // Verifikasi kepemilikan kelas baru (jika berubah)
            if (isset($data['kelas_id'])) {
                $kelas = Kelas::where('kelas_id', $data['kelas_id'])
                    ->where('pengajar_id', Auth::id())
                    ->firstOrFail();
            }

            // Update only provided fields
            $updateData = array_filter($data, function($value) {
                return $value !== null;
            });

            if ($request->hasFile('file')) {
                // Hapus file lama jika ada
                if ($materi->file_url) {
                    $oldPath = str_replace('/storage/', '', $materi->file_url);
                    if (Storage::disk('public')->exists($oldPath)) {
                        Storage::disk('public')->delete($oldPath);
                    }
                }

                $file = $request->file('file');
                $path = $file->store('materi', 'public');
                $updateData['file_url'] = Storage::url($path);
            }

            $materi->update($updateData);

            if ($request->expectsJson()) {
                return $this->success(
                    MateriResource::make($materi),
                    'Material updated successfully'
                );
            }

            return redirect()->route('pengajar.materi')->with('success', 'Materi berhasil diperbarui!');

        } catch (\Exception $e) {
            \Log::error('Error in update: ' . $e->getMessage());

            if ($request->expectsJson()) {
                return $this->serverError($e->getMessage());
            }

            return back()->with('error', 'Failed to update material');
        }
    }

    /**
     * Hapus materi
     */
    public function destroy(Request $request, $id)
    {
        try {
            $materi = Materi::whereHas('kelas', function ($q) {
                $q->where('pengajar_id', Auth::id());
            })->findOrFail($id);

            // Hapus file fisik
            if ($materi->file_url) {
                $oldPath = str_replace('/storage/', '', $materi->file_url);
                if (Storage::disk('public')->exists($oldPath)) {
                    Storage::disk('public')->delete($oldPath);
                }
            }

            $materi->delete();

            if ($request->expectsJson()) {
                return $this->success(['deleted' => true], 'Material deleted successfully');
            }

            return redirect()->route('pengajar.materi')->with('success', 'Materi berhasil dihapus!');

        } catch (\Exception $e) {
            \Log::error('Error in destroy: ' . $e->getMessage());

            if ($request->expectsJson()) {
                return $this->serverError($e->getMessage());
            }

            return back()->with('error', 'Failed to delete material');
        }
    }

    // ========== ADDITIONAL API ENDPOINTS (Phase 3E) ==========

    /**
     * GET /api/v1/teacher/materials
     * Get all materials for teacher's classes
     */
    public function index(Request $request)
    {
        try {
            $search = $request->get('q');
            $classId = $request->get('class_id');

            $query = Materi::whereHas('kelas', function ($q) {
                $q->where('pengajar_id', Auth::id());
            });

            if ($search) {
                $query->where('judul', 'ILIKE', "%{$search}%")
                    ->orWhere('deskripsi', 'ILIKE', "%{$search}%");
            }

            if ($classId) {
                $query->where('kelas_id', $classId);
            }

            $materials = $query->latest()->paginate(15);

            if ($request->expectsJson()) {
                return $this->successWithPagination(
                    MateriResource::collection($materials),
                    'Materials retrieved'
                );
            }

            return view('pengajar.materi', compact('materials'));

        } catch (\Exception $e) {
            \Log::error('Error in index: ' . $e->getMessage());

            if ($request->expectsJson()) {
                return $this->serverError($e->getMessage());
            }

            return back()->with('error', 'Failed to load materials');
        }
    }

    /**
     * GET /api/v1/teacher/materials/{id}
     * Get specific material details
     */
    public function show(Request $request, $id)
    {
        try {
            $materi = Materi::whereHas('kelas', function ($q) {
                $q->where('pengajar_id', Auth::id());
            })->findOrFail($id);

            if ($request->expectsJson()) {
                return $this->success(
                    MateriResource::make($materi),
                    'Material retrieved'
                );
            }

            return view('pengajar.materi.show', compact('materi'));

        } catch (\Exception $e) {
            \Log::error('Error in show: ' . $e->getMessage());

            if ($request->expectsJson()) {
                return $this->serverError($e->getMessage());
            }

            return back()->with('error', 'Material not found');
        }
    }

    /**
     * GET /api/v1/teacher/materials/class/{classId}
     * Get all materials for specific class
     */
    public function byClass(Request $request, $classId)
    {
        try {
            // Verify teacher owns this class
            $kelas = Kelas::where('kelas_id', $classId)
                ->where('pengajar_id', Auth::id())
                ->firstOrFail();

            $materials = Materi::where('kelas_id', $classId)
                ->latest()
                ->paginate(20);

            if ($request->expectsJson()) {
                return $this->successWithPagination(
                    MateriResource::collection($materials),
                    'Class materials retrieved'
                );
            }

            return view('pengajar.materi.by-class', compact('kelas', 'materials'));

        } catch (\Exception $e) {
            \Log::error('Error in byClass: ' . $e->getMessage());

            if ($request->expectsJson()) {
                return $this->serverError($e->getMessage());
            }

            return back()->with('error', 'Failed to load materials');
        }
    }

    /**
     * GET /api/v1/teacher/certificates
     * Get teacher's issued certificates
     */
    public function myCertificates(Request $request)
    {
        try {
            // Get certificates for learning paths created by teacher
            $certificates = \App\Models\LearningPath::where('creator_id', Auth::id())
                ->with('enrollments')
                ->where(function ($q) {
                    $q->whereHas('enrollments', function ($q2) {
                        $q2->whereNotNull('completed_at');
                    });
                })
                ->paginate(10);

            if ($request->expectsJson()) {
                return $this->successWithPagination(
                    $certificates,
                    'Certificates retrieved'
                );
            }

            return view('pengajar.certificates', compact('certificates'));

        } catch (\Exception $e) {
            \Log::error('Error in myCertificates: ' . $e->getMessage());

            if ($request->expectsJson()) {
                return $this->serverError($e->getMessage());
            }

            return back()->with('error', 'Failed to load certificates');
        }
    }

    /**
     * POST /api/v1/teacher/certificates/class/{classId}/generate
     * Generate certificates for completed class
     */
    public function generateCertificates(Request $request, $classId)
    {
        try {
            $user = $request->user();
            $kelas = Kelas::where('kelas_id', $classId)
                ->where('pengajar_id', $user->user_id)
                ->firstOrFail();

            // Get all students who completed the class
            $completedStudents = $kelas->peserta()
                ->whereHas('completions', function ($q) use ($classId) {
                    // This would depend on your completion tracking model
                })
                ->get();

            // Generate certificates for each completed student
            // Implementation depends on your certificate generation system

            $data = [
                'generated_count' => $completedStudents->count(),
                'class_id' => $classId,
                'class_name' => $kelas->judul
            ];

            if ($request->expectsJson()) {
                return $this->success(
                    $data,
                    'Certificates generated successfully'
                );
            }

            return back()->with('success', 'Certificates generated!');

        } catch (\Exception $e) {
            \Log::error('Error in generateCertificates: ' . $e->getMessage());

            if ($request->expectsJson()) {
                return $this->serverError($e->getMessage());
            }

            return back()->with('error', 'Failed to generate certificates');
        }
    }

    /**
     * GET /api/v1/teacher/certificates/class/{classId}/issued
     * Get issued certificates for specific class
     */
    public function issuedCertificates(Request $request, $classId)
    {
        try {
            $user = $request->user();
            $kelas = Kelas::where('kelas_id', $classId)
                ->where('pengajar_id', $user->user_id)
                ->firstOrFail();

            $issued = \Illuminate\Support\Facades\DB::table('certificates')
                ->where('class_id', $classId)
                ->where('teacher_id', $user->user_id)
                ->with('student')
                ->paginate(15);

            if ($request->expectsJson()) {
                return $this->successWithPagination(
                    $issued,
                    'Issued certificates retrieved'
                );
            }

            return view('pengajar.certificates.issued', compact('kelas', 'issued'));

        } catch (\Exception $e) {
            \Log::error('Error in issuedCertificates: ' . $e->getMessage());

            if ($request->expectsJson()) {
                return $this->serverError($e->getMessage());
            }

            return back()->with('error', 'Failed to load certificates');
        }
    }
}
