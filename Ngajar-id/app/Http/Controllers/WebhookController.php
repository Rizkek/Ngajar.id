<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Transaction;
use App\Services\WebhookValidationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    /**
     * Handle Midtrans payment webhook
     *
     * POST /webhook/midtrans
     *
     * ✅ PHASE 1 SECURITY: Validates:
     * - Webhook signature (HMAC)
     * - Timestamp (prevent replay)
     * - Idempotency (prevent duplicate processing)
     */
    public function midtrans(Request $request)
    {
        Log::info('Midtrans webhook received', [
            'ip' => $request->ip(),
            'timestamp' => now(),
        ]);

        // ✅ Security Step 1: Validate signature
        if (!WebhookValidationService::validateMidtransSignature($request)) {
            Log::error('Midtrans webhook REJECTED: Invalid signature', [
                'ip' => $request->ip(),
                'order_id' => $request->input('order_id'),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Invalid signature',
            ], 401);
        }

        try {
            return DB::transaction(function () use ($request) {
                $orderId = $request->input('order_id');
                $transactionStatus = $request->input('transaction_status');
                $transactionId = $request->input('transaction_id');
                $grossAmount = $request->input('gross_amount');

                // ✅ Security Step 2: Check idempotency (avoid double-processing)
                if (WebhookValidationService::isIdempotent($transactionId, 'midtrans')) {
                    Log::info('Midtrans webhook: Already processed', [
                        'transaction_id' => $transactionId,
                        'order_id' => $orderId,
                    ]);

                    return response()->json([
                        'success' => true,
                        'message' => 'Already processed',
                    ], 200);
                }

                // Find order
                $order = Order::where('order_id', $orderId)
                    ->orWhere('midtrans_id', $transactionId)
                    ->first();

                if (!$order) {
                    Log::error('Midtrans webhook: Order not found', [
                        'order_id' => $orderId,
                        'transaction_id' => $transactionId,
                    ]);

                    return response()->json([
                        'success' => false,
                        'message' => 'Order not found',
                    ], 404);
                }

                // ✅ Security Step 3: Validate amount matches
                if ((int) $grossAmount !== (int) $order->total_amount) {
                    Log::error('Midtrans webhook REJECTED: Amount mismatch', [
                        'order_id' => $orderId,
                        'expected' => $order->total_amount,
                        'received' => $grossAmount,
                    ]);

                    return response()->json([
                        'success' => false,
                        'message' => 'Amount mismatch',
                    ], 400);
                }

                // Update order based on status
                switch ($transactionStatus) {
                    case 'settlement':
                    case 'capture':
                        $order->update([
                            'status' => 'paid',
                            'paid_at' => now(),
                            'midtrans_id' => $transactionId,
                        ]);

                        // Dispatch event for notifications, access grants, etc
                        event(new \App\Events\OrderPaid($order));

                        Log::info('Midtrans payment CONFIRMED', [
                            'order_id' => $orderId,
                            'amount' => $grossAmount,
                            'transaction_id' => $transactionId,
                        ]);
                        break;

                    case 'pending':
                        $order->update(['status' => 'pending']);
                        Log::info('Midtrans payment PENDING', ['order_id' => $orderId]);
                        break;

                    case 'deny':
                    case 'cancel':
                        $order->update(['status' => 'failed']);
                        Log::warning('Midtrans payment DENIED', ['order_id' => $orderId]);
                        break;

                    default:
                        Log::warning('Midtrans webhook: Unknown status', [
                            'status' => $transactionStatus,
                            'order_id' => $orderId,
                        ]);
                }

                // Log webhook for audit trail
                Transaction::create([
                    'order_id' => $order->id,
                    'external_id' => $transactionId,
                    'gateway' => 'midtrans',
                    'status' => $transactionStatus,
                    'amount' => $grossAmount,
                    'raw_response' => json_encode($request->all()),
                    'ip_address' => $request->ip(),
                ]);

                Log::info('Midtrans webhook PROCESSED successfully', [
                    'order_id' => $orderId,
                    'transaction_id' => $transactionId,
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Webhook processed',
                ], 200);
            });

        } catch (\Exception $e) {
            Log::error('Midtrans webhook ERROR', [
                'error' => $e->getMessage(),
                'order_id' => $request->input('order_id'),
                'trace' => $e->getTraceAsString(),
            ]);

            // Return 500 so Midtrans retries
            return response()->json([
                'success' => false,
                'message' => 'Processing error',
            ], 500);
        }
    }

    /**
     * Handle Xendit payment webhook
     *
     * POST /webhook/xendit
     *
     * ✅ PHASE 1 SECURITY: Same validation as Midtrans
     */
    public function xendit(Request $request)
    {
        Log::info('Xendit webhook received', [
            'ip' => $request->ip(),
            'timestamp' => now(),
        ]);

        // ✅ Security: Validate signature
        if (!WebhookValidationService::validateXenditSignature($request)) {
            Log::error('Xendit webhook REJECTED: Invalid signature', [
                'ip' => $request->ip(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Invalid signature',
            ], 401);
        }

        try {
            return DB::transaction(function () use ($request) {
                $externalId = $request->input('external_id');
                $status = $request->input('status');
                $amount = $request->input('amount');

                // Check idempotency
                if (WebhookValidationService::isIdempotent($externalId, 'xendit')) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Already processed',
                    ], 200);
                }

                $order = Order::where('xendit_id', $externalId)->first();

                if (!$order) {
                    Log::error('Xendit webhook: Order not found', [
                        'external_id' => $externalId,
                    ]);

                    return response()->json([
                        'success' => false,
                        'message' => 'Order not found',
                    ], 404);
                }

                // Validate amount
                if ((int) $amount !== (int) $order->total_amount) {
                    Log::error('Xendit webhook: Amount mismatch', [
                        'expected' => $order->total_amount,
                        'received' => $amount,
                    ]);

                    return response()->json([
                        'success' => false,
                        'message' => 'Amount mismatch',
                    ], 400);
                }

                // Update based on status
                if ($status === 'PAID') {
                    $order->update([
                        'status' => 'paid',
                        'paid_at' => now(),
                        'xendit_id' => $externalId,
                    ]);

                    event(new \App\Events\OrderPaid($order));

                    Log::info('Xendit payment CONFIRMED', [
                        'external_id' => $externalId,
                        'amount' => $amount,
                    ]);
                } else {
                    $order->update(['status' => 'failed']);
                    Log::warning('Xendit payment FAILED', [
                        'external_id' => $externalId,
                        'status' => $status,
                    ]);
                }

                Transaction::create([
                    'order_id' => $order->id,
                    'external_id' => $externalId,
                    'gateway' => 'xendit',
                    'status' => $status,
                    'amount' => $amount,
                    'raw_response' => json_encode($request->all()),
                    'ip_address' => $request->ip(),
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Webhook processed',
                ], 200);
            });

        } catch (\Exception $e) {
            Log::error('Xendit webhook ERROR', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Processing error',
            ], 500);
        }
    }
}
