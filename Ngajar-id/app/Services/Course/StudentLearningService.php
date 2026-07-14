<?php

namespace App\Services\Course;

use App\Repositories\Contracts\KelasRepositoryInterface;
use App\Repositories\Contracts\MateriRepositoryInterface;
use Illuminate\Support\Facades\Session;
use App\Jobs\SendCourseCompletionEmail;
use App\Exceptions\NotEnrolledException;
use App\Exceptions\PremiumLockedException;
use Exception;

class StudentLearningService
{
    protected $kelasRepository;
    protected $materiRepository;

    public function __construct(
        KelasRepositoryInterface $kelasRepository,
        MateriRepositoryInterface $materiRepository
    ) {
        $this->kelasRepository = $kelasRepository;
        $this->materiRepository = $materiRepository;
    }

    /**
     * Get all data required for the learning dashboard
     * 
     * @param \App\Models\User $user
     * @param int|string $kelas_id
     * @param int|string|null $materi_id
     * @return array
     * @throws NotEnrolledException
     * @throws PremiumLockedException
     * @throws Exception
     */
    public function getLearningDashboardData($user, $kelas_id, $materi_id = null)
    {
        $kelas = $this->kelasRepository->findByIdWithInstructor($kelas_id);

        $isEnrolled = $user->kelasIkuti()->where('kelas_peserta.kelas_id', $kelas_id)->exists();
        $isOwner = $kelas->pengajar_id == $user->user_id;

        if (!$isEnrolled && !$isOwner) {
            throw new NotEnrolledException('Anda belum terdaftar di kelas ini.');
        }

        $materiList = $this->materiRepository->getByKelasId($kelas_id);

        if ($materiList->isEmpty()) {
            throw new Exception('Kelas ini belum memiliki materi.');
        }

        if ($materi_id) {
            $activeMateri = $materiList->where('materi_id', $materi_id)->first();
            if (!$activeMateri) {
                abort(404, 'Materi tidak ditemukan');
            }
        } else {
            $activeMateri = $materiList->first();
        }

        if (!$activeMateri->isUnlockedBy($user)) {
            throw new PremiumLockedException("Materi '{$activeMateri->judul}' terkunci (Premium). Silakan buka menggunakan Token.");
        }

        $currentIndex = $materiList->search(function ($item) use ($activeMateri) {
            return $item->materi_id === $activeMateri->materi_id;
        });

        $prevMateri = $currentIndex > 0 ? $materiList[$currentIndex - 1] : null;
        $nextMateri = $currentIndex < $materiList->count() - 1 ? $materiList[$currentIndex + 1] : null;

        $progress = round((($currentIndex + 1) / $materiList->count()) * 100);

        if ($progress == 100 && !Session::has("completed_email_sent_{$kelas_id}")) {
            SendCourseCompletionEmail::dispatch($user, $kelas);
            Session::put("completed_email_sent_{$kelas_id}", true);
        }

        $userReview = \App\Models\Review::where('user_id', $user->user_id)
            ->where('kelas_id', $kelas_id)
            ->first();

        $diskusi = \App\Models\CourseDiscussion::with(['user', 'replies.user'])
            ->where('kelas_id', $kelas_id)
            ->whereNull('parent_id')
            ->latest()
            ->paginate(10);

        $catatan = \App\Models\UserNote::where('user_id', $user->user_id)
            ->where('materi_id', $activeMateri->materi_id)
            ->first();

        return compact(
            'kelas',
            'materiList',
            'activeMateri',
            'prevMateri',
            'nextMateri',
            'progress',
            'userReview',
            'diskusi',
            'catatan'
        );
    }
}

