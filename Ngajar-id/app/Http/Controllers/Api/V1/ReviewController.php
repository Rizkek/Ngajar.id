<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Kelas;
use App\Http\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ReviewController extends Controller
{
    use ApiResponse;

    /**
     * GET /api/v1/courses/{id}/reviews
     * Get course reviews with ratings
     */
    public function index($kelasId, Request $request)
    {
        try {
            $kelas = Kelas::findOrFail($kelasId);

            $reviews = DB::table('ulasans')
                ->join('users', 'ulasans.user_id', '=', 'users.user_id')
                ->where('kelas_id', $kelasId)
                ->select(
                    'ulasans.ulasan_id',
                    'ulasans.user_id',
                    'users.name',
                    'users.avatar',
                    'ulasans.rating',
                    'ulasans.komentar',
                    'ulasans.helpful_count',
                    'ulasans.is_verified_purchase',
                    'ulasans.created_at'
                )
                ->orderBy('ulasans.created_at', 'desc')
                ->paginate($request->input('per_page', 10));

            // Get rating statistics
            $stats = DB::table('ulasans')
                ->where('kelas_id', $kelasId)
                ->selectRaw('AVG(rating) as avg_rating, COUNT(*) as total_reviews')
                ->selectRaw('SUM(CASE WHEN rating = 5 THEN 1 ELSE 0 END) as five_star')
                ->selectRaw('SUM(CASE WHEN rating = 4 THEN 1 ELSE 0 END) as four_star')
                ->selectRaw('SUM(CASE WHEN rating = 3 THEN 1 ELSE 0 END) as three_star')
                ->selectRaw('SUM(CASE WHEN rating = 2 THEN 1 ELSE 0 END) as two_star')
                ->selectRaw('SUM(CASE WHEN rating = 1 THEN 1 ELSE 0 END) as one_star')
                ->first();

            if ($request->expectsJson()) {
                return $this->successWithPagination(
                    $reviews->items(),
                    'Course reviews retrieved',
                    $reviews->total(),
                    $reviews->per_page(),
                    $reviews->current_page(),
                    (array)$stats
                );
            }

            return view('reviews.index', compact('kelas', 'reviews', 'stats'));

        } catch (\Exception $e) {
            return $this->error('Failed to retrieve reviews: ' . $e->getMessage(), 400);
        }
    }

    /**
     * POST /api/v1/courses/{id}/reviews
     * Create course review
     */
    public function store($kelasId, Request $request)
    {
        try {
            $kelas = Kelas::findOrFail($kelasId);

            // Check enrollment
            $isEnrolled = DB::table('kelas_peserta')
                ->where('kelas_id', $kelasId)
                ->where('siswa_id', auth()->id())
                ->exists();

            if (!$isEnrolled) {
                return $this->error('You must be enrolled in this course to leave a review', 403);
            }

            $validator = Validator::make($request->all(), [
                'rating' => 'required|integer|min:1|max:5',
                'komentar' => 'required|string|min:10|max:1000',
            ]);

            if ($validator->fails()) {
                return $this->error('Validation failed: ' . implode(', ', $validator->errors()->all()), 400);
            }

            // Check if already reviewed
            $existing = DB::table('ulasans')
                ->where('user_id', auth()->id())
                ->where('kelas_id', $kelasId)
                ->first();

            if ($existing) {
                return $this->error('You already reviewed this course', 400);
            }

            $review = DB::table('ulasans')->insertGetId([
                'user_id' => auth()->id(),
                'kelas_id' => $kelasId,
                'rating' => $request->rating,
                'komentar' => $request->komentar,
                'is_verified_purchase' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Award XP for review
            auth()->user()->update(['xp' => (auth()->user()->xp ?? 0) + 50]);

            // Notify course teacher
            $this->notifyTeacher($kelas, "New review on '{$kelas->judul}'", "Student gave {$request->rating}/5 stars");

            if ($request->expectsJson()) {
                return $this->success(['ulasan_id' => $review, 'xp_earned' => 50], 'Review posted successfully', 201);
            }

            return redirect()->back()->with('success', 'Review posted successfully');

        } catch (\Exception $e) {
            return $this->error('Failed to post review: ' . $e->getMessage(), 400);
        }
    }

    /**
     * PUT /api/v1/reviews/{id}
     * Update review
     */
    public function update($reviewId, Request $request)
    {
        try {
            $review = DB::table('ulasans')->where('ulasan_id', $reviewId)->first();

            if (!$review) {
                return $this->error('Review not found', 404);
            }

            if ($review->user_id !== auth()->id()) {
                return $this->error('Not authorized to edit this review', 403);
            }

            $validator = Validator::make($request->all(), [
                'rating' => 'sometimes|integer|min:1|max:5',
                'komentar' => 'sometimes|string|min:10|max:1000',
            ]);

            if ($validator->fails()) {
                return $this->error('Validation failed: ' . implode(', ', $validator->errors()->all()), 400);
            }

            DB::table('ulasans')
                ->where('ulasan_id', $reviewId)
                ->update([
                    'rating' => $request->input('rating', $review->rating),
                    'komentar' => $request->input('komentar', $review->komentar),
                    'updated_at' => now(),
                ]);

            if ($request->expectsJson()) {
                return $this->success([], 'Review updated successfully');
            }

            return redirect()->back()->with('success', 'Review updated successfully');

        } catch (\Exception $e) {
            return $this->error('Failed to update review: ' . $e->getMessage(), 400);
        }
    }

    /**
     * DELETE /api/v1/reviews/{id}
     * Delete review
     */
    public function destroy($reviewId, Request $request)
    {
        try {
            $review = DB::table('ulasans')->where('ulasan_id', $reviewId)->first();

            if (!$review) {
                return $this->error('Review not found', 404);
            }

            if ($review->user_id !== auth()->id()) {
                return $this->error('Not authorized to delete this review', 403);
            }

            DB::table('ulasans')->where('ulasan_id', $reviewId)->delete();

            if ($request->expectsJson()) {
                return $this->success([], 'Review deleted successfully');
            }

            return redirect()->back()->with('success', 'Review deleted successfully');

        } catch (\Exception $e) {
            return $this->error('Failed to delete review: ' . $e->getMessage(), 400);
        }
    }

    /**
     * POST /api/v1/reviews/{id}/helpful
     * Mark review as helpful
     */
    public function markHelpful($reviewId, Request $request)
    {
        try {
            $review = DB::table('ulasans')->where('ulasan_id', $reviewId)->first();

            if (!$review) {
                return $this->error('Review not found', 404);
            }

            DB::table('ulasans')
                ->where('ulasan_id', $reviewId)
                ->increment('helpful_count');

            if ($request->expectsJson()) {
                return $this->success(['helpful_count' => $review->helpful_count + 1], 'Marked as helpful');
            }

            return redirect()->back();

        } catch (\Exception $e) {
            return $this->error('Failed to mark helpful: ' . $e->getMessage(), 400);
        }
    }

    /**
     * POST /api/v1/materials/{id}/feedback
     * Add material feedback
     */
    public function addMaterialFeedback($materiId, Request $request)
    {
        try {
            $materi = \App\Models\Materi::findOrFail($materiId);

            // Check if student completed material
            $isCompleted = DB::table('material_progress')
                ->where('student_id', auth()->id())
                ->where('materi_id', $materiId)
                ->where('is_completed', true)
                ->exists();

            if (!$isCompleted) {
                return $this->error('You must complete this material to submit feedback', 403);
            }

            $validator = Validator::make($request->all(), [
                'rating' => 'required|integer|min:1|max:5',
                'feedback' => 'sometimes|string|max:500',
                'is_helpful' => 'sometimes|boolean',
            ]);

            if ($validator->fails()) {
                return $this->error('Validation failed', 400);
            }

            DB::table('materi_feedback')->updateOrCreate(
                [
                    'user_id' => auth()->id(),
                    'materi_id' => $materiId,
                ],
                [
                    'rating' => $request->rating,
                    'feedback' => $request->input('feedback'),
                    'is_helpful' => $request->input('is_helpful'),
                    'updated_at' => now(),
                ]
            );

            if ($request->expectsJson()) {
                return $this->success([], 'Feedback submitted successfully', 201);
            }

            return redirect()->back()->with('success', 'Feedback submitted');

        } catch (\Exception $e) {
            return $this->error('Failed to submit feedback: ' . $e->getMessage(), 400);
        }
    }

    /**
     * Helper to notify teacher
     */
    private function notifyTeacher($kelas, $title, $message)
    {
        try {
            DB::table('notifications')->insert([
                'user_id' => $kelas->pengajar_id,
                'type' => 'course_update',
                'title' => $title,
                'message' => $message,
                'action_url' => "/teacher/kelas/{$kelas->kelas_id}",
                'is_read' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } catch (\Exception $e) {
            \Log::warning('Failed to create notification: ' . $e->getMessage());
        }
    }
}
