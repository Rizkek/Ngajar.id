<?php

use Illuminate\Support\Facades\Route;

Route::get('/test-db', function () {
    try {
        \Illuminate\Support\Facades\DB::connection()->getPdo();
        $db_name = \Illuminate\Support\Facades\DB::connection()->getDatabaseName();
        $driver = \Illuminate\Support\Facades\DB::connection()->getDriverName();
        $host = \Illuminate\Support\Facades\DB::getConfig('host');

        return "Connected successfully to database: <b>$db_name</b> via driver: <b>$driver</b> on host: <b>$host</b>";
    } catch (\Exception $e) {
        return "Could not connect to the database. Error: " . $e->getMessage();
    }
});
Route::get('/', [\App\Http\Controllers\LandingController::class, 'index'])->name('home');

// Rute Halaman Program (Data Real)
Route::get('/programs', [\App\Http\Controllers\ProgramController::class, 'index'])->name('programs');
Route::post('/programs/{id}/join', [\App\Http\Controllers\ProgramController::class, 'join'])->name('programs.join');

// Rute Halaman Mentor
Route::get('/mentors', [\App\Http\Controllers\MentorController::class, 'index'])->name('mentors');


// Rute Otentikasi (Login/Register)
Route::view('/login', 'auth.login')->name('login')->middleware('guest');
Route::post('/login', [\App\Http\Controllers\AuthController::class, 'login'])->middleware('guest');

Route::view('/register', 'auth.register')->name('register')->middleware('guest');
Route::post('/register', [\App\Http\Controllers\AuthController::class, 'register'])->middleware('guest');

Route::post('/logout', [\App\Http\Controllers\AuthController::class, 'logout'])->middleware('auth')->name('logout');

Route::get('/donasi', [\App\Http\Controllers\DonasiController::class, 'index'])->name('donasi');
Route::post('/donasi', [\App\Http\Controllers\DonasiController::class, 'store'])->name('donasi.store');
Route::post('/donasi/webhook', [\App\Http\Controllers\DonasiController::class, 'webhook'])->name('donasi.webhook');
Route::get('/donasi/payment/finish', [\App\Http\Controllers\DonasiController::class, 'paymentFinish'])->name('donasi.payment.finish');

Route::get('/tentang-kami', function () {
    // Data Tim Developer (Statis)
    $teams = [
        ['name' => 'Muhammad Abdul Azis', 'nim' => '2308937', 'role' => 'Project Manager', 'image' => 'img/azis.jpg'],
        ['name' => 'Muhammad Naufal Fadhlurrahman', 'nim' => '2310837', 'role' => 'Backend Developer', 'image' => 'img/Maman.jpg'],
        ['name' => 'Ihsan Abdurrahman Bi Amrillah', 'nim' => '2301308', 'role' => 'Frontend Developer', 'image' => 'img/ihsan.jpg'],
        ['name' => 'Syahdan Alfiansyah', 'nim' => '2305929', 'role' => 'UI/UX Designer', 'image' => 'img/Syahdan.jpg'],
        ['name' => 'Pujma Rizqy Fadetra', 'nim' => '2301130', 'role' => 'QA Engineer', 'image' => 'img/Pujma.jpg'],
    ];

    // Data Simulasi Transparansi Donasi (Real dari Database)
    $total_collected = \App\Models\Donasi::sum('jumlah');
    $donors_count = \App\Models\Donasi::count();
    $latest_donations = \App\Models\Donasi::orderBy('tanggal', 'desc')->take(5)->get();

    $donation_stats = [
        'total_collected' => $total_collected,
        'target' => 200000000, // Hardcoded Target
        'donors_count' => $donors_count,
        'allocation' => [
            ['label' => 'Server & Infrastruktur', 'percentage' => 40, 'color' => 'bg-blue-500'],
            ['label' => 'Insentif & Sertifikasi Relawan', 'percentage' => 30, 'color' => 'bg-teal-500'],
            ['label' => 'Pengembangan Modul', 'percentage' => 20, 'color' => 'bg-amber-500'],
            ['label' => 'Operasional & Marketing', 'percentage' => 10, 'color' => 'bg-purple-500'],
        ]
    ];

    // Data Top Relawan (Real dari tabel Users)
    // Ambil 3 pengajar acak yang aktif
    $top_relawan_db = \App\Models\User::where('role', 'pengajar')
        ->where('status', 'aktif')
        ->inRandomOrder()
        ->take(3)
        ->get();

    // Format data untuk view
    $top_relawan = $top_relawan_db->map(function ($user) {
        return [
            'name' => $user->name,
            'role' => 'Relawan Pengajar', // Default role name
            'hours' => rand(50, 150), // Mock hours for now
            'image' => 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&background=random'
        ];
    });

    return view('tentang-kami', compact('teams', 'donation_stats', 'top_relawan', 'latest_donations'));
})->name('tentang-kami');

// --- RUTE DASHBOARD ---
// Dilindungi middleware auth (harus login) dan cek role
Route::middleware('auth')->group(function () {
    // Dashboard Murid
    Route::get('/murid/dashboard', [\App\Http\Controllers\DashboardController::class, 'muridDashboard'])
        ->name('murid.dashboard');

    // Murid - Kelas Saya
    Route::get('/murid/kelas', [\App\Http\Controllers\DashboardController::class, 'muridKelas'])
        ->name('murid.kelas');

    // Murid - Materi
    Route::get('/murid/materi', [\App\Http\Controllers\DashboardController::class, 'muridMateri'])
        ->name('murid.materi');

    // Dashboard Pengajar
    Route::get('/pengajar/dashboard', [\App\Http\Controllers\DashboardController::class, 'pengajarDashboard'])
        ->name('pengajar.dashboard');

    // Pengajar - Kelas Saya
    Route::get('/pengajar/kelas', [\App\Http\Controllers\DashboardController::class, 'pengajarKelas'])
        ->name('pengajar.kelas');

    // Pengajar - Kelola Kelas (CRUD)
    Route::get('/pengajar/kelas/create', [\App\Http\Controllers\KelasController::class, 'create'])->name('pengajar.kelas.create');
    Route::post('/pengajar/kelas', [\App\Http\Controllers\KelasController::class, 'store'])->name('pengajar.kelas.store');
    Route::get('/pengajar/kelas/{id}/edit', [\App\Http\Controllers\KelasController::class, 'edit'])->name('pengajar.kelas.edit');
    Route::put('/pengajar/kelas/{id}', [\App\Http\Controllers\KelasController::class, 'update'])->name('pengajar.kelas.update');
    Route::delete('/pengajar/kelas/{id}', [\App\Http\Controllers\KelasController::class, 'destroy'])->name('pengajar.kelas.destroy');

    // Pengajar - Materi
    Route::get('/pengajar/materi', [\App\Http\Controllers\DashboardController::class, 'pengajarMateri'])
        ->name('pengajar.materi');

    // Dashboard Admin
    Route::get('/admin', [\App\Http\Controllers\AdminController::class, 'index'])
        ->name('admin.dashboard');

    // Ruang Kelas Live
    Route::get('/kelas/{id}/live', [\App\Http\Controllers\LiveClassController::class, 'join'])
        ->name('kelas.live');

    // Halaman Belajar (LMS)
    Route::get('/belajar/kelas/{kelas_id}/materi/{materi_id?}', [\App\Http\Controllers\BelajarController::class, 'show'])
        ->name('belajar.show');
});



