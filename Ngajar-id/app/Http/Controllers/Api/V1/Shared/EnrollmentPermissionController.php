<?php

namespace App\Http\Controllers\Api\V1\Shared;

use App\Http\Controllers\Controller;

use App\Http\Traits\ApiResponse;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;

class EnrollmentPermissionController extends Controller
{
    use ApiResponse;

    /**
     * POST /api/v1/enrollment/check
     * Check if user can enroll in a course
     */
    public function check(Request $request)
    {
        try {
            $validated = $request->validate([
                'kelas_id' => 'required|exists:kelas,kelas_id',
            ]);

            $userId = auth()->id();
            $kelasId = $validated['kelas_id'];

            $course = DB::table('kelas')->where('kelas_id', $kelasId)->first();

            if (!$course) {
                return $this->error('Course not found', 404);
            }

            $user = DB::table('users')->where('user_id', $userId)->first();

            // Check 1: Already enrolled
            $isEnrolled = DB::table('kelas_peserta')
                ->where('siswa_id', $userId)
                ->where('kelas_id', $kelasId)
                ->exists();

            if ($isEnrolled) {
                return $this->success([
                    'can_enroll' => false,
                    'reason' => 'already_enrolled',
                    'message' => 'You are already enrolled in this course'
                ], 'Enrollment check completed', 200);
            }

            // Check 2: Is teacher (can't enroll in own course)
            if ($user->role === 'pengajar' && $course->pengajar_id === $userId) {
                return $this->success([
                    'can_enroll' => false,
                    'reason' => 'is_instructor',
                    'message' => 'Instructors cannot enroll in their own courses'
                ], 'Enrollment check completed', 200);
            }

            // Check 3: Prerequisites
            if ($course->prerequisite_kelas_id) {
                $prerequisiteCompleted = DB::table('kelas_peserta')
                    ->where('siswa_id', $userId)
                    ->where('kelas_id', $course->prerequisite_kelas_id)
                    ->where('status', 'completed')
                    ->exists();

                if (!$prerequisiteCompleted) {
                    $prerequisiteCourse = DB::table('kelas')
                        ->where('kelas_id', $course->prerequisite_kelas_id)
                        ->first();

                    return $this->success([
                        'can_enroll' => false,
                        'reason' => 'prerequisite_not_completed',
                        'message' => 'You must complete ' . $prerequisiteCourse->judul . ' first',
                        'prerequisite' => [
                            'kelas_id' => $prerequisiteCourse->kelas_id,
                            'judul' => $prerequisiteCourse->judul
                        ]
                    ], 'Enrollment check completed', 200);
                }
            }

            // Check 4: Minimum level requirement
            if ($course->min_level) {
                $userLevel = $user->level ?? 1;
                if ($userLevel < $course->min_level) {
                    return $this->success([
                        'can_enroll' => false,
                        'reason' => 'level_requirement_not_met',
                        'message' => 'You need to reach level ' . $course->min_level,
                        'current_level' => $userLevel,
                        'required_level' => $course->min_level
                    ], 'Enrollment check completed', 200);
                }
            }

            // Check 5: Min XP requirement
            if ($course->min_xp) {
                $userXp = $user->xp ?? 0;
                if ($userXp < $course->min_xp) {
                    return $this->success([
                        'can_enroll' => false,
                        'reason' => 'xp_requirement_not_met',
                        'message' => 'You need ' . $course->min_xp . ' XP to enroll',
                        'current_xp' => $userXp,
                        'required_xp' => $course->min_xp
                    ], 'Enrollment check completed', 200);
                }
            }

            // Check 6: Age restriction
            if ($course->min_age) {
                $userAge = $this->calculateAge($user->tanggal_lahir ?? null);
                if ($userAge < $course->min_age) {
                    return $this->success([
                        'can_enroll' => false,
                        'reason' => 'age_requirement_not_met',
                        'message' => 'Minimum age required: ' . $course->min_age,
                        'current_age' => $userAge,
                        'required_age' => $course->min_age
                    ], 'Enrollment check completed', 200);
                }
            }

            // Check 7: Course full (max students)
            if ($course->max_students) {
                $enrolledCount = DB::table('kelas_peserta')
                    ->where('kelas_id', $kelasId)
                    ->count();

                if ($enrolledCount >= $course->max_students) {
                    return $this->success([
                        'can_enroll' => false,
                        'reason' => 'course_full',
                        'message' => 'This course has reached maximum enrollment',
                        'enrolled' => $enrolledCount,
                        'max_students' => $course->max_students
                    ], 'Enrollment check completed', 200);
                }
            }

            // Check 8: Enrollment deadline
            if ($course->enrollment_deadline && $course->enrollment_deadline < now()) {
                return $this->success([
                    'can_enroll' => false,
                    'reason' => 'enrollment_deadline_passed',
                    'message' => 'Enrollment deadline has passed',
                    'deadline' => $course->enrollment_deadline
                ], 'Enrollment check completed', 200);
            }

            // All checks passed
            return $this->success([
                'can_enroll' => true,
                'reason' => 'eligible',
                'message' => 'You are eligible to enroll in this course'
            ], 'Enrollment check completed', 200);

        } catch (\Exception $e) {
            return $this->error('Failed to check enrollment permission: ' . $e->getMessage(), 400);
        }
    }

