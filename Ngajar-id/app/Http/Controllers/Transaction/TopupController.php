<?php

namespace App\Http\Controllers\Transaction;

use App\Http\Controllers\Controller;

use App\Models\Topup;
use App\Models\TokenLog;
use App\Services\XenditService;
use App\Http\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TopupController extends Controller
{
    use ApiResponse;

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
        try {
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
                'tanggal' => now(),
            ]);

            // Use XenditService
            $invoice = $this->xendit->createInvoice([
                'external_id' => "TOPUP-" . $topup->topup_id,
                'amount' => (int) $request->amount,
                'payer_email' => $user->email,
                'payer_name' => $user->name,
                'description' => "Topup " . $request->tokens . " Token Ngajar.ID",
                'success_redirect_url' => route('dashboard'),
                'failure_redirect_url' => route('dashboard'),
            ]);

            $data = [
                'topup_id' => $topup->topup_id,
                'invoice_url' => $invoice['invoice_url'],
                'amount' => $request->amount,
                'tokens' => $request->tokens,
                'status' => 'pending'
            ];

            if ($request->expectsJson()) {
                return $this->success($data, 'Invoice created successfully', 201);
            }

            return response()->json([
                'success' => true,
                'invoice_url' => $invoice['invoice_url'],
                'topup_id' => $topup->topup_id,
            ]);

        } catch (\Exception $e) {
            \Log::error('Error in topup create: ' . $e->getMessage());

            if ($request->expectsJson()) {
                return $this->serverError('Failed to create invoice: ' . $e->getMessage());
            }

            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat transaksi: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * GET /api/v1/student/token/balance
     * Get user's current token balance
     */
    public function balance(Request $request)
    {
        try {
            $user = $request->user();
            $balance = $user->getSaldoToken();

            // Get token usage stats
            $totalSpent = TokenLog::where('user_id', $user->user_id)
                ->where('tipe', 'penggunaan')
                ->sum('jumlah');

            $totalEarned = TokenLog::where('user_id', $user->user_id)
                ->where('tipe', 'pendapatan')
                ->sum('jumlah');

            $data = [
                'current_balance' => $balance,
                'total_earned' => $totalEarned ?? 0,
                'total_spent' => $totalSpent ?? 0,
                'level' => $user->level ?? 1,
                'xp' => $user->xp ?? 0
            ];

            if ($request->expectsJson()) {
                return $this->success($data, 'Token balance retrieved');
            }

            return view('student.token.balance', compact('balance', 'totalEarned', 'totalSpent'));

        } catch (\Exception $e) {
            \Log::error('Error in balance: ' . $e->getMessage());

            if ($request->expectsJson()) {
                return $this->serverError($e->getMessage());
            }

            return back()->with('error', 'Failed to load balance');
        }
    }

    /**
     * GET /api/v1/student/token/history
     * Get user's token transaction history
     */
    public function history(Request $request)
    {
        try {
            $user = $request->user();
            $type = $request->get('type'); // Filter: all, penggunaan, pendapatan
            $limit = $request->get('limit', 20);

            $query = TokenLog::where('user_id', $user->user_id);

            if ($type && in_array($type, ['penggunaan', 'pendapatan', 'komisi'])) {
                $query->where('tipe', $type);
            }

            $history = $query->latest('tanggal')->paginate($limit);

            if ($request->expectsJson()) {
                return $this->successWithPagination(
                    $history,
                    'Token history retrieved',
                    $history
                );
            }

            return view('student.token.history', compact('history'));

        } catch (\Exception $e) {
            \Log::error('Error in history: ' . $e->getMessage());

            if ($request->expectsJson()) {
                return $this->serverError($e->getMessage());
            }

            return back()->with('error', 'Failed to load history');
        }
    }

    /**
     * POST /api/v1/student/token/topup
     * Alternative alias for create() method (consistent endpoint)
     */
    public function createTopup(Request $request)
    {
        return $this->create($request);
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

