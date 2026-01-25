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
        // Validasi akses user ke kelas ini
        $user = Auth::user();
        if (!$user->kelasIkuti()->where('kelas_peserta.kelas_id', $kelas_id)->exists()) {
            return redirect()->route('murid.kelas')->with('error', 'Anda belum terdaftar di kelas ini.');
        }

        $kelas = Kelas::findOrFail($kelas_id);

        // Ambil semua materi di kelas ini untuk navigasi
        $materiList = Materi::where('kelas_id', $kelas_id)->orderBy('created_at', 'asc')->get();

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

        // Cari materi berikutnya dan sebelumnya
        $currentIndex = $materiList->search(function ($item) use ($activeMateri) {
            return $item->materi_id === $activeMateri->materi_id;
        });

        $prevMateri = $currentIndex > 0 ? $materiList[$currentIndex - 1] : null;
        $nextMateri = $currentIndex < $materiList->count() - 1 ? $materiList[$currentIndex + 1] : null;

        // Hitung progress (sederhana: index / total)
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
