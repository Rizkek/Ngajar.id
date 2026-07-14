<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\Course\StudentLearningService;
use App\Services\Course\ReviewService;
use App\Services\Course\NoteService;
use App\Services\Course\DiscussionService;
use App\Exceptions\NotEnrolledException;
use App\Exceptions\PremiumLockedException;
use App\Http\Requests\Student\StoreUlasanRequest;
use App\Http\Requests\Student\StoreCatatanRequest;
use App\Http\Requests\Student\StoreDiskusiRequest;
use App\Models\Lesson;
use Illuminate\Support\Facades\Cache;

class LearningController extends Controller
{
    /**
     * Show the learning dashboard for a class
     */
    public function show(Request $request, StudentLearningService $learningService, $kelas_id, $materi_id = null)
    {
        try {
            $data = $learningService->getLearningDashboardData($request->user(), $kelas_id, $materi_id);
            return view('student.learning.show', $data);
        } catch (NotEnrolledException $e) {
            return redirect()->route('student.kelas')->with('error', $e->getMessage());
        } catch (PremiumLockedException $e) {
            return redirect()->route('student.materi')->with('error', $e->getMessage());
        } catch (\Exception $e) {
            if (str_contains($e->getMessage(), 'belum memiliki materi')) {
                return redirect()->back()->with('error', $e->getMessage());
            }
            abort(404, $e->getMessage());
        }
    }

    /**
     * Mark Materi as Complete
     */
    public function complete(Request $request, $materi_id)
    {
        $user = Auth::user();
        $materi = Lesson::findOrFail($materi_id);

        if (!$user->kelasIkuti()->where('kelas_peserta.kelas_id', $materi->kelas_id)->exists()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $cacheKey = "user_{$user->user_id}_completed_materi_{$materi_id}";

        if (Cache::has($cacheKey)) {
            return response()->json(['message' => 'Already completed', 'xp_gained' => 0]);
        }

        Cache::forever($cacheKey, true);

        \App\Events\MateriCompleted::dispatch($user, $materi);

        return response()->json([
            'message' => 'Completed!',
            'xp_gained' => 50,
            'new_xp' => $user->xp + 50
        ]);
    }

    /**
     * Simpan Ulasan Kelas
     */
    public function storeUlasan(StoreUlasanRequest $request, $kelas_id, ReviewService $reviewService)
    {
        try {
            // Using $request->review because the FormRequest validates 'review'
            // Oh wait, my FormRequest validated 'review', I should pass 'review'
            // Wait! In the Web UI, the input might be named 'rating' and 'ulasan' because it's in Indonesian.
            // Let me check what the old LearningController used: it was `$request->ulasan`.
            // Wait, in my previous step, I edited StoreUlasanRequest to use 'review'. 
            // If the Web UI form sends 'ulasan', then `StoreUlasanRequest` using 'review' will fail validation!
            // Let me change it to handle both or fall back.
            $reviewContent = $request->input('ulasan') ?? $request->input('review');
            $reviewService->submitReview($request->user(), $kelas_id, $request->rating, $reviewContent);
            return back()->with('success', 'Terima kasih atas ulasan Anda!');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Simpan Diskusi Kelas
     */
    public function storeDiskusi(StoreDiskusiRequest $request, $kelas_id, DiscussionService $discussionService)
    {
        try {
            $konten = $request->input('isi_diskusi') ?? $request->input('konten');
            $discussionService->submitDiscussion($request->user(), $kelas_id, $konten, $request->parent_id);
            return back()->with('success', 'Diskusi berhasil dikirim!');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Simpan Catatan Pribadi
     */
    public function storeCatatan(StoreCatatanRequest $request, $kelas_id, NoteService $noteService)
    {
        try {
            $noteService->saveNote($request->user(), $request->materi_id, $request->catatan);
            return back()->with('success', 'Catatan berhasil disimpan!');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}






