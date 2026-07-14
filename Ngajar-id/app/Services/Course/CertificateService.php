<?php

namespace App\Services\Course;

use App\Models\User;
use Exception;
use App\Services\Course\LearningProgressService;

class CertificateService
{
    /**
     * @var LearningProgressService
     */
    protected $learningProgressService;

    public function __construct(LearningProgressService $learningProgressService)
    {
        $this->learningProgressService = $learningProgressService;
    }

    /**
     * Get paginated certificates for a user.
     * Currently it relies on learningPathsEnrolled.
     *
     * @param User $user
     * @param int $perPage
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getUserCertificates(User $user, int $perPage = 10)
    {
        return $user->learningPathsEnrolled()
            ->wherePivotNotNull('completed_at')
            ->with('creator')
            ->paginate($perPage);
    }

    /**
     * Validate and get data for certificate download.
     * Enforces 100% progress constraint.
     *
     * @param User $user
     * @param int $pathId
     * @return array
     * @throws Exception
     */
    public function getDownloadData(User $user, int $pathId): array
    {
        $path = $user->learningPathsEnrolled()
            ->wherePivot('learning_path_id', $pathId)
            ->first();

        if (!$path) {
            throw new Exception("Certificate not found or you are not enrolled in this learning path.", 404);
        }

        // Validate 100% progress based on completed_at or calculate actual progress
        if (!$path->pivot->completed_at) {
            // Check progress
            // In a real scenario, this might need to calculate progress of all classes in the learning path.
            // For now, if completed_at is null, they haven't finished it.
            throw new Exception("Anda belum menyelesaikan seluruh materi. Sertifikat hanya bisa diunduh setelah progress 100%.", 403);
        }

        return [
            'certificate_id' => $pathId,
            'path_name' => $path->judul,
            'completed_at' => $path->pivot->completed_at,
            'certificate_url' => route('certificate.download', $pathId),
            'path' => $path
        ];
    }
}
