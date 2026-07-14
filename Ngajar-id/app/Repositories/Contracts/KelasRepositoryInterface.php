<?php

namespace App\Repositories\Contracts;

use App\Models\Course;
use Illuminate\Pagination\LengthAwarePaginator;

interface KelasRepositoryInterface
{
    public function findById(int $id): Course;
    public function findByIdWithInstructor(int $id): Course;
    public function getTeacherClasses(int $teacherId, int $perPage = 10): LengthAwarePaginator;
    public function create(array $data): Course;
    public function update(int $id, array $data): Course;
    public function delete(int $id): bool;
}

