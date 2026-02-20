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
    public function index(Request $request)
    {
        // Build query
        $query = Kelas::with(['pengajar', 'materi', 'peserta'])
            ->where('status', 'aktif');

        // Filter by search
        if ($request->has('search') && $request->search) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('judul', 'ILIKE', "%{$searchTerm}%")
                    ->orWhere('deskripsi', 'ILIKE', "%{$searchTerm}%");
            });
        }

        // Filter by category
        if ($request->has('kategori') && $request->kategori) {
            $query->where('kategori', $request->kategori);
        }

        // Get results
        $programs = $query->latest()->paginate(9);

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

        // Logic Pembayaran Token
        $harga = $kelas->harga_token ?? 0;

        // Bypass pembayaran jika user adalah penerima beasiswa
        if ($user->hasBeasiswa()) {
            $harga = 0;
        }

        if ($harga > 0) {
            // Cek saldo token user
            $userToken = $user->token;

            if (!$userToken || !$userToken->cukup($harga)) {
                return redirect()->route('topup.create')
                    ->with('error', "Token tidak mencukupi. Harga kelas: {$harga} Token. Saldo Anda: " . ($userToken->jumlah ?? 0));
            }

            try {
                \Illuminate\Support\Facades\DB::transaction(function () use ($user, $kelas, $userToken, $harga) {
                    $userToken->kurang($harga);

                    \App\Models\TokenLog::create([
                        'user_id' => $user->user_id,
                        'jumlah' => $harga,
                        'aksi' => 'kurang',
                        'tipe' => 'pembelian_kelas',
                        'keterangan' => "Membeli akses kelas: {$kelas->judul}",
                        'tanggal' => now(),
                    ]);

                    $kelas->peserta()->attach($user->user_id, ['tanggal_daftar' => now()]);
                });
            } catch (\Exception $e) {
                return back()->with('error', 'Terjadi kesalahan saat memproses pembayaran token: ' . $e->getMessage());
            }

            return redirect()->route('murid.kelas')->with('success', "Berhasil bergabung ke kelas seharga {$harga} Token! Selamat belajar.");

        } else {
            // Jika Gratis (atau Beasiswa)
            $kelas->peserta()->attach($user->user_id, ['tanggal_daftar' => now()]);

            $msg = $user->hasBeasiswa() ? "Fasilitas Beasiswa: Berhasil bergabung secara GRATIS!" : "Berhasil bergabung ke kelas gratis! Selamat belajar.";

            return redirect()->route('murid.kelas')->with('success', $msg);
        }
    }
}

