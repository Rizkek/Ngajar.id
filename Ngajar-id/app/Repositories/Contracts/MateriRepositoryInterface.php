<?php

namespace App\Repositories\Contracts;

use App\Models\Lesson;
use Illuminate\Database\Eloquent\Collection;

interface MateriRepositoryInterface
{
    public function findById(int $id): Materi;
    public function getByKelasId(int $kelasId): Collection;
    public function create(array $data): Materi;
    public function update(int $id, array $data): Materi;
    public function delete(int $id): bool;
}

