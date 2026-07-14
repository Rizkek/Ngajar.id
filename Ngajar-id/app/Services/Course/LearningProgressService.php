<?php

namespace App\Services\Course;

use App\Models\Course;
use App\Models\Lesson;
use App\Models\User;
use App\Models\TokenLog;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use App\Services\Core\GamificationService;
use App\Events\MateriCompleted;

class LearningProgressService
{
    /**
     * @var GamificationService
     */
    protected $gamificationService;

    public function __construct(GamificationService $gamificationService)
    {
        $this->gamificationService = $gamificationService;
    }

    /**
     * Mark material as complete and award XP
     */
    public function markMaterialAsComplete(User $user, int $materiId): array
    {
        $materi = Lesson::findOrFail($materiId);

        // Verify enrollment
        if (!$user->kelasIkuti()->where('kelas_peserta.kelas_id', $materi->kelas_id)->exists()) {
            throw new Exception('Not enrolled in this class', 403);
        }

        $cacheKey = "user_{$user->user_id}_completed_materi_{$materiId}";

        if (Cache::has($cacheKey)) {
            throw new Exception('Material already completed', 409);
        }

        Cache::forever($cacheKey, true);

        // Dispatch event
        MateriCompleted::dispatch($user, $materi);

        // Gamification
        $xpToAward = 50;
        $gamificationResult = $this->gamificationService->awardXp($user, $xpToAward, 'material_completion');

        return [
            'message' => 'Material completed!',
            'xp_gained' => $xpToAward,
            'new_xp' => $user->fresh()->xp,
            'gamification' => $gamificationResult
        ];
    }

    /**
     * Unlock premium material with tokens
     */
    public function unlockPremiumMaterial(User $user, int $materiId): array
    {
        $materi = Lesson::findOrFail($materiId);

        // Check enrollment
        if (!$user->kelasIkuti()->where('kelas_peserta.kelas_id', $materi->kelas_id)->exists()) {
            throw new Exception('Not enrolled in this class', 403);
        }

        // Check if already unlocked
        if ($materi->isUnlockedBy($user)) {
            throw new Exception('Material already unlocked', 409);
        }

        // Check if premium
        if (!$materi->is_premium) {
            throw new Exception('Material is not premium', 422);
        }

        $harga = $materi->harga_token;
        $userToken = $user->token;

        if (!$userToken || !$userToken->cukup($harga)) {
            throw new Exception("Insufficient token. Required: {$harga}, Balance: " . ($userToken->jumlah ?? 0), 402);
        }

        // Transaction
        DB::transaction(function () use ($user, $materi, $userToken, $harga) {
            $userToken->kurang($harga);

            TokenLog::create([
                'user_id' => $user->user_id,
                'jumlah' => $harga,
                'aksi' => 'kurang',
                'tipe' => 'pembelian_materi',
                'keterangan' => "Membeli materi: {$materi->judul}",
                'tanggal' => now(),
            ]);

            DB::table('materi_akses')->insert([
                'user_id' => $user->user_id,
                'materi_id' => $materi->materi_id,
                'unlocked_at' => now(),
            ]);
        });

        return [
            'material' => $materi,
            'tokens_used' => $harga,
            'new_balance' => $user->fresh()->getSaldoToken()
        ];
    }

    /**
     * Calculate user progress in a class
     */
    public function calculateClassProgress(User $user, int $kelasId): array
    {
        $kelas = Course::with('materi')->findOrFail($kelasId);

        // Check enrollment
        $isEnrolled = $user->kelasIkuti()->where('kelas_peserta.kelas_id', $kelasId)->exists();
        $isOwner = $kelas->pengajar_id == $user->user_id;

        if (!$isEnrolled && !$isOwner) {
            throw new Exception('Not enrolled in this class', 403);
        }

        $materiCount = $kelas->materi->count();
        $completedCount = 0;

        foreach ($kelas->materi as $materi) {
            if (Cache::has("user_{$user->user_id}_completed_materi_{$materi->materi_id}")) {
                $completedCount++;
            }
        }

        $percentage = $materiCount > 0 ? round(($completedCount / $materiCount) * 100) : 0;

        return [
            'class_id' => $kelas->kelas_id,
            'class_name' => $kelas->judul,
            'total_materials' => $materiCount,
            'completed_materials' => $completedCount,
            'progress_percentage' => $percentage
        ];
    }
}


