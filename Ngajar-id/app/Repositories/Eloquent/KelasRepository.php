<?php

namespace App\Repositories\Eloquent;

use App\Models\Course;
use App\Repositories\Contracts\KelasRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;

class KelasRepository implements KelasRepositoryInterface
{
    public function findById(int $id): Course
    {
        // Simple find, maybe cached if accessed often, but keeping it direct for now
        return Course::findOrFail($id);
    }

    public function findByIdWithInstructor(int $id): Course
    {
        // Caching class details with instructor to prevent N+1 and repeated DB hits
        return Cache::remember("kelas_with_pengajar_{$id}", 60 * 60, function () use ($id) {
            return Course::with(['pengajar'])->findOrFail($id);
        });
    }

    public function getTeacherClasses(int $teacherId, int $perPage = 10): LengthAwarePaginator
    {
        // Eager load related counts or basic relations to prevent N+1 on dashboard
        return Course::where('pengajar_id', $teacherId)
            ->withCount('peserta')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    public function create(array $data): Course
    {
        return Course::create($data);
    }

    public function update(int $id, array $data): Course
    {
        $kelas = Course::findOrFail($id);
        $kelas->update($data);
        
        // Invalidate cache
        Cache::forget("kelas_with_pengajar_{$id}");
        
        return $kelas;
    }

    public function delete(int $id): bool
    {
        $kelas = Course::findOrFail($id);
        Cache::forget("kelas_with_pengajar_{$id}");
        
        return $kelas->delete();
    }
}


