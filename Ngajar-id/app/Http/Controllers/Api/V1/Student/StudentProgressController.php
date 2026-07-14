<?php

namespace App\Http\Controllers\Api\V1\Student;

use App\Http\Controllers\Controller;

use App\Models\Lesson;
use App\Models\User;
use App\Http\Traits\ApiResponse;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;

class StudentProgressController extends Controller
{
    use ApiResponse;

    /**
     * POST /api/v1/materi/{id}/complete
     * Mark a material as completed and update course progress
     */
    public function completeMaterial($id, Request $request)
    {
        try {
            $user = auth()->user();
            $materi = Lesson::findOrFail($id);

            // Check if user is enrolled in this course
            $enrollment = DB::table('kelas_peserta')
                ->where('kelas_id', $materi->kelas_id)
                ->where('siswa_id', $user->user_id)
                ->first();

            if (!$enrollment) {
                return $this->error('Not enrolled in this course', 403);
            }

            // Check if already completed
            $existing = DB::table('material_progress')
                ->where('student_id', $user->user_id)
                ->where('materi_id', $id)
                ->first();

            $xpAwarded = 0;

            if ($existing && $existing->is_completed) {
                return $this->error('Material already completed', 400);
            }

            if ($existing) {
                // Update existing progress
                DB::table('material_progress')
                    ->where('student_id', $user->user_id)
                    ->where('materi_id', $id)
                    ->update([
                        'is_completed' => true,
                        'completed_at' => now(),
                    ]);

                $xpAwarded = 50; // XP for completing material
            } else {
                // Create new progress record
                DB::table('material_progress')->insert([
                    'student_id' => $user->user_id,
                    'materi_id' => $id,
                    'is_completed' => true,
                    'completed_at' => now(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $xpAwarded = 50; // XP for completing material
            }

            // Update course progress
            $totalMateri = DB::table('materi')
                ->where('kelas_id', $materi->kelas_id)
                ->count();

            $completedMateri = DB::table('material_progress')
                ->where('student_id', $user->user_id)
                ->whereIn('materi_id', function($q) use ($materi) {
                    $q->select('materi_id')
                      ->from('materi')
                      ->where('kelas_id', $materi->kelas_id);
                })
                ->where('is_completed', true)
                ->count();

            $progress = intdiv($completedMateri * 100, $totalMateri);
            $isCompleted = $progress == 100;

            DB::table('kelas_peserta')
                ->where('kelas_id', $materi->kelas_id)
                ->where('siswa_id', $user->user_id)
                ->update([
                    'progress' => $progress,
                    'status' => $isCompleted ? 'completed' : 'active',
                    'completion_date' => $isCompleted ? now() : null,
                    'last_accessed_at' => now(),
                ]);

            // Award XP to user
            $newXP = ($user->xp ?? 0) + $xpAwarded;
            $newLevel = max(1, intdiv($newXP, 2000) + 1);

            $user->update([
                'xp' => $newXP,
                'level' => $newLevel,
            ]);

            // Award bonus XP if course completed
            if ($isCompleted) {
                $bonusXP = 500;
                $newXP = $newXP + $bonusXP;
                $newLevel = max(1, intdiv($newXP, 2000) + 1);
                $user->update([
                    'xp' => $newXP,
                    'level' => $newLevel,
                ]);
                $xpAwarded += $bonusXP;
            }

            return $this->success([
                    'materi_id' => $id,
                    'is_completed' => true,
                    'completed_at' => now()->toIso8601String(),
                    'course_progress' => $progress,
                    'course_status' => $isCompleted ? 'completed' : 'active',
                    'xp_earned' => $xpAwarded,
                    'user_level' => $newLevel,
                    'user_xp' => $newXP,
                ], $isCompleted ? 'Course completed! Bonus XP awarded!' : 'Material marked as completed', 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->error('Material not found', 404);
        } catch (\Exception $e) {
            return $this->error('Failed to mark material as completed: ' . $e->getMessage(), 400);
        }
    }

    /**
     * GET /api/v1/my-progress/{kelas_id}
     * Get detailed progress for a specific course
     */
    public function getCourseProgress($kelasId, Request $request)
    {
        try {
            $userId = auth()->id();

            // Check enrollment
            $enrollment = DB::table('kelas_peserta')
                ->where('kelas_id', $kelasId)
                ->where('siswa_id', $userId)
                ->first();

            if (!$enrollment) {
                return $this->error('Not enrolled in this course', 403);
            }

            // Get all materials
            $materials = DB::table('materi')
                ->where('kelas_id', $kelasId)
                ->select('materi_id', 'judul', 'tipe')
                ->orderBy('created_at')
                ->get();

            // Get progress for each material
            $progress = DB::table('material_progress')
                ->where('student_id', $userId)
                ->whereIn('materi_id', $materials->pluck('materi_id'))
                ->get()
                ->keyBy('materi_id');

            $materialsWithProgress = $materials->map(function($m) use ($progress) {
                $p = $progress[$m->materi_id] ?? null;
                return [
                    'materi_id' => $m->materi_id,
                    'judul' => $m->judul,
                    'tipe' => $m->tipe,
                    'is_completed' => $p?->is_completed ?? false,
                    'completed_at' => $p?->completed_at,
                ];
            });

            return $this->success([
                'kelas_id' => $kelasId,
                'overall_progress' => $enrollment->progress,
                'status' => $enrollment->status,
                'completion_date' => $enrollment->completion_date,
                'materials' => $materialsWithProgress,
                'completed_count' => $materialsWithProgress->where('is_completed', true)->count(),
                'total_count' => $materialsWithProgress->count(),
            ], 'Course progress retrieved successfully');

        } catch (\Exception $e) {
            return $this->error('Failed to retrieve progress: ' . $e->getMessage(), 400);
        }
    }

    /**
     * GET /api/v1/my-progress
     * Get all progress across all enrolled courses
     */
    public function getAllProgress(Request $request)
    {
        try {
            $userId = auth()->id();

            $courses = DB::table('kelas_peserta')
                ->join('kelas', 'kelas_peserta.kelas_id', '=', 'kelas.kelas_id')
                ->where('kelas_peserta.siswa_id', $userId)
                ->select(
                    'kelas.kelas_id',
                    'kelas.judul',
                    'kelas_peserta.progress',
                    'kelas_peserta.status',
                    'kelas_peserta.completion_date',
                    'kelas_peserta.tanggal_daftar'
                )
                ->get();

            $coursesWithDetails = $courses->map(function($c) use ($userId) {
                $total = DB::table('materi')
                    ->where('kelas_id', $c->kelas_id)
                    ->count();

                $completed = DB::table('material_progress')
                    ->where('student_id', $userId)
                    ->where('is_completed', true)
                    ->whereIn('materi_id', function($q) use ($c) {
                        $q->select('materi_id')->from('materi')->where('kelas_id', $c->kelas_id);
                    })
                    ->count();

                return [
                    'kelas_id' => $c->kelas_id,
                    'judul' => $c->judul,
                    'progress' => $c->progress,
                    'status' => $c->status,
                    'completion_date' => $c->completion_date,
                    'enrolled_at' => $c->tanggal_daftar,
                    'materials_completed' => $completed,
                    'total_materials' => $total,
                ];
            });

            return $this->success([
                'total_enrolled' => $coursesWithDetails->count(),
                'completed' => $coursesWithDetails->where('status', 'completed')->count(),
                'in_progress' => $coursesWithDetails->where('status', 'active')->count(),
                'courses' => $coursesWithDetails,
            ], 'All progress retrieved successfully');

        } catch (\Exception $e) {
            return $this->error('Failed to retrieve progress: ' . $e->getMessage(), 400);
        }
    }
}


