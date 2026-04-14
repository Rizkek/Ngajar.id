<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WebhookValidationService
{
    /**
     * Validate Midtrans webhook signature
     *
     * Midtrans sends X-Override-Notification header with signature
     * Format: hash_hmac('sha256', $json_body, $serverKey)
     */
    public static function validateMidtransSignature(Request $request): bool
    {
        try {
            $body = $request->getContent();
            $signature = $request->header('X-Override-Notification');

            if (!$signature) {
                Log::error('Midtrans webhook: Missing X-Override-Notification header');
                return false;
            }

            // Midtrans signature validation
            $serverKey = env('MIDTRANS_SERVER_KEY');
            $expectedSignature = hash_hmac('sha256', $body, $serverKey);

            // Use hash_equals to prevent timing attacks
            $valid = hash_equals($expectedSignature, $signature);

            if (!$valid) {
                Log::error('Midtrans webhook: Invalid signature', [
                    'expected' => substr($expectedSignature, 0, 20) . '...',
                    'received' => substr($signature, 0, 20) . '...',
                ]);
            }

            return $valid;

        } catch (\Exception $e) {
            Log::error('Midtrans signature validation error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Validate Xendit webhook signature
     *
     * Xendit sends X-Callback-Token header
     * Format: HMAC-SHA256 of request body with webhook token
     */
    public static function validateXenditSignature(Request $request): bool
    {
        try {
            $body = $request->getContent();
            $signature = $request->header('X-Callback-Token');

            if (!$signature) {
                Log::error('Xendit webhook: Missing X-Callback-Token header');
                return false;
            }

            $webhookToken = env('XENDIT_WEBHOOK_TOKEN');
            $expectedSignature = hash_hmac('sha256', $body, $webhookToken);

            // Use hash_equals to prevent timing attacks
            $valid = hash_equals($signature, $expectedSignature);

            if (!$valid) {
                Log::error('Xendit webhook: Invalid signature');
            }

            return $valid;

        } catch (\Exception $e) {
            Log::error('Xendit signature validation error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Prevent replay attacks - check timestamp is recent
     *
     * @param int $timestamp Unix timestamp from webhook
     * @param int $windowSeconds Maximum age allowed (default 5 minutes)
     */
    public static function isWithinTimeWindow(int $timestamp, int $windowSeconds = 300): bool
    {
        try {
            $now = time();
            $diff = abs($now - $timestamp);

            // Reject if older than allowed window
            if ($diff > $windowSeconds) {
                Log::warning('Webhook rejected: timestamp outside acceptable window', [
                    'timestamp' => $timestamp,
                    'current' => $now,
                    'diff' => $diff,
                    'window' => $windowSeconds,
                ]);
                return false;
            }

            return true;

        } catch (\Exception $e) {
            Log::error('Timestamp validation error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Verify webhook came from legitimate source
     * Also checks if we've already processed this webhook (idempotency)
     */
    public static function isIdempotent(string $externalId, string $gateway): bool
    {
        try {
            // Check if we've already processed this webhook
            $exists = \App\Models\WebhookLog::where('external_id', $externalId)
                ->where('gateway', $gateway)
                ->exists();

            if ($exists) {
                Log::info('Webhook already processed (idempotent check)', [
                    'external_id' => $externalId,
                    'gateway' => $gateway,
                ]);
                return true; // Already processed, don't process again
            }

            return false; // New webhook, safe to process

        } catch (\Exception $e) {
            Log::error('Idempotency check error: ' . $e->getMessage());
            return false;
        }
    }
}
