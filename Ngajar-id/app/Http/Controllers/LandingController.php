<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Modul;
use App\Models\Donasi;
use App\Models\Ulasan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class LandingController extends Controller
{
    /**
     * Tampilkan halaman landing aplikasi.
     * Volunteers di-cache 1 jam agar tidak query Supabase setiap request.
     */
    public function index()
    {
        // Cache diperpanjang ke 3600 detik (1 jam) untuk mempercepat dev lokal.
        // Cukup pakai Cache::remember() langsung — tidak perlu Cache::has() terpisah.
        $volunteers = Cache::remember('landing_volunteers', 3600, function () {
            try {
                return User::pengajar()
                    ->aktif()
                    ->withAvg('kelasAjar', 'rating')
                    ->inRandomOrder()
                    ->take(4)
                    ->get();
            } catch (\Exception $e) {
                return collect([]);
            }
        });

        return view('welcome', compact('volunteers'));
    }

    /**
     * Endpoint AJAX untuk mengambil statistik landing page.
     * Semua angka statistik digabung dalam SATU query DB raw agar lebih efisien,
     * lalu di-cache 1 jam sehingga hit ke Supabase cukup sekali.
     */
    public function stats()
    {
        // Cache::remember() sudah handle has() + get() + put() dalam satu call.
        // Naikkan ke 3600 detik (1 jam) agar koneksi Supabase yang lambat
        // hanya terjadi pertama kali, request berikutnya langsung dari file cache.
        $stats = Cache::remember('landing_stats', 3600, function () {
            try {
                // Gabungkan semua query COUNT/SUM dalam satu round-trip ke DB
                $row = DB::selectOne("
                    SELECT
                        (SELECT COUNT(*) FROM users WHERE role = 'murid'    AND status = 'aktif') AS pelajar_active,
                        (SELECT COUNT(*) FROM users WHERE role = 'pengajar' AND status = 'aktif') AS relawan_active,
                        (SELECT COUNT(*) FROM modul)                                               AS modul_count,
                        (SELECT COALESCE(SUM(jumlah), 0) FROM donasi)                             AS total_donasi,
                        (SELECT COALESCE(AVG(rating), 5.0) FROM ulasans)                          AS avg_rating
                ");

                // Rating relawan: rata-rata dari avg rating per kelas yang dikelola pengajar aktif
                // Query terpisah karena butuh JOIN + GROUP BY, tapi tetap 1 round-trip
                $relawanRow = DB::selectOne("
                    SELECT COALESCE(AVG(avg_per_kelas), 5.0) AS relawan_rating
                    FROM (
                        SELECT AVG(u.rating) AS avg_per_kelas
                        FROM kelas k
                        JOIN ulasans u ON u.kelas_id = k.kelas_id
                        JOIN users usr ON usr.user_id = k.pengajar_id AND usr.role = 'pengajar' AND usr.status = 'aktif'
                        GROUP BY k.kelas_id
                    ) sub
                ");

                return [
                    'pelajar_active' => (int) ($row->pelajar_active ?? 0),
                    'relawan_active' => (int) ($row->relawan_active ?? 0),
                    'modul_count' => (int) ($row->modul_count ?? 0),
                    'total_donasi' => (float) ($row->total_donasi ?? 0),
                    'rating' => number_format((float) ($row->avg_rating ?? 5.0), 1, '.', ''),
                    'relawan_rating' => number_format((float) ($relawanRow->relawan_rating ?? 5.0), 1, '.', ''),
                ];

            } catch (\Exception $e) {
                // Fallback jika DB tidak bisa diakses
                return [
                    'pelajar_active' => 0,
                    'relawan_active' => 0,
                    'modul_count' => 0,
                    'total_donasi' => 0,
                    'rating' => '5.0',
                    'relawan_rating' => '5.0',
                ];
            }
        });

        return response()->json($stats);
    }
}
