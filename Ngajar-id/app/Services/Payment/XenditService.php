<?php

namespace App\Services\Payment;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class XenditService
{
    protected $apiKey;
    protected $baseUrl = 'https://api.xendit.co';

    public function __construct()
    {
        // Prioritaskan API Key dari database (Setting), fallback ke config/env
        $this->apiKey = \App\Models\Setting::get('xendit_secret_key', config('xendit.api_key'));
    }

    /**
     * Create an invoice for donation
     */
    public function createInvoice($data)
    {
        try {
            // Encode API Key for Basic Auth
            $secretKey = $this->apiKey . ':';
            $base64Key = base64_encode($secretKey);

            $response = Http::withHeaders([
                'Authorization' => 'Basic ' . $base64Key,
                'Content-Type' => 'application/json'
            ])->post($this->baseUrl . '/v2/invoices', [
                        'external_id' => $data['external_id'],
                        'amount' => $data['amount'],
                        'payer_email' => $data['payer_email'],
                        'description' => $data['description'] ?? 'Donasi Ngajar.ID',
                        'customer' => [
                            'given_names' => $data['payer_name'],
                            'email' => $data['payer_email']
                        ],
                        // Redirect URLs
                        'success_redirect_url' => route('donasi.payment.finish', ['order_id' => $data['external_id']]),
                        'failure_redirect_url' => route('donasi.index'),
                    ]);

            if ($response->successful()) {
                return $response->json(); // Contains 'invoice_url'
            }

            Log::error('Xendit Error: ' . $response->body());
            throw new \Exception('Gagal membuat invoice Xendit: ' . $response->json()['message'] ?? 'Unknown error');

        } catch (\Exception $e) {
            Log::error('Xendit Exception: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Check invoice status
     */
    public function getInvoice($invoiceId)
    {
        $secretKey = $this->apiKey . ':';
        $base64Key = base64_encode($secretKey);

        $response = Http::withHeaders([
            'Authorization' => 'Basic ' . $base64Key,
        ])->get($this->baseUrl . '/v2/invoices/' . $invoiceId);

        return $response->json();
    }

    public function handleNotification($notification, $callbackToken = null)
    {
        // Validasi Callback Token dari Xendit
        $expectedToken = \App\Models\Setting::get('xendit_webhook_token', config('xendit.webhook_token', ''));
        
        if (!empty($expectedToken) && $callbackToken !== $expectedToken) {
            throw new \Exception('Invalid Xendit Callback Token');
        }

        $status = 'pending';
        $externalId = $notification['external_id'] ?? null;
        $xenditStatus = $notification['status'] ?? 'PENDING';

        if ($xenditStatus === 'PAID' || $xenditStatus === 'SETTLED') {
            $status = 'paid';
        } elseif ($xenditStatus === 'EXPIRED') {
            $status = 'failed';
        }

        return [
            'order_id' => $externalId,
            'status' => $status,
            'transaction_status' => $xenditStatus,
            'payment_type' => 'xendit_invoice',
        ];
    }
}
