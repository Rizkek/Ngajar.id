<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DonasiController;
use App\Http\Controllers\MentorController;
use App\Http\Controllers\ProgramController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Public API Routes (No Authentication Required)
Route::prefix('v1')->group(function () {

    // Authentication
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    // Programs/Classes
    Route::get('/programs', [ProgramController::class, 'index']);
    Route::get('/programs/{id}', [ProgramController::class, 'show']);

    // Mentors
    Route::get('/mentors', [MentorController::class, 'index']);
    Route::get('/mentors/{id}', [MentorController::class, 'show']);

    // Donations
    Route::get('/donasi', [DonasiController::class, 'index']);
    Route::post('/donasi', [DonasiController::class, 'store']);
});

// Protected API Routes (Authentication Required)
Route::prefix('v1')->middleware('auth:sanctum')->group(function () {

    // User
    Route::get('/user', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // Dashboard
    Route::get('/dashboard/murid', [\App\Http\Controllers\DashboardController::class, 'muridDashboard']);
    Route::get('/dashboard/pengajar', [\App\Http\Controllers\DashboardController::class, 'pengajarDashboard']);
});
