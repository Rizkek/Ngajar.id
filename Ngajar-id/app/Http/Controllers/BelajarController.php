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
        $kelas = Kelas::findOrFail($kelas_id);

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
        // Nanti bisa dikembangkan dengan table 'user_materi_progress'
        $progress = round((($currentIndex + 1) / $materiList->count()) * 100);

        // [COMPLEXITY UPGRADE] Background Job & Queue
        // Jika user menyelesaikan kelas (progress 100%), kirim email sertifikat di background.
        // Cek Session biar job tidak didispatch berulang kali saat refresh page.
        if ($progress == 100 && !Session::has("completed_email_sent_{$kelas_id}")) {
            SendCourseCompletionEmail::dispatch($user, $kelas);
            Session::put("completed_email_sent_{$kelas_id}", true);
        }

        return view('murid.belajar.show', compact(
            'kelas',
            'materiList',
            'activeMateri',
            'prevMateri',
            'nextMateri',
            'progress'
        ));
    }

    /**
     * Mark Materi as Complete
     */
    public function complete(Request $request, $materi_id)
    {
        $user = Auth::user();
        $materi = Materi::findOrFail($materi_id);

        // Validasi enrollment
        if (!$user->kelasIkuti()->where('kelas_peserta.kelas_id', $materi->kelas_id)->exists()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Cek apakah sudah pernah completed (biar gak farm XP)
        // Idealnya kita punya table 'user_materi_progress', tapi utk MVP kita pakai Cache/Session atau check logic sederhana
        // Disini kita assume trigger event selalu, tapi Listener yang filter. 
        // ATAU better: pakai Cache key "user_X_completed_materi_Y" selamanya (atau db table)

        $cacheKey = "user_{$user->user_id}_completed_materi_{$materi_id}";

        if (Cache::has($cacheKey)) {
            return response()->json(['message' => 'Already completed', 'xp_gained' => 0]);
        }

        // Tandai completed (Permanent Cache / DB)
        Cache::forever($cacheKey, true);

        // Fire Event Gamification
        \App\Events\MateriCompleted::dispatch($user, $materi);

        return response()->json([
            'message' => 'Completed!',
            'xp_gained' => 50,
            'new_xp' => $user->xp + 50
        ]);
    }
}
