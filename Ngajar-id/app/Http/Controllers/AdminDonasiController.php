<?php

namespace App\Http\Controllers;

use App\Models\Donasi;
use Illuminate\Http\Request;

class AdminDonasiController extends Controller
{
    /**
     * Display all donations with advanced filtering
     */
    public function index(Request $request)
    {
        $query = Donasi::query();

        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        // Filter by payment method
        if ($request->has('metode') && $request->metode) {
            $query->where('metode_pembayaran', $request->metode);
        }

        // Filter by date range
        if ($request->has('start_date') && $request->start_date) {
            $query->whereDate('tanggal', '>=', $request->start_date);
        }
        if ($request->has('end_date') && $request->end_date) {
            $query->whereDate('tanggal', '<=', $request->end_date);
        }

        // Search by name or email
        if ($request->has('search') && $request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('nama', 'ILIKE', '%' . $request->search . '%')
                    ->orWhere('email', 'ILIKE', '%' . $request->search . '%')
                    ->orWhere('nomor_transaksi', 'ILIKE', '%' . $request->search . '%');
            });
        }

        $donasi = $query->latest('tanggal')->paginate(20);

        // Statistics
        $stats = [
            'total' => Donasi::whereIn('status', ['paid', 'settlement', 'capture'])->sum('jumlah'),
            'pending' => Donasi::where('status', 'pending')->count(),
            'success' => Donasi::whereIn('status', ['paid', 'settlement', 'capture'])->count(),
            'failed' => Donasi::whereIn('status', ['failed', 'expired'])->count(),
        ];

        return view('admin.donasi.index', compact('donasi', 'stats'));
    }

    /**
     * Display the specified donation
     */
    public function show($id)
    {
        $donasi = Donasi::findOrFail($id);
        return view('admin.donasi.show', compact('donasi'));
    }

    /**
     * Update donation status manually
     */
    public function updateStatus(Request $request, $id)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,paid,settlement,capture,failed,expired,refunded',
            'admin_note' => 'nullable|string|max:500',
        ]);

        $donasi = Donasi::findOrFail($id);
        $donasi->update([
            'status' => $validated['status'],
            'catatan_admin' => $validated['admin_note'] ?? null,
        ]);

        return back()->with('success', 'Status donasi berhasil diupdate!');
    }

    /**
     * Mark donation as refunded
     */
    public function refund(Request $request, $id)
    {
        $validated = $request->validate([
            'refund_reason' => 'required|string|max:500',
        ]);

        $donasi = Donasi::findOrFail($id);

        if (!in_array($donasi->status, ['paid', 'settlement', 'capture'])) {
            return back()->with('error', 'Only paid donations can be refunded!');
        }

        $donasi->update([
            'status' => 'refunded',
            'catatan_admin' => 'Refund: ' . $validated['refund_reason'],
        ]);

        // TODO: Integrate with payment gateway refund API

        return back()->with('success', 'Donasi berhasil di-refund! (Manual process required)');
    }

    /**
     * Delete donation (soft delete or hard delete based on status)
     */
    public function destroy($id)
    {
        $donasi = Donasi::findOrFail($id);

        // Only allow deletion of pending/failed donations
        if (!in_array($donasi->status, ['pending', 'failed', 'expired'])) {
            return back()->with('error', 'Cannot delete completed donations!');
        }

        $donasi->delete();

        return redirect()->route('admin.donasi.index')
            ->with('success', 'Donasi berhasil dihapus!');
    }
}
