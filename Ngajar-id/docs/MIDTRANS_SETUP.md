# 🚀 Midtrans Payment Gateway Integration

## 📋 Setup Guide

### 1. Install Midtrans SDK

```bash
composer require midtrans/midtrans-php
```

### 2. Konfigurasi Environment Variables

Tambahkan ke file `.env`:

```env
# Midtrans Configuration
MIDTRANS_SERVER_KEY=your-server-key-here
MIDTRANS_CLIENT_KEY=your-client-key-here
MIDTRANS_IS_PRODUCTION=false
MIDTRANS_MERCHANT_ID=your-merchant-id
```

**Catatan:**

- Untuk testing, gunakan **Sandbox** credentials dari https://dashboard.sandbox.midtrans.com/
- Untuk production, gunakan credentials dari https://dashboard.midtrans.com/

### 3. Dapatkan Credentials Midtrans

#### Sandbox (Testing):

1. Daftar di: https://dashboard.sandbox.midtrans.com/register
2. Login ke dashboard
3. Klik **Settings** → **Access Keys**
4. Copy **Server Key** dan **Client Key**

#### Production:

1. Daftar di: https://dashboard.midtrans.com/register
2. Lengkapi verifikasi bisnis
3. Klik **Settings** → **Access Keys**
4. Copy **Server Key** dan **Client Key**

### 4. Set Webhook URL di Midtrans Dashboard

1. Login ke Midtrans Dashboard
2. Go to **Settings** → **Configuration**
3. Set **Payment Notification URL** ke:
    ```
    https://your-domain.com/donasi/webhook
    ```
4. Set **Finish Redirect URL** ke:
    ```
    https://your-domain.com/donasi/payment/finish
    ```

### 5. Jalankan Migration

```bash
php artisan migrate:fresh --seed
```

---

## 🧪 Testing dengan Test Cards

Midtrans menyediakan test cards untuk testing di Sandbox:

### Credit Card Success:

- **Card Number**: 4811 1111 1111 1114
- **CVV**: 123
- **Exp Date**: 01/25

### Credit Card Failure:

- **Card Number**: 4911 1111 1111 1113
- **CVV**: 123
- **Exp Date**: 01/25

### GoPay, OVO, DANA, dll:

Semua akan sukses di Sandbox mode.

---

## 📦 File Structure

```
app/
├── Services/
│   └── MidtransService.php          # Midtrans service handler
├── Http/Controllers/
│   └── DonasiController.php         # Controller dengan payment integration
config/
└── midtrans.php                     # Midtrans configuration
resources/
├── views/
│   ├── donasi.blade.php             # Halaman donasi dengan Snap
│   ├── donasi/
│   │   └── payment-finish.blade.php # Success page
│   └── emails/
│       └── donasi-thank-you.blade.php # Email template
routes/
└── web.php                          # Routes untuk donasi
```

---

## 🔄 Flow Diagram

```
1. User mengisi form donasi
   ↓
2. Frontend kirim AJAX ke /donasi (POST)
   ↓
3. Backend validasi & create donasi (status: pending)
   ↓
4. Generate Midtrans Snap Token
   ↓
5. Return snap_token ke frontend
   ↓
6. Frontend open Midtrans Snap popup
   ↓
7. User pilih metode & bayar
   ↓
8. Midtrans kirim notifikasi ke /donasi/webhook
   ↓
9. Backend update status donasi (paid/failed)
   ↓
10. Kirim email confirmation
   ↓
11. Redirect ke payment-finish page
```

---

## ⚙️ API Endpoints

### POST /donasi

Create donasi baru dan generate snap token.

**Request:**

```json
{
    "jumlah": 50000,
    "nama": "John Doe",
    "email": "john@example.com",
    "pesan": "Semoga bermanfaat",
    "metode_pembayaran": "ewallet"
}
```

**Response:**

```json
{
    "success": true,
    "message": "Donasi berhasil dibuat!",
    "data": {
        "donasi_id": 1,
        "nomor_transaksi": "DNT-20260125-A1B2C3",
        "nama": "John Doe",
        "jumlah": 50000,
        "metode_pembayaran": "ewallet",
        "status": "pending",
        "snap_token": "xxxxx-xxxxx-xxxxx"
    }
}
```

### POST /donasi/webhook

Webhook untuk notifikasi dari Midtrans.

**Request dari Midtrans:**

```json
{
    "transaction_status": "settlement",
    "order_id": "DNT-20260125-A1B2C3",
    "payment_type": "gopay",
    "fraud_status": "accept"
}
```

### GET /donasi/payment/finish

Halaman setelah pembayaran selesai.

**Query Params:**

- `order_id`: Nomor transaksi

---

## 📧 Email Notification

Email otomatis dikirim ke donatur ketika:

1. Status transaksi berubah jadi **paid**
2. Berisi:
    - Detail donasi (nominal, nomor transaksi, waktu)
    - Impact message
    - CTA untuk donasi lagi

Template: `resources/views/emails/donasi-thank-you.blade.php`

---

## 🐛 Debugging

### Enable Logging

Tambahkan di `.env`:

```env
APP_DEBUG=true
LOG_CHANNEL=stack
LOG_LEVEL=debug
```

### Check Logs

```bash
tail -f storage/logs/laravel.log
```

### Test Webhook Locally dengan ngrok:

```bash
ngrok http 8000
```

Gunakan URL ngrok untuk webhook di Midtrans Dashboard.

---

## ✅ Checklist Deployment

- [ ] Set `MIDTRANS_IS_PRODUCTION=true` di production
- [ ] Gunakan production credentials
- [ ] Set webhook URL ke domain production
- [ ] Test dengan real payment
- [ ] Setup email SMTP untuk production
- [ ] Enable HTTPS (required oleh Midtrans)
- [ ] Monitor webhook logs

---

## 🔒 Security Best Practices

1. **Never commit credentials** - Gunakan `.env`
2. **Validate webhook** - Cek signature dari Midtrans
3. **HTTPS only** - Midtrans require HTTPS untuk production
4. **Rate limiting** - Batasi request ke endpoint donasi
5. **CSRF protection** - Sudah dihandle Laravel

---

## 📚 Resources

- [Midtrans Documentation](https://docs.midtrans.com/)
- [Snap Integration Guide](https://docs.midtrans.com/en/snap/overview)
- [Test Credentials](https://docs.midtrans.com/en/technical-reference/sandbox-test)
- [Webhook Documentation](https://docs.midtrans.com/en/after-payment/http-notification)

---

**Created by:** Ngajar.ID Team  
**Version:** 1.0.0  
**Last Updated:** 25 Januari 2026
