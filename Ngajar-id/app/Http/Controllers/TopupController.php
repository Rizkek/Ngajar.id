<?php

namespace App\Http\Controllers;

use App\Models\Topup;
use App\Services\XenditService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TopupController extends Controller
{
    protected $xendit;

    public function __construct(XenditService $xendit)
    {
        $this->xendit = $xendit;
    }

    /**
     * Create topup transaction and generate Xendit Invoice
     */
    public function create(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:10000',
            'tokens' => 'required|integer|min:1',
        ]);

        $user = auth()->user();

        // Create topup record
        $topup = Topup::create([
            'topup_id' => (string) Str::uuid(),
            'user_id' => $user->user_id,
            'jumlah_token' => $request->tokens,
            'harga' => (int) $request->amount,
            // 'status' => 'pending', // Default database
            'tanggal' => now(),
        ]);

        try {
            // Use XenditService
            $invoice = $this->xendit->createInvoice([
                'external_id' => "TOPUP-" . $topup->topup_id,
                'amount' => (int) $request->amount,
                'payer_email' => $user->email,
                'payer_name' => $user->name,
                'description' => "Topup " . $request->tokens . " Token Ngajar.ID",
                'success_redirect_url' => route('dashboard'), // Or specific topup success page
                'failure_redirect_url' => route('dashboard'),
            ]);

            return response()->json([
                'success' => true,
                'invoice_url' => $invoice['invoice_url'],
                'topup_id' => $topup->topup_id,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat transaksi: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Xendit notification callback
     */
    public function callback(Request $request)
    {
        try {
            $notification = $request->all();
            $result = $this->xendit->handleNotification($notification);

            $orderId = $result['order_id'];
            $status = $result['status'];

            // Extract topup_id from order_id (Format: TOPUP-UUID)
            $topupId = str_replace('TOPUP-', '', $orderId);
            $topup = Topup::where('topup_id', $topupId)->first();

            if (!$topup) {
                return response()->json(['message' => 'Topup not found'], 404);
            }

            if ($status == 'paid') {
                // Payment success
                // This update triggers the 'updated' event in Topup model
                // which handles token addition and logging automatically
                $topup->update(['status' => 'success']);
            } elseif ($status == 'failed') {
                $topup->update(['status' => 'failed']);
            }

            return response()->json(['message' => 'OK']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
