<?php

use Illuminate\Support\Facades\Route;

// Dashboard Admin
Route::get('/admin', [\App\Http\Controllers\Admin\AdminController::class, 'index'])
    ->name('admin.dashboard');

// Admin - Kelola Pengajar
Route::get('/admin/pengajar', [\App\Http\Controllers\Admin\AdminUserController::class, 'teacherIndex'])->name('admin.pengajar.index');
Route::get('/admin/pengajar/{id}', [\App\Http\Controllers\Admin\AdminUserController::class, 'show'])->name('admin.pengajar.show');
Route::post('/admin/pengajar/{id}/status', [\App\Http\Controllers\Admin\AdminUserController::class, 'updateStatus'])->name('admin.pengajar.updateStatus');
Route::delete('/admin/pengajar/{id}', [\App\Http\Controllers\Admin\AdminUserController::class, 'destroy'])->name('admin.pengajar.destroy');

// Admin - Kelola Murid
Route::get('/admin/murid', [\App\Http\Controllers\Admin\AdminUserController::class, 'studentIndex'])->name('admin.murid.index');
Route::get('/admin/murid/{id}', [\App\Http\Controllers\Admin\AdminUserController::class, 'show'])->name('admin.murid.show');
Route::post('/admin/murid/{id}/status', [\App\Http\Controllers\Admin\AdminUserController::class, 'updateStatus'])->name('admin.murid.updateStatus');
Route::post('/admin/murid/{id}/token', [\App\Http\Controllers\Admin\AdminUserController::class, 'adjustToken'])->name('admin.murid.updateToken');
Route::post('/admin/murid/{id}/beasiswa', [\App\Http\Controllers\Admin\AdminUserController::class, 'grantScholarship'])->name('admin.murid.updateBeasiswa');
Route::delete('/admin/murid/{id}', [\App\Http\Controllers\Admin\AdminUserController::class, 'destroy'])->name('admin.murid.destroy');

// Admin - Moderasi Kelas
Route::get('/admin/courses', [\App\Http\Controllers\Admin\AdminCourseController::class, 'index'])->name('admin.courses.index');
Route::get('/admin/courses/{id}', [\App\Http\Controllers\Admin\AdminCourseController::class, 'show'])->name('admin.courses.show');
Route::post('/admin/courses/{id}/status', [\App\Http\Controllers\Admin\AdminCourseController::class, 'updateStatus'])->name('admin.courses.updateStatus');
Route::delete('/admin/courses/{id}', [\App\Http\Controllers\Admin\AdminCourseController::class, 'destroy'])->name('admin.courses.destroy');

// Admin - Laporan & Analytics (FASE 2)
Route::get('/admin/laporan/donasi', [\App\Http\Controllers\Admin\AdminReportController::class, 'donasiIndex'])->name('admin.laporan.donasi');
Route::get('/admin/laporan/donasi/export', [\App\Http\Controllers\Admin\AdminReportController::class, 'donasiExport'])->name('admin.laporan.donasi.export');

Route::get('/admin/laporan/revenue', [\App\Http\Controllers\Admin\AdminReportController::class, 'revenueIndex'])->name('admin.laporan.revenue');
Route::get('/admin/laporan/revenue/export', [\App\Http\Controllers\Admin\AdminReportController::class, 'revenueExport'])->name('admin.laporan.revenue.export');

// Admin - Learning Paths Management
Route::get('/admin/learning-paths', [\App\Http\Controllers\Admin\AdminLearningPathController::class, 'index'])->name('admin.learning-paths.index');
Route::get('/admin/learning-paths/create', [\App\Http\Controllers\Admin\AdminLearningPathController::class, 'create'])->name('admin.learning-paths.create');
Route::post('/admin/learning-paths', [\App\Http\Controllers\Admin\AdminLearningPathController::class, 'store'])->name('admin.learning-paths.store');
Route::get('/admin/learning-paths/{id}', [\App\Http\Controllers\Admin\AdminLearningPathController::class, 'show'])->name('admin.learning-paths.show');
Route::get('/admin/learning-paths/{id}/edit', [\App\Http\Controllers\Admin\AdminLearningPathController::class, 'edit'])->name('admin.learning-paths.edit');
Route::put('/admin/learning-paths/{id}', [\App\Http\Controllers\Admin\AdminLearningPathController::class, 'update'])->name('admin.learning-paths.update');
Route::delete('/admin/learning-paths/{id}', [\App\Http\Controllers\Admin\AdminLearningPathController::class, 'destroy'])->name('admin.learning-paths.destroy');
Route::post('/admin/learning-paths/{id}/attach', [\App\Http\Controllers\Admin\AdminLearningPathController::class, 'attachKelas'])->name('admin.learning-paths.attach');
Route::delete('/admin/learning-paths/{id}/detach/{kelasId}', [\App\Http\Controllers\Admin\AdminLearningPathController::class, 'detachKelas'])->name('admin.learning-paths.detach');

