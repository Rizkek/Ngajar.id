<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Models\Materi;
use App\Http\Traits\ApiResponse;
use App\Http\Resources\MateriResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;
use App\Jobs\SendCourseCompletionEmail;

class BelajarController extends Controller
{
    use ApiResponse;
    public function show($kelas_id, $materi_id = null)
    {
        // Validasi akses user ke kelas ini
        $user = Auth::user();
        $kelas = Kelas::with(['pengajar'])->findOrFail($kelas_id);

        $isEnrolled = $user->kelasIkuti()->where('kelas_peserta.kelas_id', $kelas_id)->exists();
        $isOwner = $kelas->pengajar_id == $user->user_id;

        if (!$isEnrolled && !$isOwner) {
            // Jika belum join, redirect ke halaman katalog/join (nanti kita buat)
            // Untuk sementara redirect ke dashboard dengan error
            return redirect()->route('murid.kelas')->with('error', 'Anda belum terdaftar di kelas ini.');
        }

        // Ambil semua materi di kelas ini untuk navigasi (Cached for 1 hour)
        $materiList = Cache::remember("kelas_materi_{$kelas_id}", 60 * 60, function () use ($kelas_id) {
            return Materi::where('kelas_id', $kelas_id)->orderBy('created_at', 'asc')->get();
        });

        if ($materiList->isEmpty()) {
            return redirect()->back()->with('error', 'Kelas ini belum memiliki materi.');
        }

        // Tentukan materi aktif
        if ($materi_id) {
            $activeMateri = $materiList->where('materi_id', $materi_id)->first();
            if (!$activeMateri) {
                return abort(404, 'Materi tidak ditemukan');
            }
        } else {
            // Jika tidak ada ID, buka materi pertama
            $activeMateri = $materiList->first();
        }

        // --- CEK AKSES PREMIUM ---
        if (!$activeMateri->isUnlockedBy($user)) {
            return redirect()->route('murid.materi')
                ->with('error', "Materi '{$activeMateri->judul}' terkunci (Premium). Silakan buka menggunakan Token.");
        }

        // Cari materi berikutnya dan sebelumnya
        $currentIndex = $materiList->search(function ($item) use ($activeMateri) {
            return $item->materi_id === $activeMateri->materi_id;
        });

        $prevMateri = $currentIndex > 0 ? $materiList[$currentIndex - 1] : null;
        $nextMateri = $currentIndex < $materiList->count() - 1 ? $materiList[$currentIndex + 1] : null;

        // Hitung progress (sederhana: index / total)
        $progress = round((($currentIndex + 1) / $materiList->count()) * 100);

        // [COMPLEXITY UPGRADE] Background Job & Queue
        if ($progress == 100 && !Session::has("completed_email_sent_{$kelas_id}")) {
            SendCourseCompletionEmail::dispatch($user, $kelas);
            Session::put("completed_email_sent_{$kelas_id}", true);
        }

        // --- FETCH FITUR TAMBAHAN (Ulasan, Diskusi, Catatan) ---

        // 1. Data Ulasan (Cek apakah user sudah review)
        $userReview = \App\Models\Ulasan::where('user_id', $user->user_id)
            ->where('kelas_id', $kelas_id)
            ->first();

        // 2. Data Diskusi (Lazy load user)
        $diskusi = \App\Models\DiskusiKelas::with(['user', 'replies.user'])
            ->where('kelas_id', $kelas_id)
            ->whereNull('parent_id')
            ->latest()
            ->paginate(10); // Pagination for comments

        // 3. Catatan Pribadi User untuk materi ini
        $catatan = \App\Models\CatatanUser::where('user_id', $user->user_id)
            ->where('materi_id', $activeMateri->materi_id)
            ->first();

        return view('murid.belajar.show', compact(
            'kelas',
            'materiList',
            'activeMateri',
            'prevMateri',
            'nextMateri',
            'progress',
            'userReview',
            'diskusi',
            'catatan'
        ));
    }

