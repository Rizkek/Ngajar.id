<?php

namespace App\Http\Controllers;

use App\Models\Donasi;
use App\Http\Traits\ApiResponse;
use App\Http\Resources\DonasiResource;
use App\Services\MidtransService;
use App\Services\XenditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class DonasiController extends Controller
{
    use ApiResponse;
    protected $midtrans;

    public function __construct(MidtransService $midtrans)
    {
        $this->midtrans = $midtrans;
    }

    // ========== API ENDPOINTS ==========

    /**
     * Get list of all donations (paginated) OR Show donation page
     * API: GET /api/v1/donations?status=success&limit=10
     * Web: GET /donasi
     * Supports both Web & API
     */
    public function index(Request $request)
    {
        try {
            // If JSON request, return API response
            if ($request->expectsJson()) {
                $query = Donasi::query();

                // Filter by status
                if ($request->has('status') && $request->status !== 'all') {
                    $query->where('status', $request->status);
                } else {
                    $query->whereIn('status', ['paid', 'settlement', 'capture', 'success']);
                }

                // Get paginated results
                $limit = $request->get('limit', 15);
                $donations = $query->latest('tanggal')->paginate($limit);

                return $this->successWithPagination(
                    DonasiResource::collection($donations),
                    'Donations retrieved successfully'
                );
            }

            // Web view response
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

        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return $this->serverError($e->getMessage());
            }
            return redirect()->back()->with('error', 'Failed to load donations: ' . $e->getMessage());
        }
    }

    /**
     * Get donation statistics
     * API: GET /api/v1/donations/stats
     */
    public function stats()
    {
        try {
            $stats = [
                'total_donations' => Cache::remember('total_donations_stat', 300, function () {
                    return Donasi::whereIn('status', ['paid', 'settlement', 'capture', 'success'])->sum('jumlah');
                }),
                'total_donors' => Cache::remember('total_donors_stat', 300, function () {
                    return Donasi::whereIn('status', ['paid', 'settlement', 'capture', 'success'])->distinct('email')->count('email');
                }),
                'total_transactions' => Cache::remember('total_transactions_stat', 300, function () {
                    return Donasi::whereIn('status', ['paid', 'settlement', 'capture', 'success'])->count();
                }),
                'avg_donation' => Cache::remember('avg_donation_stat', 300, function () {
                    return Donasi::whereIn('status', ['paid', 'settlement', 'capture', 'success'])->avg('jumlah');
                }),
            ];

            return $this->success($stats, 'Donation statistics retrieved successfully');

        } catch (\Exception $e) {
            return $this->serverError($e->getMessage());
        }
    }

    /**
     * Get recent donations
     * API: GET /api/v1/donations/recent?limit=10
     */
    public function recent(Request $request)
    {
        try {
            $limit = $request->get('limit', 10);

            $donations = Donasi::whereIn('status', ['paid', 'settlement', 'capture', 'success'])
                ->latest('tanggal')
                ->take($limit)
                ->get();

            if ($donations->isEmpty()) {
                return $this->success(
                    collect(),
                    'No donations yet'
                );
            }

            return $this->success(
                DonasiResource::collection($donations),
                'Recent donations retrieved successfully'
            );

        } catch (\Exception $e) {
            return $this->serverError($e->getMessage());
        }
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
     * Support both Web & API
     * API: POST /api/v1/donations
     */
    public function store(Request $request)
    {
        try {
            // Validasi input
            $validated = $request->validate([
                'jumlah' => 'required|numeric|min:10000',
                'nama' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'pesan' => 'nullable|string|max:1000',
                'metode_pembayaran' => 'required|string',
                'anonymous' => 'boolean',
            ]);

            // Buat Nomor Transaksi Unik
            $nomorTransaksi = 'DNT-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6));

            // Input data donasi ke tabel (Status awal: Pending)
            $donasi = Donasi::create([
                'nama' => $validated['anonymous'] ? 'Hamba Allah' : ($validated['nama'] ?? 'Hamba Allah'),
                'email' => $validated['email'],
                'jumlah' => $validated['jumlah'],
                'tanggal' => now(),
                'pesan' => $validated['pesan'],
                'status' => 'pending',
                'metode_pembayaran' => $validated['metode_pembayaran'],
                'nomor_transaksi' => $nomorTransaksi,
                'anonymous' => $validated['anonymous'] ?? false,
            ]);

            // Get Snap Token from Midtrans Service
            $snapToken = $this->midtrans->createTransaction([
                'nomor_transaksi' => $nomorTransaksi,
                'jumlah' => $validated['jumlah'],
                'nama' => $donasi->nama,
                'email' => $donasi->email,
            ]);

            $response_data = [
                'id' => $donasi->donasi_id,
                'transaction_number' => $nomorTransaksi,
                'name' => $donasi->nama,
                'amount' => $donasi->jumlah,
                'payment_method' => $donasi->metode_pembayaran,
                'status' => $donasi->status,
                'snap_token' => $snapToken,
            ];

            if ($request->expectsJson()) {
                return $this->success(
                    $response_data,
                    'Donation created successfully',
                    201
                );
            }

            return response()->json([
                'success' => true,
                'message' => 'Donasi berhasil dibuat!',
                'data' => $response_data,
            ], 201);

        } catch (\Exception $e) {
            Log::error('Gagal membuat donasi: ' . $e->getMessage());

            if ($request->expectsJson()) {
                return $this->validationError(['error' => $e->getMessage()]);
            }

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

