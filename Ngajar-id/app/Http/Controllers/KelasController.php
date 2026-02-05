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
        // Validasi sudah ditangani otomatis oleh StoreKelasRequest

        // Cek policy create (opsional jika sudah di handle di request authorize, tapi good practice)
        $this->authorize('create', Kelas::class);

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
    public function update(UpdateKelasRequest $request, $id)
    {
        $kelas = Kelas::findOrFail($id);

        // Authorization pakai Policy
        $this->authorize('update', $kelas);

        // Validasi sudah via UpdateKelasRequest

        $kelas->update($request->validated());

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
