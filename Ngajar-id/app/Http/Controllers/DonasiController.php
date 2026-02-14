<?php

namespace App\Http\Controllers;

use App\Models\Donasi;
use App\Services\MidtransService;
use App\Services\XenditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class DonasiController extends Controller
{
    protected $xendit;
    protected $midtrans; // Keep for backward compatibility or remove if fully migrated

    public function __construct(XenditService $xendit)
    {
        $this->xendit = $xendit;
    }

    /**
     * Tampilkan halaman donasi dengan riwayat
     * Menghitung total donasi, progress bar, dan list donatur terbaru.
     */
    public function index()
    {
        // Total donasi - Cache 5 menit
        $total_donasi = Cache::remember('total_donasi', 300, function () {
            return Donasi::whereIn('status', ['paid', 'settlement', 'capture'])->sum('jumlah');
        });

        $donatur_count = Cache::remember('donatur_count', 300, function () {
            return Donasi::whereIn('status', ['paid', 'settlement', 'capture'])->count();
        });

        // Ambil 10 riwayat donasi terbaru dari database
        $riwayat_donasi = Donasi::whereIn('status', ['paid', 'settlement', 'capture'])
            ->latest('tanggal')
            ->take(10)
            ->get();

        // Debug: If no paid donations, show all donations for testing
        if ($riwayat_donasi->isEmpty()) {
            Log::info('No paid donations found. Showing all donations for debugging.');
            $riwayat_donasi = Donasi::latest('tanggal')->take(10)->get();

            // Also get total of all donations
            if ($total_donasi == 0) {
                $total_donasi = Donasi::sum('jumlah');
                $donatur_count = Donasi::count();
            }
        }

        return view('donasi', compact('total_donasi', 'riwayat_donasi', 'donatur_count'));
    }

    /**
     * Tampilkan halaman riwayat donasi lengkap (Public)
     */
    public function riwayat()
    {
        // Ambil donasi yang sukses
        $query = Donasi::whereIn('status', ['paid', 'settlement', 'capture']);

        // Debug/Dev: Jika kosong, tampilkan semua (biar user bisa lihat data tes yang masih pending)
        if ($query->count() === 0) {
            $riwayat_donasi = Donasi::orderBy('tanggal', 'desc')->paginate(20);
        } else {
            $riwayat_donasi = $query->orderBy('tanggal', 'desc')->paginate(20);
        }

        return view('donasi.riwayat', compact('riwayat_donasi'));
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

            // Buat Invoice via Xendit
            $invoice = $this->xendit->createInvoice([
                'external_id' => $nomorTransaksi,
                'amount' => $validated['jumlah'],
                'payer_email' => $donasi->email ?? 'donatur@ngajar.id',
                'payer_name' => $donasi->nama,
                'description' => 'Donasi untuk ' . ($validated['pesan'] ? substr($validated['pesan'], 0, 50) . '...' : 'Ngajar.ID'),
            ]);

            // Kirim respons sukses & invoice URL ke frontend
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
                    'invoice_url' => $invoice['invoice_url'], // URL pembayaran Xendit
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

