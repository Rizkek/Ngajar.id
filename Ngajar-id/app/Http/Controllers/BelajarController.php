<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Models\Materi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BelajarController extends Controller
{
    public function show($kelas_id, $materi_id = null)
    {
        // 1. Validasi Akses: Apakah user terdaftar di kelas ini?
        $user = Auth::user();
        if (!$user->kelasIkuti()->where('kelas_peserta.kelas_id', $kelas_id)->exists()) {
            return redirect()->route('murid.kelas')->with('error', 'Anda belum terdaftar di kelas ini.');
        }

        $kelas = Kelas::findOrFail($kelas_id);

        // 2. Ambil Semua Materi di Kelas ini (untuk navigasi)
        $materiList = Materi::where('kelas_id', $kelas_id)->orderBy('created_at', 'asc')->get();

        if ($materiList->isEmpty()) {
            return redirect()->back()->with('error', 'Kelas ini belum memiliki materi.');
        }

        // 3. Tentukan materi aktif
        if ($materi_id) {
            $activeMateri = $materiList->where('materi_id', $materi_id)->first();
            if (!$activeMateri) {
                return abort(404, 'Materi tidak ditemukan');
            }
        } else {
            // Jika tidak ada ID, buka materi pertama
            $activeMateri = $materiList->first();
        }

        // 4. Cari Next & Prev Materi
        $currentIndex = $materiList->search(function ($item) use ($activeMateri) {
            return $item->materi_id === $activeMateri->materi_id;
        });

        $prevMateri = $currentIndex > 0 ? $materiList[$currentIndex - 1] : null;
        $nextMateri = $currentIndex < $materiList->count() - 1 ? $materiList[$currentIndex + 1] : null;

        // 5. Hitung Progress (Sederhana: index / total)
        // Nanti bisa dikembangkan dengan table 'user_materi_progress'
        $progress = round((($currentIndex + 1) / $materiList->count()) * 100);

        return view('murid.belajar.show', compact(
            'kelas',
            'materiList',
            'activeMateri',
            'prevMateri',
            'nextMateri',
            'progress'
        ));
    }
}
