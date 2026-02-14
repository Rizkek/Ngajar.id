<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreKelasRequest;
use App\Http\Requests\UpdateKelasRequest;
use App\Models\Kelas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests; // Penting untuk $this->authorize

class KelasController extends Controller
{
    use AuthorizesRequests; // Trait ini wajib di Laravel 11/12 kalau Controller base-nya bersih
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
    public function store(StoreKelasRequest $request)
    {
        // Cek policy create 
        $this->authorize('create', Kelas::class);

        $data = $request->validated();
        $data['pengajar_id'] = Auth::id();

        // Handle File Upload
        if ($request->hasFile('thumbnail')) {
            $path = $request->file('thumbnail')->store('thumbnails', 'public');
            $data['thumbnail'] = $path;
        }

        Kelas::create($data);

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
    public function update(UpdateKelasRequest $request, $id)
    {
        $kelas = Kelas::findOrFail($id);

        // Authorization pakai Policy
        $this->authorize('update', $kelas);

        $data = $request->validated();

        // Handle File Update
        if ($request->hasFile('thumbnail')) {
            // Hapus file lama jika ada
            if ($kelas->thumbnail && \Illuminate\Support\Facades\Storage::disk('public')->exists($kelas->thumbnail)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($kelas->thumbnail);
            }

            $path = $request->file('thumbnail')->store('thumbnails', 'public');
            $data['thumbnail'] = $path;
        }

        $kelas->update($data);

        return redirect()->route('pengajar.kelas')->with('success', 'Kelas berhasil diperbarui!');
    }

    /**
     * Hapus kelas dari database
     */
    public function destroy($id)
    {
        $kelas = Kelas::findOrFail($id);

        $this->authorize('delete', $kelas);

        // Opsional: cek jika sudah ada murid atau materi, mungkin jangan dihapus langsung
        // Tapi untuk sekarang kita allow delete
        $kelas->delete();

        return redirect()->route('pengajar.kelas')->with('success', 'Kelas berhasil dihapus!');
    }
}
