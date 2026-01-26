<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Models\Materi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class MateriController extends Controller
{
    /**
     * Tampilkan form untuk upload materi baru
     */
    public function create()
    {
        // Ambil kelas yang dibuat oleh pengajar ini
        $kelas = Kelas::where('pengajar_id', Auth::id())->where('status', '!=', 'ditolak')->get();
        return view('pengajar.materi.create', compact('kelas'));
    }

    /**
     * Simpan materi baru
     */
    public function store(Request $request)
    {
        $request->validate([
            'judul' => 'required|string|max:150',
            'kelas_id' => 'required|exists:kelas,kelas_id',
            'tipe' => 'required|in:video,pdf,soal',
            'deskripsi' => 'nullable|string',
            'file' => 'required|file|mimes:pdf,mp4,mov,avi,doc,docx,ppt,pptx,zip|max:51200', // Max 50MB
        ]);

        // Verifikasi kepemilikan kelas
        $kelas = Kelas::where('kelas_id', $request->kelas_id)->where('pengajar_id', Auth::id())->firstOrFail();

        $path = null;
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            // Simpan di public/materi
            $path = $file->store('materi', 'public');
        }

        Materi::create([
            'kelas_id' => $request->kelas_id,
            'judul' => $request->judul,
            'tipe' => $request->tipe,
            'deskripsi' => $request->deskripsi,
            'file_url' => $path ? Storage::url($path) : null,
        ]);

        return redirect()->route('pengajar.materi')->with('success', 'Materi berhasil diupload!');
    }

    /**
     * Tampilkan form edit materi
     */
    public function edit($id)
    {
        // Cari materi dan pastikan milik kelas pengajar ini
        $materi = Materi::whereHas('kelas', function ($q) {
            $q->where('pengajar_id', Auth::id());
        })->findOrFail($id);

        $kelas = Kelas::where('pengajar_id', Auth::id())->where('status', '!=', 'ditolak')->get();

        return view('pengajar.materi.edit', compact('materi', 'kelas'));
    }

    /**
     * Update materi
     */
    public function update(Request $request, $id)
    {
        $materi = Materi::whereHas('kelas', function ($q) {
            $q->where('pengajar_id', Auth::id());
        })->findOrFail($id);

        $request->validate([
            'judul' => 'required|string|max:150',
            'kelas_id' => 'required|exists:kelas,kelas_id',
            'tipe' => 'required|in:video,pdf,soal',
            'deskripsi' => 'nullable|string',
            'file' => 'nullable|file|mimes:pdf,mp4,mov,avi,doc,docx,ppt,pptx,zip|max:51200', // Max 50MB
        ]);

        // Verifikasi kepemilikan kelas baru (jika berubah)
        $kelas = Kelas::where('kelas_id', $request->kelas_id)->where('pengajar_id', Auth::id())->firstOrFail();

        $data = [
            'kelas_id' => $request->kelas_id,
            'judul' => $request->judul,
            'tipe' => $request->tipe,
            'deskripsi' => $request->deskripsi,
        ];

        if ($request->hasFile('file')) {
            // Hapus file lama jika ada
            if ($materi->file_url) {
                $oldPath = str_replace('/storage/', '', $materi->file_url);
                if (Storage::disk('public')->exists($oldPath)) {
                    Storage::disk('public')->delete($oldPath);
                }
            }

            $file = $request->file('file');
            $path = $file->store('materi', 'public');
            $data['file_url'] = Storage::url($path);
        }

        $materi->update($data);

        return redirect()->route('pengajar.materi')->with('success', 'Materi berhasil diperbarui!');
    }

    /**
     * Hapus materi
     */
    public function destroy($id)
    {
        $materi = Materi::whereHas('kelas', function ($q) {
            $q->where('pengajar_id', Auth::id());
        })->findOrFail($id);

        // Hapus file fisik
        if ($materi->file_url) {
            $oldPath = str_replace('/storage/', '', $materi->file_url);
            if (Storage::disk('public')->exists($oldPath)) {
                Storage::disk('public')->delete($oldPath);
            }
        }

        $materi->delete();

        return redirect()->route('pengajar.materi')->with('success', 'Materi berhasil dihapus!');
    }
}
