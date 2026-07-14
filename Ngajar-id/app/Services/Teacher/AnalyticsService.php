<?php

namespace App\Services\Teacher;

use App\Models\Course;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class AnalyticsService
{
    /**
     * Get overall analytics for a teacher.
     */
    public function getOverviewAnalytics(User $teacher)
    {
        $totalClasses = Course::where('pengajar_id', $teacher->user_id)->count();

        $totalStudents = DB::table('kelas_peserta')
            ->join('kelas', 'kelas.kelas_id', '=', 'kelas_peserta.kelas_id')
            ->where('kelas.pengajar_id', $teacher->user_id)
            ->count();

        $averageRating = DB::table('ulasan')
            ->join('kelas', 'kelas.kelas_id', '=', 'ulasan.kelas_id')
            ->where('kelas.pengajar_id', $teacher->user_id)
            ->avg('ulasan.rating') ?? 0;

        return [
            'total_classes' => $totalClasses,
            'total_students' => $totalStudents,
            'average_rating' => round($averageRating, 1)
        ];
    }

    /**
     * Get specific class analytics.
     */
    public function getClassAnalytics(User $teacher, int $classId)
    {
        $kelas = Course::where('pengajar_id', $teacher->user_id)
            ->where('kelas_id', $classId)
            ->firstOrFail();

        $totalStudents = DB::table('kelas_peserta')->where('kelas_id', $classId)->count();
        
        $averageRating = DB::table('ulasan')->where('kelas_id', $classId)->avg('rating') ?? 0;

        // Count how many students have completed all materials
        $totalMaterials = $kelas->materi()->count();
        $completedStudents = 0;

        if ($totalMaterials > 0) {
            $studentProgress = DB::table('materi_akses')
                ->join('materi', 'materi.materi_id', '=', 'materi_akses.materi_id')
                ->where('materi.kelas_id', $classId)
                ->select('materi_akses.user_id', DB::raw('count(materi_akses.materi_id) as completed_count'))
                ->groupBy('materi_akses.user_id')
                ->get();

            foreach ($studentProgress as $progress) {
                if ($progress->completed_count >= $totalMaterials) {
                    $completedStudents++;
                }
            }
        }

        return [
            'class' => $kelas,
            'total_students' => $totalStudents,
            'average_rating' => round($averageRating, 1),
            'completion_rate' => $totalStudents > 0 ? round(($completedStudents / $totalStudents) * 100) : 0,
            'completed_students' => $completedStudents
        ];
    }
}


