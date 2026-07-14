<?php



























use App\Http\Controllers\Api\V1\Auth\ApiAuthController;
use App\Http\Controllers\Transaction\DonationController;
use App\Http\Controllers\Teacher\MentorController;
use App\Http\Controllers\Front\ProgramController;
use App\Http\Controllers\Student\StudentDashboardController;
use App\Http\Controllers\Teacher\TeacherDashboardController;
use App\Http\Controllers\Api\V1\Admin\AdminController;
use App\Http\Controllers\Api\V1\Admin\AdminUserController;
use App\Http\Controllers\Api\V1\Admin\AdminCourseController;
use App\Http\Controllers\Api\V1\Admin\AdminLessonController;
use App\Http\Controllers\Api\V1\Admin\AdminDonationController;
use App\Http\Controllers\Api\V1\Admin\AdminReportController;
use App\Http\Controllers\Api\V1\Admin\AdminNotificationController;
use App\Http\Controllers\Api\V1\Admin\AdminSettingsController;
use App\Http\Controllers\Api\V1\Admin\AdminLearningPathController;
use App\Http\Controllers\Api\V1\Student\StudentCourseController;
use App\Http\Controllers\Api\V1\Student\StudentProgressController;
use App\Http\Controllers\Api\V1\Teacher\TeacherCourseController;
use App\Http\Controllers\Api\V1\Teacher\MaterialUploadController;
use App\Http\Controllers\Api\V1\Student\ReviewController;
use App\Http\Controllers\Api\V1\Shared\NotificationController as ApiNotificationController;
use App\Http\Controllers\Api\V1\Front\LeaderboardController;
use App\Http\Controllers\Api\V1\Front\SearchController;
use App\Http\Controllers\Api\V1\Student\CertificateController;
use App\Http\Controllers\Api\V1\Shared\EnrollmentPermissionController;
use App\Http\Controllers\Api\V1\Shared\LearningPathApiController;
use App\Http\Controllers\Api\V1\Student\StudentLearningController;
use App\Http\Controllers\Api\V1\Student\CatalogApiController;
use App\Http\Controllers\Api\V1\Teacher\TeacherClassController;
use App\Http\Controllers\Teacher\LessonController;
use App\Http\Controllers\Student\LearningPathController;
use App\Http\Controllers\Transaction\TopupController;
use App\Http\Controllers\Transaction\WebhookController;
use App\Http\Controllers\Api\V1\Shared\NotificationController;

use App\Http\Controllers\Student\LiveClassController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| API v1 - Comprehensive endpoints untuk Landing, Admin, Student, Teacher
| Prefix: /api/v1
|
*/

