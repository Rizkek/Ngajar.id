<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Token;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminUserController extends Controller
{
    // ==========================================
    // KELOLA PENGAJAR
    // ==========================================

    /**
     * Tampilkan daftar pengajar
     */
    public function pengajarIndex(Request $request)
    {
        $query = User::pengajar()
            ->withCount('kelasAjar')
            ->with('kelasAjar');

        // Filter by status
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Search
        if ($request->has('search') && $request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        $pengajars = $query->latest()->paginate(15);

        return view('admin.pengajar.index', compact('pengajars'));
    }

    /**
     * Tampilkan detail pengajar
     */
    public function pengajarShow($id)
    {
        $pengajar = User::pengajar()
            ->withCount('kelasAjar')
            ->with(['kelasAjar.peserta', 'kelasAjar.materi'])
            ->findOrFail($id);

        // Hitung statistik
        $totalSiswa = 0;
        $totalMateri = 0;

        foreach ($pengajar->kelasAjar as $kelas) {
            $totalSiswa += $kelas->peserta->count();
            $totalMateri += $kelas->materi->count();
        }

        return view('admin.pengajar.show', compact('pengajar', 'totalSiswa', 'totalMateri'));
    }

    /**
     * Update status pengajar (Aktivasi/Suspend)
     */
    public function pengajarUpdateStatus(Request $request, $id)
    {
        $pengajar = User::pengajar()->findOrFail($id);

        $request->validate([
            'status' => 'required|in:aktif,nonaktif'
        ]);

        $pengajar->update([
            'status' => $request->status
        ]);

        $message = $request->status === 'aktif'
            ? 'Pengajar berhasil diaktifkan!'
            : 'Pengajar berhasil disuspend.';

        return redirect()->back()->with('success', $message);
    }

    /**
     * Hapus pengajar
     */
    public function pengajarDestroy($id)
    {
        $pengajar = User::pengajar()->findOrFail($id);

        // Soft check: Jangan hapus jika masih punya kelas aktif
        if ($pengajar->kelasAjar()->where('status', 'aktif')->count() > 0) {
            return redirect()->back()->with('error', 'Tidak dapat menghapus pengajar dengan kelas aktif. Arsipkan kelas terlebih dahulu.');
        }

        $pengajar->delete();

        return redirect()->route('admin.pengajar.index')->with('success', 'Pengajar berhasil dihapus.');
    }

    // ==========================================
    // KELOLA MURID
    // ==========================================

    /**
     * Tampilkan daftar murid
     */
    public function muridIndex(Request $request)
    {
        $query = User::murid()
            ->withCount('kelasIkuti')
            ->with('token');

        // Filter by status
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Search
        if ($request->has('search') && $request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        $murids = $query->latest()->paginate(15);

        return view('admin.murid.index', compact('murids'));
    }

    /**
     * Tampilkan detail murid
     */
    public function muridShow($id)
    {
        $murid = User::murid()
            ->withCount('kelasIkuti')
            ->with(['kelasIkuti.pengajar', 'token', 'modulDimiliki'])
            ->findOrFail($id);

        return view('admin.murid.show', compact('murid'));
    }

    /**
     * Update status murid (Aktivasi/Suspend)
     */
    public function muridUpdateStatus(Request $request, $id)
    {
        $murid = User::murid()->findOrFail($id);

        $request->validate([
            'status' => 'required|in:aktif,nonaktif'
        ]);

        $murid->update([
            'status' => $request->status
        ]);

        $message = $request->status === 'aktif'
            ? 'Murid berhasil diaktifkan!'
            : 'Murid berhasil disuspend.';

        return redirect()->back()->with('success', $message);
    }

    /**
     * Tambah/Kurangi Token Murid (Manual)
     */
    public function muridUpdateToken(Request $request, $id)
    {
        $murid = User::murid()->findOrFail($id);

        $request->validate([
            'amount' => 'required|integer',
            'action' => 'required|in:add,subtract'
        ]);

        $tokenRecord = Token::firstOrCreate(
            ['user_id' => $murid->user_id],
            ['jumlah' => 0, 'last_update' => now()]
        );

        if ($request->action === 'add') {
            $tokenRecord->increment('jumlah', $request->amount);
            $message = "Berhasil menambahkan {$request->amount} token.";
        } else {
            if ($tokenRecord->jumlah < $request->amount) {
                return redirect()->back()->with('error', 'Saldo token tidak cukup untuk dikurangi.');
            }
            $tokenRecord->decrement('jumlah', $request->amount);
            $message = "Berhasil mengurangi {$request->amount} token.";
        }

        $tokenRecord->update(['last_update' => now()]);

        return redirect()->back()->with('success', $message);
    }

    /**
     * Hapus murid
     */
    public function muridDestroy($id)
    {
        $murid = User::murid()->findOrFail($id);
        $murid->delete();

        return redirect()->route('admin.murid.index')->with('success', 'Murid berhasil dihapus.');
    }
}