// Admin - Kategori Management
Route::get('/admin/categories', [\App\Http\Controllers\Admin\AdminCategoryController::class, 'index'])->name('admin.categories.index');
Route::post('/admin/categories/bulk-update', [\App\Http\Controllers\Admin\AdminCategoryController::class, 'updateBulk'])->name('admin.categories.bulk-update');
Route::get('/admin/categories/{kategori}', [\App\Http\Controllers\Admin\AdminCategoryController::class, 'showByKategori'])->name('admin.categories.show');

// Admin - Materi Moderation
Route::get('/admin/lessons', [\App\Http\Controllers\Admin\AdminLessonController::class, 'index'])->name('admin.lessons.index');
Route::get('/admin/lessons/{id}', [\App\Http\Controllers\Admin\AdminLessonController::class, 'show'])->name('admin.lessons.show');
Route::put('/admin/lessons/{id}', [\App\Http\Controllers\Admin\AdminLessonController::class, 'update'])->name('admin.lessons.update');
Route::delete('/admin/lessons/{id}', [\App\Http\Controllers\Admin\AdminLessonController::class, 'destroy'])->name('admin.lessons.destroy');

// Admin - Enhanced Donation Management
Route::get('/admin/donations', [\App\Http\Controllers\Admin\AdminDonationController::class, 'index'])->name('admin.donations.index');
Route::get('/admin/donations/{id}', [\App\Http\Controllers\Admin\AdminDonationController::class, 'show'])->name('admin.donations.show');
Route::post('/admin/donations/{id}/status', [\App\Http\Controllers\Admin\AdminDonationController::class, 'updateStatus'])->name('admin.donations.updateStatus');
Route::post('/admin/donations/{id}/refund', [\App\Http\Controllers\Admin\AdminDonationController::class, 'refund'])->name('admin.donations.refund');
Route::delete('/admin/donations/{id}', [\App\Http\Controllers\Admin\AdminDonationController::class, 'destroy'])->name('admin.donations.destroy');

// Admin - Notification Broadcast Center
Route::get('/admin/notifications', [\App\Http\Controllers\Admin\AdminNotificationController::class, 'index'])->name('admin.notifications.index');
Route::get('/admin/notifications/create', [\App\Http\Controllers\Admin\AdminNotificationController::class, 'create'])->name('admin.notifications.create');
Route::post('/admin/notifications/send', [\App\Http\Controllers\Admin\AdminNotificationController::class, 'send'])->name('admin.notifications.send');
Route::post('/admin/notifications/live-class', [\App\Http\Controllers\Admin\AdminNotificationController::class, 'sendLiveClass'])->name('admin.notifications.sendLiveClass');
Route::post('/admin/notifications/send-live', [\App\Http\Controllers\Admin\AdminNotificationController::class, 'sendLiveClass'])->name('admin.notifications.send_live');

// Admin - Settings & Configuration
Route::get('/admin/settings', [\App\Http\Controllers\Admin\AdminSettingsController::class, 'index'])->name('admin.settings.index');
Route::post('/admin/settings/general', [\App\Http\Controllers\Admin\AdminSettingsController::class, 'updateGeneral'])->name('admin.settings.updateGeneral');
Route::post('/admin/settings/social', [\App\Http\Controllers\Admin\AdminSettingsController::class, 'updateSocial'])->name('admin.settings.updateSocial');
Route::post('/admin/settings/payment', [\App\Http\Controllers\Admin\AdminSettingsController::class, 'updatePayment'])->name('admin.settings.updatePayment');
Route::post('/admin/settings/rules', [\App\Http\Controllers\Admin\AdminSettingsController::class, 'updateRules'])->name('admin.settings.updateRules');
