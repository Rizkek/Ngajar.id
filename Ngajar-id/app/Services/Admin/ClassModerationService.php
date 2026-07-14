<?php

namespace App\Services\Admin;

use App\Models\Course;
use Exception;

class ClassModerationService
{
    /**
     * List classes for admin review with optional filters.
     */
    public function listClasses(array $filters = [], int $perPage = 15)
    {
        $query = Course::with('pengajar')->withCount('peserta', 'materi');

        if (!empty($filters['status']) && $filters['status'] !== 'all') {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['kategori'])) {
            $query->where('kategori', $filters['kategori']);
        }

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('judul', 'like', "%{$search}%")
                    ->orWhere('deskripsi', 'like', "%{$search}%")
                    ->orWhereHas('pengajar', fn($q) => $q->where('name', 'like', "%{$search}%"));
            });
        }

        return $query->latest()->paginate($perPage);
    }

    /**
     * Get a single class with details.
     */
    public function getClass(int $classId): Kelas
    {
        return Course::with(['pengajar', 'materi', 'peserta'])->findOrFail($classId);
    }

    /**
     * Approve a class (sets status to 'aktif').
     */
    public function approveClass(int $classId): Kelas
    {
        $kelas = Course::findOrFail($classId);
        $kelas->update(['status' => 'aktif', 'catatan_admin' => null]);
        return $kelas->fresh();
    }

    /**
     * Reject a class with an optional reason.
     */
    public function rejectClass(int $classId, ?string $reason = null): Kelas
    {
        $kelas = Course::findOrFail($classId);
        $kelas->update([
            'status' => 'ditolak',
            'catatan_admin' => $reason,
        ]);
        return $kelas->fresh();
    }

    /**
     * Archive a class (sets status to 'selesai').
     */
    public function archiveClass(int $classId): Kelas
    {
        $kelas = Course::findOrFail($classId);
        $kelas->update(['status' => 'selesai']);
        return $kelas->fresh();
    }

    /**
     * Delete a class. Prevents deletion if students are enrolled.
     */
    public function deleteClass(int $classId): void
    {
        $kelas = Course::findOrFail($classId);

        if ($kelas->peserta()->count() > 0) {
            throw new Exception('Cannot delete class with enrolled students. Archive it instead.');
        }

        $kelas->delete();
    }

    /**
     * Flag a class for review with a mandatory reason.
     */
    public function flagClass(int $classId, string $reason): Kelas
    {
        $kelas = Course::findOrFail($classId);
        $kelas->update([
            'flagged_for_review' => true,
            'catatan_admin' => $reason,
        ]);
        return $kelas->fresh();
    }
}


