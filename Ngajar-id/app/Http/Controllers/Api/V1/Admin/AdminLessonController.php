<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;

use App\Models\Lesson;
use App\Models\Course;
use App\Http\Traits\ApiResponse;
use App\Http\Resources\MateriResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminLessonController extends Controller
{
    use ApiResponse;

    /**
     * List all materi (with pagination)
     * GET /admin/lessonsals
     */
    public function index(Request $request)
    {
        try {
            $query = Lesson::with('kelas', 'kelas.pengajar')
                ->withCount('modulLevelCompleted');

            // Filter by class
            if ($request->has('kelas_id') && $request->kelas_id) {
                $query->where('kelas_id', $request->kelas_id);
            }

            // Filter by type
            if ($request->has('tipe') && $request->tipe) {
                $query->where('tipe', $request->tipe);
            }

            // Filter by is_premium
            if ($request->has('is_premium')) {
                $query->where('is_premium', $request->is_premium);
            }

            // Search
            if ($request->has('search') && $request->search) {
                $query->where(function ($q) use ($request) {
                    $q->where('judul', 'like', "%{$request->search}%")
                        ->orWhere('deskripsi', 'like', "%{$request->search}%");
                });
            }

            $data = $query->latest()->paginate($request->get('per_page', 15));

            return $this->successWithPagination(
                    MateriResource::collection($data),
                    'Materials retrieved successfully'
                );

            
        } catch (\Exception $e) {
            \Log::error('AdminLessonController@index: ' . $e->getMessage());
            return $this->serverError($e->getMessage());
            
        }
    }

    /**
     * Get single materi details
     * GET /admin/lessonsals/{id}
     */
    public function show(Request $request, $id)
    {
        try {
            $materi = Lesson::with('kelas', 'kelas.pengajar')
                ->findOrFail($id);

            return $this->success(
                    new MateriResource($materi),
                    'Material retrieved successfully'
                );

            
        } catch (\Exception $e) {
            \Log::error('AdminLessonController@show: ' . $e->getMessage());
            return $this->notFound('Material not found');
            
        }
    }

    /**
     * Update materi
     * PUT /admin/lessonsals/{id}
     */
    public function update(Request $request, $id)
    {
        try {
            $materi = Lesson::findOrFail($id);

            $validated = $request->validate([
                'judul' => 'nullable|string|max:255',
                'deskripsi' => 'nullable|string',
                'tipe' => 'nullable|in:video,artikel,quiz,dokumen',
                'is_premium' => 'nullable|boolean',
            ]);

            $materi->update($validated);

            return $this->success(
                    new MateriResource($materi),
                    'Material updated successfully'
                );

            
        } catch (\Exception $e) {
            \Log::error('AdminLessonController@update: ' . $e->getMessage());
            return $this->serverError($e->getMessage());
            
        }
    }

    /**
     * Delete materi
     * DELETE /admin/lessonsals/{id}
     */
    public function destroy(Request $request, $id)
    {
        try {
            $materi = Lesson::findOrFail($id);

            // Delete file if exists
            if ($materi->file_url && Storage::exists($materi->file_url)) {
                Storage::delete($materi->file_url);
            }

            $materi->delete();

            return $this->success(null, 'Material deleted successfully');

            
        } catch (\Exception $e) {
            \Log::error('AdminLessonController@destroy: ' . $e->getMessage());
            return $this->serverError($e->getMessage());
            
        }
    }

    /**
     * Verify materi
     * POST /admin/lessonsals/{id}/verify
     */
    public function verify(Request $request, $id)
    {
        try {
            $materi = Lesson::findOrFail($id);

            $validated = $request->validate([
                'is_verified' => 'required|boolean',
                'notes' => 'nullable|string|max:500',
            ]);

            $materi->update([
                'is_verified' => $validated['is_verified'],
                'verification_notes' => $validated['notes'] ?? null,
                'verified_at' => $validated['is_verified'] ? now() : null,
            ]);

            return $this->success(
                    new MateriResource($materi),
                    'Material verification updated'
                );

            
        } catch (\Exception $e) {
            \Log::error('AdminLessonController@verify: ' . $e->getMessage());
            return $this->serverError($e->getMessage());
            
        }
    }
}