Route::prefix('v1')->group(function () {

    // ========== PUBLIC ROUTES (No Authentication) ==========

    // ===== LANDING PAGE ENDPOINTS =====
    Route::prefix('landing')->group(function () {
        // Statistics endpoint
        Route::get('/stats', [\App\Http\Controllers\Front\LandingController::class, 'stats']);
        // Volunteer/mentor list
        Route::get('/volunteers', [\App\Http\Controllers\Front\LandingController::class, 'volunteers']);
        // About/team info
        Route::get('/info', [\App\Http\Controllers\Front\LandingController::class, 'info']);
        // Courses list (for landing page - no auth needed)
        Route::get('/courses', [StudentCourseController::class, 'index']);
    });

    // ===== AUTHENTICATION =====
    // ÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â¢ÃƒÆ’Ã¢â‚¬Â¦ÃƒÂ¢Ã¢â€šÂ¬Ã…â€œÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â‚¬Å¡Ã‚Â¬Ãƒâ€šÃ‚Â¦ PHASE 1 SECURITY: Apply rate limiting to auth routes
    Route::middleware(['throttle:register'])->group(function () {
        Route::post('/register', [ApiAuthController::class, 'register']);
    });

    Route::middleware(['throttle:login'])->group(function () {
        Route::post('/login', [ApiAuthController::class, 'login']);
    });

    Route::middleware(['throttle:forgot-password'])->group(function () {
        Route::post('/password/forgot', [\App\Http\Controllers\Auth\PasswordController::class, 'forgot']);
        Route::post('/password/reset', [\App\Http\Controllers\Auth\PasswordController::class, 'reset']);
    });

    Route::post('/verify-email/{token}', [ApiAuthController::class, 'verifyEmail'])->name('api.verify-email');
    Route::post('/resend-verification', [ApiAuthController::class, 'resendVerificationEmail']);

    // ===== PUBLIC PROGRAM/CLASS ENDPOINTS =====
    // ÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â¢ÃƒÆ’Ã¢â‚¬Â¦ÃƒÂ¢Ã¢â€šÂ¬Ã…â€œÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â‚¬Å¡Ã‚Â¬Ãƒâ€šÃ‚Â¦ PHASE 1 SECURITY: Rate limit public API endpoints
    Route::middleware(['throttle:public'])->prefix('programs')->group(function () {
        Route::get('/', [ProgramController::class, 'index']);
        Route::get('/{id}', [ProgramController::class, 'show']);
        Route::get('/{id}/reviews', [ProgramController::class, 'reviews']);
        Route::get('/{id}/materials', [ProgramController::class, 'materials']);
    });

    // ===== PUBLIC MENTOR/TEACHER ENDPOINTS =====
    Route::prefix('mentors')->group(function () {
        Route::get('/', [MentorController::class, 'index']);
        Route::get('/{id}', [MentorController::class, 'show']);
        Route::get('/{id}/classes', [MentorController::class, 'classes']);
        Route::get('/{id}/reviews', [MentorController::class, 'reviews']);
    });

    // ===== PUBLIC LEARNING PATHS ENDPOINTS =====
    Route::prefix('learning-paths')->group(function () {
        Route::get('/', [LearningPathController::class, 'index']);
        Route::get('/{id}', [LearningPathController::class, 'show']);
        Route::get('/{id}/courses', [LearningPathController::class, 'courses']);
    });

    // ===== PUBLIC DONATION ENDPOINTS =====
    Route::prefix('donations')->group(function () {
        Route::get('/', [DonationController::class, 'index']);
        Route::get('/stats', [DonationController::class, 'stats']);
        Route::get('/recent', [DonationController::class, 'recent']);
        Route::post('/', [DonationController::class, 'store']);
    });

    // ========== WEBHOOK ENDPOINTS (Public but with signature validation) ==========
    // ÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â¢ÃƒÆ’Ã¢â‚¬Â¦ÃƒÂ¢Ã¢â€šÂ¬Ã…â€œÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â‚¬Å¡Ã‚Â¬Ãƒâ€šÃ‚Â¦ PHASE 1 SECURITY: Webhook endpoints with rate limiting and signature validation
    Route::middleware(['throttle:webhook'])->group(function () {
        Route::post('/webhook/midtrans', [WebhookController::class, 'midtrans']);
        Route::post('/webhook/xendit', [WebhookController::class, 'xendit']);
    });

    // ========== PROTECTED ROUTES (Require Authentication) ==========
    // ÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â¢ÃƒÆ’Ã¢â‚¬Â¦ÃƒÂ¢Ã¢â€šÂ¬Ã…â€œÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â‚¬Å¡Ã‚Â¬Ãƒâ€šÃ‚Â¦ PHASE 1 SECURITY: Check token expiration on all authenticated requests
    Route::middleware(['auth:sanctum', 'check-token-expiration'])->group(function () {

        // ===== USER ENDPOINTS =====
        Route::prefix('user')->group(function () {
            Route::get('/', [ApiAuthController::class, 'me']);
            Route::post('/logout', [ApiAuthController::class, 'logout']);
            Route::put('/profile', [ApiAuthController::class, 'updateProfile']);
            Route::post('/avatar', [ApiAuthController::class, 'uploadAvatar']);
            Route::get('/preferences', [ApiAuthController::class, 'getPreferences']);
            Route::put('/preferences', [ApiAuthController::class, 'updatePreferences']);
        });
        // ===== MATERIAL UPLOAD (Teacher + Student) =====
        Route::prefix('materials')->group(function () {
            Route::post('/upload', [MaterialUploadController::class, 'upload'])->middleware('role:pengajar');
            Route::delete('/{id}/file', [MaterialUploadController::class, 'deleteFile'])->middleware('role:pengajar');
            Route::get('/{id}/download', [MaterialUploadController::class, 'download']);
            Route::post('/{id}/stream', [MaterialUploadController::class, 'stream']);
            Route::get('/stats', [MaterialUploadController::class, 'stats'])->middleware('role:pengajar');
            Route::post('/{id}/feedback', [ReviewController::class, 'addMaterialFeedback'])->middleware('auth:sanctum');
        });

        // ===== REVIEWS & RATINGS =====
        Route::prefix('reviews')->middleware('auth:sanctum')->group(function () {
            Route::put('/{id}', [ReviewController::class, 'update']);
            Route::delete('/{id}', [ReviewController::class, 'destroy']);
            Route::post('/{id}/helpful', [ReviewController::class, 'markHelpful']);
        });

        Route::get('/courses/{id}/reviews', [ReviewController::class, 'index']);
        Route::post('/courses/{id}/reviews', [ReviewController::class, 'store'])->middleware('auth:sanctum');

        // ===== NOTIFICATIONS =====
        Route::prefix('notifications')->middleware('auth:sanctum')->group(function () {
            Route::get('/', [ApiNotificationController::class, 'index']);
            Route::get('/unread-count', [ApiNotificationController::class, 'unreadCount']);
            Route::get('/{id}', [ApiNotificationController::class, 'show']);
            Route::put('/{id}/read', [ApiNotificationController::class, 'markAsRead']);
            Route::put('/mark-all-read', [ApiNotificationController::class, 'markAllAsRead']);
            Route::delete('/{id}', [ApiNotificationController::class, 'destroy']);
            Route::delete('/clear-all', [ApiNotificationController::class, 'clearAll']);
        });

        // ===== LEADERBOARD & ACHIEVEMENTS =====
        Route::prefix('leaderboard')->group(function () {
            Route::get('/global', [LeaderboardController::class, 'global']);
            Route::get('/friends', [LeaderboardController::class, 'friends'])->middleware('auth:sanctum');
            Route::get('/my-rank', [LeaderboardController::class, 'myRank'])->middleware('auth:sanctum');
            Route::get('/user/{id}', [LeaderboardController::class, 'getUserRank']);
            Route::get('/stats', [LeaderboardController::class, 'stats']);
        });

        Route::prefix('achievements')->group(function () {
            Route::get('/', [LeaderboardController::class, 'achievements']);
            Route::get('/my', [LeaderboardController::class, 'myAchievements'])->middleware('auth:sanctum');
        });

        // ===== SEARCH & FILTER =====
        Route::prefix('search')->group(function () {
            Route::get('/courses', [SearchController::class, 'courses']);
            Route::get('/instructors', [SearchController::class, 'instructors']);
            Route::get('/categories', [SearchController::class, 'categories']);
            Route::get('/trending', [SearchController::class, 'trending']);
            Route::get('/filters', [SearchController::class, 'filters']);
        });

        // ===== ENROLLMENT PERMISSIONS =====
        Route::prefix('enrollment')->group(function () {
            Route::post('/check', [EnrollmentPermissionController::class, 'check'])->middleware('auth:sanctum');
            Route::get('/prerequisites/{kelasId}', [EnrollmentPermissionController::class, 'getPrerequisites'])->middleware('auth:sanctum');
            Route::get('/requirements/{kelasId}', [EnrollmentPermissionController::class, 'getRequirements'])->middleware('auth:sanctum');
            Route::get('/restrictions', [EnrollmentPermissionController::class, 'getRestrictions'])->middleware('auth:sanctum');
        });

        // ===== CERTIFICATES =====
        Route::prefix('certificates')->group(function () {
            Route::get('/', [CertificateController::class, 'index'])->middleware('auth:sanctum');
            Route::get('/{id}', [CertificateController::class, 'show'])->middleware('auth:sanctum');
            Route::post('/generate/{kelasId}', [CertificateController::class, 'generate'])->middleware('auth:sanctum');
            Route::get('/{id}/download', [CertificateController::class, 'download'])->middleware('auth:sanctum');
            Route::delete('/{id}', [CertificateController::class, 'destroy'])->middleware('auth:sanctum');
            Route::get('/verify/{certificateNumber}', [CertificateController::class, 'verify']);
            Route::get('/stats', [CertificateController::class, 'stats'])->middleware('auth:sanctum');
        });

        // ===== LEARNING PATHS API V1 =====
        Route::prefix('learning-paths-api')->group(function () {
            Route::get('/', [LearningPathApiController::class, 'index']);
            Route::post('/', [LearningPathApiController::class, 'store'])->middleware('role:admin');
            Route::get('/{id}', [LearningPathApiController::class, 'show']);
            Route::put('/{id}', [LearningPathApiController::class, 'update'])->middleware('role:admin');
            Route::delete('/{id}', [LearningPathApiController::class, 'destroy'])->middleware('role:admin');
            Route::post('/{id}/enroll', [LearningPathApiController::class, 'enroll'])->middleware('auth:sanctum');
            Route::get('/{id}/progress', [LearningPathApiController::class, 'progress'])->middleware('auth:sanctum');
            Route::get('/my/paths', [LearningPathApiController::class, 'myPaths'])->middleware('auth:sanctum');
            Route::post('/{id}/attach-courses', [LearningPathApiController::class, 'attachCourses'])->middleware('role:admin');
        });

        // ===== COURSE BROWSING & ENROLLMENT (Authenticated Users) =====
        Route::prefix('kelas')->group(function () {
            Route::get('/', [StudentCourseController::class, 'index']);
            Route::get('/{id}', [StudentCourseController::class, 'show']);
            Route::post('/{id}/enroll', [StudentCourseController::class, 'enroll'])->middleware('role:murid');
        });

        // ===== STUDENT PROGRESS TRACKING (Student Only) =====
        Route::prefix('my-progress')->middleware('role:murid')->group(function () {
            Route::get('/', [StudentProgressController::class, 'getAllProgress']);
            Route::get('/{kelasId}', [StudentProgressController::class, 'getCourseProgress']);
            Route::post('/materi/{id}/complete', [StudentProgressController::class, 'completeMaterial']);
        });

        Route::prefix('my-courses')->middleware('role:murid')->group(function () {
            Route::get('/', [StudentCourseController::class, 'myCourses']);
        });

        // ===== STUDENT ENDPOINTS =====
        Route::prefix('student')->middleware('role:murid')->group(function () {
            require __DIR__ . '/api/v1/student.php';
        });

        // ===== TEACHER ENDPOINTS =====
        Route::prefix('teacher')->middleware('role:pengajar')->group(function () {
            require __DIR__ . '/api/v1/teacher.php';
        });

        // ===== ADMIN ENDPOINTS =====
        Route::prefix('admin')->middleware('role:admin')->group(function () {
            require __DIR__ . '/api/v1/admin.php';
        });

        // ===== SPRINT 2: NOTIFICATIONS, PROGRESS, RECOMMENDATIONS, LIVE CLASSES =====
        Route::prefix('user')->group(function () {
            // Notifications
            Route::prefix('notifications')->group(function () {
                Route::get('/', [\App\Http\Controllers\Api\V1\Shared\NotificationController::class, 'index']);
                Route::post('/mark-read/{id}', [\App\Http\Controllers\Api\V1\Shared\NotificationController::class, 'markAsRead']);
                Route::post('/mark-all-read', [\App\Http\Controllers\Api\V1\Shared\NotificationController::class, 'markAllAsRead']);
                Route::get('/unread-count', [\App\Http\Controllers\Api\V1\Shared\NotificationController::class, 'unreadCount']);
            });
        });

        // Live Classes (Available to all authenticated users)
        Route::prefix('live-class')->group(function () {
            Route::get('/', [\App\Http\Controllers\Student\LiveClassController::class, 'index']);
            Route::post('/create', [\App\Http\Controllers\Student\LiveClassController::class, 'create']);
            Route::post('/join/{sessionId}', [\App\Http\Controllers\Student\LiveClassController::class, 'join']);
            Route::post('/end/{sessionId}', [\App\Http\Controllers\Student\LiveClassController::class, 'end']);
            Route::get('/{sessionId}/attendance', [\App\Http\Controllers\Student\LiveClassController::class, 'getAttendance']);
        });

    });

});


