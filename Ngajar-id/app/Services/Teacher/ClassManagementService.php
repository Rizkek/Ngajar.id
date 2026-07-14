<?php

namespace App\Services\Teacher;

use App\Repositories\Contracts\KelasRepositoryInterface;
use App\Models\Course;
use App\Models\User;
use Illuminate\Support\Str;
use Exception;

class ClassManagementService
{
    protected $kelasRepository;

    public function __construct(KelasRepositoryInterface $kelasRepository)
    {
        $this->kelasRepository = $kelasRepository;
    }

    /**
     * Get paginated classes for a teacher.
     */
    public function getTeacherClasses(User $teacher, int $perPage = 10)
    {
        return $this->kelasRepository->getTeacherClasses($teacher->user_id, $perPage);
    }

    /**
     * Create a new class.
     */
    public function createClass(User $teacher, array $data): Course
    {
        $data['pengajar_id'] = $teacher->user_id;
        $data['slug'] = Str::slug($data['judul']) . '-' . time();
        $data['status'] = 'draft'; // default to draft

        return $this->kelasRepository->create($data);
    }

    /**
     * Update an existing class.
     */
    public function updateClass(User $teacher, int $classId, array $data): Course
    {
        $kelas = $this->kelasRepository->findById($classId);

        if ($kelas->pengajar_id != $teacher->user_id) {
            throw new Exception("Anda tidak memiliki akses ke kelas ini.", 403);
        }

        if (isset($data['judul']) && $data['judul'] !== $kelas->judul) {
            $data['slug'] = Str::slug($data['judul']) . '-' . time();
        }

        return $this->kelasRepository->update($classId, $data);
    }

    /**
     * Delete a class.
     */
    public function deleteClass(User $teacher, int $classId)
    {
        $kelas = $this->kelasRepository->findById($classId);

        if ($kelas->pengajar_id != $teacher->user_id) {
            throw new Exception("Anda tidak memiliki akses ke kelas ini.", 403);
        }

        $this->kelasRepository->delete($classId);
    }

    /**
     * Publish a class.
     */
    public function publishClass(User $teacher, int $classId): Course
    {
        $kelas = $this->kelasRepository->findById($classId);

        if ($kelas->pengajar_id != $teacher->user_id) {
            throw new Exception("Anda tidak memiliki akses ke kelas ini.", 403);
        }

        return $this->kelasRepository->update($classId, ['status' => 'published']);
    }

    /**
     * Archive a class.
     */
    public function archiveClass(User $teacher, int $classId): Course
    {
        $kelas = $this->kelasRepository->findById($classId);

        if ($kelas->pengajar_id != $teacher->user_id) {
            throw new Exception("Anda tidak memiliki akses ke kelas ini.", 403);
        }

        return $this->kelasRepository->update($classId, ['status' => 'archived']);
    }
}

