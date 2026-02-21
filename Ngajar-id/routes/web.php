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

// Global Search (Public)
Route::get('/search', [\App\Http\Controllers\SearchController::class, 'index'])->name('search');


// Rute Otentikasi (Login/Register)
Route::get('/privacy-policy', function () {
    return view('privacy-policy');
})->name('privacy-policy');

Route::get('/terms-of-service', function () {
    return view('terms-of-service');
})->name('terms-of-service');

Route::view('/login', 'auth.login')->name('login')->middleware('guest');
Route::post('/login', [\App\Http\Controllers\AuthController::class, 'login'])->middleware('guest');

Route::view('/register', 'auth.register')->name('register')->middleware('guest');
Route::post('/register', [\App\Http\Controllers\AuthController::class, 'register'])->middleware('guest');

// Google Auth
Route::get('auth/google', [\App\Http\Controllers\AuthController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('auth/google/callback', [\App\Http\Controllers\AuthController::class, 'handleGoogleCallback']);

// Password Reset Routes
Route::get('password/reset', [\App\Http\Controllers\ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('password/email', [\App\Http\Controllers\ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('password/reset/{token}', [\App\Http\Controllers\ForgotPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('password/reset', [\App\Http\Controllers\ForgotPasswordController::class, 'reset'])->name('password.update');

// Logout - Show logout page with loading
Route::view('/logout', 'auth.logout')->middleware('auth')->name('logout.page');
Route::post('/logout', [\App\Http\Controllers\AuthController::class, 'logout'])->middleware('auth')->name('logout');

Route::get('/donasi', [\App\Http\Controllers\DonasiController::class, 'index'])->name('donasi');
Route::post('/donasi', [\App\Http\Controllers\DonasiController::class, 'store'])->name('donasi.store');
Route::post('/donasi/webhook', [\App\Http\Controllers\DonasiController::class, 'webhook'])->name('donasi.webhook');
Route::get('/donasi/payment/finish', [\App\Http\Controllers\DonasiController::class, 'paymentFinish'])->name('donasi.payment.finish');
Route::get('/donasi/riwayat', [\App\Http\Controllers\DonasiController::class, 'riwayat'])->name('donasi.riwayat');

// Topup Token Routes
Route::post('/topup/create', [\App\Http\Controllers\TopupController::class, 'create'])->middleware('auth')->name('topup.create');
Route::post('/topup/callback', [\App\Http\Controllers\TopupController::class, 'callback'])->name('topup.callback');


Route::get('/tentang-kami', function () {
    // Data Tim Developer (Statis)
    $teams = [
        ['name' => 'Muhammad Abdul Azis', 'nim' => '2308937', 'role' => 'Project Manager', 'image' => 'azis.jpg'],
        ['name' => 'Muhammad Naufal Fadhlurrahman', 'nim' => '2310837', 'role' => 'Backend Developer', 'image' => 'Maman.jpg'],
        ['name' => 'Ihsan Abdurrahman Bi Amrillah', 'nim' => '2301308', 'role' => 'Frontend Developer', 'image' => 'ihsan.jpg'],
        ['name' => 'Syahdan Alfiansyah', 'nim' => '2305929', 'role' => 'UI/UX Designer', 'image' => 'Syahdan.jpg'],
        ['name' => 'Pujma Rizqy Fadetra', 'nim' => '2301130', 'role' => 'QA Engineer', 'image' => 'Pujma.jpg'],
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
            ['label' => 'Server & Infrastruktur', 'percentage' => 40, 'color' => 'bg-brand-600'],
            ['label' => 'Insentif & Sertifikasi Relawan', 'percentage' => 30, 'color' => 'bg-secondary-500'],
            ['label' => 'Pengembangan Modul', 'percentage' => 20, 'color' => 'bg-brand-500'],
            ['label' => 'Operasional & Marketing', 'percentage' => 10, 'color' => 'bg-secondary-600'],
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
    // --- PROFILE ROUTES ---
    Route::get('/profile', [\App\Http\Controllers\ProfileController::class, 'index'])->name('profile');
    Route::put('/profile/update', [\App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [\App\Http\Controllers\ProfileController::class, 'updatePassword'])->name('profile.password');

    // Dashboard Murid
    Route::get('/murid/dashboard', [\App\Http\Controllers\DashboardController::class, 'muridDashboard'])
        ->name('murid.dashboard');

    // Murid - Kelas Saya
    Route::get('/murid/kelas', [\App\Http\Controllers\DashboardController::class, 'muridKelas'])
        ->name('murid.kelas');

    // Murid - Katalog & Join Kelas
    Route::get('/murid/katalog', [\App\Http\Controllers\CatalogController::class, 'index'])->name('murid.katalog');
    Route::post('/murid/katalog/{id}/join', [\App\Http\Controllers\CatalogController::class, 'join'])->name('murid.katalog.join');

    // Murid - Materi
    Route::get('/murid/materi', [\App\Http\Controllers\DashboardController::class, 'muridMateri'])
        ->name('murid.materi');
    Route::post('/murid/materi/{id}/beli', [\App\Http\Controllers\DashboardController::class, 'beliMateri'])
        ->name('murid.materi.beli');

    // Murid - Learning Paths
    Route::get('/murid/learning-paths', [\App\Http\Controllers\LearningPathController::class, 'myPaths'])
        ->name('murid.learning-paths.index');
    Route::get('/learning-paths', [\App\Http\Controllers\LearningPathController::class, 'index'])
        ->name('learning-paths.index');
    Route::get('/learning-paths/{id}', [\App\Http\Controllers\LearningPathController::class, 'show'])
        ->name('learning-paths.show');
    Route::post('/learning-paths/{id}/enroll', [\App\Http\Controllers\LearningPathController::class, 'enroll'])
        ->name('learning-paths.enroll');
    Route::get('/learning-paths/{id}/certificate', [\App\Http\Controllers\LearningPathController::class, 'downloadCertificate'])
        ->name('learning-paths.certificate');

    // Murid - Sertifikat (Agregasi)
    Route::get('/murid/sertifikat', [\App\Http\Controllers\DashboardController::class, 'muridSertifikat'])
        ->name('murid.sertifikat');

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

    // Pengajar - Materi (CRUD)
    Route::get('/pengajar/materi', [\App\Http\Controllers\DashboardController::class, 'pengajarMateri'])
        ->name('pengajar.materi');
    Route::get('/pengajar/materi/create', [\App\Http\Controllers\MateriController::class, 'create'])->name('pengajar.materi.create');
    Route::post('/pengajar/materi', [\App\Http\Controllers\MateriController::class, 'store'])->name('pengajar.materi.store');
    Route::get('/pengajar/materi/{id}/edit', [\App\Http\Controllers\MateriController::class, 'edit'])->name('pengajar.materi.edit');
    Route::put('/pengajar/materi/{id}', [\App\Http\Controllers\MateriController::class, 'update'])->name('pengajar.materi.update');
    Route::delete('/pengajar/materi/{id}', [\App\Http\Controllers\MateriController::class, 'destroy'])->name('pengajar.materi.destroy');

    // Pengajar - Download Sertifikat
    Route::get('/pengajar/sertifikat/download', function () {
        // TODO: Implement PDF generation untuk sertifikat
        return back()->with('info', 'Fitur download sertifikat akan segera tersedia!');
    })->name('pengajar.sertifikat.download');


    // Dashboard Admin
    Route::get('/admin', [\App\Http\Controllers\AdminController::class, 'index'])
        ->name('admin.dashboard');

    // Admin - Kelola Pengajar
    Route::get('/admin/pengajar', [\App\Http\Controllers\AdminUserController::class, 'pengajarIndex'])->name('admin.pengajar.index');
    Route::get('/admin/pengajar/{id}', [\App\Http\Controllers\AdminUserController::class, 'pengajarShow'])->name('admin.pengajar.show');
    Route::post('/admin/pengajar/{id}/status', [\App\Http\Controllers\AdminUserController::class, 'pengajarUpdateStatus'])->name('admin.pengajar.updateStatus');
    Route::delete('/admin/pengajar/{id}', [\App\Http\Controllers\AdminUserController::class, 'pengajarDestroy'])->name('admin.pengajar.destroy');

    // Admin - Kelola Murid
    Route::get('/admin/murid', [\App\Http\Controllers\AdminUserController::class, 'muridIndex'])->name('admin.murid.index');
    Route::get('/admin/murid/{id}', [\App\Http\Controllers\AdminUserController::class, 'muridShow'])->name('admin.murid.show');
    Route::post('/admin/murid/{id}/status', [\App\Http\Controllers\AdminUserController::class, 'muridUpdateStatus'])->name('admin.murid.updateStatus');
    Route::post('/admin/murid/{id}/token', [\App\Http\Controllers\AdminUserController::class, 'muridUpdateToken'])->name('admin.murid.updateToken');
    Route::post('/admin/murid/{id}/beasiswa', [\App\Http\Controllers\AdminUserController::class, 'muridUpdateBeasiswa'])->name('admin.murid.updateBeasiswa');
    Route::delete('/admin/murid/{id}', [\App\Http\Controllers\AdminUserController::class, 'muridDestroy'])->name('admin.murid.destroy');

    // Admin - Moderasi Kelas
    Route::get('/admin/kelas', [\App\Http\Controllers\AdminKelasController::class, 'index'])->name('admin.kelas.index');
    Route::get('/admin/kelas/{id}', [\App\Http\Controllers\AdminKelasController::class, 'show'])->name('admin.kelas.show');
    Route::post('/admin/kelas/{id}/status', [\App\Http\Controllers\AdminKelasController::class, 'updateStatus'])->name('admin.kelas.updateStatus');
    Route::delete('/admin/kelas/{id}', [\App\Http\Controllers\AdminKelasController::class, 'destroy'])->name('admin.kelas.destroy');

    // Admin - Laporan & Analytics (FASE 2)
    Route::get('/admin/laporan/donasi', [\App\Http\Controllers\AdminReportController::class, 'donasiIndex'])->name('admin.laporan.donasi');
    Route::get('/admin/laporan/donasi/export', [\App\Http\Controllers\AdminReportController::class, 'donasiExport'])->name('admin.laporan.donasi.export');

    Route::get('/admin/laporan/revenue', [\App\Http\Controllers\AdminReportController::class, 'revenueIndex'])->name('admin.laporan.revenue');
    Route::get('/admin/laporan/revenue/export', [\App\Http\Controllers\AdminReportController::class, 'revenueExport'])->name('admin.laporan.revenue.export');

    // Admin - Learning Paths Management
    Route::get('/admin/learning-paths', [\App\Http\Controllers\AdminLearningPathController::class, 'index'])->name('admin.learning-paths.index');
    Route::get('/admin/learning-paths/create', [\App\Http\Controllers\AdminLearningPathController::class, 'create'])->name('admin.learning-paths.create');
    Route::post('/admin/learning-paths', [\App\Http\Controllers\AdminLearningPathController::class, 'store'])->name('admin.learning-paths.store');
    Route::get('/admin/learning-paths/{id}', [\App\Http\Controllers\AdminLearningPathController::class, 'show'])->name('admin.learning-paths.show');
    Route::get('/admin/learning-paths/{id}/edit', [\App\Http\Controllers\AdminLearningPathController::class, 'edit'])->name('admin.learning-paths.edit');
    Route::put('/admin/learning-paths/{id}', [\App\Http\Controllers\AdminLearningPathController::class, 'update'])->name('admin.learning-paths.update');
    Route::delete('/admin/learning-paths/{id}', [\App\Http\Controllers\AdminLearningPathController::class, 'destroy'])->name('admin.learning-paths.destroy');
    Route::post('/admin/learning-paths/{id}/attach', [\App\Http\Controllers\AdminLearningPathController::class, 'attachKelas'])->name('admin.learning-paths.attach');
    Route::delete('/admin/learning-paths/{id}/detach/{kelasId}', [\App\Http\Controllers\AdminLearningPathController::class, 'detachKelas'])->name('admin.learning-paths.detach');

    // Admin - Kategori Management
    Route::get('/admin/kategori', [\App\Http\Controllers\AdminKategoriController::class, 'index'])->name('admin.kategori.index');
    Route::post('/admin/kategori/bulk-update', [\App\Http\Controllers\AdminKategoriController::class, 'updateBulk'])->name('admin.kategori.bulk-update');
    Route::get('/admin/kategori/{kategori}', [\App\Http\Controllers\AdminKategoriController::class, 'showByKategori'])->name('admin.kategori.show');

    // Admin - Materi Moderation
    Route::get('/admin/materi', [\App\Http\Controllers\AdminMateriController::class, 'index'])->name('admin.materi.index');
    Route::get('/admin/materi/{id}', [\App\Http\Controllers\AdminMateriController::class, 'show'])->name('admin.materi.show');
    Route::put('/admin/materi/{id}', [\App\Http\Controllers\AdminMateriController::class, 'update'])->name('admin.materi.update');
    Route::delete('/admin/materi/{id}', [\App\Http\Controllers\AdminMateriController::class, 'destroy'])->name('admin.materi.destroy');

    // Admin - Enhanced Donation Management
    Route::get('/admin/donasi', [\App\Http\Controllers\AdminDonasiController::class, 'index'])->name('admin.donasi.index');
    Route::get('/admin/donasi/{id}', [\App\Http\Controllers\AdminDonasiController::class, 'show'])->name('admin.donasi.show');
    Route::post('/admin/donasi/{id}/status', [\App\Http\Controllers\AdminDonasiController::class, 'updateStatus'])->name('admin.donasi.updateStatus');
    Route::post('/admin/donasi/{id}/refund', [\App\Http\Controllers\AdminDonasiController::class, 'refund'])->name('admin.donasi.refund');
    Route::delete('/admin/donasi/{id}', [\App\Http\Controllers\AdminDonasiController::class, 'destroy'])->name('admin.donasi.destroy');

    // Admin - Notification Broadcast Center
    Route::get('/admin/notifications', [\App\Http\Controllers\AdminNotificationController::class, 'index'])->name('admin.notifications.index');
    Route::get('/admin/notifications/create', [\App\Http\Controllers\AdminNotificationController::class, 'create'])->name('admin.notifications.create');
    Route::post('/admin/notifications/send', [\App\Http\Controllers\AdminNotificationController::class, 'send'])->name('admin.notifications.send');
    Route::post('/admin/notifications/live-class', [\App\Http\Controllers\AdminNotificationController::class, 'sendLiveClass'])->name('admin.notifications.sendLiveClass');

    // Admin - Settings & Configuration
    Route::get('/admin/settings', [\App\Http\Controllers\AdminSettingsController::class, 'index'])->name('admin.settings.index');
    Route::post('/admin/settings/general', [\App\Http\Controllers\AdminSettingsController::class, 'updateGeneral'])->name('admin.settings.updateGeneral');
    Route::post('/admin/settings/social', [\App\Http\Controllers\AdminSettingsController::class, 'updateSocial'])->name('admin.settings.updateSocial');
    Route::post('/admin/settings/payment', [\App\Http\Controllers\AdminSettingsController::class, 'updatePayment'])->name('admin.settings.updatePayment');
    Route::post('/admin/settings/rules', [\App\Http\Controllers\AdminSettingsController::class, 'updateRules'])->name('admin.settings.updateRules');

    // Ruang Kelas Live
    Route::get('/kelas/{id}/live', [\App\Http\Controllers\LiveClassController::class, 'join'])
        ->name('kelas.live');

    // Halaman Belajar (LMS)
    Route::get('/belajar/kelas/{kelas_id}/materi/{materi_id?}', [\App\Http\Controllers\BelajarController::class, 'show'])
        ->name('belajar.show');

    // Mark Materi as Complete (Ajax)
    Route::post('/belajar/materi/{materi_id}/complete', [\App\Http\Controllers\BelajarController::class, 'complete'])
        ->name('belajar.complete');

    // Fitur Tambahan LMS
    Route::post('/belajar/kelas/{id}/ulasan', [\App\Http\Controllers\BelajarController::class, 'storeUlasan'])->name('belajar.ulasan.store');
    Route::post('/belajar/kelas/{id}/diskusi', [\App\Http\Controllers\BelajarController::class, 'storeDiskusi'])->name('belajar.diskusi.store');
    Route::post('/belajar/kelas/{id}/catatan', [\App\Http\Controllers\BelajarController::class, 'storeCatatan'])->name('belajar.catatan.store');

    // Test Notification (Dev only)
    Route::post('/admin/notifications/send-live', function (\Illuminate\Http\Request $request) {
        if (!auth()->user()->isAdmin())
            abort(403);
        $kelas = \App\Models\Kelas::findOrFail($request->kelas_id);

        // Kirim notifikasi ke semua murid yang ikut kelas ini (via database notification)
        $muridIds = \Illuminate\Support\Facades\DB::table('kelas_peserta')
            ->where('kelas_id', $kelas->kelas_id)
            ->pluck('siswa_id');

        $murids = \App\Models\User::whereIn('user_id', $muridIds)->get();
        \Illuminate\Support\Facades\Notification::send($murids, new \App\Notifications\LiveClassStarted($kelas));

        return back()->with('success', 'Notifikasi Live Class dikirim!');
    })->name('admin.notifications.send_live');
});
