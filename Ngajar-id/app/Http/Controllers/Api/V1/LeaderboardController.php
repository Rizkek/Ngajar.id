<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;

class LeaderboardController extends Controller
{
    use ApiResponse;

    /**
     * GET /api/v1/leaderboard/global
     * Get global leaderboard rankings
     */
    public function global(Request $request)
    {
        try {
            $page = $request->input('page', 1);
            $perPage = $request->input('per_page', 20);
            $period = $request->input('period', 'all'); // all, month, week

            $query = DB::table('leaderboards')
                ->join('users', 'leaderboards.user_id', '=', 'users.user_id')
                ->select(
                    'leaderboards.user_id',
                    'users.name',
                    'users.avatar',
                    'leaderboards.xp',
                    'leaderboards.level',
                    'leaderboards.courses_completed',
                    'leaderboards.total_points'
                )
                ->orderBy('leaderboards.xp', 'desc');

            // Filter by period (if needed with historical data)
            // For now, just all-time

            $total = $query->count();
            $leaderboard = $query->paginate($perPage, ['*'], 'page', $page);

            // Add rank to each user
            $items = collect($leaderboard->items())->map(function ($item, $index) {
                $item->rank = (($leaderboard->current_page() - 1) * $leaderboard->per_page()) + $index + 1;
                return $item;
            });

            // Get user's rank
            $userRank = null;
            if (auth()->check()) {
                $userRank = DB::table('leaderboards')
                    ->where('xp', '>', DB::table('leaderboards')->where('user_id', auth()->id())->value('xp') ?? 0)
                    ->count() + 1;
            }

            return $this->successWithPagination(
                $items->toArray(),
                'Global leaderboard retrieved',
                $total,
                $perPage,
                $page,
                ['user_rank' => $userRank, 'period' => $period]
            );

        } catch (\Exception $e) {
            return $this->error('Failed to retrieve leaderboard: ' . $e->getMessage(), 400);
        }
    }

    /**
     * GET /api/v1/leaderboard/friends
     * Get friends leaderboard
     */
    public function friends(Request $request)
    {
        try {
            $userId = auth()->id();
            $perPage = $request->input('per_page', 10);

            // For now, get top users follow same courses
            $leaderboard = DB::table('leaderboards')
                ->join('users', 'leaderboards.user_id', '=', 'users.user_id')
                ->select(
                    'leaderboards.user_id',
                    'users.name',
                    'users.avatar',
                    'leaderboards.xp',
                    'leaderboards.level',
                    'leaderboards.courses_completed'
                )
                ->orderBy('leaderboards.xp', 'desc')
                ->limit($perPage)
                ->get();

            $leaderboard = $leaderboard->map(function ($item, $index) {
                $item->rank = $index + 1;
                return $item;
            });

            return $this->success($leaderboard, 'Friends leaderboard retrieved');

        } catch (\Exception $e) {
            return $this->error('Failed to retrieve friends leaderboard: ' . $e->getMessage(), 400);
        }
    }

    /**
     * GET /api/v1/leaderboard/user/{id}
     * Get user ranking info
     */
    public function getUserRank($userId, Request $request)
    {
        try {
            $user = DB::table('leaderboards')
                ->join('users', 'leaderboards.user_id', '=', 'users.user_id')
                ->where('leaderboards.user_id', $userId)
                ->select(
                    'leaderboards.user_id',
                    'users.name',
                    'users.avatar',
                    'leaderboards.xp',
                    'leaderboards.level',
                    'leaderboards.courses_completed',
                    'leaderboards.total_points'
                )
                ->first();

            if (!$user) {
                return $this->error('User ranking not found', 404);
            }

            // Calculate rank
            $rank = DB::table('leaderboards')
                ->where('xp', '>', $user->xp)
                ->count() + 1;

            // Get achievements
            $achievements = DB::table('user_achievements')
                ->join('achievements', 'user_achievements.achievement_id', '=', 'achievements.id')
                ->where('user_achievements.user_id', $userId)
                ->select('achievements.name', 'achievements.slug', 'achievements.icon_url', 'user_achievements.earned_at')
                ->get();

            $user->rank = $rank;
            $user->achievements = $achievements;

            return $this->success($user, 'User ranking retrieved');

        } catch (\Exception $e) {
            return $this->error('Failed to retrieve user ranking: ' . $e->getMessage(), 400);
        }
    }

