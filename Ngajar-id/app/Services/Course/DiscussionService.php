<?php

namespace App\Services\Course;

use App\Models\CourseDiscussion;
use App\Models\Course;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\RateLimiter;

class DiscussionService
{
    /**
     * Submit a discussion post or reply.
     * Incorporates rate limiting to prevent spam.
     *
     * @param User $user
     * @param int $kelasId
     * @param string $konten
     * @param int|null $parentId
     * @return DiskusiKelas
     * @throws Exception
     */
    public function submitDiscussion(User $user, int $kelasId, string $konten, ?int $parentId = null): DiskusiKelas
    {
        // 1. Rate Limiting: Max 5 posts per minute per user
        $rateLimiterKey = 'discussion-post:' . $user->user_id;
        
        if (RateLimiter::tooManyAttempts($rateLimiterKey, 5)) {
            $seconds = RateLimiter::availableIn($rateLimiterKey);
            throw new Exception("Terlalu banyak permintaan. Silakan coba lagi dalam {$seconds} detik.", 429);
        }

        RateLimiter::hit($rateLimiterKey, 60); // Decay 60 seconds

        // 2. Validate Enrollment (Optional but recommended)
        $kelas = Course::find($kelasId);
        $isEnrolled = $user->kelasIkuti()->where('kelas_peserta.kelas_id', $kelasId)->exists();
        $isOwner = $kelas && $kelas->pengajar_id == $user->user_id;

        if (!$isEnrolled && !$isOwner) {
            throw new Exception("Anda tidak memiliki akses untuk berdiskusi di kelas ini.", 403);
        }

        // 3. Validate Parent ID if exists
        if ($parentId) {
            $parent = DiskusiCourse::find($parentId);
            if (!$parent || $parent->kelas_id != $kelasId) {
                throw new Exception("Diskusi induk tidak valid.", 422);
            }
        }

        // 4. Create Discussion
        return DiskusiCourse::create([
            'user_id' => $user->user_id,
            'kelas_id' => $kelasId,
            'parent_id' => $parentId,
            'konten' => $konten
        ]);
    }

    /**
     * Get paginated discussions for a class (top-level only, with replies eager loaded).
     *
     * @param int $kelasId
     * @param int $perPage
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getClassDiscussions(int $kelasId, int $perPage = 10)
    {
        return DiskusiCourse::with(['user', 'replies.user'])
            ->where('kelas_id', $kelasId)
            ->whereNull('parent_id')
            ->latest()
            ->paginate($perPage);
    }
}


