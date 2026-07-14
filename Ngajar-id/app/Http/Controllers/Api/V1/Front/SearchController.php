<?php

namespace App\Http\Controllers\Api\V1\Front;

use App\Http\Controllers\Controller;

use App\Http\Traits\ApiResponse;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;

class SearchController extends Controller
{
    use ApiResponse;

    /**
     * GET /api/v1/search/courses
     * Search & filter courses with advanced options
     */
    public function courses(Request $request)
    {
        try {
            $query = $request->input('q', '');
            $category = $request->input('category');
            $level = $request->input('level'); // beginner, intermediate, advanced
            $instructor = $request->input('instructor');
            $minPrice = $request->input('min_price', 0);
            $maxPrice = $request->input('max_price', 99999999);
            $sortBy = $request->input('sort_by', 'newest'); // newest, popular, rating, price_low, price_high
            $page = $request->input('page', 1);
            $perPage = $request->input('per_page', 12);

            $courses = DB::table('kelas')
                ->join('users', 'kelas.pengajar_id', '=', 'users.user_id')
                ->leftJoin('ulasans', 'kelas.kelas_id', '=', 'ulasans.kelas_id')
                ->select(
                    'kelas.kelas_id',
                    'kelas.judul',
                    'kelas.deskripsi',
                    'kelas.harga',
                    'kelas.level',
                    'kelas.kategori',
                    'kelas.thumbnail',
                    'kelas.created_at',
                    'users.name as instructor_name',
                    'users.avatar',
                    DB::raw('COUNT(DISTINCT ulasans.ulasan_id) as review_count'),
                    DB::raw('ROUND(AVG(ulasans.rating), 1) as average_rating')
                )
                ->where('kelas.status', 'approved')
                ->whereNull('kelas.deleted_at');

            // Search by keyword
            if ($query) {
                $courses->where(function ($q) use ($query) {
                    $q->where('kelas.judul', 'like', "%{$query}%")
                        ->orWhere('kelas.deskripsi', 'like', "%{$query}%")
                        ->orWhere('users.name', 'like', "%{$query}%");
                });
            }

            // Filter by category
            if ($category) {
                $courses->where('kelas.kategori', $category);
            }

            // Filter by level
            if ($level) {
                $courses->where('kelas.level', $level);
            }

            // Filter by instructor
            if ($instructor) {
                $courses->where('users.user_id', $instructor);
            }

            // Filter by price range
            $courses->whereBetween('kelas.harga', [$minPrice, $maxPrice]);

            // Group for aggregates
            $courses->groupBy('kelas.kelas_id', 'kelas.judul', 'kelas.deskripsi', 'kelas.harga',
                'kelas.level', 'kelas.kategori', 'kelas.thumbnail', 'kelas.created_at',
                'users.name', 'users.avatar', 'users.user_id');

            // Sorting
            switch ($sortBy) {
                case 'popular':
                    $courses->orderBy('review_count', 'desc');
                    break;
                case 'rating':
                    $courses->orderBy('average_rating', 'desc');
                    break;
                case 'price_low':
                    $courses->orderBy('kelas.harga', 'asc');
                    break;
                case 'price_high':
                    $courses->orderBy('kelas.harga', 'desc');
                    break;
                default: // newest
                    $courses->orderBy('kelas.created_at', 'desc');
            }

            $total = $courses->count();
            $courses = $courses->paginate($perPage, ['*'], 'page', $page);

            return $this->successWithPagination(
                $courses->items(),
                'Courses search results',
                $total,
                $perPage,
                $page,
                ['query' => $query, 'sort_by' => $sortBy]
            );

        } catch (\Exception $e) {
            return $this->error('Failed to search courses: ' . $e->getMessage(), 400);
        }
    }

