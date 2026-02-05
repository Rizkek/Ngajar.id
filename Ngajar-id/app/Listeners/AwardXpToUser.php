<?php

namespace App\Listeners;

use App\Events\MateriCompleted;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class AwardXpToUser
{
    /**
     * Handle the event.
     */
    public function handle(MateriCompleted $event): void
    {
        $user = $event->user;
        $xpGained = 50; // XP standar per materi

        $user->increment('xp', $xpGained);

        Log::info("User {$user->name} gained {$xpGained} XP from Materi {$event->materi->judul}");

        // Level Up Logic
        // Level 1: 0-100
        // Level 2: 101-300
        // Rumus sederhana: Level = floor(XP / 200) + 1 (Contoh)

        $currentLevel = $user->level;
        $calculatedLevel = floor($user->xp / 500) + 1;

        if ($calculatedLevel > $currentLevel) {
            $user->level = $calculatedLevel;
            $user->save();

            // TODO: Kirim notifikasi 'Level Up!'
            Log::info("User {$user->name} leveled up to {$calculatedLevel}!");

            // Tambah achievement jika level tertentu (contoh: Level 5)
            if ($calculatedLevel == 5) {
                $achievements = $user->achievements ?? [];
                if (!in_array('High Achiever', $achievements)) {
                    $achievements[] = 'High Achiever';
                    $user->achievements = $achievements;
                    $user->save();
                }
            }
        }
    }
}
