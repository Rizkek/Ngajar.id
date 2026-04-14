<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Kelas;
use App\Models\Modul;
use App\Models\Donasi;
use App\Models\Ulasan;
use App\Http\Traits\ApiResponse;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class LandingController extends Controller
{
    use ApiResponse;
    /**
     * Tampilkan halaman landing aplikasi.
     * Volunteers di-cache 1 jam agar tidak query Supabase setiap request.
     * ISR (Incremental Static Regeneration): Data diupdate setiap jam otomatis
     */
    public function index()
    {
        // ISR Strategy: Cache untuk 3600 detik (1 jam)
        // Setelah 1 jam, request pertama akan trigger regenerasi data, yang lain pakai stale data
        $volunteers = Cache::remember('landing_volunteers', 3600, function () {
            try {
                return User::where('role', '=', 'pengajar')
                    ->where('status', '=', 'aktif')
                    ->with('kelasAjar:kelas_id,pengajar_id,rating')
                    ->withAvg('kelasAjar', 'rating')
                    ->withCount('kelasAjar')
                    ->inRandomOrder()
                    ->take(4)
                    ->get();
            } catch (\Exception $e) {
                \Log::error('Landing volunteers query failed: ' . $e->getMessage());
                return collect([]);
            }
        });

        return view('welcome', compact('volunteers'));
    }

    /**
     * Endpoint AJAX untuk mengambil statistik landing page.
     * Semua angka statistik digabung dalam SATU query DB raw agar lebih efisien,
     * lalu di-cache 1 jam sehingga hit ke Supabase cukup sekali.
     *
     * API Endpoint: GET /api/v1/landing/stats
     */
    public function stats()
    {
        try {
            // Cache::remember() sudah handle has() + get() + put() dalam satu call.
            $stats = Cache::remember('landing_stats', 3600, function () {
                try {
                    // Gabungkan semua query COUNT/SUM dalam satu round-trip ke DB
                    $row = DB::selectOne("
                        SELECT
                            (SELECT COUNT(*) FROM users WHERE role = 'murid'    AND status = 'aktif') AS pelajar_active,
                            (SELECT COUNT(*) FROM users WHERE role = 'pengajar' AND status = 'aktif') AS relawan_active,
                            (SELECT COUNT(*) FROM kelas WHERE status = 'aktif')                        AS kelas_count,
                            (SELECT COALESCE(SUM(jumlah), 0) FROM donasi WHERE status = 'success')   AS total_donasi,
                            (SELECT COALESCE(AVG(rating), 5.0) FROM ulasans)                          AS avg_rating
                    ");

                    // Rating relawan: rata-rata dari avg rating per kelas
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
                        'total_students' => (int) ($row->pelajar_active ?? 0),
                        'total_teachers' => (int) ($row->relawan_active ?? 0),
                        'total_courses' => (int) ($row->kelas_count ?? 0),
                        'total_donated' => (float) ($row->total_donasi ?? 0),
                        'avg_course_rating' => number_format((float) ($row->avg_rating ?? 5.0), 1, '.', ''),
                        'avg_teacher_rating' => number_format((float) ($relawanRow->relawan_rating ?? 5.0), 1, '.', ''),
                    ];

                } catch (\Exception $e) {
                    return [
                        'total_students' => 0,
                        'total_teachers' => 0,
                        'total_courses' => 0,
                        'total_donated' => 0,
                        'avg_course_rating' => '5.0',
                        'avg_teacher_rating' => '5.0',
                    ];
                }
            });

            return $this->success($stats, 'Platform statistics retrieved successfully');

        } catch (\Exception $e) {
            return $this->serverError($e->getMessage());
        }
    }

    /**
     * Get featured volunteer/teacher list for landing page
     * API Endpoint: GET /api/v1/landing/volunteers
     */
    public function volunteers()
    {
        try {
            $volunteers = Cache::remember('landing_volunteers', 3600, function () {
                return User::where('role', '=', 'pengajar')
                    ->where('status', '=', 'aktif')
                    ->with('kelasAjar:kelas_id,pengajar_id,rating')
                    ->withAvg('kelasAjar', 'rating')
                    ->withCount('kelasAjar')
                    ->inRandomOrder()
                    ->take(6)
                    ->get();
            });

            return $this->success(
                UserResource::collection($volunteers),
                'Featured teachers retrieved successfully'
            );

        } catch (\Exception $e) {
            return $this->serverError($e->getMessage());
        }
    }

    /**
     * Get platform information
     * API Endpoint: GET /api/v1/landing/info
     */
    public function info()
    {
        try {
            $info = [
                'name' => config('app.name', 'Ngajar.ID'),
                'description' => 'Platform pembelajaran online terpercaya dengan ribuan kursus berkualitas dari instruktur berpengalaman.',
                'tagline' => 'Belajar Dimulai Dari Sini',
                'contact' => [
                    'email' => config('app.support_email', 'support@ngajar.id'),
                    'phone' => config('app.support_phone', '+62-'),
                    'address' => 'Indonesia',
                ],
                'social' => [
                    'instagram' => config('app.social_instagram', '#'),
                    'facebook' => config('app.social_facebook', '#'),
                    'twitter' => config('app.social_twitter', '#'),
                    'youtube' => config('app.social_youtube', '#'),
                ],
                'features' => [
                    'Ribuan kursus berkualitas',
                    'Instruktur profesional dan berpengalaman',
                    'Sertifikat terakui',
                    'Akses seumur hidup',
                    'Komunitas sesama pelajar',
                ],
                'version' => '1.0.0',
                'api_version' => 'v1',
            ];

            return $this->success($info, 'Platform information retrieved successfully');

        } catch (\Exception $e) {
            return $this->serverError($e->getMessage());
        }
    }
}
