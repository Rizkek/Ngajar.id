<?php

namespace App\Services\Teacher;

use App\Models\Course;
use App\Models\Lesson;
use App\Models\User;
use Illuminate\Support\Str;
use Exception;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\UploadedFile;

class MaterialManagementService
{
    /**
     * Get materials by class.
     */
    public function getMaterialsByClass(User $teacher, int $classId)
    {
        $kelas = Course::findOrFail($classId);

        if ($kelas->pengajar_id != $teacher->user_id) {
            throw new Exception("Anda tidak memiliki akses ke kelas ini.", 403);
        }

        return $kelas->materi()->orderBy('urutan')->get();
    }

    /**
     * Create material.
     */
    public function createMaterial(User $teacher, int $classId, array $data): Materi
    {
        $kelas = Course::findOrFail($classId);

        if ($kelas->pengajar_id != $teacher->user_id) {
            throw new Exception("Anda tidak memiliki akses ke kelas ini.", 403);
        }

        $data['kelas_id'] = $classId;
        $data['slug'] = Str::slug($data['judul']) . '-' . time();
        
        return Lesson::create($data);
    }

    /**
     * Update material.
     */
    public function updateMaterial(User $teacher, int $materialId, array $data): Materi
    {
        $materi = Lesson::findOrFail($materialId);
        $kelas = $materi->kelas;

        if ($kelas->pengajar_id != $teacher->user_id) {
            throw new Exception("Anda tidak memiliki akses ke kelas ini.", 403);
        }

        if (isset($data['judul']) && $data['judul'] !== $materi->judul) {
            $data['slug'] = Str::slug($data['judul']) . '-' . time();
        }

        $materi->update($data);

        return $materi;
    }

    /**
     * Delete material.
     */
    public function deleteMaterial(User $teacher, int $materialId)
    {
        $materi = Lesson::findOrFail($materialId);
        $kelas = $materi->kelas;

        if ($kelas->pengajar_id != $teacher->user_id) {
            throw new Exception("Anda tidak memiliki akses ke kelas ini.", 403);
        }

        if ($materi->storage_path && Storage::disk('supabase')->exists($materi->storage_path)) {
            Storage::disk('supabase')->delete($materi->storage_path);
        } elseif ($materi->file_path && Storage::disk('public')->exists($materi->file_path)) {
            Storage::disk('public')->delete($materi->file_path);
        }

        $materi->delete();
    }

    /**
     * Upload material file to Supabase S3
     */
    public function uploadFile(User $teacher, int $materialId, UploadedFile $file): array
    {
        $materi = Lesson::findOrFail($materialId);
        $kelas = $materi->kelas;

        if ($kelas->pengajar_id != $teacher->user_id) {
            throw new Exception("Anda tidak memiliki akses ke kelas ini.", 403);
        }

        $fileName = Str::slug($materi->judul) . '_' . time() . '.' . $file->getClientOriginalExtension();
        $filePath = Storage::disk('supabase')->putFileAs(
            "materials/{$materi->kelas_id}/{$materi->materi_id}",
            $file,
            $fileName,
            'public'
        );

        if (!$filePath) {
            throw new Exception("Failed to upload file to storage.");
        }

        $fileUrl = Storage::disk('supabase')->url($filePath);

        $materi->update([
            'file_url' => $fileUrl,
            'file_size' => $file->getSize(),
            'file_mime_type' => $file->getMimeType(),
            'storage_path' => $filePath,
            'uploaded_by' => $teacher->user_id,
            'uploaded_at' => now(),
        ]);

        // Award XP
        $teacher->update(['xp' => ($teacher->xp ?? 0) + 250]);

        return [
            'materi_id' => $materi->materi_id,
            'file_url' => $fileUrl,
            'file_size' => $file->getSize(),
            'file_type' => $file->getMimeType(),
            'xp_earned' => 250,
            'storage_path' => $filePath,
        ];
    }

    /**
     * Delete uploaded file from Supabase S3
     */
    public function deleteUploadedFile(User $teacher, int $materialId)
    {
        $materi = Lesson::findOrFail($materialId);
        $kelas = $materi->kelas;

        if ($kelas->pengajar_id != $teacher->user_id) {
            throw new Exception("Anda tidak memiliki akses ke kelas ini.", 403);
        }

        if (!$materi->storage_path) {
            throw new Exception("No file to delete.");
        }

        Storage::disk('supabase')->delete($materi->storage_path);

        $materi->update([
            'file_url' => null,
            'file_size' => null,
            'file_mime_type' => null,
            'storage_path' => null,
            'uploaded_by' => null,
            'uploaded_at' => null,
            'download_count' => 0,
        ]);
    }

    /**
     * Process student download tracking (should probably be in Course module but placed here for continuity)
     */
    public function processDownload(User $user, int $materialId): array
    {
        $materi = Lesson::findOrFail($materialId);

        if (!$materi->file_url) {
            throw new Exception("No file available for download.");
        }

        if ($materi->kelas->is_premium ?? false) {
            $hasPurchase = DB::table('pembelian_materi')
                ->where('user_id', $user->user_id ?? auth()->id())
                ->where('materi_id', $materialId)
                ->exists();

            if (!$hasPurchase) {
                throw new Exception("You need to purchase this material to download.", 403);
            }
        }

        $materi->increment('download_count');

        DB::table('material_downloads')->updateOrCreate(
            ['user_id' => $user->user_id ?? auth()->id(), 'materi_id' => $materialId],
            ['last_downloaded_at' => now()]
        );

        return [
            'download_url' => $materi->file_url,
            'file_name' => basename($materi->file_url),
            'file_size' => $materi->file_size,
        ];
    }

    /**
     * Process student stream tracking
     */
    public function processStream(User $user, int $materialId, $timestamp = 0): array
    {
        $materi = Lesson::findOrFail($materialId);

        if ($materi->tipe !== 'video' || !$materi->file_url) {
            throw new Exception("This material is not a video or has no file.");
        }

        $isEnrolled = DB::table('kelas_peserta')
            ->where('kelas_id', $materi->kelas_id)
            ->where('user_id', $user->user_id ?? auth()->id())
            ->exists();

        if (!$isEnrolled && ($materi->kelas->status ?? null) !== 'aktif') {
            throw new Exception("You are not enrolled in this course.", 403);
        }

        if ($timestamp) {
            DB::table('video_watches')->updateOrCreate(
                ['user_id' => $user->user_id ?? auth()->id(), 'materi_id' => $materialId],
                ['watched_duration' => $timestamp, 'last_watched_at' => now()]
            );
        }

        $materi->increment('download_count');

        return [
            'stream_url' => $materi->file_url,
            'mime_type' => $materi->file_mime_type,
            'duration_watched' => $timestamp,
        ];
    }
    
    /**
     * Get Upload Statistics for a teacher
     */
    public function getUploadStats(User $teacher)
    {
        $kelasIds = Course::where('pengajar_id', $teacher->user_id)->pluck('kelas_id');

        $totalStorage = Lesson::whereIn('kelas_id', $kelasIds)->sum('file_size') ?? 0;
        $totalFiles = Lesson::whereIn('kelas_id', $kelasIds)->whereNotNull('file_url')->count();
        $totalDownloads = Lesson::whereIn('kelas_id', $kelasIds)->sum('download_count') ?? 0;

        return [
            'total_storage_bytes' => $totalStorage,
            'total_storage_formatted' => round($totalStorage / 1048576, 2) . ' MB',
            'total_files' => $totalFiles,
            'total_downloads' => $totalDownloads
        ];
    }
}