    /**
     * POST /api/v1/enrollment/prerequisites/{kelasId}
     * Get course prerequisites
     */
    public function getPrerequisites($kelasId, Request $request)
    {
        try {
            $course = DB::table('kelas')->where('kelas_id', $kelasId)->first();

            if (!$course) {
                return $this->error('Course not found', 404);
            }

            $prereqs = [];

            // Direct prerequisite
            if ($course->prerequisite_kelas_id) {
                $prereq = DB::table('kelas')
                    ->where('kelas_id', $course->prerequisite_kelas_id)
                    ->select('kelas_id', 'judul', 'harga')
                    ->first();

                if ($prereq) {
                    $userCompleted = auth()->check() ? DB::table('kelas_peserta')
                        ->where('siswa_id', auth()->id())
                        ->where('kelas_id', $prereq->kelas_id)
                        ->where('status', 'completed')
                        ->exists() : false;

                    $prereqs[] = [
                        'kelas_id' => $prereq->kelas_id,
                        'judul' => $prereq->judul,
                        'harga' => $prereq->harga,
                        'type' => 'prerequisite',
                        'user_completed' => $userCompleted
                    ];
                }
            }

            return $this->success([
                'kelas_id' => $kelasId,
                'prerequisites' => $prereqs,
                'total_prerequisites' => count($prereqs)
            ], 'Prerequisites retrieved');

        } catch (\Exception $e) {
            return $this->error('Failed to get prerequisites: ' . $e->getMessage(), 400);
        }
    }

    /**
     * POST /api/v1/enrollment/requirements/{kelasId}
     * Get all enrollment requirements for a course
     */
    public function getRequirements($kelasId, Request $request)
    {
        try {
            $course = DB::table('kelas')->where('kelas_id', $kelasId)->first();

            if (!$course) {
                return $this->error('Course not found', 404);
            }

            $user = auth()->check() ? DB::table('users')->where('user_id', auth()->id())->first() : null;

            $requirements = [
                'level_requirement' => [
                    'required' => $course->min_level ? true : false,
                    'minimum_level' => $course->min_level,
                    'user_level' => $user->level ?? null,
                    'met' => $user ? ($user->level >= ($course->min_level ?? 0)) : false
                ],
                'xp_requirement' => [
                    'required' => $course->min_xp ? true : false,
                    'minimum_xp' => $course->min_xp,
                    'user_xp' => $user->xp ?? null,
                    'met' => $user ? ($user->xp >= ($course->min_xp ?? 0)) : false
                ],
                'age_requirement' => [
                    'required' => $course->min_age ? true : false,
                    'minimum_age' => $course->min_age,
                    'user_age' => $user && $user->tanggal_lahir ? $this->calculateAge($user->tanggal_lahir) : null,
                    'met' => $user && $user->tanggal_lahir ? $this->calculateAge($user->tanggal_lahir) >= ($course->min_age ?? 0) : false
                ],
                'capacity' => [
                    'max_students' => $course->max_students,
                    'enrolled_count' => DB::table('kelas_peserta')->where('kelas_id', $kelasId)->count(),
                    'available_slots' => $course->max_students ? $course->max_students - DB::table('kelas_peserta')->where('kelas_id', $kelasId)->count() : null,
                    'full' => $course->max_students ? DB::table('kelas_peserta')->where('kelas_id', $kelasId)->count() >= $course->max_students : false
                ],
                'enrollment_deadline' => [
                    'deadline' => $course->enrollment_deadline,
                    'passed' => $course->enrollment_deadline ? $course->enrollment_deadline < now() : false
                ]
            ];

            return $this->success($requirements, 'Enrollment requirements retrieved');

        } catch (\Exception $e) {
            return $this->error('Failed to get requirements: ' . $e->getMessage(), 400);
        }
    }

    /**
     * POST /api/v1/enrollment/restrictions
     * Get user enrollment restrictions
     */
    public function getRestrictions(Request $request)
    {
        try {
            if (!auth()->check()) {
                return $this->error('Unauthorized', 401);
            }

            $userId = auth()->id();
            $user = DB::table('users')->where('user_id', $userId)->first();

            $restrictions = [];

            // Check if user is suspended
            if ($user->is_suspended) {
                $restrictions[] = [
                    'type' => 'suspended',
                    'message' => 'Your account is suspended',
                    'reason' => $user->suspension_reason
                ];
            }

            // Check if user has unpaid courses
            $unpaidCourses = DB::table('course_payments')
                ->where('user_id', $userId)
                ->where('status', 'pending')
                ->count();

            if ($unpaidCourses > 0) {
                $restrictions[] = [
                    'type' => 'pending_payment',
                    'message' => 'You have pending course payments',
                    'count' => $unpaidCourses
                ];
            }

            return $this->success([
                'has_restrictions' => count($restrictions) > 0,
                'restrictions' => $restrictions
            ], 'User restrictions retrieved');

        } catch (\Exception $e) {
            return $this->error('Failed to get restrictions: ' . $e->getMessage(), 400);
        }
    }

    /**
     * Helper function to calculate age
     */
    private function calculateAge($birthDate)
    {
        if (!$birthDate) return null;

        $today = date("Y-m-d");
        $diff = date_diff(date_create($birthDate), date_create($today));
        return $diff->y;
    }
}
