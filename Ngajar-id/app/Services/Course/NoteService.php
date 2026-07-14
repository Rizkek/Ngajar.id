<?php

namespace App\Services\Course;

use App\Models\UserNote;
use App\Models\User;
use App\Models\Lesson;
use Exception;

class NoteService
{
    /**
     * Save a note for a specific material.
     *
     * @param User $user
     * @param int $materiId
     * @param string|null $catatan
     * @return CatatanUser
     * @throws Exception
     */
    public function saveNote(User $user, int $materiId, ?string $catatan): CatatanUser
    {
        $materi = Lesson::findOrFail($materiId);
        $kelasId = $materi->kelas_id;

        if (!$user->kelasIkuti()->where('kelas_peserta.kelas_id', $kelasId)->exists()) {
            throw new Exception('Anda belum terdaftar di kelas ini.', 403);
        }

        return UserNote::updateOrCreate(
            [
                'user_id' => $user->user_id,
                'kelas_id' => $kelasId,
                'materi_id' => $materiId
            ],
            ['catatan' => $catatan]
        );
    }

    /**
     * Get a specific note for a material.
     *
     * @param User $user
     * @param int $materiId
     * @return CatatanUser|null
     */
    public function getMaterialNote(User $user, int $materiId): ?CatatanUser
    {
        return UserNote::where('user_id', $user->user_id)
            ->where('materi_id', $materiId)
            ->first();
    }

    /**
     * Get all notes for a user across all materials.
     *
     * @param User $user
     * @param int $perPage
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getAllUserNotes(User $user, int $perPage = 15)
    {
        return UserNote::with(['materi:materi_id,judul', 'kelas:kelas_id,judul'])
            ->where('user_id', $user->user_id)
            ->whereNotNull('catatan')
            ->where('catatan', '!=', '')
            ->orderByDesc('updated_at')
            ->paginate($perPage);
    }
}


