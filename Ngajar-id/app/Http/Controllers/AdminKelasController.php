<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use Illuminate\Http\Request;

class AdminKelasController extends Controller
{
    /**
     * Tampilkan daftar semua kelas untuk moderasi
     */
    public function index(Request $request)
    {
        $query = Kelas::with(['pengajar', 'materi'])
            ->withCount(['peserta', 'materi']);

        // Filter by status
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Search
        if ($request->has('search') && $request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('judul', 'like', '%' . $request->search . '%')
                    ->orWhere('deskripsi', 'like', '%' . $request->search . '%')
                    ->orWhereHas('pengajar', function ($q) use ($request) {
                        $q->where('name', 'like', '%' . $request->search . '%');
                    });
            });
        }

        $kelasList = $query->latest()->paginate(15);

        // Stats untuk cards
        $stats = [
            'total' => Kelas::count(),
            'aktif' => Kelas::where('status', 'aktif')->count(),
            'selesai' => Kelas::where('status', 'selesai')->count(),
            'ditolak' => Kelas::where('status', 'ditolak')->count(),
        ];

        return view('admin.kelas.index', compact('kelasList', 'stats'));
    }

    /**
     * Detail kelas untuk review
     */
    public function show($id)
    {
        $kelas = Kelas::with(['pengajar', 'materi', 'peserta'])
            ->withCount(['peserta', 'materi'])
            ->findOrFail($id);

        return view('admin.kelas.show', compact('kelas'));
    }

    /**
     * Update status kelas (Approve/Reject/Archive)
     */
    public function updateStatus(Request $request, $id)
    {
        $kelas = Kelas::findOrFail($id);

        $request->validate([
            'status' => 'required|in:aktif,selesai,ditolak'
        ]);

        $oldStatus = $kelas->status;
        $kelas->update([
            'status' => $request->status
        ]);

        $messages = [
            'aktif' => 'Kelas berhasil disetujui dan dipublikasikan!',
            'selesai' => 'Kelas berhasil diarsipkan.',
            'ditolak' => 'Kelas telah ditolak dan tidak akan ditampilkan di platform.',
        ];

        return redirect()->back()->with('success', $messages[$request->status]);
    }

    /**
     * Hapus kelas permanen
     */
    public function destroy($id)
    {
        $kelas = Kelas::findOrFail($id);

        // Soft check: Warn if kelas has students
        if ($kelas->peserta()->count() > 0) {
            return redirect()->back()->with('error', 'Kelas ini memiliki ' . $kelas->peserta()->count() . ' peserta. Arsipkan saja daripada menghapus.');
        }

        $kelas->delete();

        return redirect()->route('admin.kelas.index')->with('success', 'Kelas berhasil dihapus.');
    }
}
