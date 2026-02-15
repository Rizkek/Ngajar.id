<?php

namespace App\Http\Controllers;

use App\Models\LearningPath;
use App\Models\Kelas;
use Illuminate\Http\Request;

class AdminLearningPathController extends Controller
{
    /**
     * Display a listing of learning paths
     */
    public function index()
    {
        $learningPaths = LearningPath::withCount('kelas')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.learning-paths.index', compact('learningPaths'));
    }

    /**
     * Show the form for creating a new learning path
     */
    public function create()
    {
        return view('admin.learning-paths.create');
    }

    /**
     * Store a newly created learning path
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'kategori' => 'nullable|string|max:100',
            'level' => 'required|in:Beginner,Intermediate,Advanced',
            'estimated_hours' => 'nullable|integer|min:0',
            'thumbnail' => 'nullable|string|max:255',
            'is_active' => 'nullable|boolean',
        ]);

        LearningPath::create($validated);

        return redirect()->route('admin.learning-paths.index')
            ->with('success', 'Learning Path berhasil dibuat!');
    }

    /**
     * Display the specified learning path
     */
    public function show($id)
    {
        $learningPath = LearningPath::with('kelas.pengajar')->findOrFail($id);
        $availableKelas = Kelas::where('learning_path_id', null)
            ->orWhere('learning_path_id', $id)
            ->where('status', 'aktif')
            ->get();

        return view('admin.learning-paths.show', compact('learningPath', 'availableKelas'));
    }

    /**
     * Show the form for editing the specified learning path
     */
    public function edit($id)
    {
        $learningPath = LearningPath::findOrFail($id);
        return view('admin.learning-paths.edit', compact('learningPath'));
    }

    /**
     * Update the specified learning path
     */
    public function update(Request $request, $id)
    {
        $learningPath = LearningPath::findOrFail($id);

        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'kategori' => 'nullable|string|max:100',
            'level' => 'required|in:Beginner,Intermediate,Advanced',
            'estimated_hours' => 'nullable|integer|min:0',
            'thumbnail' => 'nullable|string|max:255',
            'is_active' => 'nullable|boolean',
        ]);

        $learningPath->update($validated);

        return redirect()->route('admin.learning-paths.index')
            ->with('success', 'Learning Path berhasil diupdate!');
    }

    /**
     * Remove the specified learning path
     */
    public function destroy($id)
    {
        $learningPath = LearningPath::findOrFail($id);

        // Set learning_path_id kelas menjadi null sebelum delete
        Kelas::where('learning_path_id', $id)->update(['learning_path_id' => null]);

        $learningPath->delete();

        return redirect()->route('admin.learning-paths.index')
            ->with('success', 'Learning Path berhasil dihapus!');
    }

    /**
     * Attach kelas to learning path
     */
    public function attachKelas(Request $request, $id)
    {
        $validated = $request->validate([
            'kelas_id' => 'required|exists:kelas,kelas_id',
        ]);

        Kelas::where('kelas_id', $validated['kelas_id'])
            ->update(['learning_path_id' => $id]);

        return back()->with('success', 'Kelas berhasil ditambahkan ke Learning Path!');
    }

    /**
     * Detach kelas from learning path
     */
    public function detachKelas($id, $kelasId)
    {
        Kelas::where('kelas_id', $kelasId)
            ->update(['learning_path_id' => null]);

        return back()->with('success', 'Kelas berhasil dilepas dari Learning Path!');
    }
}
