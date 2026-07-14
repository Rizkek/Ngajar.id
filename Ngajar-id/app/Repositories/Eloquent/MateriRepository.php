<?php

namespace App\Repositories\Eloquent;

use App\Models\Lesson;
use App\Repositories\Contracts\MateriRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;

class MateriRepository implements MateriRepositoryInterface
{
    public function findById(int $id): Materi
    {
        return Lesson::findOrFail($id);
    }

    public function getByKelasId(int $kelasId): Collection
    {
        return Cache::remember("kelas_materi_{$kelasId}", 60 * 60, function () use ($kelasId) {
            return Lesson::where('kelas_id', $kelasId)->orderBy('created_at', 'asc')->get();
        });
    }

    public function create(array $data): Materi
    {
        $materi = Lesson::create($data);
        Cache::forget("kelas_materi_{$materi->kelas_id}");
        return $materi;
    }

    public function update(int $id, array $data): Materi
    {
        $materi = Lesson::findOrFail($id);
        $materi->update($data);
        Cache::forget("kelas_materi_{$materi->kelas_id}");
        return $materi;
    }

    public function delete(int $id): bool
    {
        $materi = Lesson::findOrFail($id);
        $kelasId = $materi->kelas_id;
        $deleted = $materi->delete();
        Cache::forget("kelas_materi_{$kelasId}");
        return $deleted;
    }
}


