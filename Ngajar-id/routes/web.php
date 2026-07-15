<?php

use Illuminate\Support\Facades\Route;


Route::get('/', [\App\Http\Controllers\Front\LandingController::class, 'index'])->name('home');

// Stats async endpoint ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â‚¬Å¡Ã‚Â¬ÃƒÂ¢Ã¢â€šÂ¬Ã‚Â dipanggil AJAX setelah halaman render (menghindari timeout)
Route::get('/landing/stats', [\App\Http\Controllers\Front\LandingController::class, 'stats'])->name('landing.stats');

// AI Chat - Pusat Bantuan Widget (Public, throttle 20 req/menit per IP)
Route::post('/ai-chat', [\App\Http\Controllers\Front\AiChatController::class, 'chat'])
    ->name('ai.chat')
    ->middleware('throttle:20,1');

// Rute Halaman Program (Data Real)
Route::get('/programs', [\App\Http\Controllers\Front\ProgramController::class, 'index'])->name('programs');
Route::post('/programs/{id}/join', [\App\Http\Controllers\Front\ProgramController::class, 'join'])->name('programs.join');

// Rute Halaman Mentor
Route::get('/mentors', [\App\Http\Controllers\Teacher\MentorController::class, 'index'])->name('mentors');

// Global Search (Public)
Route::get('/search', [\App\Http\Controllers\Api\V1\Front\SearchController::class, 'index'])->name('search');


// Rute Otentikasi (Login/Register)
Route::get('/privacy-policy', [\App\Http\Controllers\Front\PageController::class, 'privacyPolicy'])->name('privacy-policy');

Route::get('/terms-of-service', [\App\Http\Controllers\Front\PageController::class, 'termsOfService'])->name('terms-of-service');

Route::view('/login', 'auth.login')->name('login')->middleware('guest');
Route::post('/login', [\App\Http\Controllers\Auth\AuthController::class, 'login'])->middleware(['guest', 'throttle:5,1']);

Route::view('/register', 'auth.register')->name('register')->middleware('guest');
Route::post('/register', [\App\Http\Controllers\Auth\AuthController::class, 'register'])->middleware(['guest', 'throttle:3,1']);

// Email Verification Routes
Route::get('/verify-email/{token}', [\App\Http\Controllers\Auth\AuthController::class, 'verifyEmail'])->name('auth.verify-email');
Route::post('/resend-verification-email', [\App\Http\Controllers\Auth\AuthController::class, 'resendVerificationEmail'])->name('auth.resend-verification');

// Google Auth
Route::get('auth/google', [\App\Http\Controllers\Auth\AuthController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('auth/google/callback', [\App\Http\Controllers\Auth\AuthController::class, 'handleGoogleCallback']);

// Password Reset Routes
Route::get('password/reset', [\App\Http\Controllers\Auth\ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('password/email', [\App\Http\Controllers\Auth\ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('password/reset/{token}', [\App\Http\Controllers\Auth\ForgotPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('password/reset', [\App\Http\Controllers\Auth\ForgotPasswordController::class, 'reset'])->name('password.update');

// Logout - Show logout page with loading
Route::view('/logout', 'auth.logout')->middleware('auth')->name('logout.page');
Route::post('/logout', [\App\Http\Controllers\Auth\AuthController::class, 'logout'])->middleware('auth')->name('logout');

Route::get('/donasi', [\App\Http\Controllers\Transaction\DonationController::class, 'index'])->name('donasi');
Route::post('/donasi', [\App\Http\Controllers\Transaction\DonationController::class, 'store'])->name('donasi.store');
Route::post('/donasi/webhook', [\App\Http\Controllers\Transaction\DonationController::class, 'webhook'])->name('donasi.webhook');
Route::get('/donasi/payment/finish', [\App\Http\Controllers\Transaction\DonationController::class, 'paymentFinish'])->name('donasi.payment.finish');
Route::get('/donasi/riwayat', [\App\Http\Controllers\Transaction\DonationController::class, 'riwayat'])->name('donasi.riwayat');

Route::get('/campaigns', [\App\Http\Controllers\Front\CampaignController::class, 'index'])->name('campaigns.index');

Route::get('/campaigns/{slug}', [\App\Http\Controllers\Front\CampaignController::class, 'show'])->name('campaigns.show');

// Topup Token Routes
Route::post('/topup/create', [\App\Http\Controllers\Transaction\TopupController::class, 'create'])->middleware('auth')->name('topup.create');
Route::post('/topup/callback', [\App\Http\Controllers\Transaction\TopupController::class, 'callback'])->name('topup.callback');


Route::get('/tentang-kami', [\App\Http\Controllers\Front\LandingController::class, 'about'])->name('tentang-kami');

// --- RUTE DASHBOARD ---
// Dilindungi middleware auth (harus login) dan cek role
Route::middleware('auth')->group(function () {
    // --- PROFILE ROUTES ---
    Route::get('/profile', [\App\Http\Controllers\Auth\ProfileController::class, 'index'])->name('profile');
    Route::put('/profile/update', [\App\Http\Controllers\Auth\ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [\App\Http\Controllers\Auth\ProfileController::class, 'updatePassword'])->name('profile.password');

    // --- NOTIFICATIONS ROUTES ---
    Route::get('/notifications', [\App\Http\Controllers\NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/latest-json', [\App\Http\Controllers\NotificationController::class, 'latestJson'])->name('notifications.latest');
    Route::post('/notifications/{id}/read', [\App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [\App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('notifications.readAll');

    // Load separated route files
    require __DIR__ . '/web/student.php';
    require __DIR__ . '/web/teacher.php';
    require __DIR__ . '/web/admin.php';
});



