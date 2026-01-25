<?php

namespace App\Http\Controllers;

use App\Models\Donasi;
use App\Services\MidtransService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class DonasiController extends Controller
{
    protected $midtrans;

    public function __construct(MidtransService $midtrans)
    {
        $this->midtrans = $midtrans;
    }

    /**
     * Tampilkan halaman donasi dengan riwayat
     * Menghitung total donasi, progress bar, dan list donatur terbaru.
     */
    public function index()
    {
        // Total donasi
        $total_donasi = Donasi::sum('jumlah');

        // Hitung persentase progress donasi (Max 100%)
        $target_donasi = 50000000; // Target Rp 50.000.000
        $progress_percentage = $total_donasi > 0 ? min(($total_donasi / $target_donasi) * 100, 100) : 0;
        $donatur_count = Donasi::count();

        // Ambil 10 riwayat donasi terbaru dari database
        $riwayat_donasi = Donasi::latest('tanggal')
            ->take(10)
            ->get();

        return view('donasi', compact('total_donasi', 'riwayat_donasi', 'target_donasi', 'progress_percentage', 'donatur_count'));
    }

    /**
     * Simpan data donasi baru ke database & request Token Pembayaran ke Midtrans
     */
    public function store(Request $request)
    {
        // Validasi input
        $validated = $request->validate([
            'jumlah' => 'required|numeric|min:10000',
            'nama' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'pesan' => 'nullable|string|max:1000',
            'metode_pembayaran' => 'required|in:bank,ewallet,qris',
        ]);

        try {
            // Buat Nomor Transaksi Unik (Format: DNT-Tanggal-Random)
            $nomorTransaksi = 'DNT-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6));

            // Input data donasi ke tabel (Status awal: Pending)
            $donasi = Donasi::create([
                'nama' => $validated['nama'] ?? 'Hamba Allah',
                'email' => $validated['email'],
                'jumlah' => $validated['jumlah'],
                'tanggal' => now(),
                'pesan' => $validated['pesan'],
                'status' => 'pending',
                'metode_pembayaran' => $validated['metode_pembayaran'],
                'nomor_transaksi' => $nomorTransaksi,
            ]);

            // Minta Snap Token ke API Midtrans
            $snapToken = $this->midtrans->createTransaction([
                'nomor_transaksi' => $nomorTransaksi,
                'jumlah' => $validated['jumlah'],
                'nama' => $donasi->nama,
                'email' => $donasi->email ?? 'donatur@ngajar.id',
            ]);

            // Kirim respons sukses & token ke frontend
            return response()->json([
                'success' => true,
                'message' => 'Donasi berhasil dibuat!',
                'data' => [
                    'donasi_id' => $donasi->donasi_id,
                    'nomor_transaksi' => $nomorTransaksi,
                    'nama' => $donasi->nama,
                    'jumlah' => $donasi->jumlah,
                    'metode_pembayaran' => $donasi->metode_pembayaran,
                    'status' => $donasi->status,
                    'snap_token' => $snapToken, // Snap token untuk payment
                ]
            ], 201);

        } catch (\Exception $e) {
            Log::error('Gagal membuat donasi: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan donasi: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Menerima Webhook notifikasi status pembayaran dari Midtrans
     * (Dipanggil otomatis oleh server Midtrans saat ada update pembayaran)
     */
    public function webhook(Request $request)
    {
        try {
            $notification = $request->all();

            // Proses notifikasi status dari Midtrans
            $result = $this->midtrans->handleNotification($notification);

            // Cari data donasi berdasarkan Order ID
            $donasi = Donasi::where('nomor_transaksi', $result['order_id'])->first();

            if ($donasi) {
                // Update status pembayaran di database
                $donasi->update([
                    'status' => $result['status'],
                ]);

                // Jika status 'paid' (lunas), kirim email terima kasih
                if ($result['status'] === 'paid') {
                    $this->sendThankYouEmail($donasi);
                }

                return response()->json([
                    'success' => true,
                    'message' => 'Notifikasi berhasil diproses'
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Donasi tidak ditemukan'
            ], 404);

        } catch (\Exception $e) {
            Log::error('Webhook error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Halaman 'Terima Kasih' setelah user menyelesaikan pembayaran
     */
    public function paymentFinish(Request $request)
    {
        $orderId = $request->query('order_id');
        $donasi = Donasi::where('nomor_transaksi', $orderId)->first();

        return view('donasi.payment-finish', compact('donasi'));
    }

    /**
     * Fungsi Helper: Kirim email apresiasi ke donatur
     */
    private function sendThankYouEmail($donasi)
    {
        try {
            if ($donasi->email) {
                Mail::send('emails.donasi-thank-you', ['donasi' => $donasi], function ($message) use ($donasi) {
                    $message->to($donasi->email)
                        ->subject('Terima Kasih atas Donasi Anda - Ngajar.ID');
                });
            }
        } catch (\Exception $e) {
            Log::error('Gagal mengirim email: ' . $e->getMessage());
        }
    }
}

