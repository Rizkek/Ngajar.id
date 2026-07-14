<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Student\StudentDashboardController;
use App\Http\Controllers\Api\V1\Student\StudentLearningController;
use App\Http\Controllers\Api\V1\Student\CatalogApiController;
use App\Http\Controllers\Student\LearningPathController;
use App\Http\Controllers\Transaction\TopupController;

// Dashboard
Route::get('/dashboard', [StudentDashboardController::class, 'muridDashboard']);

// My Classes/Enrollments
Route::prefix('classes')->group(function () {
    Route::get('/', [CatalogApiController::class, 'myClasses']);
    Route::get('/{id}', [CatalogApiController::class, 'show']);
    Route::post('/{id}/enroll', [CatalogApiController::class, 'enroll']);
    Route::get('/{id}/progress', [StudentLearningController::class, 'classProgress']);
});

// Learning/Course Content
Route::prefix('learning')->group(function () {
    Route::get('/materials', [StudentLearningController::class, 'myMaterials']);
    Route::get('/materials/{id}', [StudentLearningController::class, 'getMaterial']);
    Route::post('/materials/{id}/complete', [StudentLearningController::class, 'completeMaterial']);
    Route::post('/materials/{id}/unlock', [StudentLearningController::class, 'unlockMaterial']);
    Route::get('/progress', [StudentLearningController::class, 'overallProgress']);
});

// Reviews & Discussions
Route::prefix('reviews')->group(function () {
    Route::post('/classes/{id}', [StudentLearningController::class, 'storeReview']);
    Route::get('/classes/{id}', [StudentLearningController::class, 'classReviews']);
});

Route::prefix('discussions')->group(function () {
    Route::post('/classes/{id}', [StudentLearningController::class, 'storeDiskusi']);
    Route::get('/classes/{id}', [StudentLearningController::class, 'classDiskusi']);
});

Route::prefix('notes')->group(function () {
    Route::post('/materials/{id}', [StudentLearningController::class, 'storeCatatan']);
    Route::get('/materials/{id}', [StudentLearningController::class, 'getMaterialNotes']);
    Route::get('/', [StudentLearningController::class, 'allNotes']);
});

// Learning Paths
Route::prefix('learning-paths')->group(function () {
    Route::get('/', [LearningPathController::class, 'myPaths']);
    Route::get('/{id}', [LearningPathController::class, 'showUserPath']);
    Route::post('/{id}/enroll', [LearningPathController::class, 'enroll']);
    Route::get('/{id}/progress', [LearningPathController::class, 'pathProgress']);
    Route::get('/{id}/certificate', [LearningPathController::class, 'downloadCertificate']);
});

// Certificates
Route::prefix('certificates')->group(function () {
    Route::get('/', [StudentLearningController::class, 'myCertificates']);
    Route::get('/{id}', [StudentLearningController::class, 'downloadCertificate']);
});

// Token/Topup
Route::prefix('token')->group(function () {
    Route::get('/balance', [TopupController::class, 'balance']);
    Route::get('/history', [TopupController::class, 'history']);
    Route::post('/topup', [TopupController::class, 'createTopup']);
});

// Material Purchase
Route::prefix('materials')->group(function () {
    Route::post('/{id}/buy', [StudentLearningController::class, 'beliMateri']);
});

// Wishlist/Saved
Route::prefix('saved')->group(function () {
    Route::get('/', [CatalogApiController::class, 'savedClasses']);
    Route::post('/classes/{id}', [CatalogApiController::class, 'saveClass']);
    Route::delete('/classes/{id}', [CatalogApiController::class, 'unsaveClass']);
});
