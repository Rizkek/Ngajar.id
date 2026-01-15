<?php

namespace App\Http\Controllers;

use App\Models\Donasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DonasiController extends Controller
{
    /**
     * Display donation summary and recent donations
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Contracts\View\View
     */
    public function index(Request $request)
    {
        // Get total donations
        $totalDonasi = Donasi::sum('jumlah');

        // Get recent donations
        $perPage = $request->get('per_page', 10);
        $riwayatDonasi = Donasi::latest('tanggal')
            ->paginate($perPage)
            ->map(function ($donasi) {
                return [
                    'nama' => $donasi->nama,
                    'jumlah' => $donasi->jumlah,
                    'tanggal' => $donasi->tanggal,
                ];
            });

        $data = [
            'total_donasi' => $totalDonasi,
            'riwayat_donasi' => $riwayatDonasi->items(),
        ];

        // API response
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => $data,
                'pagination' => [
                    'current_page' => $riwayatDonasi->currentPage(),
                    'last_page' => $riwayatDonasi->lastPage(),
                    'per_page' => $riwayatDonasi->perPage(),
                    'total' => $riwayatDonasi->total(),
                ],
            ]);
        }

        // Web view
        return view('donasi', [
            'total_donasi' => $totalDonasi,
            'riwayat_donasi' => $riwayatDonasi->items(),
        ]);
    }

    /**
     * Store a new donation
     * 
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:100'],
            'jumlah' => ['required', 'integer', 'min:1000'],
        ], [
            'nama.required' => 'Nama donatur wajib diisi.',
            'nama.max' => 'Nama maksimal 100 karakter.',
            'jumlah.required' => 'Jumlah donasi wajib diisi.',
            'jumlah.integer' => 'Jumlah harus berupa angka.',
            'jumlah.min' => 'Donasi minimal Rp 1.000.',
        ]);

        try {
            $donasi = Donasi::create([
                'nama' => $validated['nama'],
                'jumlah' => $validated['jumlah'],
                'tanggal' => now(),
            ]);

            // API response
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Terima kasih atas donasi Anda!',
                    'data' => $donasi,
                ], 201);
            }

            // Web response
            return redirect()->route('donasi')
                ->with('success', 'Terima kasih atas donasi Anda sebesar Rp ' . number_format($validated['jumlah'], 0, ',', '.'));

        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menyimpan donasi: ' . $e->getMessage(),
                ], 500);
            }

            return back()->withInput()
                ->withErrors(['error' => 'Gagal menyimpan donasi. Silakan coba lagi.']);
        }
    }
}
