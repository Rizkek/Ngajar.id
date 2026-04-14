<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Kelas;
use App\Http\Traits\ApiResponse;
use App\Http\Resources\UserResource;
use App\Http\Resources\KelasResource;
use Illuminate\Http\Request;

class MentorController extends Controller
{
    use ApiResponse;

    /**
     * Display a listing of the mentors (volunteers).
     * Both Web & API support
     */
    public function index(Request $request)
    {
        try {
            $query = User::where('role', 'pengajar')
                ->where('status', 'aktif')
                ->with('kelasAjar:kelas_id,pengajar_id,judul,rating')
                ->withAvg('kelasAjar', 'rating')
                ->withCount('kelasAjar');

            // Search filter
            if ($request->has('search') && $request->search) {
                $search = $request->search;
                $query->where('name', 'ILIKE', "%{$search}%")
                    ->orWhere('bio', 'ILIKE', "%{$search}%");
            }

            // Rating filter
            if ($request->has('rating_min')) {
                $minRating = (float) $request->rating_min;
                $query->havingAvg('kelasAjar.rating', '>=', $minRating);
            }

            $limit = $request->get('limit', 12);
            $mentors = $query->latest()->paginate($limit);

            // Support both web & API
            if ($request->expectsJson()) {
                return $this->successWithPagination(
                    UserResource::collection($mentors),
                    'Mentors retrieved successfully'
                );
            }

            // Web view mapping - with all required fields
            $mentors_mapped = $mentors->through(function ($user) {
                $subjects = $user->kelasAjar->pluck('judul')->implode(', ');
                return [
                    'id' => $user->user_id,
                    'name' => $user->name,
                    'bio' => $user->bio ?? 'Mentor berpengalaman',
                    'role' => 'Relawan Pengajar',
                    'subjects' => !empty($subjects) ? $subjects : 'Berbagai Subjek',
                    'university' => $user->bio ?? 'Universitas',
                    'availability' => 'Flexible',
                    'tags' => ['Berpengalaman', 'Ramah', 'Responsif'],
                    'is_top' => ($user->kelas_ajar_avg_rating ?? 0) >= 4.5,
                    'photo' => $user->avatar_path ? asset('storage/' . $user->avatar_path) : 'https://via.placeholder.com/400',
                    'rating' => number_format($user->kelas_ajar_avg_rating ?? 5.0, 1),
                    'reviews' => $user->kelas_ajar_count ?? 0,
                ];
            });

            return view('mentors', ['mentors' => $mentors_mapped]);

        } catch (\Exception $e) {
            \Log::error('Mentor loading error: ' . $e->getMessage());
            if ($request->expectsJson()) {
                return $this->serverError('Failed to load mentors');
            }
            return redirect()->back()->with('error', 'Failed to load mentors');
        }
    }

    /**
     * Get single mentor details
     * API: GET /api/v1/mentors/{id}
     */
    public function show($id)
    {
        try {
            $mentor = User::where('user_id', $id)
                ->where('role', 'pengajar')
                ->where('status', 'aktif')
                ->firstOrFail();

            return $this->success(
                new UserResource($mentor),
                'Mentor details retrieved successfully'
            );

        } catch (\Exception $e) {
            return $this->notFound('Mentor not found');
        }
    }

    /**
     * Get mentor's classes
     * API: GET /api/v1/mentors/{id}/classes
     */
    public function classes($id)
    {
        try {
            $mentor = User::where('user_id', $id)
                ->where('role', 'pengajar')
                ->where('status', 'aktif')
                ->firstOrFail();

            $classes = $mentor->kelasAjar()
                ->where('status', 'aktif')
                ->paginate(10);

            if ($classes->isEmpty()) {
                return $this->successWithPagination(
                    collect(),
                    'No active classes from this mentor'
                );
            }

            return $this->successWithPagination(
                KelasResource::collection($classes),
                'Mentor classes retrieved successfully'
            );

        } catch (\Exception $e) {
            return $this->notFound('Mentor not found');
        }
    }

    /**
     * Get mentor's reviews
     * API: GET /api/v1/mentors/{id}/reviews
     */
    public function reviews($id)
    {
        try {
            $mentor = User::where('user_id', $id)
                ->where('role', 'pengajar')
                ->where('status', 'aktif')
                ->firstOrFail();

            // Get reviews for all mentor's classes
            $reviews = $mentor->kelasAjar()
                ->with('ulasan.siswa')
                ->get()
                ->flatMap->ulasan
                ->paginate(10);

            if ($reviews->isEmpty()) {
                return $this->successWithPagination(
                    collect(),
                    'No reviews yet for this mentor'
                );
            }

            return $this->successWithPagination(
                $reviews->map(function ($review) {
                    return [
                        'id' => $review->id,
                        'rating' => $review->rating,
                        'comment' => $review->komentar,
                        'author' => $review->siswa->nama ?? 'Anonymous',
                        'created_at' => $review->created_at?->toIso8601String(),
                    ];
                }),
                'Mentor reviews retrieved successfully'
            );

        } catch (\Exception $e) {
            return $this->notFound('Mentor not found');
        }
    }
}
