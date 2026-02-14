<?php

namespace App\Services;

use Midtrans\Config;
use Midtrans\Snap;
use Midtrans\Transaction;

class MidtransService
{
    public function __construct()
    {
        // Konfigurasi Midtrans
        // Konfigurasi Midtrans (Hardcoded untuk        )
        Config::$serverKey = 'SB-Mid-server-H7_YlkYcZOpjf_SLTEyaAbX5';
        Config::$clientKey = 'SB-Mid-client-3dM-8XdhXJEIWh2a';
        Config::$isProduction = false;
        Config::$isSanitized = true;
        Config::$is3ds = true;
    }

    /**
     * Generate Snap Token untuk pembayaran donasi
     * 
     * @param array $donasiData
     * @return string Snap Token
     */
    public function createTransaction($donasiData)
    {
        // Fix SSL Error on Windows Localhost (Development Only)
        if (!config('midtrans.is_production') && strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            // Initialize as array if not already
            if (!is_array(Config::$curlOptions)) {
                Config::$curlOptions = [];
            }
            Config::$curlOptions[CURLOPT_SSL_VERIFYPEER] = false;
            Config::$curlOptions[CURLOPT_SSL_VERIFYHOST] = 0;

            // Fix for Midtrans library bug: "Undefined array key 10023" (CURLOPT_HTTPHEADER)
            // We must provide a non-empty array to prevent the library from wiping out default headers
            Config::$curlOptions[CURLOPT_HTTPHEADER] = ['X-Dev-Mode: true'];
        }

        $params = [
            'transaction_details' => [
                'order_id' => $donasiData['nomor_transaksi'],
                'gross_amount' => $donasiData['jumlah'],
            ],
            'customer_details' => [
                'first_name' => $donasiData['nama'],
                'email' => $donasiData['email'] ?? 'donatur@ngajar.id',
            ],
            'item_details' => [
                [
                    'id' => 'DONASI',
                    'price' => $donasiData['jumlah'],
                    'quantity' => 1,
                    'name' => 'Donasi Ngajar.ID',
                ]
            ],
            'callbacks' => [
                'finish' => route('donasi.payment.finish'),
            ],
        ];

        try {
            $snapToken = Snap::getSnapToken($params);
            return $snapToken;
        } catch (\Exception $e) {
            throw new \Exception('Gagal membuat transaksi: ' . $e->getMessage());
        }
    }

    /**
     * Cek status transaksi dari Midtrans
     * 
     * @param string $orderId
     * @return mixed
     */
    public function getTransactionStatus($orderId): mixed
    {
        try {
            return Transaction::status($orderId);
        } catch (\Exception $e) {
            throw new \Exception('Gagal mengecek status: ' . $e->getMessage());
        }
    }

    /**
     * Verifikasi notifikasi dari Midtrans (Webhook)
     * 
     * @param array $notification
     * @return array
     */
    public function handleNotification($notification)
    {
        $orderId = $notification['order_id'];
        $transactionStatus = $notification['transaction_status'];
        $fraudStatus = $notification['fraud_status'] ?? 'accept';

        $status = 'pending';

        if ($transactionStatus == 'capture') {
            if ($fraudStatus == 'accept') {
                $status = 'paid';
            }
        } elseif ($transactionStatus == 'settlement') {
            $status = 'paid';
        } elseif ($transactionStatus == 'pending') {
            $status = 'pending';
        } elseif (in_array($transactionStatus, ['deny', 'expire', 'cancel'])) {
            $status = 'failed';
        }

        return [
            'order_id' => $orderId,
            'status' => $status,
            'transaction_status' => $transactionStatus,
            'payment_type' => $notification['payment_type'] ?? null,
        ];
    }
}
