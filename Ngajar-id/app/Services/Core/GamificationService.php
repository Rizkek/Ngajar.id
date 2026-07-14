<?php

namespace App\Services\Core;

use App\Models\User;
use Illuminate\Support\Facades\DB;

class GamificationService
{
    /**
     * Award XP to user and check for level up
     */
    public function awardXp(User $user, int $amount, string $reason = 'general'): array
    {
        $oldLevel = $user->level ?? 1;
        $oldXp = $user->xp ?? 0;

        // Award XP
        $newXp = $oldXp + $amount;
        $newLevel = $this->calculateLevel($newXp);

        // Update user
        $user->update([
            'xp' => $newXp,
            'level' => $newLevel,
        ]);

        // Log achievement if level up
        if ($newLevel > $oldLevel) {
            $this->logLevelUp($user, $oldLevel, $newLevel);
        }

        return [
            'xp_gained' => $amount,
            'total_xp' => $newXp,
            'old_level' => $oldLevel,
            'new_level' => $newLevel,
            'level_up' => $newLevel > $oldLevel,
        ];
    }

    /**
     * Calculate level from total XP
     */
    public function calculateLevel(int $totalXp): int
    {
        // Level formula: level = floor(sqrt(xp / 1000))
        return (int) floor(sqrt($totalXp / 1000)) + 1;
    }

    /**
     * Get XP needed for next level
     */
    public function getXpForNextLevel(User $user): int
    {
        $currentLevel = $user->level ?? 1;
        $nextLevel = $currentLevel + 1;
        return $nextLevel * $nextLevel * 1000;
    }

    /**
     * Get progress to next level (0-100)
     */
    public function getProgressToNextLevel(User $user): int
    {
        $currentXp = $user->xp ?? 0;
        $currentLevelXp = ($user->level ?? 1) * ($user->level ?? 1) * 1000;
        $nextLevelXp = ($user->level ?? 2) * ($user->level ?? 2) * 1000;

        $progressXp = $currentXp - $currentLevelXp;
        $requiredXp = $nextLevelXp - $currentLevelXp;

        return $requiredXp > 0 ? (int) round(($progressXp / $requiredXp) * 100) : 100;
    }

    /**
     * Log level up achievement
     */
    private function logLevelUp(User $user, int $oldLevel, int $newLevel): void
    {
        DB::table('achievement_logs')->insert([
            'user_id' => $user->user_id,
            'achievement' => 'level_up',
            'level' => $newLevel,
            'data' => json_encode([
                'old_level' => $oldLevel,
                'new_level' => $newLevel,
                'timestamp' => now(),
            ]),
            'created_at' => now(),
        ]);
    }

    /**
     * Get user achievements
     */
    public function getUserAchievements(User $user): array
    {
        return [
            'level' => $user->level ?? 1,
            'xp' => $user->xp ?? 0,
            'progress_to_next_level' => $this->getProgressToNextLevel($user),
            'xp_for_next_level' => $this->getXpForNextLevel($user),
            'badges' => $user->achievements ?? [],
        ];
    }

    /**
     * Get gamification stats for a teacher
     */
    public function getTeacherGamificationStats(User $teacher, array $stats): array
    {
        $poin = ($stats['total_kelas'] * 50) + ($stats['total_materi'] * 10) + ($stats['total_siswa'] * 2);

        if ($poin >= 1000) {
            $level = 'Legenda Ngajar.ID';
            $badgeColor = 'purple';
        } elseif ($poin >= 500) {
            $level = 'Pahlawan Pendidikan';
            $badgeColor = 'amber';
        } elseif ($poin >= 100) {
            $level = 'Relawan Bersemi';
            $badgeColor = 'teal';
        } else {
            $level = 'Relawan Tunas';
            $badgeColor = 'slate';
        }

        return [
            'poin' => $poin,
            'level' => $level,
            'badge_color' => $badgeColor,
            'next_target' => $poin < 100 ? 100 : ($poin < 500 ? 500 : ($poin < 1000 ? 1000 : $poin * 2)),
            'points_needed' => ($poin < 100 ? 100 : ($poin < 500 ? 500 : ($poin < 1000 ? 1000 : $poin * 2))) - $poin
        ];
    }

    /**
     * Get the leaderboard data
     */
    public function getLeaderboard(User $currentUser, int $currentUserPoin)
    {
        return collect([
            ['name' => 'Budi Santoso', 'poin' => 1250, 'avatar' => 'https://ui-avatars.com/api/?name=Budi+Santoso&background=random'],
            ['name' => 'Siti Aminah', 'poin' => 980, 'avatar' => 'https://ui-avatars.com/api/?name=Siti+Aminah&background=random'],
            ['name' => 'Rizky Fadillah', 'poin' => 850, 'avatar' => 'https://ui-avatars.com/api/?name=Rizky+Fadillah&background=random'],
            ['name' => $currentUser->name, 'poin' => $currentUserPoin, 'avatar' => 'https://ui-avatars.com/api/?name=' . urlencode($currentUser->name) . '&background=random']
        ])->sortByDesc('poin')->values();
    }
}
