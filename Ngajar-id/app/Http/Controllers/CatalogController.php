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

        // 1. Cek apakah sudah terdaftar
        if ($user->kelasIkuti()->where('kelas_peserta.kelas_id', $id)->exists()) {
            return redirect()->route('belajar.show', ['kelas_id' => $id])
                ->with('info', 'Anda sudah terdaftar di kelas ini.');
        }

        // 2. Cek Role
        if (!$user->isMurid()) {
            return back()->with('error', 'Hanya akun Murid yang bisa mendaftar kelas.');
        }

        // 3. Logic Pembayaran Token
        $harga = $kelas->harga_token ?? 0;

        // Bypass pembayaran jika user adalah penerima beasiswa
        if ($user->hasBeasiswa()) {
            $harga = 0;
        }

        if ($harga > 0) {
            // Cek saldo token user
            $userToken = $user->token;

            if (!$userToken || !$userToken->cukup($harga)) {
                return redirect()->route('topup.create') // Asumsi ada route ini atau ke halaman donasi/topup
                    ->with('error', "Token tidak mencukupi. Harga kelas: {$harga} Token. Saldo Anda: " . ($userToken->jumlah ?? 0));
            }

            // Gunakan Transaction untuk atomic operation
            try {
                \Illuminate\Support\Facades\DB::transaction(function () use ($user, $kelas, $userToken, $harga) {
                    // Potong Token
                    $userToken->kurang($harga);

                    // Catat Log
                    \App\Models\TokenLog::create([
                        'user_id' => $user->user_id,
                        'jumlah' => $harga,
                        'aksi' => 'kurang',
                        'tipe' => 'pembelian_kelas',
                        'keterangan' => "Membeli akses kelas: {$kelas->judul}",
                        'tanggal' => now(),
                    ]);

                    // Enroll User
                    $user->kelasIkuti()->attach($kelas->kelas_id, ['tanggal_daftar' => now()]);
                });
            } catch (\Exception $e) {
                return back()->with('error', 'Terjadi kesalahan saat memproses pembayaran token: ' . $e->getMessage());
            }

            return redirect()->route('belajar.show', ['kelas_id' => $id])
                ->with('success', "Berhasil membeli kelas seharga {$harga} Token!");

        } else {
            // Jika Gratis (atau Beasiswa)
            $user->kelasIkuti()->attach($id, ['tanggal_daftar' => now()]);

            $msg = $user->hasBeasiswa() ? "Fasilitas Beasiswa: Berhasil bergabung secara GRATIS!" : "Berhasil bergabung ke kelas gratis!";

            return redirect()->route('belajar.show', ['kelas_id' => $id])
                ->with('success', $msg);
        }
    }
}

