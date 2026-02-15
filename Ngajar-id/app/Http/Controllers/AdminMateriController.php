<?php

namespace App\Http\Controllers;

use App\Models\Materi;
use App\Models\Kelas;
use Illuminate\Http\Request;

class AdminMateriController extends Controller
{
    /**
     * Display all materi for moderation
     */
    public function index(Request $request)
    {
        $query = Materi::with(['kelas', 'kelas.pengajar']);

        // Filter by kelas
        if ($request->has('kelas_id') && $request->kelas_id) {
            $query->where('kelas_id', $request->kelas_id);
        }

        // Filter by type
        if ($request->has('tipe') && $request->tipe) {
            $query->where('tipe', $request->tipe);
        }

        // Search
        if ($request->has('search') && $request->search) {
            $query->where('judul', 'ILIKE', '%' . $request->search . '%');
        }

        $materi = $query->latest('created_at')->paginate(20);
        $allKelas = Kelas::where('status', 'aktif')->get();

        return view('admin.materi.index', compact('materi', 'allKelas'));
    }

    /**
     * Display the specified materi
     */
    public function show($id)
    {
        $materi = Materi::with(['kelas', 'kelas.pengajar'])->findOrFail($id);
        return view('admin.materi.show', compact('materi'));
    }

    /**
     * Remove the specified materi
     */
    public function destroy($id)
    {
        $materi = Materi::findOrFail($id);

        // Delete file if exists
        if ($materi->file_url && \Storage::exists($materi->file_url)) {
            \Storage::delete($materi->file_url);
        }

        $materi->delete();

        return redirect()->route('admin.materi.index')
            ->with('success', 'Materi berhasil dihapus!');
    }

    /**
     * Update materi details from admin
     */
    public function update(Request $request, $id)
    {
        $materi = Materi::findOrFail($id);

        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            // urutan mungkin tidak ada di Materi? Cek model Materi.php
            // durasi_menit juga tidak ada di Materi.php
        ]);

        $materi->update($validated);

        return back()->with('success', 'Materi berhasil diupdate!');
    }
}
