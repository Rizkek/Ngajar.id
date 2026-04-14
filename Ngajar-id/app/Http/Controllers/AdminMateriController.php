<?php

namespace App\Http\Controllers;

use App\Models\Materi;
use App\Models\Kelas;
use App\Http\Traits\ApiResponse;
use App\Http\Resources\MateriResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminMateriController extends Controller
{
    use ApiResponse;

    /**
     * List all materi (with pagination)
     * GET /admin/materials
     */
    public function index(Request $request)
    {
        try {
            $query = Materi::with('kelas', 'kelas.pengajar')
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

            if ($request->expectsJson()) {
                return $this->successWithPagination(
                    MateriResource::collection($data),
                    'Materials retrieved successfully'
                );
            }

            return view('admin.materials.index', compact('data'));
        } catch (\Exception $e) {
            \Log::error('AdminMateriController@index: ' . $e->getMessage());
            if ($request->expectsJson()) {
                return $this->serverError($e->getMessage());
            }
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Get single materi details
     * GET /admin/materials/{id}
     */
    public function show(Request $request, $id)
    {
        try {
            $materi = Materi::with('kelas', 'kelas.pengajar')
                ->findOrFail($id);

            if ($request->expectsJson()) {
                return $this->success(
                    new MateriResource($materi),
                    'Material retrieved successfully'
                );
            }

            return view('admin.materials.show', compact('materi'));
        } catch (\Exception $e) {
            \Log::error('AdminMateriController@show: ' . $e->getMessage());
            if ($request->expectsJson()) {
                return $this->notFound('Material not found');
            }
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Update materi
     * PUT /admin/materials/{id}
     */
    public function update(Request $request, $id)
    {
        try {
            $materi = Materi::findOrFail($id);

            $validated = $request->validate([
                'judul' => 'nullable|string|max:255',
                'deskripsi' => 'nullable|string',
                'tipe' => 'nullable|in:video,artikel,quiz,dokumen',
                'is_premium' => 'nullable|boolean',
            ]);

            $materi->update($validated);

            if ($request->expectsJson()) {
                return $this->success(
                    new MateriResource($materi),
                    'Material updated successfully'
                );
            }

            return back()->with('success', 'Material updated successfully');
        } catch (\Exception $e) {
            \Log::error('AdminMateriController@update: ' . $e->getMessage());
            if ($request->expectsJson()) {
                return $this->serverError($e->getMessage());
            }
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Delete materi
     * DELETE /admin/materials/{id}
     */
    public function destroy(Request $request, $id)
    {
        try {
            $materi = Materi::findOrFail($id);

            // Delete file if exists
            if ($materi->file_url && Storage::exists($materi->file_url)) {
                Storage::delete($materi->file_url);
            }

            $materi->delete();

            if ($request->expectsJson()) {
                return $this->success(null, 'Material deleted successfully');
            }

            return back()->with('success', 'Material deleted successfully');
        } catch (\Exception $e) {
            \Log::error('AdminMateriController@destroy: ' . $e->getMessage());
            if ($request->expectsJson()) {
                return $this->serverError($e->getMessage());
            }
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Verify materi
     * POST /admin/materials/{id}/verify
     */
    public function verify(Request $request, $id)
    {
        try {
            $materi = Materi::findOrFail($id);

            $validated = $request->validate([
                'is_verified' => 'required|boolean',
                'notes' => 'nullable|string|max:500',
            ]);

            $materi->update([
                'is_verified' => $validated['is_verified'],
                'verification_notes' => $validated['notes'] ?? null,
                'verified_at' => $validated['is_verified'] ? now() : null,
            ]);

            if ($request->expectsJson()) {
                return $this->success(
                    new MateriResource($materi),
                    'Material verification updated'
                );
            }

            return back()->with('success', 'Material verification updated');
        } catch (\Exception $e) {
            \Log::error('AdminMateriController@verify: ' . $e->getMessage());
            if ($request->expectsJson()) {
                return $this->serverError($e->getMessage());
            }
            return back()->with('error', $e->getMessage());
        }
    }
}
