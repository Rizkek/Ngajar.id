<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Http\Traits\ApiResponse;
use App\Http\Resources\KelasResource;
use Illuminate\Http\Request;

class AdminKelasController extends Controller
{
    use ApiResponse;

    /**
     * List all classes (with pagination)
     * GET /admin/classes
     */
    public function index(Request $request)
    {
        try {
            $query = Kelas::with('pengajar')
                ->withCount('peserta', 'materi');

            // Filter by status
            if ($request->has('status') && $request->status !== 'all') {
                $query->where('status', $request->status);
            }

            // Filter by category
            if ($request->has('kategori') && $request->kategori) {
                $query->where('kategori', $request->kategori);
            }

            // Search
            if ($request->has('search') && $request->search) {
                $query->where(function ($q) use ($request) {
                    $q->where('judul', 'like', "%{$request->search}%")
                        ->orWhere('deskripsi', 'like', "%{$request->search}%")
                        ->orWhereHas('pengajar', function ($q) use ($request) {
                            $q->where('name', 'like', "%{$request->search}%");
                        });
                });
            }

            $data = $query->latest()->paginate($request->get('per_page', 15));

            if ($request->expectsJson()) {
                return $this->successWithPagination(
                    KelasResource::collection($data),
                    'Classes retrieved successfully'
                );
            }

            return view('admin.classes.index', compact('data'));
        } catch (\Exception $e) {
            \Log::error('AdminKelasController@index: ' . $e->getMessage());
            if ($request->expectsJson()) {
                return $this->serverError($e->getMessage());
            }
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Get single class details
     * GET /admin/classes/{id}
     */
    public function show(Request $request, $id)
    {
        try {
            $kelas = Kelas::with('pengajar', 'peserta', 'materi')
                ->withCount('peserta', 'materi')
                ->findOrFail($id);

            if ($request->expectsJson()) {
                return $this->success(
                    new KelasResource($kelas),
                    'Class retrieved successfully'
                );
            }

            return view('admin.classes.show', compact('kelas'));
        } catch (\Exception $e) {
            \Log::error('AdminKelasController@show: ' . $e->getMessage());
            if ($request->expectsJson()) {
                return $this->notFound('Class not found');
            }
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Approve class
     * POST /admin/classes/{id}/approve
     */
    public function approve(Request $request, $id)
    {
        try {
            $kelas = Kelas::findOrFail($id);

            $kelas->update(['status' => 'aktif']);

            if ($request->expectsJson()) {
                return $this->success(
                    new KelasResource($kelas),
                    'Class approved successfully'
                );
            }

            return back()->with('success', 'Class approved successfully');
        } catch (\Exception $e) {
            \Log::error('AdminKelasController@approve: ' . $e->getMessage());
            if ($request->expectsJson()) {
                return $this->serverError($e->getMessage());
            }
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Reject class
     * POST /admin/classes/{id}/reject
     */
    public function reject(Request $request, $id)
    {
        try {
            $kelas = Kelas::findOrFail($id);

            $validated = $request->validate([
                'reason' => 'nullable|string|max:500',
            ]);

            $kelas->update([
                'status' => 'ditolak',
                'catatan_admin' => $validated['reason'] ?? null,
            ]);

            if ($request->expectsJson()) {
                return $this->success(
                    new KelasResource($kelas),
                    'Class rejected successfully'
                );
            }

            return back()->with('success', 'Class rejected successfully');
        } catch (\Exception $e) {
            \Log::error('AdminKelasController@reject: ' . $e->getMessage());
            if ($request->expectsJson()) {
                return $this->serverError($e->getMessage());
            }
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Archive class
     * POST /admin/classes/{id}/archive
     */
    public function archive(Request $request, $id)
    {
        try {
            $kelas = Kelas::findOrFail($id);

            $kelas->update(['status' => 'selesai']);

            if ($request->expectsJson()) {
                return $this->success(
                    new KelasResource($kelas),
                    'Class archived successfully'
                );
            }

            return back()->with('success', 'Class archived successfully');
        } catch (\Exception $e) {
            \Log::error('AdminKelasController@archive: ' . $e->getMessage());
            if ($request->expectsJson()) {
                return $this->serverError($e->getMessage());
            }
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Delete class
     * DELETE /admin/classes/{id}
     */
    public function destroy(Request $request, $id)
    {
        try {
            $kelas = Kelas::findOrFail($id);

            // Check if class has students
            if ($kelas->peserta()->count() > 0) {
                throw new \Exception('Cannot delete class with enrolled students. Archive it instead.');
            }

            $kelas->delete();

            if ($request->expectsJson()) {
                return $this->success(null, 'Class deleted successfully');
            }

            return back()->with('success', 'Class deleted successfully');
        } catch (\Exception $e) {
            \Log::error('AdminKelasController@destroy: ' . $e->getMessage());
            if ($request->expectsJson()) {
                return $this->serverError($e->getMessage());
            }
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Flag class for review
     * POST /admin/classes/{id}/flag
     */
    public function flag(Request $request, $id)
    {
        try {
            $kelas = Kelas::findOrFail($id);

            $validated = $request->validate([
                'reason' => 'required|string|max:500',
            ]);

            $kelas->update([
                'flagged_for_review' => true,
                'catatan_admin' => $validated['reason'],
            ]);

            if ($request->expectsJson()) {
                return $this->success(
                    new KelasResource($kelas),
                    'Class flagged for review'
                );
            }

            return back()->with('success', 'Class flagged for review');
        } catch (\Exception $e) {
            \Log::error('AdminKelasController@flag: ' . $e->getMessage());
            if ($request->expectsJson()) {
                return $this->serverError($e->getMessage());
            }
            return back()->with('error', $e->getMessage());
        }
    }
}
