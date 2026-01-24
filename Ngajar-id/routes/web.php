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
Route::get('/', function () {
    // TODO: Replace with actual database queries via Controller
    // e.g. \App\Models\User::where('role', 'murid')->count();
    $jumlah_murid = 1500;
    $jumlah_pengajar = 500;

    return view('welcome', compact('jumlah_murid', 'jumlah_pengajar'));
});

// Programs Page Route
// Programs Page Route (Real Data)
Route::get('/programs', [\App\Http\Controllers\ProgramController::class, 'index'])->name('programs');
Route::post('/programs/{id}/join', [\App\Http\Controllers\ProgramController::class, 'join'])->name('programs.join');

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

    // Murid - Kelas Saya
    Route::get('/murid/kelas', [\App\Http\Controllers\DashboardController::class, 'muridKelas'])
        ->name('murid.kelas');

    // Murid - Materi
    Route::get('/murid/materi', [\App\Http\Controllers\DashboardController::class, 'muridMateri'])
        ->name('murid.materi');

    // Pengajar Dashboard
    Route::get('/pengajar/dashboard', [\App\Http\Controllers\DashboardController::class, 'pengajarDashboard'])
        ->name('pengajar.dashboard');

    // Pengajar - Kelas Saya
    Route::get('/pengajar/kelas', [\App\Http\Controllers\DashboardController::class, 'pengajarKelas'])
        ->name('pengajar.kelas');

    // Pengajar - Kelas CRUD
    Route::get('/pengajar/kelas/create', [\App\Http\Controllers\KelasController::class, 'create'])->name('pengajar.kelas.create');
    Route::post('/pengajar/kelas', [\App\Http\Controllers\KelasController::class, 'store'])->name('pengajar.kelas.store');
    Route::get('/pengajar/kelas/{id}/edit', [\App\Http\Controllers\KelasController::class, 'edit'])->name('pengajar.kelas.edit');
    Route::put('/pengajar/kelas/{id}', [\App\Http\Controllers\KelasController::class, 'update'])->name('pengajar.kelas.update');
    Route::delete('/pengajar/kelas/{id}', [\App\Http\Controllers\KelasController::class, 'destroy'])->name('pengajar.kelas.destroy');

    // Pengajar - Materi
    Route::get('/pengajar/materi', [\App\Http\Controllers\DashboardController::class, 'pengajarMateri'])
        ->name('pengajar.materi');

    // Admin Dashboard
    Route::get('/admin', [\App\Http\Controllers\AdminController::class, 'index'])
        ->name('admin.dashboard');

    // Live Class Room
    Route::get('/kelas/{id}/live', [\App\Http\Controllers\LiveClassController::class, 'join'])
        ->name('kelas.live');

    // Halaman Belajar (LMS)
    Route::get('/belajar/kelas/{kelas_id}/materi/{materi_id?}', [\App\Http\Controllers\BelajarController::class, 'show'])
        ->name('belajar.show');
});



