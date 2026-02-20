<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Models\Materi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;
use App\Jobs\SendCourseCompletionEmail;

class BelajarController extends Controller
{
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

    /**
     * Simpan Diskusi/Pertanyaan
     */
    public function storeDiskusi(Request $request, $kelas_id)
    {
        $request->validate([
            'konten' => 'required|string|max:1000',
            'parent_id' => 'nullable|exists:diskusi_kelas,id'
        ]);

        \App\Models\DiskusiKelas::create([
            'user_id' => Auth::id(),
            'kelas_id' => $kelas_id,
            'parent_id' => $request->parent_id,
            'konten' => $request->konten
        ]);

        return back()->with('success', 'Diskusi berhasil dikirim.');
    }

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
}

