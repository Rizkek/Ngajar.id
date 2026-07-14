<?php

use Illuminate\Support\Facades\Route;

// Dashboard Pengajar
Route::get('/teacher/dashboard', [\App\Http\Controllers\Teacher\TeacherDashboardController::class, 'pengajarDashboard'])
    ->name('teacher.dashboard');

// Pengajar - Kelas Saya
Route::get('/teacher/courses', [\App\Http\Controllers\Teacher\TeacherDashboardController::class, 'pengajarKelas'])
    ->name('teacher.kelas');

// Pengajar - Kelola Kelas (CRUD)
Route::get('/teacher/courses/create', [\App\Http\Controllers\Teacher\CourseController::class, 'create'])->name('teacher.kelas.create');
Route::post('/teacher/courses', [\App\Http\Controllers\Teacher\CourseController::class, 'store'])->name('teacher.kelas.store');
Route::get('/teacher/courses/{id}/edit', [\App\Http\Controllers\Teacher\CourseController::class, 'edit'])->name('teacher.kelas.edit');
Route::get('/teacher/courses/{id}/students', [\App\Http\Controllers\Teacher\CourseController::class, 'students'])->name('teacher.kelas.students');
Route::get('/teacher/courses/{id}/analytics', [\App\Http\Controllers\Teacher\CourseController::class, 'analytics'])->name('teacher.kelas.analytics');
Route::put('/teacher/courses/{id}', [\App\Http\Controllers\Teacher\CourseController::class, 'update'])->name('teacher.kelas.update');
Route::delete('/teacher/courses/{id}', [\App\Http\Controllers\Teacher\CourseController::class, 'destroy'])->name('teacher.kelas.destroy');

// Pengajar - Materi (CRUD)
Route::get('/teacher/lessons', [\App\Http\Controllers\Teacher\TeacherDashboardController::class, 'pengajarMateri'])
    ->name('teacher.materi');
Route::get('/teacher/lessons/create', [\App\Http\Controllers\Teacher\LessonController::class, 'create'])->name('teacher.materi.create');
Route::post('/teacher/lessons', [\App\Http\Controllers\Teacher\LessonController::class, 'store'])->name('teacher.materi.store');
Route::get('/teacher/lessons/{id}/edit', [\App\Http\Controllers\Teacher\LessonController::class, 'edit'])->name('teacher.materi.edit');
Route::put('/teacher/lessons/{id}', [\App\Http\Controllers\Teacher\LessonController::class, 'update'])->name('teacher.materi.update');
Route::delete('/teacher/lessons/{id}', [\App\Http\Controllers\Teacher\LessonController::class, 'destroy'])->name('teacher.materi.destroy');

// Pengajar - Download Sertifikat
Route::get('/pengajar/sertifikat/download', [\App\Http\Controllers\Teacher\TeacherDashboardController::class, 'downloadSertifikatStub'])->name('teacher.sertifikat.download');
