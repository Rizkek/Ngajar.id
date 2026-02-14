<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CatalogController extends Controller
{
    /**
     * Tampilkan semua kelas yang tersedia (Katalog).
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $search = $request->query('q');

        // Ambil kelas aktif, exclude yang sudah diikuti user (opsional, tapi bagus buat UX)
        // Atau tampilkan status "Sudah Bergabung"

        $query = Kelas::with(['pengajar'])
            ->where('status', 'aktif')
            ->orderBy('created_at', 'desc');

        if ($search) {
            $query->where('judul', 'ILIKE', "%{$search}%")
                ->orWhere('deskripsi', 'ILIKE', "%{$search}%");
        }

        $allKelas = $query->paginate(9);

        // Map data untuk view
        // Kita perlu tahu user sudah join kelas mana aja
        $user = Auth::user();
        $enrolledKelasIds = $user ? $user->kelasIkuti()->pluck('kelas_peserta.kelas_id')->toArray() : [];

        return view('murid.katalog.index', compact('allKelas', 'enrolledKelasIds'));
    }

    /**
     * Proses gabung kelas (Enrollment).
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function join($id)
    {
        $kelas = Kelas::findOrFail($id);
        $user = Auth::user();

        // Cek apakah sudah terdaftar
        if ($user->kelasIkuti()->where('kelas_peserta.kelas_id', $id)->exists()) {
            return redirect()->route('belajar.show', ['kelas_id' => $id])
                ->with('info', 'Anda sudah terdaftar di kelas ini.');
        }

        // Logic Enrollment (Gratis dulu)
        $user->kelasIkuti()->attach($id, ['tanggal_daftar' => now()]);

        return redirect()->route('belajar.show', ['kelas_id' => $id])
            ->with('success', 'Berhasil bergabung ke kelas! Selamat belajar.');
    }
}
