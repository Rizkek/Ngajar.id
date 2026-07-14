<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreKelasRequest;
use App\Http\Requests\UpdateKelasRequest;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Services\Teacher\ClassManagementService;

class CourseController extends Controller
{
    use AuthorizesRequests;

    protected $classService;

    public function __construct(ClassManagementService $classService)
    {
        $this->classService = $classService;
    }

    /**
     * Tampilkan form untuk membuat kelas baru
     */
    public function create()
    {
        return view('teacher.courses.create');
    }

    /**
     * Simpan kelas baru ke database
     */
    public function store(StoreKelasRequest $request)
    {
        try {
            $this->authorize('create', Course::class);

            $data = $request->validated();

            if ($request->hasFile('thumbnail')) {
                $data['thumbnail'] = $request->file('thumbnail')->store('thumbnails', 'public');
            }

            $this->classService->createClass($request->user() ?? Auth::user(), $data);

            return redirect()->route('teacher.kelas')->with('success', 'Kelas berhasil dibuat!');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to create class: ' . $e->getMessage());
        }
    }

    /**
     * Tampilkan form untuk edit kelas
     */
    public function edit($id)
    {
        $kelas = Course::where('pengajar_id', Auth::id())->findOrFail($id);
        return view('teacher.courses.edit', compact('kelas'));
    }

    /**
     * Update kelas ke database
     */
    public function update(UpdateKelasRequest $request, $id)
    {
        try {
            $user = $request->user() ?? Auth::user();
            $kelas = Course::where('pengajar_id', $user->user_id)->findOrFail($id);

            $this->authorize('update', $kelas);

            $data = $request->validated();

            if ($request->hasFile('thumbnail')) {
                if ($kelas->thumbnail && Storage::disk('public')->exists($kelas->thumbnail)) {
                    Storage::disk('public')->delete($kelas->thumbnail);
                }
                $data['thumbnail'] = $request->file('thumbnail')->store('thumbnails', 'public');
            }

            $this->classService->updateClass($user, $id, array_filter($data));

            return redirect()->route('teacher.kelas')->with('success', 'Kelas berhasil diperbarui!');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to update class: ' . $e->getMessage());
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

            return redirect()->route('teacher.kelas')->with('success', 'Kelas berhasil dihapus!');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete class: ' . $e->getMessage());
        }
    }

    /**
     * Tampilkan daftar siswa dalam kelas
     */
    public function students($id)
    {
        $kelas = Course::where('pengajar_id', Auth::id())->findOrFail($id);
        // Simulasi atau ambil data siswa sebenarnya (tergantung relasi di model Course)
        // Kita asumsikan ada relasi `siswa()` atau `peserta()`
        // Jika belum ada, kita pass kosong dulu untuk layouting arsitektur
        $students = method_exists($kelas, 'siswa') ? $kelas->siswa()->paginate(20) : collect([]);
        
        return view('teacher.courses.students', compact('kelas', 'students'));
    }

    /**
     * Tampilkan analitik kelas
     */
    public function analytics($id)
    {
        $kelas = Course::where('pengajar_id', Auth::id())->findOrFail($id);
        
        // Data dummy untuk arsitektur visual
        $stats = [
            'total_students' => method_exists($kelas, 'siswa') ? $kelas->siswa()->count() : rand(10, 50),
            'completion_rate' => rand(40, 90),
            'average_rating' => rand(40, 50) / 10,
        ];

        return view('teacher.courses.analytics', compact('kelas', 'stats'));
    }
}






