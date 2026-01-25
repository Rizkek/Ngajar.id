<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProgramController extends Controller
{
    /**
     * Tampilkan katalog kelas yang aktif
     */
    public function index()
    {
        // Ambil kelas yang statusnya 'aktif', urutkan terbaru
        // Eager load pengajar dan hitung jumlah materi & peserta
        $programs = Kelas::with('pengajar')
            ->withCount(['materi', 'peserta'])
            ->where('status', 'aktif')
            ->latest()
            ->get();

        return view('programs', compact('programs'));
    }

    /**
     * Proses murid bergabung ke kelas
     */
    public function join(Request $request, $kelasId)
    {
        // Cek login
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu untuk mendaftar kelas.');
        }

        $user = Auth::user();

        // Cek role (hanya murid yang bisa gabung)
        if (!$user->isMurid()) {
            return redirect()->back()->with('error', 'Hanya akun Murid yang bisa mendaftar kelas.');
        }

        $kelas = Kelas::findOrFail($kelasId);

        // Cek apakah sudah terdaftar
        $isRegistered = $kelas->peserta()->where('kelas_peserta.siswa_id', $user->user_id)->exists();

        if ($isRegistered) {
            return redirect()->route('murid.kelas')->with('info', 'Anda sudah terdaftar di kelas ini.');
        }

        // Proses pendaftaran (insert ke pivot table)
        // Menggunakan attach() untuk many-to-many relationship
        $kelas->peserta()->attach($user->user_id, ['tanggal_daftar' => now()]);

        return redirect()->route('murid.kelas')->with('success', 'Berhasil bergabung ke kelas! Selamat belajar.');
    }
}
