<?php

use Illuminate\Support\Facades\Route;

// Dashboard Murid
Route::get('/student/dashboard', [\App\Http\Controllers\Student\StudentDashboardController::class, 'muridDashboard'])
    ->name('student.dashboard');

// Murid - Kelas Saya
Route::get('/student/courses', [\App\Http\Controllers\Student\StudentDashboardController::class, 'muridKelas'])
    ->name('student.kelas');

// Murid - Katalog & Join Kelas
Route::get('/student/catalog', [\App\Http\Controllers\Student\CatalogController::class, 'index'])->name('student.katalog');
Route::post('/student/catalog/{id}/join', [\App\Http\Controllers\Student\CatalogController::class, 'join'])->name('student.katalog.join');

// Murid - Materi
Route::get('/student/lessons', [\App\Http\Controllers\Student\StudentDashboardController::class, 'muridMateri'])
    ->name('student.materi');
Route::post('/student/lessons/{id}/beli', [\App\Http\Controllers\Student\StudentDashboardController::class, 'beliMateri'])
    ->name('student.materi.beli');

// Murid - Learning Paths
Route::get('/murid/learning-paths', [\App\Http\Controllers\Student\LearningPathController::class, 'myPaths'])
    ->name('student.learning-paths.index');
Route::get('/learning-paths', [\App\Http\Controllers\Student\LearningPathController::class, 'index'])
    ->name('learning-paths.index');
Route::get('/learning-paths/{id}', [\App\Http\Controllers\Student\LearningPathController::class, 'show'])
    ->name('learning-paths.show');
Route::post('/learning-paths/{id}/enroll', [\App\Http\Controllers\Student\LearningPathController::class, 'enroll'])
    ->name('learning-paths.enroll');
Route::get('/learning-paths/{id}/certificate', [\App\Http\Controllers\Student\LearningPathController::class, 'downloadCertificate'])
    ->name('learning-paths.certificate');

// Murid - Sertifikat (Agregasi)
Route::get('/student/certificates', [\App\Http\Controllers\Student\StudentDashboardController::class, 'muridSertifikat'])
    ->name('student.sertifikat');

// Ruang Kelas Live
Route::get('/kelas/{id}/live', [\App\Http\Controllers\Student\LiveClassController::class, 'join'])
    ->name('kelas.live');

// Halaman Belajar (LMS)
Route::get('/belajar/kelas/{kelas_id}/materi/{materi_id?}', [\App\Http\Controllers\Student\LearningController::class, 'show'])
    ->name('belajar.show');

// Mark Materi as Complete (Ajax)
Route::post('/belajar/materi/{materi_id}/complete', [\App\Http\Controllers\Student\LearningController::class, 'complete'])
    ->name('belajar.complete');

// Fitur Tambahan LMS
Route::post('/belajar/kelas/{id}/ulasan', [\App\Http\Controllers\Student\LearningController::class, 'storeUlasan'])->name('belajar.ulasan.store');
Route::post('/belajar/kelas/{id}/diskusi', [\App\Http\Controllers\Student\LearningController::class, 'storeDiskusi'])->name('belajar.diskusi.store');
Route::post('/belajar/kelas/{id}/catatan', [\App\Http\Controllers\Student\LearningController::class, 'storeCatatan'])->name('belajar.catatan.store');
