<?php

namespace App\Http\Controllers\Api\V1\Student;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Lesson;
use App\Http\Traits\ApiResponse;
use App\Http\Resources\MateriResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use App\Services\Course\LearningProgressService;
use App\Services\Course\ReviewService;
use App\Services\Course\NoteService;
use App\Services\Course\DiscussionService;
use App\Services\Course\CertificateService;
use App\Http\Requests\Student\StoreUlasanRequest;
use App\Http\Requests\Student\StoreDiskusiRequest;
use App\Http\Requests\Student\StoreCatatanRequest;

class StudentLearningController extends Controller
{
    use ApiResponse;

    /**
     * GET /api/v1/student/learning/materials
     * Get all materials for user's enrolled classes
     */
    public function myMaterials(Request $request)
    {
        try {
            $user = $request->user();

            $materials = Lesson::whereIn('kelas_id',
                $user->kelasIkuti()->pluck('kelas.kelas_id')
            )
            ->with('kelas:kelas_id,judul')
            ->paginate(20);

            return $this->successWithPagination(
                MateriResource::collection($materials),
                'Materials retrieved successfully'
            );
        } catch (\Exception $e) {
            \Log::error('Error in myMaterials: ' . $e->getMessage());
            return $this->serverError($e->getMessage());
        }
    }

    /**
     * GET /api/v1/student/learning/materials/{id}
     * Get single material details
     */
    public function getMaterial(Request $request, $id)
    {
        try {
            $user = $request->user();
            $materi = Lesson::with('kelas')->findOrFail($id);

            $isEnrolled = $user->kelasIkuti()->where('kelas_peserta.kelas_id', $materi->kelas_id)->exists();
            $isOwner = $materi->kelas->pengajar_id == $user->user_id;

            if (!$isEnrolled && !$isOwner) {
                return $this->forbidden('Not enrolled in this class');
            }

            $isUnlocked = $materi->isUnlockedBy($user);

            $data = [
                'material' => MateriResource::make($materi),
                'is_unlocked' => $isUnlocked,
                'is_completed' => Cache::has("user_{$user->user_id}_completed_materi_{$id}")
            ];

            return $this->success($data, 'Material retrieved successfully');
        } catch (\Exception $e) {
            \Log::error('Error in getMaterial: ' . $e->getMessage());
            return $this->serverError($e->getMessage());
        }
    }

    /**
     * POST /api/v1/student/learning/materials/{id}/complete
     * Mark material as complete
     */
    public function completeMaterial(Request $request, $id, LearningProgressService $learningProgressService)
    {
        try {
            $user = $request->user();
            $result = $learningProgressService->markMaterialAsComplete($user, $id);

            return $this->success($result, 'Material marked as complete', 201);
        } catch (\Exception $e) {
            $msg = $e->getMessage();
            if (str_contains($msg, 'Material already completed')) {
                return $this->success(['xp_gained' => 0], $msg);
            }
            if (str_contains($msg, 'Not enrolled')) {
                return $this->forbidden($msg);
            }
            return $this->serverError($msg);
        }
    }

    /**
     * POST /api/v1/student/learning/materials/{id}/unlock
     * Unlock premium material with tokens
     */
    public function unlockMaterial(Request $request, $id, LearningProgressService $learningProgressService)
    {
        try {
            $user = $request->user();
            
            $result = $learningProgressService->unlockPremiumMaterial($user, $id);
            $result['material'] = MateriResource::make($result['material']);

            return $this->success($result, 'Material unlocked successfully', 201);
        } catch (\Exception $e) {
            $msg = $e->getMessage();
            if (str_contains($msg, 'Material already unlocked') || str_contains($msg, 'Material is not premium')) {
                return $this->validationError(['error' => $msg]);
            }
            if (str_contains($msg, 'Not enrolled')) {
                return $this->forbidden($msg);
            }
            if (str_contains($msg, 'Insufficient token')) {
                return $this->validationError(['error' => $msg]);
            }
            return $this->serverError($msg);
        }
    }

    /**
     * GET /api/v1/student/learning/progress
     * Get overall learning progress
     */
    public function overallProgress(Request $request)
    {
        try {
            $user = $request->user();
            $classes = $user->kelasIkuti()->with('materi')->get();

            $progressData = [];
            $totalCompleted = 0;
            $totalMaterials = 0;

            foreach ($classes as $kelas) {
                $materiCount = $kelas->materi->count();
                $completedCount = 0;

                foreach ($kelas->materi as $materi) {
                    if (Cache::has("user_{$user->user_id}_completed_materi_{$materi->materi_id}")) {
                        $completedCount++;
                    }
                }

                $percentage = $materiCount > 0 ? round(($completedCount / $materiCount) * 100) : 0;

                $progressData[] = [
                    'class_id' => $kelas->kelas_id,
                    'class_name' => $kelas->judul,
                    'total_materials' => $materiCount,
                    'completed_materials' => $completedCount,
                    'progress_percentage' => $percentage
                ];

                $totalCompleted += $completedCount;
                $totalMaterials += $materiCount;
            }

            $overallPercentage = $totalMaterials > 0 ? round(($totalCompleted / $totalMaterials) * 100) : 0;

            $data = [
                'overall_progress' => $overallPercentage,
                'total_materials' => $totalMaterials,
                'completed_materials' => $totalCompleted,
                'classes' => $progressData
            ];

            return $this->success($data, 'Progress retrieved successfully');
        } catch (\Exception $e) {
            \Log::error('Error in overallProgress: ' . $e->getMessage());
            return $this->serverError($e->getMessage());
        }
    }

