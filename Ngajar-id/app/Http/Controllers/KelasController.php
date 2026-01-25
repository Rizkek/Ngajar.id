<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KelasController extends Controller
{
    /**
     * Tampilkan form untuk membuat kelas baru
     */
    public function create()
    {
        return view('pengajar.kelas.create');
    }

    /**
     * Simpan kelas baru ke database
     */
    public function store(Request $request)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'status' => 'required|in:aktif,nonaktif',
        ]);

        Kelas::create([
            'pengajar_id' => Auth::id(),
            'judul' => $request->judul,
            'deskripsi' => $request->deskripsi,
            'status' => $request->status,
        ]);

        return redirect()->route('pengajar.kelas')->with('success', 'Kelas berhasil dibuat!');
    }

    /**
     * Tampilkan form untuk edit kelas
     */
    public function edit($id)
    {
        $kelas = Kelas::where('pengajar_id', Auth::id())->findOrFail($id);
        return view('pengajar.kelas.edit', compact('kelas'));
    }

    /**
     * Update kelas ke database
     */
    public function update(Request $request, $id)
    {
        $kelas = Kelas::where('pengajar_id', Auth::id())->findOrFail($id);

        $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'status' => 'required|in:aktif,nonaktif,selesai',
        ]);

        $kelas->update([
            'judul' => $request->judul,
            'deskripsi' => $request->deskripsi,
            'status' => $request->status,
        ]);

        return redirect()->route('pengajar.kelas')->with('success', 'Kelas berhasil diperbarui!');
    }

    /**
     * Hapus kelas dari database
     */
    public function destroy($id)
    {
        $kelas = Kelas::where('pengajar_id', Auth::id())->findOrFail($id);

        // Opsional: cek jika sudah ada murid atau materi, mungkin jangan dihapus langsung
        // Tapi untuk sekarang kita allow delete
        $kelas->delete();

        return redirect()->route('pengajar.kelas')->with('success', 'Kelas berhasil dihapus!');
    }
}
