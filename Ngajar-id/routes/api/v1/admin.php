<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\Admin\AdminController;
use App\Http\Controllers\Api\V1\Admin\AdminUserController;
use App\Http\Controllers\Api\V1\Admin\AdminCourseController;
use App\Http\Controllers\Api\V1\Admin\AdminLessonController;
use App\Http\Controllers\Api\V1\Admin\AdminDonationController;
use App\Http\Controllers\Api\V1\Admin\AdminReportController;
use App\Http\Controllers\Api\V1\Admin\AdminNotificationController;
use App\Http\Controllers\Api\V1\Admin\AdminSettingsController;
use App\Http\Controllers\Api\V1\Admin\AdminLearningPathController;

// Dashboard
Route::get('/dashboard', [AdminController::class, 'index']);

// User Management
Route::prefix('users')->group(function () {
    Route::get('/', [AdminUserController::class, 'index']);
    Route::get('/{id}', [AdminUserController::class, 'show']);
    Route::put('/{id}', [AdminUserController::class, 'update']);
    Route::post('/{id}/status', [AdminUserController::class, 'updateStatus']);
    Route::delete('/{id}', [AdminUserController::class, 'destroy']);

    // Teacher management
    Route::get('/teachers/list', [AdminUserController::class, 'teacherIndex']);
    Route::post('/{id}/verify-teacher', [AdminUserController::class, 'verifyTeacher']);
    Route::post('/{id}/revoke-teacher', [AdminUserController::class, 'revokeTeacher']);

    // Student management
    Route::get('/students/list', [AdminUserController::class, 'studentIndex']);
    Route::post('/{id}/scholarship', [AdminUserController::class, 'grantScholarship']);
    Route::post('/{id}/token', [AdminUserController::class, 'adjustToken']);
});

// Class Moderation
Route::prefix('classes')->group(function () {
    Route::get('/', [AdminCourseController::class, 'index']);
    Route::get('/{id}', [AdminCourseController::class, 'show']);
    Route::post('/{id}/approve', [AdminCourseController::class, 'approve']);
    Route::post('/{id}/reject', [AdminCourseController::class, 'reject']);
    Route::post('/{id}/archive', [AdminCourseController::class, 'archive']);
    Route::delete('/{id}', [AdminCourseController::class, 'destroy']);
    Route::post('/{id}/flag', [AdminCourseController::class, 'flag']);
});

// Material Moderation
Route::prefix('materials')->group(function () {
    Route::get('/', [AdminLessonController::class, 'index']);
    Route::get('/{id}', [AdminLessonController::class, 'show']);
    Route::put('/{id}', [AdminLessonController::class, 'update']);
    Route::delete('/{id}', [AdminLessonController::class, 'destroy']);
    Route::post('/{id}/verify', [AdminLessonController::class, 'verify']);
});

// Donation Management
Route::prefix('donations')->group(function () {
    Route::get('/', [AdminDonationController::class, 'index']);
    Route::get('/{id}', [AdminDonationController::class, 'show']);
    Route::post('/{id}/verify', [AdminDonationController::class, 'verify']);
    Route::post('/{id}/refund', [AdminDonationController::class, 'refund']);
    Route::delete('/{id}', [AdminDonationController::class, 'destroy']);
});

// Reports & Analytics
Route::prefix('reports')->group(function () {
    Route::get('/donations', [AdminReportController::class, 'donasiIndex']);
    Route::get('/donations/export', [AdminReportController::class, 'donasiExport']);
    Route::get('/revenue', [AdminReportController::class, 'revenueIndex']);
    Route::get('/revenue/export', [AdminReportController::class, 'revenueExport']);
    Route::get('/users', [AdminReportController::class, 'usersReport']);
    Route::get('/classes', [AdminReportController::class, 'classesReport']);
    Route::get('/engagement', [AdminReportController::class, 'engagementReport']);
});

// Notifications & Broadcasting
Route::prefix('notifications')->group(function () {
    Route::get('/', [AdminNotificationController::class, 'index']);
    Route::post('/send', [AdminNotificationController::class, 'send']);
    Route::post('/broadcast', [AdminNotificationController::class, 'broadcast']);
    Route::get('/history', [AdminNotificationController::class, 'history']);
});

// Settings
Route::prefix('settings')->group(function () {
    Route::get('/', [AdminSettingsController::class, 'index']);
    Route::post('/general', [AdminSettingsController::class, 'updateGeneral']);
    Route::post('/social', [AdminSettingsController::class, 'updateSocial']);
    Route::post('/payment', [AdminSettingsController::class, 'updatePayment']);
    Route::post('/rules', [AdminSettingsController::class, 'updateRules']);
});

// Learning Paths
Route::prefix('learning-paths')->group(function () {
    Route::get('/', [AdminLearningPathController::class, 'index']);
    Route::post('/', [AdminLearningPathController::class, 'store']);
    Route::get('/{id}', [AdminLearningPathController::class, 'show']);
    Route::put('/{id}', [AdminLearningPathController::class, 'update']);
    Route::delete('/{id}', [AdminLearningPathController::class, 'destroy']);
    Route::post('/{id}/courses', [AdminLearningPathController::class, 'attachCourses']);
});
