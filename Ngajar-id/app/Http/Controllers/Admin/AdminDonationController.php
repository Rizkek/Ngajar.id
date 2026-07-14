<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\Donation;
use Illuminate\Http\Request;

class AdminDonationController extends Controller
{

    /**
     * List all donations (with pagination)
     * GET /admin/donations
     */
    public function index(Request $request)
    {
        try {
            $query = Donation::query();

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

            // Search
            if ($request->has('search') && $request->search) {
                $query->where(function ($q) use ($request) {
                    $q->where('nama', 'like', "%{$request->search}%")
                        ->orWhere('email', 'like', "%{$request->search}%")
                        ->orWhere('nomor_transaksi', 'like', "%{$request->search}%");
                });
            }

            $data = $query->latest('tanggal')->paginate($request->get('per_page', 15));

            return view('admin.donations.index', compact('data'));
        } catch (\Exception $e) {
            \Log::error('AdminDonationController@index: ' . $e->getMessage());

            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Get single donation details
     * GET /admin/donations/{id}
     */
    public function show(Request $request, $id)
    {
        try {
            $donasi = Donation::findOrFail($id);

            return view('admin.donations.show', compact('donasi'));
        } catch (\Exception $e) {
            \Log::error('AdminDonationController@show: ' . $e->getMessage());

            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Verify/approve donation
     * POST /admin/donations/{id}/verify
     */
    public function verify(Request $request, $id)
    {
        try {
            $donasi = Donation::findOrFail($id);

            $validated = $request->validate([
                'status' => 'required|in:paid,settlement,capture',
                'notes' => 'nullable|string|max:500',
            ]);

            $donasi->update([
                'status' => $validated['status'],
                'catatan_admin' => $validated['notes'] ?? null,
            ]);

            return back()->with('success', 'Donation verified successfully');
        } catch (\Exception $e) {
            \Log::error('AdminDonationController@verify: ' . $e->getMessage());

            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Refund donation
     * POST /admin/donations/{id}/refund
     */
    public function refund(Request $request, $id)
    {
        try {
            $donasi = Donation::findOrFail($id);

            $validated = $request->validate([
                'reason' => 'required|string|max:500',
            ]);

            // Check if can be refunded
            if (!in_array($donasi->status, ['paid', 'settlement', 'capture'])) {
                throw new \Exception('Only paid donations can be refunded');
            }

            $donasi->update([
                'status' => 'refunded',
                'catatan_admin' => 'Refund: ' . $validated['reason'],
            ]);

            return back()->with('success', 'Donation refunded successfully');
        } catch (\Exception $e) {
            \Log::error('AdminDonationController@refund: ' . $e->getMessage());

            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Delete donation
     * DELETE /admin/donations/{id}
     */
    public function destroy(Request $request, $id)
    {
        try {
            $donasi = Donation::findOrFail($id);

            // Only allow deletion of pending/failed donations
            if (!in_array($donasi->status, ['pending', 'failed', 'expired'])) {
                throw new \Exception('Cannot delete completed donations');
            }

            $donasi->delete();

            return back()->with('success', 'Donation deleted successfully');
        } catch (\Exception $e) {
            \Log::error('AdminDonationController@destroy: ' . $e->getMessage());

            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Format donation data for API response
     */
    private function formatDonation($donasi)
    {
        return [
            'id' => $donasi->id,
            'nama' => $donasi->nama,
            'email' => $donasi->email,
            'jumlah' => $donasi->jumlah,
            'metode_pembayaran' => $donasi->metode_pembayaran,
            'status' => $donasi->status,
            'nomor_transaksi' => $donasi->nomor_transaksi,
            'tanggal' => $donasi->tanggal?->toIso8601String(),
            'catatan' => $donasi->catatan,
            'catatan_admin' => $donasi->catatan_admin,
        ];
    }
}




