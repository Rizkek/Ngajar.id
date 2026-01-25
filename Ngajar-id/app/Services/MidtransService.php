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
        Config::$serverKey = config('midtrans.server_key');
        Config::$clientKey = config('midtrans.client_key');
        Config::$isProduction = config('midtrans.is_production', false);
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
