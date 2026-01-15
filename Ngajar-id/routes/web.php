<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    // TODO: Replace with actual database queries via Controller
    // e.g. \App\Models\User::where('role', 'murid')->count();
    $jumlah_murid = 1500;
    $jumlah_pengajar = 500;

    return view('welcome', compact('jumlah_murid', 'jumlah_pengajar'));
});

// Programs Page Route
Route::get('/programs', function () {
    $programs = [
        [
            'title' => 'Matematika Dasar SMA',
            'category' => 'Matematika',
            'level' => 'SMA Kelas 10',
            'image' => 'https://images.unsplash.com/photo-1635070041078-e363dbe005cb?auto=format&fit=crop&w=600&q=80',
            'description' => 'Pelajari konsep dasar aljabar, geometri, dan trigonometri untuk persiapan ujian sekolah.',
            'rating' => '4.8',
            'reviews' => 120,
            'students' => 450,
            'is_premium' => false
        ],
        [
            'title' => 'Bahasa Inggris Conversation',
            'category' => 'Bahasa',
            'level' => 'Umum',
            'image' => 'https://images.unsplash.com/photo-1543269865-cbf427effbad?auto=format&fit=crop&w=600&q=80',
            'description' => 'Tingkatkan kemampuan berbicara bahasa Inggris dengan metode praktis dan interaktif.',
            'rating' => '4.9',
            'reviews' => 85,
            'students' => 300,
            'is_premium' => true
        ],
        [
            'title' => 'Fisika: Hukum Newton',
            'category' => 'Sains',
            'level' => 'SMP Kelas 8',
            'image' => 'https://images.unsplash.com/photo-1636466497217-26a8cbeaf0aa?auto=format&fit=crop&w=600&q=80',
            'description' => 'Memahami hukum gerak Newton dengan eksperimen sederhana dan animasi.',
            'rating' => '4.7',
            'reviews' => 60,
            'students' => 200,
            'is_premium' => false
        ],
        [
            'title' => 'Pemrograman Web Dasar',
            'category' => 'Teknologi',
            'level' => 'SMK / Umum',
            'image' => 'https://images.unsplash.com/photo-1587620962725-abab7fe55159?auto=format&fit=crop&w=600&q=80',
            'description' => 'Belajar HTML, CSS, dan Javascript dasar untuk membuat website pertamamu.',
            'rating' => '4.9',
            'reviews' => 250,
            'students' => 800,
            'is_premium' => false
        ],
    ];
    return view('programs', compact('programs'));
})->name('programs');

// Mentors Page Route
Route::get('/mentors', function () {
    $mentors = [
        [
            'name' => 'Sarah Putri',
            'role' => 'Guru Matematika',
            'photo' => 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?auto=format&fit=crop&w=200&q=80',
            'subjects' => 'Matematika, Fisika',
            'university' => 'Universitas Pendidikan Indonesia',
            'rating' => '4.9',
            'reviews' => 45
        ],
        [
            'name' => 'Dimas Anggara',
            'role' => 'Tutor Bahasa Inggris',
            'photo' => 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?auto=format&fit=crop&w=200&q=80',
            'subjects' => 'Bahasa Inggris, TOEFL',
            'university' => 'Universitas Indonesia',
            'rating' => '4.8',
            'reviews' => 30
        ],
        [
            'name' => 'Rina Amalia',
            'role' => 'Mahasiswa Relawan',
            'photo' => 'https://images.unsplash.com/photo-1438761681033-6461ffad8d80?auto=format&fit=crop&w=200&q=80',
            'subjects' => 'Biologi, Kimia',
            'university' => 'Institut Teknologi Bandung',
            'rating' => '5.0',
            'reviews' => 20
        ],
        [
            'name' => 'Budi Santoso',
            'role' => 'Software Engineer',
            'photo' => 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?auto=format&fit=crop&w=200&q=80',
            'subjects' => 'Pemrograman, Logika',
            'university' => 'Binus University',
            'rating' => '4.9',
            'reviews' => 60
        ],
    ];
    return view('mentors', compact('mentors'));
})->name('mentors');

// Auth Routes
Route::view('/login', 'auth.login')->name('login')->middleware('guest');
Route::post('/login', [\App\Http\Controllers\AuthController::class, 'login'])->middleware('guest');

Route::view('/register', 'auth.register')->name('register')->middleware('guest');
Route::post('/register', [\App\Http\Controllers\AuthController::class, 'register'])->middleware('guest');

Route::post('/logout', [\App\Http\Controllers\AuthController::class, 'logout'])->middleware('auth')->name('logout');

Route::get('/donasi', function () {
    // Dummy Data for Donasi Page
    $total_donasi = 15000000;
    $riwayat_donasi = [
        ['nama' => 'Hamba Allah', 'jumlah' => 500000, 'tanggal' => '2025-01-10 10:00:00'],
        ['nama' => 'Budi Santoso', 'jumlah' => 100000, 'tanggal' => '2025-01-09 14:30:00'],
        ['nama' => 'Siti Aminah', 'jumlah' => 250000, 'tanggal' => '2025-01-08 09:15:00'],
    ];
    return view('donasi', compact('total_donasi', 'riwayat_donasi'));
})->name('donasi');

Route::view('/tentang-kami', 'tentang-kami')->name('tentang-kami');

// --- DASHBOARD ROUTES ---
// Protected by auth middleware and accessible based on user role

Route::middleware('auth')->group(function () {
    // Murid Dashboard
    Route::get('/murid/dashboard', [\App\Http\Controllers\DashboardController::class, 'muridDashboard'])
        ->name('murid.dashboard');

    // Pengajar Dashboard
    Route::get('/pengajar/dashboard', [\App\Http\Controllers\DashboardController::class, 'pengajarDashboard'])
        ->name('pengajar.dashboard');
});