    /**
     * GET /api/v1/leaderboard/my-rank
     * Get current user's rank and stats
     */
    public function myRank(Request $request)
    {
        try {
            $userId = auth()->id();

            $userStats = DB::table('leaderboards')
                ->join('users', 'leaderboards.user_id', '=', 'users.user_id')
                ->where('leaderboards.user_id', $userId)
                ->select(
                    'leaderboards.user_id',
                    'users.name',
                    'users.avatar',
                    'leaderboards.xp',
                    'leaderboards.level',
                    'leaderboards.courses_completed',
                    'leaderboards.total_points'
                )
                ->first();

            if (!$userStats) {
                return $this->error('Your ranking not found', 404);
            }

            // Calculate rank
            $rank = DB::table('leaderboards')
                ->where('xp', '>', $userStats->xp)
                ->count() + 1;

            // Get nearby competitors
            $nearby = DB::table('leaderboards')
                ->join('users', 'leaderboards.user_id', '=', 'users.user_id')
                ->select(
                    'leaderboards.user_id',
                    'users.name',
                    'leaderboards.xp',
                    'leaderboards.level'
                )
                ->orderBy('leaderboards.xp', 'desc')
                ->limit(10)
                ->get()
                ->map(function ($item, $index) {
                    $item->rank = $index + 1;
                    return $item;
                });

            // Get achievements
            $achievements = DB::table('user_achievements')
                ->join('achievements', 'user_achievements.achievement_id', '=', 'achievements.id')
                ->where('user_achievements.user_id', $userId)
                ->select('achievements.name', 'achievements.slug', 'achievements.icon_url', 'achievements.points', 'user_achievements.earned_at')
                ->get();

            $userStats->rank = $rank;
            $userStats->achievements = $achievements;
            $userStats->achievement_count = count($achievements);
            $userStats->top_competitors = $nearby;

            return $this->success($userStats, 'Your ranking retrieved');

        } catch (\Exception $e) {
            return $this->error('Failed to retrieve your ranking: ' . $e->getMessage(), 400);
        }
    }

    /**
     * GET /api/v1/achievements
     * Get all available achievements
     */
    public function achievements(Request $request)
    {
        try {
            $achievements = DB::table('achievements')
                ->orderBy('points', 'desc')
                ->paginate($request->input('per_page', 20));

            return $this->successWithPagination(
                $achievements->items(),
                'Achievements retrieved',
                $achievements->total(),
                $achievements->per_page(),
                $achievements->current_page()
            );

        } catch (\Exception $e) {
            return $this->error('Failed to retrieve achievements: ' . $e->getMessage(), 400);
        }
    }

    /**
     * GET /api/v1/achievements/my
     * Get user's achievements
     */
    public function myAchievements(Request $request)
    {
        try {
            $userId = auth()->id();

            $earned = DB::table('user_achievements')
                ->join('achievements', 'user_achievements.achievement_id', '=', 'achievements.id')
                ->where('user_achievements.user_id', $userId)
                ->select(
                    'achievements.id',
                    'achievements.name',
                    'achievements.slug',
                    'achievements.description',
                    'achievements.icon_url',
                    'achievements.points',
                    'user_achievements.earned_at'
                )
                ->orderBy('user_achievements.earned_at', 'desc')
                ->get();

            // Get all achievements to see which are missing
            $all = DB::table('achievements')->pluck('id')->toArray();
            $earnedIds = $earned->pluck('id')->toArray();
            $missing = array_diff($all, $earnedIds);

            $locked = DB::table('achievements')
                ->whereIn('id', $missing)
                ->select('id', 'name', 'slug', 'description', 'icon_url', 'points')
                ->get();

            return $this->success([
                'earned' => $earned,
                'locked' => $locked,
                'progress' => [
                    'earned_count' => count($earned),
                    'total_count' => count($all),
                    'percentage' => count($all) > 0 ? round((count($earned) / count($all)) * 100) : 0,
                ]
            ], 'Your achievements retrieved');

        } catch (\Exception $e) {
            return $this->error('Failed to retrieve achievements: ' . $e->getMessage(), 400);
        }
    }

    /**
     * GET /api/v1/leaderboard/stats
     * Get leaderboard statistics
     */
    public function stats(Request $request)
    {
        try {
            $totalUsers = DB::table('leaderboards')->count();
            $totalXpDistributed = DB::table('leaderboards')->sum('xp');
            $avgXpPerUser = $totalUsers > 0 ? round($totalXpDistributed / $totalUsers) : 0;
            $avgLevel = DB::table('leaderboards')->avg('level');

            $topLevel = DB::table('leaderboards')
                ->join('users', 'leaderboards.user_id', '=', 'users.user_id')
                ->select('users.name', 'leaderboards.level', 'leaderboards.xp')
                ->orderBy('leaderboards.level', 'desc')
                ->first();

            $levelDistribution = DB::table('leaderboards')
                ->selectRaw('level, COUNT(*) as count')
                ->groupBy('level')
                ->orderBy('level', 'asc')
                ->get();

            return $this->success([
                'total_users' => $totalUsers,
                'total_xp_distributed' => $totalXpDistributed,
                'avg_xp_per_user' => $avgXpPerUser,
                'avg_level' => round($avgLevel, 2),
                'top_level_user' => $topLevel,
                'level_distribution' => $levelDistribution,
            ], 'Leaderboard statistics retrieved');

        } catch (\Exception $e) {
            return $this->error('Failed to retrieve statistics: ' . $e->getMessage(), 400);
        }
    }
}