    /**
     * GET /api/v1/student/classes/{id}/progress
     */
    public function classProgress(Request $request, $id, LearningProgressService $learningProgressService)
    {
        try {
            $user = $request->user();
            $data = $learningProgressService->calculateClassProgress($user, $id);

            return $this->success($data, 'Class progress retrieved successfully');
        } catch (\Exception $e) {
            $msg = $e->getMessage();
            if (str_contains($msg, 'Not enrolled')) {
                return $this->forbidden($msg);
            }
            return $this->serverError($msg);
        }
    }

    /**
     * POST /api/v1/student/reviews/classes/{id}
     */
    public function storeReview(StoreUlasanRequest $request, $id, ReviewService $reviewService)
    {
        try {
            $reviewService->submitReview($request->user(), $id, $request->rating, $request->review);

            return $this->success(
                ['rating' => $request->rating],
                'Review submitted successfully',
                201
            );
        } catch (\Exception $e) {
            $msg = $e->getMessage();
            if (str_contains($msg, 'belum terdaftar')) {
                return $this->forbidden($msg);
            }
            return $this->serverError($msg);
        }
    }

    /**
     * GET /api/v1/student/reviews/classes/{id}
     */
    public function classReviews(Request $request, $id, ReviewService $reviewService)
    {
        try {
            $reviews = $reviewService->getClassReviews($id, 10);
            return $this->successWithPagination($reviews, 'Reviews retrieved', $reviews);
        } catch (\Exception $e) {
            return $this->serverError($e->getMessage());
        }
    }

    /**
     * POST /api/v1/student/discussions/classes/{id}
     */
    public function storeDiskusi(StoreDiskusiRequest $request, $id, DiscussionService $discussionService)
    {
        try {
            // Note: Frontend should send 'konten' for the body as it's required by the service,
            // or we use 'isi_diskusi' if we standardized on it in the request.
            // Based on original logic, the field was 'konten'. I'll map 'isi_diskusi' or 'konten'.
            $konten = $request->input('isi_diskusi') ?? $request->input('konten');
            $discussionService->submitDiscussion($request->user(), $id, $konten, $request->parent_id);

            return $this->success(['message' => 'Discussion posted'], 'Discussion posted successfully', 201);
        } catch (\Exception $e) {
            $msg = $e->getMessage();
            if (str_contains($msg, 'Terlalu banyak')) {
                return $this->error($msg, 429);
            }
            return $this->serverError($msg);
        }
    }

    /**
     * GET /api/v1/student/discussions/classes/{id}
     */
    public function classDiskusi(Request $request, $id, DiscussionService $discussionService)
    {
        try {
            $diskusi = $discussionService->getClassDiscussions($id, 10);
            return $this->successWithPagination($diskusi, 'Discussions retrieved', $diskusi);
        } catch (\Exception $e) {
            return $this->serverError($e->getMessage());
        }
    }

    /**
     * POST /api/v1/student/notes/materials/{id}
     */
    public function storeNotesForMaterial(StoreCatatanRequest $request, $id, NoteService $noteService)
    {
        try {
            $noteService->saveNote($request->user(), $id, $request->catatan);
            return $this->success(['message' => 'Notes saved'], 'Notes saved successfully', 201);
        } catch (\Exception $e) {
            return $this->serverError($e->getMessage());
        }
    }

    /**
     * GET /api/v1/student/notes/materials/{id}
     */
    public function getMaterialNotes(Request $request, $id, NoteService $noteService)
    {
        try {
            $catatan = $noteService->getMaterialNote($request->user(), $id);
            return $this->success(['catatan' => $catatan ? $catatan->catatan : null], 'Notes retrieved');
        } catch (\Exception $e) {
            return $this->serverError($e->getMessage());
        }
    }

    /**
     * GET /api/v1/student/notes
     */
    public function allNotes(Request $request, NoteService $noteService)
    {
        try {
            $notes = $noteService->getAllUserNotes($request->user(), 15);
            return $this->successWithPagination($notes, 'Notes retrieved', $notes);
        } catch (\Exception $e) {
            return $this->serverError($e->getMessage());
        }
    }

    /**
     * GET /api/v1/student/certificates
     */
    public function myCertificates(Request $request, CertificateService $certificateService)
    {
        try {
            $certificates = $certificateService->getUserCertificates($request->user(), 10);
            return $this->successWithPagination($certificates, 'Certificates retrieved', $certificates);
        } catch (\Exception $e) {
            return $this->serverError($e->getMessage());
        }
    }

    /**
     * GET /api/v1/student/certificates/{id}
     */
    public function downloadCertificate(Request $request, $id, CertificateService $certificateService)
    {
        try {
            $data = $certificateService->getDownloadData($request->user(), $id);
            return $this->success($data, 'Certificate ready for download');
        } catch (\Exception $e) {
            $msg = $e->getMessage();
            if (str_contains($msg, '100%')) {
                return $this->forbidden($msg);
            }
            return $this->notFound($msg);
        }
    }

    /**
     * POST /api/v1/student/materials/{id}/buy
     */
    public function beliMateri(Request $request, $id, LearningProgressService $learningProgressService)
    {
        return $this->unlockMaterial($request, $id, $learningProgressService);
    }
}


