<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\Teacher\TeacherCourseController;
use App\Http\Controllers\Teacher\TeacherDashboardController;
use App\Http\Controllers\Api\V1\Teacher\TeacherClassController;
use App\Http\Controllers\Teacher\LessonController;

// Dashboard (new API)
Route::get('/dashboard-api', [TeacherCourseController::class, 'dashboard']);

// My Courses (New API v1 - Kelas)
Route::prefix('kelas')->group(function () {
    Route::get('/', [TeacherCourseController::class, 'index']);
    Route::post('/', [TeacherCourseController::class, 'store']);
    Route::get('/{id}', [TeacherCourseController::class, 'show']);
    Route::put('/{id}', [TeacherCourseController::class, 'update']);
    Route::delete('/{id}', [TeacherCourseController::class, 'destroy']);
    Route::get('/{id}/students', [TeacherCourseController::class, 'getStudents']);
    Route::get('/{id}/materi', [TeacherCourseController::class, 'getMaterials']);
    Route::post('/{id}/materi', [TeacherCourseController::class, 'addMaterial']);
});

// Dashboard
Route::get('/dashboard', [TeacherDashboardController::class, 'pengajarDashboard']);

// My Classes (CRUD)
Route::prefix('classes')->group(function () {
    Route::get('/', [TeacherClassController::class, 'index']);
    Route::post('/', [TeacherClassController::class, 'store']);
    Route::get('/{id}', [TeacherClassController::class, 'show']);
    Route::put('/{id}', [TeacherClassController::class, 'update']);
    Route::delete('/{id}', [TeacherClassController::class, 'destroy']);
    Route::post('/{id}/publish', [TeacherClassController::class, 'publish']);
    Route::post('/{id}/archive', [TeacherClassController::class, 'archive']);
    Route::get('/{id}/students', [TeacherClassController::class, 'students']);
    Route::get('/{id}/stats', [TeacherClassController::class, 'stats']);
    Route::post('/{id}/grades', [TeacherClassController::class, 'uploadGrades']);
});

// Materials (CRUD)
Route::prefix('materials')->group(function () {
    Route::get('/', [LessonController::class, 'index']);
    Route::post('/', [LessonController::class, 'store']);
    Route::get('/{id}', [LessonController::class, 'show']);
    Route::put('/{id}', [LessonController::class, 'update']);
    Route::delete('/{id}', [LessonController::class, 'destroy']);
    Route::get('/class/{classId}', [LessonController::class, 'byClass']);
});

// Student Feedback & Progress
Route::prefix('feedback')->group(function () {
    Route::get('/class/{classId}', [TeacherClassController::class, 'studentFeedback']);
    Route::get('/student/{studentId}', [TeacherClassController::class, 'studentProgress']);
    Route::post('/student/{studentId}/comment', [TeacherClassController::class, 'addComment']);
});

// Certificates
Route::prefix('certificates')->group(function () {
    Route::get('/', [LessonController::class, 'myCertificates']);
    Route::post('/class/{classId}/generate', [LessonController::class, 'generateCertificates']);
    Route::get('/class/{classId}/issued', [LessonController::class, 'issuedCertificates']);
});

// Earnings & Token
Route::prefix('earnings')->group(function () {
    Route::get('/', [TeacherClassController::class, 'earnings']);
    Route::get('/history', [TeacherClassController::class, 'earningHistory']);
    Route::get('/stats', [TeacherClassController::class, 'earningStats']);
});

// Analytics
Route::prefix('analytics')->group(function () {
    Route::get('/overview', [TeacherClassController::class, 'analyticsOverview']);
    Route::get('/class/{classId}', [TeacherClassController::class, 'classAnalytics']);
});
