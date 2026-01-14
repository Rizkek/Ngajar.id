<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;

class SupabaseStorageService
{
    /**
     * Upload file to Supabase Storage
     * 
     * @param UploadedFile $file
     * @param string $folder
     * @param string|null $filename
     * @return string|false URL of uploaded file or false on failure
     */
    public function uploadFile(UploadedFile $file, string $folder = 'uploads', ?string $filename = null): string|false
    {
        try {
            // Generate unique filename if not provided
            if (!$filename) {
                $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            }

            // Full path in storage
            $path = $folder . '/' . $filename;

            // Upload to Supabase
            $uploaded = Storage::disk('supabase')->put($path, file_get_contents($file->getRealPath()));

            if ($uploaded) {
                return $this->getPublicUrl($path);
            }

            return false;
        } catch (\Exception $e) {
            \Log::error('Supabase upload error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get public URL for a file in Supabase Storage
     * 
     * @param string $path
     * @return string
     */
    public function getPublicUrl(string $path): string
    {
        $bucket = config('filesystems.disks.supabase.bucket');
        $supabaseUrl = config('filesystems.disks.supabase.url');

        return "{$supabaseUrl}/{$path}";
    }

    /**
     * Delete file from Supabase Storage
     * 
     * @param string $path
     * @return bool
     */
    public function deleteFile(string $path): bool
    {
        try {
            return Storage::disk('supabase')->delete($path);
        } catch (\Exception $e) {
            \Log::error('Supabase delete error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Check if file exists in Supabase Storage
     * 
     * @param string $path
     * @return bool
     */
    public function fileExists(string $path): bool
    {
        try {
            return Storage::disk('supabase')->exists($path);
        } catch (\Exception $e) {
            \Log::error('Supabase exists error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Upload materi file (video/pdf)
     * 
     * @param UploadedFile $file
     * @return string|false
     */
    public function uploadMateri(UploadedFile $file): string|false
    {
        return $this->uploadFile($file, 'materi');
    }

    /**
     * Upload modul file (pdf)
     * 
     * @param UploadedFile $file
     * @return string|false
     */
    public function uploadModul(UploadedFile $file): string|false
    {
        return $this->uploadFile($file, 'modul');
    }

    /**
     * Upload profile image
     * 
     * @param UploadedFile $file
     * @return string|false
     */
    public function uploadProfileImage(UploadedFile $file): string|false
    {
        return $this->uploadFile($file, 'profiles');
    }
}