    /**
     * GET /api/v1/search/instructors
     * Search for teachers/instructors
     */
    public function instructors(Request $request)
    {
        try {
            $query = $request->input('q', '');
            $page = $request->input('page', 1);
            $perPage = $request->input('per_page', 10);

            $instructors = DB::table('users')
                ->leftJoin('kelas', 'users.user_id', '=', 'kelas.pengajar_id')
                ->select(
                    'users.user_id',
                    'users.name',
                    'users.avatar',
                    'users.bio',
                    DB::raw('COUNT(DISTINCT kelas.kelas_id) as course_count')
                )
                ->where('users.role', 'pengajar')
                ->where('users.verified_at', '!=', null)
                ->whereNull('users.deleted_at');

            if ($query) {
                $instructors->where(function ($q) use ($query) {
                    $q->where('users.name', 'like', "%{$query}%")
                        ->orWhere('users.bio', 'like', "%{$query}%");
                });
            }

            $instructors->groupBy('users.user_id', 'users.name', 'users.avatar', 'users.bio')
                ->orderBy('course_count', 'desc');

            $total = $instructors->count();
            $instructors = $instructors->paginate($perPage, ['*'], 'page', $page);

            return $this->successWithPagination(
                $instructors->items(),
                'Instructors found',
                $total,
                $perPage,
                $page
            );

        } catch (\Exception $e) {
            return $this->error('Failed to search instructors: ' . $e->getMessage(), 400);
        }
    }

    /**
     * GET /api/v1/search/categories
     * Get all course categories with stats
     */
    public function categories(Request $request)
    {
        try {
            $categories = DB::table('kelas')
                ->select(
                    'kategori',
                    DB::raw('COUNT(*) as course_count'),
                    DB::raw('COUNT(DISTINCT pengajar_id) as instructor_count')
                )
                ->where('status', 'approved')
                ->whereNull('deleted_at')
                ->groupBy('kategori')
                ->orderBy('course_count', 'desc')
                ->get();

            return $this->success($categories, 'Categories retrieved');

        } catch (\Exception $e) {
            return $this->error('Failed to retrieve categories: ' . $e->getMessage(), 400);
        }
    }

    /**
     * GET /api/v1/search/trending
     * Get trending/popular courses
     */
    public function trending(Request $request)
    {
        try {
            $limit = $request->input('limit', 10);

            $trending = DB::table('kelas')
                ->join('users', 'kelas.pengajar_id', '=', 'users.user_id')
                ->select(
                    'kelas.kelas_id',
                    'kelas.judul',
                    'kelas.harga',
                    'kelas.thumbnail',
                    'users.name as instructor_name'
                )
                ->where('kelas.status', 'approved')
                ->whereNull('kelas.deleted_at')
                ->orderBy('kelas.created_at', 'desc')
                ->limit($limit)
                ->get();

            return $this->success($trending, 'Trending courses retrieved');

        } catch (\Exception $e) {
            return $this->error('Failed to retrieve trending courses: ' . $e->getMessage(), 400);
        }
    }

    /**
     * GET /api/v1/search/filters
     * Get available filter options for search
     */
    public function filters(Request $request)
    {
        try {
            $categories = DB::table('kelas')
                ->select('kategori')
                ->distinct()
                ->where('status', 'approved')
                ->whereNull('deleted_at')
                ->pluck('kategori');

            $levels = DB::table('kelas')
                ->select('level')
                ->distinct()
                ->where('status', 'approved')
                ->whereNull('deleted_at')
                ->pluck('level');

            $priceRanges = [
                ['min' => 0, 'max' => 100000, 'label' => 'Gratis - Rp 100K'],
                ['min' => 100000, 'max' => 500000, 'label' => 'Rp 100K - Rp 500K'],
                ['min' => 500000, 'max' => 1000000, 'label' => 'Rp 500K - Rp 1M'],
                ['min' => 1000000, 'max' => 5000000, 'label' => 'Rp 1M - Rp 5M'],
                ['min' => 5000000, 'max' => 999999999, 'label' => 'Rp 5M+'],
            ];

            return $this->success([
                'categories' => $categories,
                'levels' => $levels,
                'price_ranges' => $priceRanges,
                'sort_options' => [
                    'newest' => 'Terbaru',
                    'popular' => 'Paling Populer',
                    'rating' => 'Rating Tertinggi',
                    'price_low' => 'Harga Termurah',
                    'price_high' => 'Harga Termahal',
                ]
            ], 'Filter options retrieved');

        } catch (\Exception $e) {
            return $this->error('Failed to retrieve filters: ' . $e->getMessage(), 400);
        }
    }
}
