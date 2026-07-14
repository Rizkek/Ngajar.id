<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\LearningPath;
use App\Models\Course;
use Illuminate\Http\Request;

class AdminLearningPathController extends Controller
{

    /**
     * List all learning paths
     * GET /admin/learning-paths
     */
    public function index(Request $request)
    {
        try {
            $query = LearningPath::with('kelas')
                ->withCount('kelas');

            // Filter by status
            if ($request->has('is_active')) {
                $query->where('is_active', (bool) $request->is_active);
            }

            // Filter by level
            if ($request->has('level') && $request->level) {
                $query->where('level', $request->level);
            }

            // Search
            if ($request->has('search') && $request->search) {
                $query->where(function ($q) use ($request) {
                    $q->where('judul', 'like', "%{$request->search}%")
                        ->orWhere('deskripsi', 'like', "%{$request->search}%");
                });
            }

            $data = $query->latest()->paginate($request->get('per_page', 15));

            return view('admin.learning-paths.index', compact('data'));
        } catch (\Exception $e) {
            \Log::error('AdminLearningPathController@index: ' . $e->getMessage());

            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Create new learning path
     * POST /admin/learning-paths
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'judul' => 'required|string|max:255',
                'deskripsi' => 'nullable|string',
                'kategori' => 'nullable|string|max:100',
                'level' => 'required|in:Beginner,Intermediate,Advanced',
                'estimated_hours' => 'nullable|integer|min:0',
                'thumbnail' => 'nullable|url',
                'is_active' => 'nullable|boolean',
                'is_free' => 'nullable|boolean',
                'price' => 'nullable|integer|min:0',
            ]);

            $path = LearningPath::create($validated);

            return redirect()->route('admin.learning-paths.show', $path->id)
                ->with('success', 'Learning path created successfully');
        } catch (\Exception $e) {
            \Log::error('AdminLearningPathController@store: ' . $e->getMessage());

            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Get single learning path
     * GET /admin/learning-paths/{id}
     */
    public function show(Request $request, $id)
    {
        try {
            $path = LearningPath::with('kelas')
                ->withCount('kelas')
                ->findOrFail($id);

            return view('admin.learning-paths.show', compact('path'));
        } catch (\Exception $e) {
            \Log::error('AdminLearningPathController@show: ' . $e->getMessage());

            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Update learning path
     * PUT /admin/learning-paths/{id}
     */
    public function update(Request $request, $id)
    {
        try {
            $path = LearningPath::findOrFail($id);

            $validated = $request->validate([
                'judul' => 'nullable|string|max:255',
                'deskripsi' => 'nullable|string',
                'kategori' => 'nullable|string|max:100',
                'level' => 'nullable|in:Beginner,Intermediate,Advanced',
                'estimated_hours' => 'nullable|integer|min:0',
                'thumbnail' => 'nullable|url',
                'is_active' => 'nullable|boolean',
                'is_free' => 'nullable|boolean',
                'price' => 'nullable|integer|min:0',
            ]);

            $path->update(array_filter($validated));

            return back()->with('success', 'Learning path updated successfully');
        } catch (\Exception $e) {
            \Log::error('AdminLearningPathController@update: ' . $e->getMessage());

            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Delete learning path
     * DELETE /admin/learning-paths/{id}
     */
    public function destroy(Request $request, $id)
    {
        try {
            $path = LearningPath::findOrFail($id);

            // Check if has classes
            if ($path->kelas()->count() > 0) {
                throw new \Exception('Cannot delete learning path with attached classes');
            }

            $path->delete();

            return back()->with('success', 'Learning path deleted successfully');
        } catch (\Exception $e) {
            \Log::error('AdminLearningPathController@destroy: ' . $e->getMessage());

            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Attach courses to learning path
     * POST /admin/learning-paths/{id}/courses
     */
    public function attachCourses(Request $request, $id)
    {
        try {
            $path = LearningPath::findOrFail($id);

            $validated = $request->validate([
                'kelas_ids' => 'required|array',
                'kelas_ids.*' => 'integer|exists:kelas,kelas_id',
            ]);

            // Sync the classes
            $path->kelas()->sync($validated['kelas_ids']);

            return back()->with('success', 'Courses attached successfully');
        } catch (\Exception $e) {
            \Log::error('AdminLearningPathController@attachCourses: ' . $e->getMessage());

            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Format learning path for API response
     */
    private function formatLearningPath($path)
    {
        return [
            'id' => $path->id,
            'title' => $path->judul,
            'description' => $path->deskripsi,
            'level' => $path->level,
            'category' => $path->kategori,
            'classes_count' => $path->kelas_count ?? 0,
            'estimated_hours' => $path->estimated_hours,
            'thumbnail' => $path->thumbnail,
            'is_active' => $path->is_active,
            'is_free' => $path->is_free,
            'price' => $path->price,
            'created_at' => $path->created_at?->toIso8601String(),
            'updated_at' => $path->updated_at?->toIso8601String(),
        ];
    }
}

