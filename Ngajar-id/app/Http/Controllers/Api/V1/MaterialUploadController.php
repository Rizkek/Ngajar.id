<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Materi;
use App\Models\Kelas;
use App\Http\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class MaterialUploadController extends Controller
{
    use ApiResponse;

    /**
     * POST /api/v1/materials/upload
     * Upload material file to cloud storage
     */
    public function upload(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'materi_id' => 'required|exists:materi,materi_id',
                'file' => 'required|file|max:500000',
                'file_type' => 'required|in:video,pdf,image,document',
            ]);

            if ($validator->fails()) {
                return $this->error('Validation failed: ' . implode(', ', $validator->errors()->all()), 400);
            }

            $materi = Materi::findOrFail($request->materi_id);
            $kelas = Kelas::findOrFail($materi->kelas_id);

            // Ownership check
            if ($kelas->pengajar_id !== auth()->id()) {
                return $this->error('Not authorized to upload materials for this course', 403);
            }

            $file = $request->file('file');
            $fileName = Str::slug($materi->judul) . '_' . time() . '.' . $file->getClientOriginalExtension();
            $path = "materials/{$materi->kelas_id}/{$materi->materi_id}/" . $fileName;

            // Store file to cloud (Supabase S3)
            $filePath = Storage::disk('supabase')->putFileAs(
                "materials/{$materi->kelas_id}/{$materi->materi_id}",
                $file,
                $fileName,
                'public'
            );

            if (!$filePath) {
                return $this->error('Failed to upload file to storage', 400);
            }

            // Generate public URL
            $fileUrl = Storage::disk('supabase')->url($filePath);

            // Update materi record
            $materi->update([
                'file_url' => $fileUrl,
                'file_size' => $file->getSize(),
                'file_mime_type' => $file->getMimeType(),
                'storage_path' => $filePath,
                'uploaded_by' => auth()->id(),
                'uploaded_at' => now(),
            ]);

            // Award XP for uploading material
            auth()->user()->update([
                'xp' => (auth()->user()->xp ?? 0) + 250,
            ]);

            if ($request->expectsJson()) {
                return $this->success([
                    'materi_id' => $materi->materi_id,
                    'file_url' => $fileUrl,
                    'file_size' => $file->getSize(),
                    'file_type' => $file->getMimeType(),
                    'xp_earned' => 250,
                    'storage_path' => $filePath,
                ], 'File uploaded successfully', 201);
            }

            return redirect()->back()->with('success', 'File uploaded successfully');

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->error('Material or course not found', 404);
        } catch (\Exception $e) {
            \Log::error('MaterialUploadController@upload: ' . $e->getMessage());
            return $this->error('Failed to upload file: ' . $e->getMessage(), 400);
        }
    }

    /**
     * DELETE /api/v1/materials/{id}/file
     * Delete uploaded file
     */
    public function deleteFile($id, Request $request)
    {
        try {
            $materi = Materi::findOrFail($id);
            $kelas = Kelas::findOrFail($materi->kelas_id);

            // Ownership check
            if ($kelas->pengajar_id !== auth()->id()) {
                return $this->error('Not authorized to delete this file', 403);
            }

            if (!$materi->storage_path) {
                return $this->error('No file to delete', 400);
            }

            // Delete from storage
            Storage::disk('supabase')->delete($materi->storage_path);

            // Update materi record
            $materi->update([
                'file_url' => null,
                'file_size' => null,
                'file_mime_type' => null,
                'storage_path' => null,
                'uploaded_by' => null,
                'uploaded_at' => null,
                'download_count' => 0,
            ]);

            if ($request->expectsJson()) {
                return $this->success([], 'File deleted successfully');
            }

            return redirect()->back()->with('success', 'File deleted successfully');

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->error('Material not found', 404);
        } catch (\Exception $e) {
            \Log::error('MaterialUploadController@deleteFile: ' . $e->getMessage());
            return $this->error('Failed to delete file: ' . $e->getMessage(), 400);
        }
    }

    /**
     * GET /api/v1/materials/{id}/download
     * Download or stream material file
     */
    public function download($id, Request $request)
    {
        try {
            $materi = Materi::findOrFail($id);

            if (!$materi->file_url) {
                return $this->error('No file available for download', 400);
            }

            // Check if student has access (if course is premium)
            if ($materi->kelas->is_premium ?? false) {
                // Check purchase history
                $hasPurchase = auth()->user()
                    ? \DB::table('pembelian_materi')
                        ->where('user_id', auth()->id())
                        ->where('materi_id', $id)
                        ->exists()
                    : false;

                if (!$hasPurchase) {
                    return $this->error('You need to purchase this material to download', 403);
                }
            }

            // Increment download count
            $materi->increment('download_count');

            // Log download in tracking table
            \DB::table('material_downloads')->updateOrCreate(
                [
                    'user_id' => auth()->id(),
                    'materi_id' => $id,
                ],
                [
                    'last_downloaded_at' => now(),
                ]
            );

            if ($request->expectsJson()) {
                return $this->success([
                    'download_url' => $materi->file_url,
                    'file_name' => basename($materi->file_url),
                    'file_size' => $materi->file_size,
                ], 'Download URL generated');
            }

            // Redirect to file URL for direct download
            return redirect($materi->file_url);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->error('Material not found', 404);
        } catch (\Exception $e) {
            \Log::error('MaterialUploadController@download: ' . $e->getMessage());
            return $this->error('Failed to process download: ' . $e->getMessage(), 400);
        }
    }

    /**
     * POST /api/v1/materials/{id}/stream
     * Stream video material (for video players)
     */
    public function stream($id, Request $request)
    {
        try {
            $materi = Materi::findOrFail($id);

            if ($materi->tipe !== 'video' || !$materi->file_url) {
                return $this->error('This material is not a video or has no file', 400);
            }

            // Check enrollment first
            $isEnrolled = \DB::table('kelas_peserta')
                ->where('kelas_id', $materi->kelas_id)
                ->where('siswa_id', auth()->id())
                ->exists();

            if (!$isEnrolled && ($materi->kelas->status ?? null) !== 'aktif') {
                return $this->error('You are not enrolled in this course', 403);
            }

            // Track video watch duration
            if ($request->has('timestamp')) {
                \DB::table('video_watches')->updateOrCreate(
                    [
                        'user_id' => auth()->id(),
                        'materi_id' => $id,
                    ],
                    [
                        'watched_duration' => $request->input('timestamp'),
                        'last_watched_at' => now(),
                    ]
                );
            }

            $materi->increment('download_count');

            if ($request->expectsJson()) {
                return $this->success([
                    'stream_url' => $materi->file_url,
                    'mime_type' => $materi->file_mime_type,
                    'duration_watched' => $request->input('timestamp', 0),
                ], 'Video stream ready');
            }

            return redirect($materi->file_url);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->error('Material not found', 404);
        } catch (\Exception $e) {
            \Log::error('MaterialUploadController@stream: ' . $e->getMessage());
            return $this->error('Failed to stream video: ' . $e->getMessage(), 400);
        }
    }

    /**
     * GET /api/v1/materials/stats
     * Get upload statistics
     */
    public function stats(Request $request)
    {
        try {
            $teacherId = auth()->id();

            $totalUploaded = Materi::whereHas('kelas', function ($q) use ($teacherId) {
                $q->where('pengajar_id', $teacherId);
            })->where('uploaded_at', '!=', null)->count();

            $totalDownloads = Materi::whereHas('kelas', function ($q) use ($teacherId) {
                $q->where('pengajar_id', $teacherId);
            })->sum('download_count');

            $totalSize = Materi::whereHas('kelas', function ($q) use ($teacherId) {
                $q->where('pengajar_id', $teacherId);
            })->sum('file_size');

            $recentUploads = Materi::whereHas('kelas', function ($q) use ($teacherId) {
                $q->where('pengajar_id', $teacherId);
            })
                ->where('uploaded_at', '!=', null)
                ->orderBy('uploaded_at', 'desc')
                ->take(5)
                ->get(['materi_id', 'judul', 'file_size', 'download_count', 'uploaded_at']);

            return $this->success([
                'total_uploaded' => $totalUploaded,
                'total_downloads' => $totalDownloads,
                'total_size_bytes' => $totalSize,
                'total_size_mb' => round($totalSize / (1024 * 1024), 2),
                'recent_uploads' => $recentUploads,
            ], 'Upload statistics retrieved');

        } catch (\Exception $e) {
            \Log::error('MaterialUploadController@stats: ' . $e->getMessage());
            return $this->error('Failed to retrieve statistics: ' . $e->getMessage(), 400);
        }
    }
}
