<?php

namespace App\Services\Course;

use App\Models\Course;
use App\Models\Review;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;

class ReviewService
{
    /**
     * Submit a review for a class.
     *
     * @param User $user
     * @param int $kelasId
     * @param int $rating
     * @param string|null $review
     * @return Ulasan
     * @throws Exception
     */
    public function submitReview(User $user, int $kelasId, int $rating, ?string $review): Ulasan
    {
        // Optional: check if user is enrolled
        if (!$user->kelasIkuti()->where('kelas_peserta.kelas_id', $kelasId)->exists()) {
            throw new Exception('Anda belum terdaftar di kelas ini.', 403);
        }

        $ulasan = Review::updateOrCreate(
            ['user_id' => $user->user_id, 'kelas_id' => $kelasId],
            ['rating' => $rating, 'ulasan' => $review]
        );

        // Update average rating on the class
        $kelas = Course::find($kelasId);
        if ($kelas) {
            $avgRating = Review::where('kelas_id', $kelasId)->avg('rating');
            $kelas->update(['rating' => $avgRating]);
        }

        return $ulasan;
    }

    /**
     * Get paginated reviews for a class.
     *
     * @param int $kelasId
     * @param int $perPage
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getClassReviews(int $kelasId, int $perPage = 10)
    {
        return Review::with('user:user_id,name,avatar')
            ->where('kelas_id', $kelasId)
            ->orderByDesc('updated_at')
            ->paginate($perPage);
    }
}


