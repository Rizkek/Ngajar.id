<?php



























use App\Http\Controllers\AuthController;
use App\Http\Controllers\DonasiController;
use App\Http\Controllers\MentorController;
use App\Http\Controllers\ProgramController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\AdminKelasController;
use App\Http\Controllers\AdminMateriController;
use App\Http\Controllers\AdminDonasiController;
use App\Http\Controllers\AdminReportController;
use App\Http\Controllers\AdminNotificationController;
use App\Http\Controllers\AdminSettingsController;
use App\Http\Controllers\AdminLearningPathController;
use App\Http\Controllers\Api\V1\StudentCourseController;
use App\Http\Controllers\Api\V1\StudentProgressController;
use App\Http\Controllers\Api\V1\TeacherCourseController;
use App\Http\Controllers\Api\V1\MaterialUploadController;
use App\Http\Controllers\Api\V1\ReviewController;
use App\Http\Controllers\Api\V1\NotificationController as ApiNotificationController;
use App\Http\Controllers\Api\V1\LeaderboardController;
use App\Http\Controllers\Api\V1\SearchController;
use App\Http\Controllers\Api\V1\CertificateController;
use App\Http\Controllers\Api\V1\EnrollmentPermissionController;
use App\Http\Controllers\Api\V1\LearningPathApiController;
use App\Http\Controllers\BelajarController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\KelasController;
use App\Http\Controllers\MateriController;
use App\Http\Controllers\LearningPathController;
use App\Http\Controllers\TopupController;
use App\Http\Controllers\WebhookController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\GeometryController;
use App\Http\Controllers\LiveClassController;
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
        Route::get('/stats', [\App\Http\Controllers\LandingController::class, 'stats']);
        // Volunteer/mentor list
        Route::get('/volunteers', [\App\Http\Controllers\LandingController::class, 'volunteers']);
        // About/team info
        Route::get('/info', [\App\Http\Controllers\LandingController::class, 'info']);
        // Courses list (for landing page - no auth needed)
        Route::get('/courses', [StudentCourseController::class, 'index']);
    });

    // ===== AUTHENTICATION =====
    // ✅ PHASE 1 SECURITY: Apply rate limiting to auth routes
    Route::middleware(['throttle:register'])->group(function () {
        Route::post('/register', [AuthController::class, 'register']);
    });

    Route::middleware(['throttle:login'])->group(function () {
        Route::post('/login', [AuthController::class, 'login']);
    });

    Route::middleware(['throttle:forgot-password'])->group(function () {
        Route::post('/password/forgot', [\App\Http\Controllers\PasswordController::class, 'forgot']);
        Route::post('/password/reset', [\App\Http\Controllers\PasswordController::class, 'reset']);
    });

    Route::post('/verify-email/{token}', [AuthController::class, 'verifyEmail'])->name('api.verify-email');
    Route::post('/resend-verification', [AuthController::class, 'resendVerificationEmail']);

    // ===== PUBLIC PROGRAM/CLASS ENDPOINTS =====
    // ✅ PHASE 1 SECURITY: Rate limit public API endpoints
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
        Route::get('/', [DonasiController::class, 'index']);
        Route::get('/stats', [DonasiController::class, 'stats']);
        Route::get('/recent', [DonasiController::class, 'recent']);
        Route::post('/', [DonasiController::class, 'store']);
    });

    // ========== WEBHOOK ENDPOINTS (Public but with signature validation) ==========
    // ✅ PHASE 1 SECURITY: Webhook endpoints with rate limiting and signature validation
    Route::middleware(['throttle:webhook'])->group(function () {
        Route::post('/webhook/midtrans', [WebhookController::class, 'midtrans']);
        Route::post('/webhook/xendit', [WebhookController::class, 'xendit']);
    });

    // ========== PROTECTED ROUTES (Require Authentication) ==========
    // ✅ PHASE 1 SECURITY: Check token expiration on all authenticated requests
    Route::middleware(['auth:sanctum', 'check-token-expiration'])->group(function () {

        // ===== USER ENDPOINTS =====
        Route::prefix('user')->group(function () {
            Route::get('/', [AuthController::class, 'me']);
            Route::post('/logout', [AuthController::class, 'logout']);
            Route::put('/profile', [AuthController::class, 'updateProfile']);
            Route::post('/avatar', [AuthController::class, 'uploadAvatar']);
            Route::get('/preferences', [AuthController::class, 'getPreferences']);
            Route::put('/preferences', [AuthController::class, 'updatePreferences']);
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

            // Dashboard
            Route::get('/dashboard', [DashboardController::class, 'muridDashboard']);

            // My Classes/Enrollments
            Route::prefix('classes')->group(function () {
                Route::get('/', [CatalogController::class, 'myClasses']);
                Route::get('/{id}', [CatalogController::class, 'show']);
                Route::post('/{id}/enroll', [CatalogController::class, 'enroll']);
                Route::get('/{id}/progress', [BelajarController::class, 'classProgress']);
            });

            // Learning/Course Content
            Route::prefix('learning')->group(function () {
                Route::get('/materials', [BelajarController::class, 'myMaterials']);
                Route::get('/materials/{id}', [BelajarController::class, 'getMaterial']);
                Route::post('/materials/{id}/complete', [BelajarController::class, 'completeMaterial']);
                Route::post('/materials/{id}/unlock', [BelajarController::class, 'unlockMaterial']);
                Route::get('/progress', [BelajarController::class, 'overallProgress']);
            });

            // Reviews & Discussions
            Route::prefix('reviews')->group(function () {
                Route::post('/classes/{id}', [BelajarController::class, 'storeReview']);
                Route::get('/classes/{id}', [BelajarController::class, 'classReviews']);
            });

            Route::prefix('discussions')->group(function () {
                Route::post('/classes/{id}', [BelajarController::class, 'storeDiskusi']);
                Route::get('/classes/{id}', [BelajarController::class, 'classDiskusi']);
            });

            Route::prefix('notes')->group(function () {
                Route::post('/materials/{id}', [BelajarController::class, 'storeCatatan']);
                Route::get('/materials/{id}', [BelajarController::class, 'getMaterialNotes']);
                Route::get('/', [BelajarController::class, 'allNotes']);
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
                Route::get('/', [BelajarController::class, 'myCertificates']);
                Route::get('/{id}', [BelajarController::class, 'downloadCertificate']);
            });

            // Token/Topup
            Route::prefix('token')->group(function () {
                Route::get('/balance', [TopupController::class, 'balance']);
                Route::get('/history', [TopupController::class, 'history']);
                Route::post('/topup', [TopupController::class, 'createTopup']);
            });

            // Material Purchase
            Route::prefix('materials')->group(function () {
                Route::post('/{id}/buy', [BelajarController::class, 'beliMateri']);
            });

            // Wishlist/Saved
            Route::prefix('saved')->group(function () {
                Route::get('/', [CatalogController::class, 'savedClasses']);
                Route::post('/classes/{id}', [CatalogController::class, 'saveClass']);
                Route::delete('/classes/{id}', [CatalogController::class, 'unsaveClass']);
            });
        });

        // ===== TEACHER ENDPOINTS =====
        Route::prefix('teacher')->middleware('role:pengajar')->group(function () {

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
            Route::get('/dashboard', [DashboardController::class, 'pengajarDashboard']);

            // My Classes (CRUD)
            Route::prefix('classes')->group(function () {
                Route::get('/', [KelasController::class, 'index']);
                Route::post('/', [KelasController::class, 'store']);
                Route::get('/{id}', [KelasController::class, 'show']);
                Route::put('/{id}', [KelasController::class, 'update']);
                Route::delete('/{id}', [KelasController::class, 'destroy']);
                Route::post('/{id}/publish', [KelasController::class, 'publish']);
                Route::post('/{id}/archive', [KelasController::class, 'archive']);
                Route::get('/{id}/students', [KelasController::class, 'students']);
                Route::get('/{id}/stats', [KelasController::class, 'stats']);
                Route::post('/{id}/grades', [KelasController::class, 'uploadGrades']);
            });

            // Materials (CRUD)
            Route::prefix('materials')->group(function () {
                Route::get('/', [MateriController::class, 'index']);
                Route::post('/', [MateriController::class, 'store']);
                Route::get('/{id}', [MateriController::class, 'show']);
                Route::put('/{id}', [MateriController::class, 'update']);
                Route::delete('/{id}', [MateriController::class, 'destroy']);
                Route::get('/class/{classId}', [MateriController::class, 'byClass']);
            });

            // Student Feedback & Progress
            Route::prefix('feedback')->group(function () {
                Route::get('/class/{classId}', [KelasController::class, 'studentFeedback']);
                Route::get('/student/{studentId}', [KelasController::class, 'studentProgress']);
                Route::post('/student/{studentId}/comment', [KelasController::class, 'addComment']);
            });

            // Certificates
            Route::prefix('certificates')->group(function () {
                Route::get('/', [MateriController::class, 'myCertificates']);
                Route::post('/class/{classId}/generate', [MateriController::class, 'generateCertificates']);
                Route::get('/class/{classId}/issued', [MateriController::class, 'issuedCertificates']);
            });

            // Earnings & Token
            Route::prefix('earnings')->group(function () {
                Route::get('/', [KelasController::class, 'earnings']);
                Route::get('/history', [KelasController::class, 'earningHistory']);
                Route::get('/stats', [KelasController::class, 'earningStats']);
            });

            // Analytics
            Route::prefix('analytics')->group(function () {
                Route::get('/overview', [KelasController::class, 'analyticsOverview']);
                Route::get('/class/{classId}', [KelasController::class, 'classAnalytics']);
            });
        });

        // ===== ADMIN ENDPOINTS =====
        Route::prefix('admin')->middleware('role:admin')->group(function () {

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
                Route::get('/', [AdminKelasController::class, 'index']);
                Route::get('/{id}', [AdminKelasController::class, 'show']);
                Route::post('/{id}/approve', [AdminKelasController::class, 'approve']);
                Route::post('/{id}/reject', [AdminKelasController::class, 'reject']);
                Route::post('/{id}/archive', [AdminKelasController::class, 'archive']);
                Route::delete('/{id}', [AdminKelasController::class, 'destroy']);
                Route::post('/{id}/flag', [AdminKelasController::class, 'flag']);
            });

            // Material Moderation
            Route::prefix('materials')->group(function () {
                Route::get('/', [AdminMateriController::class, 'index']);
                Route::get('/{id}', [AdminMateriController::class, 'show']);
                Route::put('/{id}', [AdminMateriController::class, 'update']);
                Route::delete('/{id}', [AdminMateriController::class, 'destroy']);
                Route::post('/{id}/verify', [AdminMateriController::class, 'verify']);
            });

            // Donation Management
            Route::prefix('donations')->group(function () {
                Route::get('/', [AdminDonasiController::class, 'index']);
                Route::get('/{id}', [AdminDonasiController::class, 'show']);
                Route::post('/{id}/verify', [AdminDonasiController::class, 'verify']);
                Route::post('/{id}/refund', [AdminDonasiController::class, 'refund']);
                Route::delete('/{id}', [AdminDonasiController::class, 'destroy']);
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
        });

        // ===== SPRINT 2: NOTIFICATIONS, PROGRESS, RECOMMENDATIONS, LIVE CLASSES =====
        Route::prefix('user')->group(function () {
            // Notifications
            Route::prefix('notifications')->group(function () {
                Route::get('/', [\App\Http\Controllers\NotificationController::class, 'index']);
                Route::post('/mark-read/{id}', [\App\Http\Controllers\NotificationController::class, 'markAsRead']);
                Route::post('/mark-all-read', [\App\Http\Controllers\NotificationController::class, 'markAllAsRead']);
                Route::get('/unread-count', [\App\Http\Controllers\NotificationController::class, 'unreadCount']);
            });
        });

        // Live Classes (Available to all authenticated users)
        Route::prefix('live-class')->group(function () {
            Route::get('/', [\App\Http\Controllers\LiveClassController::class, 'index']);
            Route::post('/create', [\App\Http\Controllers\LiveClassController::class, 'create']);
            Route::post('/join/{sessionId}', [\App\Http\Controllers\LiveClassController::class, 'join']);
            Route::post('/end/{sessionId}', [\App\Http\Controllers\LiveClassController::class, 'end']);
            Route::get('/{sessionId}/attendance', [\App\Http\Controllers\LiveClassController::class, 'getAttendance']);
        });

    });

});