    /**
     * Mark Materi as Complete
     */
    public function complete(Request $request, $materi_id)
    {
        $user = Auth::user();
        $materi = Materi::findOrFail($materi_id);

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

    // --- FITUR BARU ---

    /**
     * Simpan Ulasan Kelas
     */
    public function storeUlasan(Request $request, $kelas_id)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'ulasan' => 'nullable|string|max:500',
        ]);

        $user = Auth::user();

        // Update or Create (karena unique constraint)
        \App\Models\Ulasan::updateOrCreate(
            ['user_id' => $user->user_id, 'kelas_id' => $kelas_id],
            ['rating' => $request->rating, 'ulasan' => $request->ulasan]
        );

        return back()->with('success', 'Terima kasih atas ulasan Anda!');
    }

    // storeDiskusi moved to line 632 to avoid duplication and support JSON responses.

    /**
     * Simpan Catatan Pribadi
     */
    public function storeCatatan(Request $request, $kelas_id)
    {
        $request->validate([
            'materi_id' => 'required|exists:materi,materi_id',
            'catatan' => 'nullable|string|max:5000' // Markdown support planned
        ]);

        \App\Models\CatatanUser::updateOrCreate(
            [
                'user_id' => Auth::id(),
                'kelas_id' => $kelas_id,
                'materi_id' => $request->materi_id
            ],
            ['catatan' => $request->catatan]
        );

        return back()->with('success', 'Catatan berhasil disimpan.');
    }

    // ========== API ENDPOINTS (Phase 3D - Student Learning) ==========

    /**
     * GET /api/v1/student/learning/materials
     * Get all materials for user's enrolled classes
     */
    public function myMaterials(Request $request)
    {
        try {
            $user = $request->user();

            // Get all materials from enrolled classes
            $materials = Materi::whereIn('kelas_id',
                $user->kelasIkuti()->pluck('kelas.kelas_id')
            )
            ->with('kelas:kelas_id,judul')
            ->paginate(20);

            if ($request->expectsJson()) {
                return $this->successWithPagination(
                    MateriResource::collection($materials),
                    'Materials retrieved successfully'
                );
            }

            return view('murid.materi', compact('materials'));

        } catch (\Exception $e) {
            \Log::error('Error in myMaterials: ' . $e->getMessage());

            if ($request->expectsJson()) {
                return $this->serverError($e->getMessage());
            }

            return back()->with('error', 'Failed to load materials');
        }
    }

    /**
     * GET /api/v1/student/learning/materials/{id}
     * Get single material details
     */
    public function getMaterial(Request $request, $id)
    {
        try {
            $user = $request->user();
            $materi = Materi::with('kelas')->findOrFail($id);

            // Check if user is enrolled in the class
            $isEnrolled = $user->kelasIkuti()->where('kelas_peserta.kelas_id', $materi->kelas_id)->exists();
            $isOwner = $materi->kelas->pengajar_id == $user->user_id;

            if (!$isEnrolled && !$isOwner) {
                if ($request->expectsJson()) {
                    return $this->forbidden('Not enrolled in this class');
                }
                return redirect()->route('murid.kelas')->with('error', 'Not enrolled');
            }

            // Check if material is unlocked
            $isUnlocked = $materi->isUnlockedBy($user);

            $data = [
                'material' => MateriResource::make($materi),
                'is_unlocked' => $isUnlocked,
                'is_completed' => Cache::has("user_{$user->user_id}_completed_materi_{$id}")
            ];

            if ($request->expectsJson()) {
                return $this->success($data, 'Material retrieved successfully');
            }

            return view('murid.belajar.show', compact('materi', 'isUnlocked'));

        } catch (\Exception $e) {
            \Log::error('Error in getMaterial: ' . $e->getMessage());

            if ($request->expectsJson()) {
                return $this->serverError($e->getMessage());
            }

            return back()->with('error', 'Material not found');
        }
    }

    /**
     * POST /api/v1/student/learning/materials/{id}/complete
     * Mark material as complete
     */
    public function completeMaterial(Request $request, $id)
    {
        try {
            $user = $request->user();
            $materi = Materi::findOrFail($id);

            // Verify enrollment
            if (!$user->kelasIkuti()->where('kelas_peserta.kelas_id', $materi->kelas_id)->exists()) {
                if ($request->expectsJson()) {
                    return $this->forbidden('Not enrolled in this class');
                }
                return redirect()->route('murid.kelas')->with('error', 'Not enrolled');
            }

            $cacheKey = "user_{$user->user_id}_completed_materi_{$id}";

            if (Cache::has($cacheKey)) {
                if ($request->expectsJson()) {
                    return $this->success(['xp_gained' => 0], 'Material already completed');
                }
                return back()->with('info', 'Already completed');
            }

            Cache::forever($cacheKey, true);

            // Dispatch event
            \App\Events\MateriCompleted::dispatch($user, $materi);

            $data = [
                'message' => 'Material completed!',
                'xp_gained' => 50,
                'new_xp' => $user->xp + 50
            ];

            if ($request->expectsJson()) {
                return $this->success($data, 'Material marked as complete', 201);
            }

            return back()->with('success', 'Material completed!');

        } catch (\Exception $e) {
            \Log::error('Error in completeMaterial: ' . $e->getMessage());

            if ($request->expectsJson()) {
                return $this->serverError($e->getMessage());
            }

            return back()->with('error', 'Failed to mark material complete');
        }
    }

    /**
     * POST /api/v1/student/learning/materials/{id}/unlock
     * Unlock premium material with tokens
     */
    public function unlockMaterial(Request $request, $id)
    {
        try {
            $user = $request->user();
            $materi = Materi::findOrFail($id);

            // Check enrollment
            if (!$user->kelasIkuti()->where('kelas_peserta.kelas_id', $materi->kelas_id)->exists()) {
                if ($request->expectsJson()) {
                    return $this->forbidden('Not enrolled in this class');
                }
                return back()->with('error', 'Not enrolled');
            }

            // Check if already unlocked
            if ($materi->isUnlockedBy($user)) {
                if ($request->expectsJson()) {
                    return $this->validationError(['error' => 'Material already unlocked']);
                }
                return back()->with('info', 'Already unlocked');
            }

            // Check if premium
            if (!$materi->is_premium) {
                if ($request->expectsJson()) {
                    return $this->validationError(['error' => 'Material is not premium']);
                }
                return back()->with('info', 'Material is free');
            }

            // Use DashboardController's beliMateri logic
            $harga = $materi->harga_token;
            $userToken = $user->token;

            if (!$userToken || !$userToken->cukup($harga)) {
                if ($request->expectsJson()) {
                    return $this->validationError(
                        ['error' => "Insufficient token. Required: {$harga}, Balance: " . ($userToken->jumlah ?? 0)]
                    );
                }
                return redirect()->route('topup.create')->with('error', 'Insufficient tokens');
            }

            // Transaction
            \Illuminate\Support\Facades\DB::transaction(function () use ($user, $materi, $userToken, $harga) {
                $userToken->kurang($harga);

                \App\Models\TokenLog::create([
                    'user_id' => $user->user_id,
                    'jumlah' => $harga,
                    'aksi' => 'kurang',
                    'tipe' => 'pembelian_materi',
                    'keterangan' => "Membeli materi: {$materi->judul}",
                    'tanggal' => now(),
                ]);

                \Illuminate\Support\Facades\DB::table('materi_akses')->insert([
                    'user_id' => $user->user_id,
                    'materi_id' => $materi->materi_id,
                    'unlocked_at' => now(),
                ]);
            });

            $data = [
                'material' => MateriResource::make($materi),
                'tokens_used' => $harga,
                'new_balance' => $user->fresh()->getSaldoToken()
            ];

            if ($request->expectsJson()) {
                return $this->success($data, 'Material unlocked successfully', 201);
            }

            return back()->with('success', "Material unlocked! {$harga} tokens used");

        } catch (\Exception $e) {
            \Log::error('Error in unlockMaterial: ' . $e->getMessage());

            if ($request->expectsJson()) {
                return $this->serverError($e->getMessage());
            }

            return back()->with('error', 'Failed to unlock material');
        }
    }

    /**
     * GET /api/v1/student/learning/progress
     * Get overall learning progress
     */
    public function overallProgress(Request $request)
    {
        try {
            $user = $request->user();

            // Get all enrolled classes
            $classes = $user->kelasIkuti()->with('materi')->get();

            $progressData = [];
            $totalCompleted = 0;
            $totalMaterials = 0;

            foreach ($classes as $kelas) {
                $materiCount = $kelas->materi->count();
                $completedCount = 0;

                foreach ($kelas->materi as $materi) {
                    if (Cache::has("user_{$user->user_id}_completed_materi_{$materi->materi_id}")) {
                        $completedCount++;
                    }
                }

                $percentage = $materiCount > 0 ? round(($completedCount / $materiCount) * 100) : 0;

                $progressData[] = [
                    'class_id' => $kelas->kelas_id,
                    'class_name' => $kelas->judul,
                    'total_materials' => $materiCount,
                    'completed_materials' => $completedCount,
                    'progress_percentage' => $percentage
                ];

                $totalCompleted += $completedCount;
                $totalMaterials += $materiCount;
            }

            $overallPercentage = $totalMaterials > 0 ? round(($totalCompleted / $totalMaterials) * 100) : 0;

            $data = [
                'overall_progress' => $overallPercentage,
                'total_materials' => $totalMaterials,
                'completed_materials' => $totalCompleted,
                'classes' => $progressData
            ];

            if ($request->expectsJson()) {
                return $this->success($data, 'Progress retrieved successfully');
            }

            return view('murid.progress', compact('progressData', 'overallPercentage'));

        } catch (\Exception $e) {
            \Log::error('Error in overallProgress: ' . $e->getMessage());

            if ($request->expectsJson()) {
                return $this->serverError($e->getMessage());
            }

            return back()->with('error', 'Failed to load progress');
        }
    }

    /**
     * GET /api/v1/student/classes/{id}/progress
     * Get progress for a specific class
     */
    public function classProgress(Request $request, $id)
    {
        try {
            $user = $request->user();
            $kelas = Kelas::with('materi')->findOrFail($id);

            // Check enrollment
            $isEnrolled = $user->kelasIkuti()->where('kelas_peserta.kelas_id', $id)->exists();
            $isOwner = $kelas->pengajar_id == $user->user_id;

            if (!$isEnrolled && !$isOwner) {
                if ($request->expectsJson()) {
                    return $this->forbidden('Not enrolled in this class');
                }
                return back()->with('error', 'Not enrolled');
            }

            $materiCount = $kelas->materi->count();
            $completedCount = 0;

            foreach ($kelas->materi as $materi) {
                if (Cache::has("user_{$user->user_id}_completed_materi_{$materi->materi_id}")) {
                    $completedCount++;
                }
            }

            $percentage = $materiCount > 0 ? round(($completedCount / $materiCount) * 100) : 0;

            $data = [
                'class_id' => $kelas->kelas_id,
                'class_name' => $kelas->judul,
                'total_materials' => $materiCount,
                'completed_materials' => $completedCount,
                'progress_percentage' => $percentage
            ];

            if ($request->expectsJson()) {
                return $this->success($data, 'Class progress retrieved successfully');
            }

            return view('murid.belajar.progress', compact('kelas', 'percentage', 'completedCount', 'materiCount'));

        } catch (\Exception $e) {
            \Log::error('Error in classProgress: ' . $e->getMessage());

            if ($request->expectsJson()) {
                return $this->serverError($e->getMessage());
            }

            return back()->with('error', 'Failed to load progress');
        }
    }

    /**
     * POST /api/v1/student/reviews/classes/{id}
     * Store class review
     */
    public function storeReview(Request $request, $id)
    {
        try {
            $request->validate([
                'rating' => 'required|integer|min:1|max:5',
                'review' => 'nullable|string|max:500',
            ]);

            $user = $request->user();

            \App\Models\Ulasan::updateOrCreate(
                ['user_id' => $user->user_id, 'kelas_id' => $id],
                ['rating' => $request->rating, 'ulasan' => $request->review]
            );

            if ($request->expectsJson()) {
                return $this->success(
                    ['rating' => $request->rating],
                    'Review submitted successfully',
                    201
                );
            }

            return back()->with('success', 'Thank you for your review!');

        } catch (\Exception $e) {
            \Log::error('Error in storeReview: ' . $e->getMessage());

            if ($request->expectsJson()) {
                return $this->serverError($e->getMessage());
            }

            return back()->with('error', 'Failed to submit review');
        }
    }

    /**
     * GET /api/v1/student/reviews/classes/{id}
     * Get class reviews
     */
    public function classReviews(Request $request, $id)
    {
        try {
            $reviews = \App\Models\Ulasan::where('kelas_id', $id)
                ->with('user:user_id,name,avatar_path')
                ->paginate(10);

            if ($request->expectsJson()) {
                return $this->successWithPagination(
                    $reviews,
                    'Reviews retrieved successfully'
                );
            }

            return view('murid.reviews.index', compact('reviews'));

        } catch (\Exception $e) {
            \Log::error('Error in classReviews: ' . $e->getMessage());

            if ($request->expectsJson()) {
                return $this->serverError($e->getMessage());
            }

            return back()->with('error', 'Failed to load reviews');
        }
    }

    /**
     * POST /api/v1/student/discussions/classes/{id}
     * Store class discussion/question
     */
    public function storeDiskusi(Request $request, $id)
    {
        try {
            $request->validate([
                'konten' => 'required|string|max:1000',
                'parent_id' => 'nullable|exists:diskusi_kelas,id'
            ]);

            \App\Models\DiskusiKelas::create([
                'user_id' => Auth::id(),
                'kelas_id' => $id,
                'parent_id' => $request->parent_id,
                'konten' => $request->konten
            ]);

            if ($request->expectsJson()) {
                return $this->success(
                    ['message' => 'Discussion posted'],
                    'Discussion posted successfully',
                    201
                );
            }

            return back()->with('success', 'Discussion posted!');

        } catch (\Exception $e) {
            \Log::error('Error in storeDiskusi: ' . $e->getMessage());

            if ($request->expectsJson()) {
                return $this->serverError($e->getMessage());
            }

            return back()->with('error', 'Failed to post discussion');
        }
    }

    /**
     * GET /api/v1/student/discussions/classes/{id}
     * Get class discussions
     */
    public function classDiskusi(Request $request, $id)
    {
        try {
            $diskusi = \App\Models\DiskusiKelas::with(['user', 'replies.user'])
                ->where('kelas_id', $id)
                ->whereNull('parent_id')
                ->latest()
                ->paginate(10);

            if ($request->expectsJson()) {
                return $this->successWithPagination(
                    $diskusi,
                    'Discussions retrieved successfully'
                );
            }

            return view('murid.discussions.index', compact('diskusi'));

        } catch (\Exception $e) {
            \Log::error('Error in classDiskusi: ' . $e->getMessage());

            if ($request->expectsJson()) {
                return $this->serverError($e->getMessage());
            }

            return back()->with('error', 'Failed to load discussions');
        }
    }

    /**
     * POST /api/v1/student/notes/materials/{id}
     * Store notes for a material
     */
    public function storeNotesForMaterial(Request $request, $id)
    {
        try {
            $request->validate([
                'catatan' => 'nullable|string|max:5000'
            ]);

            $user = $request->user();
            $materi = Materi::findOrFail($id);

            \App\Models\CatatanUser::updateOrCreate(
                [
                    'user_id' => $user->user_id,
                    'materi_id' => $id
                ],
                ['catatan' => $request->catatan]
            );

            if ($request->expectsJson()) {
                return $this->success(
                    ['noted' => true],
                    'Notes saved successfully',
                    201
                );
            }

            return back()->with('success', 'Notes saved!');

        } catch (\Exception $e) {
            \Log::error('Error in storeNotesForMaterial: ' . $e->getMessage());

            if ($request->expectsJson()) {
                return $this->serverError($e->getMessage());
            }

            return back()->with('error', 'Failed to save notes');
        }
    }

    /**
     * GET /api/v1/student/notes/materials/{id}
     * Get notes for a material
     */
    public function getMaterialNotes(Request $request, $id)
    {
        try {
            $user = $request->user();

            $notes = \App\Models\CatatanUser::where('user_id', $user->user_id)
                ->where('materi_id', $id)
                ->first();

            if (!$notes) {
                if ($request->expectsJson()) {
                    return $this->success(['notes' => null], 'No notes found');
                }
                return back()->with('info', 'No notes');
            }

            if ($request->expectsJson()) {
                return $this->success(['notes' => $notes->catatan], 'Notes retrieved');
            }

            return view('murid.notes.show', compact('notes'));

        } catch (\Exception $e) {
            \Log::error('Error in getMaterialNotes: ' . $e->getMessage());

            if ($request->expectsJson()) {
                return $this->serverError($e->getMessage());
            }

            return back()->with('error', 'Failed to load notes');
        }
    }

    /**
     * GET /api/v1/student/notes
     * Get all user notes
     */
    public function allNotes(Request $request)
    {
        try {
            $user = $request->user();

            $notes = \App\Models\CatatanUser::where('user_id', $user->user_id)
                ->with('materi:materi_id,judul')
                ->paginate(20);

            if ($request->expectsJson()) {
                return $this->successWithPagination(
                    $notes,
                    'All notes retrieved'
                );
            }

            return view('murid.notes.index', compact('notes'));

        } catch (\Exception $e) {
            \Log::error('Error in allNotes: ' . $e->getMessage());

            if ($request->expectsJson()) {
                return $this->serverError($e->getMessage());
            }

            return back()->with('error', 'Failed to load notes');
        }
    }

    /**
     * GET /api/v1/student/certificates
     * Get user certificates
     */
    public function myCertificates(Request $request)
    {
        try {
            $user = $request->user();

            $certificates = $user->learningPathsEnrolled()
                ->wherePivotNotNull('completed_at')
                ->with('creator')
                ->paginate(10);

            if ($request->expectsJson()) {
                return $this->successWithPagination(
                    $certificates,
                    'Certificates retrieved',
                    $certificates
                );
            }

            return view('murid.sertifikat', compact('certificates'));

        } catch (\Exception $e) {
            \Log::error('Error in myCertificates: ' . $e->getMessage());

            if ($request->expectsJson()) {
                return $this->serverError($e->getMessage());
            }

            return back()->with('error', 'Failed to load certificates');
        }
    }

    /**
     * GET /api/v1/student/certificates/{id}
     * Download certificate (API returns URL, web shows view)
     */
    public function downloadCertificate(Request $request, $id)
    {
        try {
            $user = $request->user();

            $path = $user->learningPathsEnrolled()
                ->wherePivot('learning_path_id', $id)
                ->wherePivotNotNull('completed_at')
                ->first();

            if (!$path) {
                if ($request->expectsJson()) {
                    return $this->notFound('Certificate not found or not completed');
                }
                return back()->with('error', 'Certificate not found');
            }

            $data = [
                'certificate_id' => $id,
                'path_name' => $path->judul,
                'completed_at' => $path->pivot->completed_at,
                'certificate_url' => route('certificate.download', $id)
            ];

            if ($request->expectsJson()) {
                return $this->success($data, 'Certificate ready for download');
            }

            return view('murid.sertifikat.download', compact('path'));

        } catch (\Exception $e) {
            \Log::error('Error in downloadCertificate: ' . $e->getMessage());

            if ($request->expectsJson()) {
                return $this->serverError($e->getMessage());
            }

            return back()->with('error', 'Failed to download certificate');
        }
    }

    /**
     * POST /api/v1/student/materials/{id}/buy
     * Alias for unlockMaterial (for backward compatibility)
     */
    public function beliMateri(Request $request, $id)
    {
        return $this->unlockMaterial($request, $id);
    }
}

