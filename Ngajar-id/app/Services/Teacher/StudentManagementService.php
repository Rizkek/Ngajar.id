<?php

namespace App\Services\Teacher;

use App\Models\Course;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;

class StudentManagementService
{
    /**
     * Get paginated students enrolled in a specific class.
     *
     * @param User $teacher
     * @param int $classId
     * @param int $perPage
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getClassStudents(User $teacher, int $classId, int $perPage = 15)
    {
        $kelas = Course::findOrFail($classId);

        if ($kelas->pengajar_id != $teacher->user_id) {
            throw new Exception("Anda tidak memiliki akses ke kelas ini.", 403);
        }

        return $kelas->peserta()->paginate($perPage);
    }

    /**
     * Upload grades/progress manually for a student.
     */
    public function uploadGrades(User $teacher, int $classId, int $studentId, array $data)
    {
        $kelas = Course::findOrFail($classId);

        if ($kelas->pengajar_id != $teacher->user_id) {
            throw new Exception("Anda tidak memiliki akses ke kelas ini.", 403);
        }

        // Logic for uploading grades
        // Assuming there is a pivot table or grade table
        DB::table('kelas_peserta')
            ->where('kelas_id', $classId)
            ->where('user_id', $studentId)
            ->update([
                'grade' => $data['grade'] ?? null,
                'updated_at' => now()
            ]);

        return true;
    }

    /**
     * Add teacher's comment/feedback for a student in a class.
     */
    public function addComment(User $teacher, int $classId, int $studentId, string $comment)
    {
        $kelas = Course::findOrFail($classId);

        if ($kelas->pengajar_id != $teacher->user_id) {
            throw new Exception("Anda tidak memiliki akses ke kelas ini.", 403);
        }

        DB::table('kelas_peserta')
            ->where('kelas_id', $classId)
            ->where('user_id', $studentId)
            ->update([
                'teacher_feedback' => $comment,
                'updated_at' => now()
            ]);

        return true;
    }

    /**
     * Get detailed progress of a specific student in a class.
     */
    public function getStudentProgress(User $teacher, int $classId, int $studentId)
    {
        $kelas = Course::findOrFail($classId);

        if ($kelas->pengajar_id != $teacher->user_id) {
            throw new Exception("Anda tidak memiliki akses ke kelas ini.", 403);
        }

        $student = User::findOrFail($studentId);
        
        // This could be refactored to use LearningProgressService
        // but since we need teacher's perspective, we get the data here.
        $completedMaterialsCount = DB::table('materi_akses')
            ->join('materi', 'materi.materi_id', '=', 'materi_akses.materi_id')
            ->where('materi.kelas_id', $classId)
            ->where('materi_akses.user_id', $studentId)
            ->count();

        $totalMaterials = $kelas->materi()->count();

        $progressPercentage = $totalMaterials > 0 ? round(($completedMaterialsCount / $totalMaterials) * 100) : 0;

        $enrollment = DB::table('kelas_peserta')
            ->where('kelas_id', $classId)
            ->where('user_id', $studentId)
            ->first();

        return [
            'student' => $student,
            'completed_materials' => $completedMaterialsCount,
            'total_materials' => $totalMaterials,
            'progress_percentage' => $progressPercentage,
            'enrollment_date' => $enrollment ? $enrollment->created_at : null,
            'grade' => $enrollment->grade ?? null,
            'teacher_feedback' => $enrollment->teacher_feedback ?? null
        ];
    }
}


