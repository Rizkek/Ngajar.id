<?php

// Learning Paths Routes - Add these to web.php inside the auth middleware group

// Learning Paths Routes
Route::prefix('learning-paths')->name('learning-paths.')->group(function () {
    // Browse/Explore Learning Paths
    Route::get('/', [\App\Http\Controllers\LearningPathController::class, 'index'])
        ->name('index');

    // Learning Path Detail
    Route::get('/{pathId}', [\App\Http\Controllers\LearningPathController::class, 'show'])
        ->name('show');

    // Enroll to Learning Path
    Route::post('/{pathId}/enroll', [\App\Http\Controllers\LearningPathController::class, 'enroll'])
        ->name('enroll');

    // My Learning Paths
    Route::get('/my/paths', [\App\Http\Controllers\LearningPathController::class, 'myPaths'])
        ->name('my-paths');

    // Mark class as completed in path (AJAX)
    Route::post('/{pathId}/kelas/{kelasId}/complete', [\App\Http\Controllers\LearningPathController::class, 'markKelasCompleted'])
        ->name('mark-complete');

    // Set current class (AJAX)
    Route::post('/{pathId}/kelas/{kelasId}/set-current', [\App\Http\Controllers\LearningPathController::class, 'setCurrentKelas'])
        ->name('set-current');

    // Download Certificate
    Route::get('/{pathId}/certificate', [\App\Http\Controllers\LearningPathController::class, 'downloadCertificate'])
        ->name('certificate');
});
