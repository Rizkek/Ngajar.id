<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;

use App\Models\User;
use App\Models\Course;
use App\Models\Module;
use App\Models\Donation;
use App\Models\Review;
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
    /**
     * Tampilkan halaman landing aplikasi.
     * Semua data dinamis dari DB, di-cache 1 jam untuk performa.
     */
    public function index()
    {
        // ─── Featured Volunteers/Mentors ───────────────────────────────────
        $volunteers = Cache::remember('landing_volunteers', 3600, function () {
            try {
                return User::where('role', '=', 'pengajar')
                    ->where('status', '=', 'aktif')
                    ->withAvg('kelasAjar', 'rating')
                    ->withCount('kelasAjar')
                    ->limit(20)
                    ->get()
                    ->shuffle()
                    ->take(6);
            } catch (\Exception $e) {
                \Log::error('Landing volunteers query failed: ' . $e->getMessage());
                return collect([]);
            }
        });

        // ─── Featured Courses ──────────────────────────────────────────────
        $featuredCourses = Cache::remember('landing_featured_courses', 3600, function () {
            try {
                return Course::where('status', 'aktif')
                    ->withAvg('ulasans', 'rating')
                    ->withCount('ulasans')
                    ->withCount('peserta')
                    ->with('pengajar:user_id,name')
                    ->withCount('materi')
                    ->limit(20)
                    ->get()
                    ->shuffle()
                    ->take(6);
            } catch (\Exception $e) {
                \Log::error('Landing featured courses query failed: ' . $e->getMessage());
                return collect([]);
            }
        });

        // ─── Categories (Distinct dari DB) ────────────────────────────────
        $categories = Cache::remember('landing_categories', 3600, function () {
            try {
                return Course::where('status', 'aktif')
                    ->whereNotNull('kategori')
                    ->select('kategori')
                    ->distinct()
                    ->orderBy('kategori')
                    ->pluck('kategori');
            } catch (\Exception $e) {
                return collect([]);
            }
        });

        // ─── Testimonials (Dinamis — hanya tampil jika ada data nyata) ────
        $testimonials = Cache::remember('landing_testimonials', 3600, function () {
            try {
                return Review::with(['user:user_id,name', 'kelas:kelas_id,judul'])
                    ->where('rating', '>=', 4)
                    ->whereNotNull('ulasan')
                    ->where('ulasan', '!=', '')
                    ->limit(20)
                    ->get()
                    ->shuffle()
                    ->take(6);
            } catch (\Exception $e) {
                \Log::error('Landing testimonials query failed: ' . $e->getMessage());
                return collect([]);
            }
        });

        return view('welcome', compact(
            'volunteers',
            'featuredCourses',
            'categories',
            'testimonials'
        ));
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
                    ->limit(20)
                    ->get()
                    ->shuffle()
                    ->take(6);
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

    /**
     * Tampilkan halaman tentang kami.
     * Menggunakan cache 1 jam untuk performa.
     */
    public function about()
    {
        // Data Tim Developer (Statis)
        $teams = [
            ['name' => 'Muhammad Abdul Azis', 'role' => 'Project Manager', 'image' => 'azis.jpg'],
            ['name' => 'Muhammad Naufal Fadhlurrahman', 'role' => 'Backend Developer', 'image' => 'Maman.jpg'],
            ['name' => 'Ihsan Abdurrahman Bi Amrillah', 'role' => 'Frontend Developer', 'image' => 'ihsan.jpg'],
            ['name' => 'Syahdan Alfiansyah', 'role' => 'UI/UX Designer', 'image' => 'Syahdan.jpg'],
            ['name' => 'Pujma Rizqy Fadetra', 'role' => 'QA Engineer', 'image' => 'Pujma.jpg'],
        ];

        // Data Simulasi Transparansi Donasi (Real dari Database) - Cached 1 jam
        $donationData = Cache::remember('about_donation_data', 3600, function () {
            return [
                'total_collected' => \App\Models\Donation::sum('jumlah'),
                'donors_count' => \App\Models\Donation::count(),
                'latest_donations' => \App\Models\Donation::orderBy('tanggal', 'desc')->take(5)->get()
            ];
        });

        $donation_stats = [
            'total_collected' => $donationData['total_collected'],
            'target' => 200000000, // Hardcoded Target
            'donors_count' => $donationData['donors_count'],
            'allocation' => [
                ['label' => 'Server & Infrastruktur', 'percentage' => 40, 'color' => 'bg-brand-600'],
                ['label' => 'Insentif & Sertifikasi Relawan', 'percentage' => 30, 'color' => 'bg-secondary-500'],
                ['label' => 'Pengembangan Modul', 'percentage' => 20, 'color' => 'bg-brand-500'],
                ['label' => 'Operasional & Marketing', 'percentage' => 10, 'color' => 'bg-secondary-600'],
            ]
        ];

        // Data Top Relawan (Real dari tabel Users) - Cached 1 jam
        $top_relawan = Cache::remember('about_top_relawan', 3600, function () {
            // Ambil 10 pengajar aktif, lalu acak di memory dan ambil 3
            $top_relawan_db = \App\Models\User::where('role', 'pengajar')
                ->where('status', 'aktif')
                ->limit(10)
                ->get()
                ->shuffle()
                ->take(3);

            return $top_relawan_db->map(function ($user) {
                return [
                    'name' => $user->name,
                    'role' => 'Relawan Pengajar', // Default role name
                    'hours' => rand(50, 150), // Mock hours for now
                    'image' => 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&background=random'
                ];
            });
        });

        return view('tentang-kami', [
            'teams' => $teams,
            'donation_stats' => $donation_stats,
            'top_relawan' => $top_relawan,
            'latest_donations' => $donationData['latest_donations']
        ]);
    }
}


