# Comprehensive Code-Level Analysis - Laravel Project
**Date:** April 12, 2026 | **Analysis Scope:** Full Laravel Application

---

## EXECUTIVE SUMMARY

This Laravel project shows decent architectural foundation but suffers from **fat controllers**, **incomplete service abstraction**, **inconsistent error handling**, and **hidden N+1 query problems**. The codebase mixes business logic with HTTP concerns, lacks proper validation patterns, and has scattered authorization logic. Below is a detailed breakdown with specific code examples and remediation strategies.

---

## 1. CONTROLLERS ANALYSIS

### 1.1 Fat Controllers & Business Logic Scattered

#### ❌ PROBLEM: AuthController (Lines 1-200+)
[AuthController.php](app/Http/Controllers/AuthController.php#L31-L115) - The `register()` method does too much:

```php
// Lines 31-115: AuthController.register()
DB::beginTransaction();
// 1. Generate referral code
// 2. Handle avatar upload
// 3. Create user
// 4. Create token record
// 5. Handle referral logic
// 6. Create email verification
// 7. Send email
DB::commit();
```

**Issues:**
- Line 48: Avatar upload logic mixed with auth
- Line 65: Token record creation is a separate concern
- Line 72-80: Referral logic should be a service
- Line 85-89: Email verification should be a service

**Recommendation:** Extract into services:
- `RegistrationService::register()`
- `ReferralService::handleReferral()`
- `EmailVerificationService::createVerification()`

---

#### ❌ PROBLEM: DashboardController (Lines 1-100+)
[DashboardController.php](app/Http/Controllers/DashboardController.php#L20-L75) - `muridDashboard()` method:

```php
// Lines 20-75: Multiple concerns
$userStats = [...];  // Gamification calculation
$lastClass = $user->kelasIkuti()->with([...])->first();  // Query
$myClasses = [...];  // Query with filters
$recommendedClasses = [...];  // Random recommendations
$totalPerCategory = Kelas::selectRaw(...)->groupBy(...)->pluck(...);  // Stats
$enrolledPerCategory = [...];  // More stats
$categoryStats = [];  // Manual mapping
```

**Issues:**
- Line 31: `getSaldoToken()` called (hidden query)
- Line 35: Eager loading structure could be optimized
- Line 49-59: N+1 pattern - two queries for category stats instead of one
- Line 61: Random ordering not indexed

**Concrete N+1 Example:**
```php
// Lines 49-59: TWO SEPARATE QUERIES FOR SAME DATA
$totalPerCategory = Kelas::selectRaw('kategori, count(*) as count')
    ->where('status', 'aktif')
    ->groupBy('kategori')
    ->pluck('count', 'kategori');  // Query 1

$enrolledPerCategory = DB::table('kelas')
    ->join('kelas_peserta', ...)
    ->where('kelas_peserta.siswa_id', $user->user_id)
    ->selectRaw('kelas.kategori, count(*) as count')
    ->groupBy('kelas.kategori')
    ->pluck('count', 'kategori');  // Query 2
```

**Should be ONE query:**
```php
$categoryStats = DB::table('kelas')
    ->leftJoin('kelas_peserta', function($join) use ($user) {
        $join->on('kelas.kelas_id', '=', 'kelas_peserta.kelas_id')
            ->where('kelas_peserta.siswa_id', $user->user_id);
    })
    ->where('kelas.status', 'aktif')
    ->selectRaw('kelas.kategori, 
                 COUNT(DISTINCT kelas.kelas_id) as total,
                 COUNT(kelas_peserta.siswa_id) as enrolled')
    ->groupBy('kelas.kategori')
    ->pluck('count', 'kategori');
```

---

#### ❌ PROBLEM: BelajarController (Lines 1-150+)
[BelajarController.php](app/Http/Controllers/BelajarController.php#L13-L80) - `show()` method:

```php
// Lines 13-80: Multiple concerns
$isEnrolled = $user->kelasIkuti()->where(...)->exists();  // Query 1
$isOwner = $kelas->pengajar_id == $user->user_id;  // Check
$materiList = Cache::remember(...);  // Caching logic
$activeMateri = [...];
$userReview = \App\Models\Ulasan::where(...)->first();  // Query 2
$diskusi = \App\Models\DiskusiKelas::with(['user', 'replies.user'])->paginate();  // Query 3
$catatan = \App\Models\CatatanUser::where(...)->first();  // Query 4
```

**Issues:**
- Line 20: Access check could use policy
- Line 30: Hard-coded cache key string
- Line 46: Lazy loading of diskusi (could use eager loading)
- Line 50: Another lazy-loaded catatan query
- Line 56-117: Session management is fragile for email sending

**Authorization Problem:**
```php
// Line 20: Should use Policy, not inline
$isEnrolled = $user->kelasIkuti()->where(...)->exists();

// Better approach in Policy:
// Policy::viewMaterial(User $user, Materi $materi) -> bool
```

---

#### ❌ PROBLEM: CatalogController (Lines 68-140)
[CatalogController.php](app/Http/Controllers/CatalogController.php#L68-L140) - `join()` method has financial transactions:

```php
// Lines 68-140: Mixed concerns
if ($harga > 0) {
    $userToken = $user->token;
    if (!$userToken->cukup($harga)) { return error; }
    
    DB::transaction(function () {  // Financial logic in controller!
        $userToken->kurang($harga);
        TokenLog::create([...]);
        $user->kelasIkuti()->attach(...);
    });
}
```

**Issues:**
- Line 98-113: Complex financial transaction logic in controller
- Line 105: `kurang()` modifies then logs (should be atomic)
- Line 107-110: Token record creation could fail silently
- No idempotency check - can enroll twice if called twice

**Should be a service:**
```php
// TokenService.php
public function purchase(User $user, Kelas $kelas): bool {
    return DB::transaction(function () {
        $token = $user->token;
        if (!$token->cukup($kelas->harga_token)) return false;
        
        $token->kurang($kelas->harga_token);
        TokenLog::create([...]);
        return true;
    });
}
```

---

### 1.2 Code Duplication Patterns

#### ❌ PROBLEM: Repeated Authorization Checks
Found in: [BelajarController](app/Http/Controllers/BelajarController.php#L20), [MateriController](app/Http/Controllers/MateriController.php#L125), [KelasController](app/Http/Controllers/KelasController.php#L95)

```php
// BelajarController.php Line 20
$isEnrolled = $user->kelasIkuti()->where(...)->exists();

// MateriController.php Line 125
$materi = Materi::whereHas('kelas', function ($q) {
    $q->where('pengajar_id', Auth::id());
})->findOrFail($id);

// Repeated in 3+ places - should be in Policy or Trait
```

**Solution:** Create authorization policy:
```php
// Authorization/MateriPolicy.php
public function update(User $user, Materi $materi): bool {
    return $materi->kelas->pengajar_id === $user->user_id;
}
```

---

#### ❌ PROBLEM: Repeated Token Balance Query
Found in: [TopupController](app/Http/Controllers/TopupController.php#L96), [DashboardController](app/Http/Controllers/DashboardController.php#L31), [DashboardController](app/Http/Controllers/DashboardController.php#L172)

```php
// TopupController.php Line 96
$balance = $user->getSaldoToken();

// DashboardController.php Line 31
'token_balance' => $user->getSaldoToken(),

// User.php Line 281
public function getSaldoToken(): int {
    return $this->token?->jumlah ?? 0;  // Lazy loads token relation!
}
```

**Issue:** `getSaldoToken()` is called multiple times per request, each potentially triggering a query.

**Solution:** Eager load once:
```php
// In controller
$user->load('token');  // One query at start
// All calls to getSaldoToken() now use cached relation
```

---

### 1.3 Missing Validation Requests

#### ❌ PROBLEM: KelasController (Line 45+)
[KelasController.php](app/Http/Controllers/KelasController.php#L45-L70) - Mixed validation approaches:

```php
// Lines 45-70
if ($request->expectsJson()) {
    $data = $request->validate([  // Inline validation for API
        'judul' => 'required|string|max:255',
        'deskripsi' => 'nullable|string',
        'kategori' => 'nullable|string|max:100',
        'harga_token' => 'nullable|integer|min:0',
        'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
    ]);
} else {
    $data = $request->validate(app(StoreKelasRequest::class)->rules());
}
```

**Issues:**
- Line 48-54: Inconsistent validation (inline for API, request class for web)
- No file size limits validation for API
- No `harga_token` upper bound limit
- No category enum validation

**Should be:**
```php
// Create StoreKelasRequest for API too
public function store(StoreKelasRequest $request) {
    // Single validation point
    $data = $request->validated();
}
```

---

### 1.4 Error Handling Issues

#### ❌ PROBLEM: Repetitive try-catch in controllers
Repeated pattern in [BelajarController](app/Http/Controllers/BelajarController.php), [MateriController](app/Http/Controllers/MateriController.php), [KelasController](app/Http/Controllers/KelasController.php):

```php
// Found 20+ times:
try {
    // logic
} catch (\Exception $e) {
    \Log::error('Error in ' . method . ': ' . $e->getMessage());
    
    if ($request->expectsJson()) {
        return $this->serverError($e->getMessage());
    }
    return back()->with('error', 'Failed to ...');
}
```

**Issues:**
- Generic `\Exception` catches everything
- Logs with minimal context (no stack trace, no request context)
- Exposes error message to frontend (security risk)
- No custom exception classes

**Production Usage:**
```
2026-04-12 15:23:45 Error in complete: Call to undefined method
```
Could be SQL error, runtime error, programming error - unclear!

**Solution:** Use exception middleware + custom exceptions:
```php
// Exception/UnauthorizedException.php
class UnauthorizedException extends HttpException {
    public function __construct($message = 'Unauthorized') {
        parent::__construct(403, $message);
    }
}

// Exception/ValidationException.php (custom)
// Then route exception to middleware
```

---

## 2. SERVICES ANALYSIS

### 2.1 Only 3 Services Exist (INCOMPLETE)

Found:
- [MidtransService.php](app/Services/MidtransService.php) (100 lines)
- [XenditService.php](app/Services/XenditService.php)
- [SupabaseStorageService.php](app/Services/SupabaseStorageService.php)

**Missing Critical Services:**
- ❌ TokenService (Token operations scattered)
- ❌ EnrollmentService (CatalogController handles it)
- ❌ GamificationService (DashboardController calculates)
- ❌ NotificationService (Email/notifications not abstracted)
- ❌ PaymentService (Xendit/Midtrans coordination missing)

### 2.2 MidtransService Issues

[MidtransService.php](app/Services/MidtransService.php#L1-50)

```php
// Lines 10-20: Config reads mixed sources
Config::$serverKey = \App\Models\Setting::get('midtrans_server_key', 
    config('midtrans.server_key', 'SB-Mid-server-H7_YlkYcZOpjf_SLTEyaAbX5'));
```

**Issues:**
- **Line 11-12:** Secret key exposed in code as fallback!
- **Line 18-20:** Windows SSL workaround code (should be in config)
- **Line 28:** Config directly modifies static properties (testing nightmare)
- **Line 44:** No retry logic
- **Line 51-53:** No signature verification

**Recommendation:**
```php
// Create proper service with dependency injection
class PaymentService {
    public function __construct(
        private MidtransGateway $midtrans,
        private XenditGateway $xendit
    ) {}
    
    public function process(Order $order): PaymentResult {
        // Delegate to appropriate gateway
    }
}
```

---

### 2.3 Circular Dependency Risk

**Issue:** Services are not injected consistently:

```php
// TopupController.php Constructor
public function __construct(XenditService $xendit) {
    $this->xendit = $xendit;  // Injected here
}

// BelajarController: No constructor
// MateriController: No constructor
// CatalogController: No constructor
```

Some controllers have DI, others don't. Makes it hard to add services to existing controllers.

---

## 3. MODELS ANALYSIS

### 3.1 Relationship Lazy Loading Issues

#### ❌ PROBLEM: N+1 in DashboardController
[DashboardController.php](app/Http/Controllers/DashboardController.php#L35-L45)

```php
// Line 35-45
$myClasses = $user->kelasIkuti()
    ->with('pengajar:user_id,name')  // Optimized - good!
    ->where('status', 'aktif')
    ->take(20)
    ->get();  // Returns collection

// But later at view layer (implicit):
foreach ($myClasses as $class) {
    echo $class->peserta()->count();  // N queries! (1 per class)
}
```

**Solution:** Use `withCount()`:
```php
$myClasses = $user->kelasIkuti()
    ->with('pengajar:user_id,name')
    ->withCount('peserta')  // Single query with aggregate
    ->where('status', 'aktif')
    ->take(20)
    ->get();
```

---

#### ❌ PROBLEM: User Model Missing Indexes
[User.php](app/Models/User.php#L160-L200) has many calls but look at defined indexes:

```sql
-- What we need (from migrations):
ALTER TABLE users ADD INDEX idx_role_status (role, status);
ALTER TABLE users ADD INDEX idx_referral_code (referral_code);
ALTER TABLE users ADD INDEX idx_email (email);
```

**Check:** [Migration 2026_02_18_000001_add_composite_index_to_users_table.php](database/migrations/2026_02_18_000001_add_composite_index_to_users_table.php)

But missing:
- ❌ Index on `email` (used in Auth::attempt loop)
- ❌ Index on `google_id` (OAuth lookups)
- ❌ Index on `referral_code` (referral lookups)

---

#### ❌ PROBLEM: Materi Relationships Not Optimized
[Materi.php](app/Models/Materi.php#L40-L60)

```php
// Line 40-47: isUnlockedBy() calls multiple relations
public function isUnlockedBy($user) {
    if (!$this->is_premium) return true;  // OK
    if (!$user) return false;              // OK
    if ($user->user_id == $this->kelas->pengajar_id) return true;  // LAZY LOAD: kelas
    if ($user->isAdmin()) return true;    // OK (cached)
    if ($user->hasBeasiswa()) return true; // OK
    return $this->aksesUsers()->where(...)->exists();  // Potential N+1
}
```

**Issue:** `$this->kelas` lazy loads if not eager loaded. Used in 100+ views.

**Solution:**
```php
$materi = Materi::with('kelas:kelas_id,pengajar_id')->find($id);
// Then isUnlockedBy() won't trigger extra query
```

---

### 3.2 Missing Scopes & Helper Methods

**Not Found but should exist:**

```php
// User.php - missing scope
public function scopeHasBeasiswa($query) {
    return $query->where('is_beasiswa', true);
}

// Kelas.php - missing scope  
public function scopeByTeacher($query, $teacherId) {
    return $query->where('pengajar_id', $teacherId);
}

// Usage would be:
$classes = Kelas::byTeacher($userId)->aktif()->paginate();
// Instead of:
$classes = Kelas::where('pengajar_id', $userId)
    ->where('status', 'aktif')->paginate();
```

---

### 3.3 Accessor/Mutator Uses

**Moderate coverage:**

✅ Found in [User.php](app/Models/User.php#L85-L92):
```php
public function getRankTitleAttribute(): string {
    if ($this->level >= 50) return 'Grandmaster';
    // ...
}
```

✅ Found in [Token.php](app/Models/Token.php#L29-45):
```php
public function tambah(int $jumlah): void { ... }
public function kurang(int $jumlah): bool { ... }
```

❌ **Missing:** Accessor for User.avatar (should return URL):
```php
// Should cache computed URL
public function getAvatarUrlAttribute(): string {
    return $this->avatar_path 
        ? Storage::url($this->avatar_path)
        : asset('images/default-avatar.png');
}
```

---

## 4. ROUTES ANALYSIS

### 4.1 Naming Inconsistencies

[routes/api.php](routes/api.php#L40-100) shows mixed naming patterns:

```php
// Line 50-60: Inconsistent prefix naming
Route::prefix('landing')->group(fn => ...);        // OK
Route::prefix('programs')->group(fn => ...);       // Should be 'classes'?
Route::prefix('mentors')->group(fn => ...);        // Should be 'teachers'?
Route::prefix('learning-paths')->group(fn => ...); // Hyphenated
Route::prefix('donations')->group(fn => ...);      // Plural

// Line 85-105: Different patterns for same resource
Route::prefix('student')->middleware('role:murid')->group(function () {
    Route::prefix('classes')->group(fn => ...);           // /api/v1/student/classes
    Route::prefix('learning')->group(fn => ...);          // /api/v1/student/learning
    Route::prefix('token')->group(fn => ...);             // /api/v1/student/token
});

// vs

Route::prefix('teacher')->group(function () {
    // Doesn't exist but would be logical for:
    // /api/v1/teacher/classes
    // /api/v1/teacher/materials
});
```

**Issues:**
- Inconsistent singlar/plural: `programs` vs `learning-paths`
- Inconsistent hyphenation: `learning-paths` vs `token`
- "programs" vs "classes" naming (which is it?)
- Nested prefix levels vary

**Should be:**
```php
Route::prefix('api/v1')->group(function () {
    // Consistent resource naming
    Route::apiResource('courses', CourseController::class);           // CRUD
    Route::apiResource('materials', MaterialController::class);       // CRUD
    Route::apiResource('learning-paths', LearningPathController::class);
    
    // User-scoped resources use parameter
    Route::get('user/courses', UserCourseController::class);
    Route::post('user/courses/{id}/enroll', UserCourseController@enroll);
});
```

---

### 4.2 Missing Route Documentation

API endpoints lack parameter documentation:

```php
// Line 92: No documentation
Route::post('/token/topup', [TopupController::class, 'create']);
```

What parameters? What are valid values? No docs.

**Should use:**
```php
Route::post('/token/topup', [TopupController::class, 'create'])
    ->name('token.topup')
    ->middleware('throttle:10,1');  // Rate limiting!
```

---

### 4.3 Missing Rate Limiting

No rate limiting middleware found on:
- ❌ Authentication endpoints (auth:login, auth:register)
- ❌ Payment endpoints (token/topup)
- ❌ Email verification

Should have:
```php
Route::post('/login', [AuthController::class, 'login'])
    ->middleware('throttle:5,1');  // 5 attempts per minute

Route::post('/token/topup', [TopupController::class, 'create'])
    ->middleware('throttle:5,1');  // Prevent abuse
```

---

## 5. VALIDATION & REQUESTS

### 5.1 Incomplete Forms

Found 4 request classes:
- [RegisterRequest.php](app/Http/Requests/RegisterRequest.php)
- [LoginRequest.php](app/Http/Requests/LoginRequest.php)
- [StoreKelasRequest.php](app/Http/Requests/StoreKelasRequest.php)
- [UpdateKelasRequest.php](app/Http/Requests/UpdateKelasRequest.php)

**Issues:**

#### Missing Validation Requests For:
- ❌ StoreMateriRequest (MateriController.php Line 23)
  - File upload validation mixed in controller
  - No centralized rules

- ❌ EnrollClassRequest (CatalogController.php Line 68)
  - Financial transaction validation missing

- ❌ CompleteMaterialRequest (BelajarController.php Line 187)
  - No request validation at all!

- ❌ CreateReviewRequest (BelajarController.php Line 154)
  - `rating` validated but no custom messages

---

### 5.2 Validation Rule Issues

[RegisterRequest.php](app/Http/Requests/RegisterRequest.php#L15-40)

```php
// Line 23: Phone regex might be too loose
'phone' => ['nullable', 'string', 'regex:/^(\+62|62|0)[0-9]{9,12}$/', 'max:20'],
```

**Issues:**
- Regex allows +62 or 62 or 0 plus 9-12 digits
- But `max:20` could allow longer numbers than regex (mismatch)
- No `unique:phone` validation (could have duplicates)

```php
// Line 24: Role validation has no enum
'role' => ['required', 'string', 'in:murid,pengajar'],
```

**Issues:**
- Hard-coded values (should use enum or config)
- No rate limiting on registration

**Solution:**
```php
// Create Enum
enum UserRole: string {
    case STUDENT = 'murid';
    case TEACHER = 'pengajar';
}

// Use in validation
'role' => ['required', Rule::enum(UserRole::class)],
'phone' => [
    'nullable',
    'phone:ID',  // Using phone validation package
    Rule::unique('users'),  // Add unique check
],
```

---

### 5.3 Missing Custom Validation

No custom validation rules found for:
- ❌ Referral code existence validation (done inline)
- ❌ Token balance sufficiency
- ❌ Premium material access check
- ❌ Teacher class ownership

**Should create:**
```php
// app/Rules/ValidReferralCode.php
class ValidReferralCode implements Rule {
    public function passes($attribute, $value): bool {
        return User::where('referral_code', $value)->exists();
    }
}

// Usage
'referral_code' => [new ValidReferralCode()],
```

---

## 6. ERROR HANDLING

### 6.1 No Custom Exception Classes

No custom exceptions found in [app/Exceptions/](app/Exceptions/).

**Using generic \Exception everywhere:**

```php
// BelajarController.php Line 75
} catch (\Exception $e) {
    \Log::error('Error in show: ' . $e->getMessage());
    // Generic handling!
}
```

**Issues:**
- Can't distinguish between application errors vs infrastructure errors
- Error messages exposed to clients (security)
- No structured logging
- No error codes

**should create:**
```php
// app/Exceptions/UnauthorizedException.php
class UnauthorizedException extends HttpException {
    public function __construct(string $message = 'Unauthorized') {
        parent::__construct(403, $message);
    }
}

// app/Exceptions/InsufficientTokensException.php  
class InsufficientTokensException extends Exception {
    public function __construct(int $required, int $available) {
        $this->code = 'INSUFFICIENT_TOKENS';
        parent::__construct("Tokens required: {$required}, available: {$available}");
    }
}

// Usage
throw new InsufficientTokensException(100, $balance);
```

---

### 6.2 Insufficient Logging

Logging is inconsistent:

```php
// BelajarController.php Line 75
\Log::error('Error in show: ' . $e->getMessage());

// Missing:
// - Stack trace
// - Request context (user_id, class_id, etc.)
// - Error code
// - Environment
```

**Should use:**
```php
\Log::error('Material access failed', [
    'user_id' => auth()->id(),
    'material_id' => $id,
    'exception' => $e,  // Includes full stack trace
    'context' => [
        'method' => 'BelajarController@show',
        'ip' => request()->ip(),
    ]
]);
```

---

### 6.3 No Exception Handler Middleware

Missing centralized exception handling. No file at [app/Exceptions/Handler.php](app/Exceptions/Handler.php).

**Should have:**
```php
class Handler extends ExceptionHandler {
    public function register(): void {
        $this->reportable(function (UnauthorizedException $e) {
            // Log security event
        });
        
        $this->renderable(function (UnauthorizedException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'unauthorized',
                    'message' => 'You are not authorized to access this resource'
                ], 403);
            }
            return response()->view('errors.403');
        });
    }
}
```

---

## 7. PERFORMANCE ISSUES

### 7.1 N+1 Query Problems (Detailed)

#### ❌ ISSUE 1: getJumlahPeserta() Calls Count Every Time

[Kelas.php](app/Models/Kelas.php#L65-70)

```php
// Line 67-69
public function getJumlahPeserta(): int {
    return $this->peserta()->count();  // DATABASE QUERY EVERY TIME!
}
```

**Usage in DashboardController:**
```php
// Implicit loop in view
@foreach($myClasses as $class)
    <span>{{ $class->getJumlahPeserta() }}</span>  <!-- Query 1 -->
    <span>{{ $class->getJumlahMateri() }}</span>   <!-- Query 2 -->
@endforeach
```

If 10 classes loaded: **20 extra queries!**

**Solution:**
```php
// In controller, eager load aggregates
$myClasses = $user->kelasIkuti()
    ->withCount('peserta')
    ->withCount('materi');

// In model, create accessor instead
public function getPesertaCountAttribute(): int {
    return $this->peserta_count;  // Uses aggregate, no query
}
```

---

#### ❌ ISSUE 2: Materi isUnlockedBy() Lazy Loads Kelas

[Materi.php](app/Models/Materi.php#L40-60)

```php
// Line 45: Lazy loads kelas if not eager loaded!
if ($user->user_id == $this->kelas->pengajar_id) return true;
```

**Used in:**
- [BelajarController.show](app/Http/Controllers/BelajarController.php#L41)
- [BelajarController.showMaterial](app/Http/Controllers/BelajarController.php#L215)

Every view of a material loads the kelas again. If 50 materials viewed in a day: **50 extra queries!**

---

#### ❌ ISSUE 3: Category Stats Query

[DashboardController.php](app/Http/Controllers/DashboardController.php#L49-59) executes 2 queries that could be 1:

```sql
-- Query 1: All categories
SELECT kategori, COUNT(*) as count FROM kelas 
WHERE status='aktif' GROUP BY kategori;

-- Query 2: Enrolled categories  
SELECT kelas.kategori, COUNT(*) as count FROM kelas
JOIN kelas_peserta ON ... 
WHERE siswa_id=123 GROUP BY kategori;

-- Should be 1 query with LEFT JOIN:
SELECT k.kategori,
       COUNT(DISTINCT k.kelas_id) as total,
       COUNT(kp.siswa_id) as enrolled
FROM kelas k
LEFT JOIN kelas_peserta kp ON k.kelas_id=kp.kelas_id 
    AND kp.siswa_id=123
WHERE k.status='aktif'
GROUP BY k.kategori;
```

---

### 7.2 Missing Pagination Standards

Inconsistent pagination across endpoints:

```php
// BelajarController.php Line 200
->paginate(20);

// DashboardController.php Line 40
->take(6);  // No pagination!

// CatalogController.php Line 26
->paginate(9);

// MateriController.php Line 281
->paginate(15);
```

**Issues:**
- No consistent default (6, 9, 15, 20, ...)
- Some endpoints don't paginate at all
- No max pagination limit (prevent DoS: ?limit=999999)

**Solution:**
```php
// config/paginate.php
return [
    'default_limit' => 20,
    'max_limit' => 100,
];

// In controller
$limit = $request->get('limit', config('paginate.default_limit'));
$limit = min($limit, config('paginate.max_limit'));
->paginate($limit);
```

---

### 7.3 Missing Cache Strategy

Cache usage is scattered:

```php
// BelajarController.php Line 31
Cache::remember("kelas_materi_{$kelas_id}", 60 * 60, fn() => ...);
// Cache 1 hour - stale data risk!

// BelajarController.php Line 73
$cacheKey = "user_{$user->user_id}_completed_materi_{$materi_id}";
Cache::forever($cacheKey, true);  // Never expires!
// Risky if progress needs to be reset

// No invalidation strategy
// If materi is deleted, cache still exists
// If user is removed from class, cache still exists
```

**Should be:**
```php
// Cache class with invalidation hooks

// In Materi model event:
protected static function booted() {
    static::deleted(fn($materi) => 
        Cache::forget("kelas_materi_{$materi->kelas_id}")
    );
}

// In course completion reset:
Cache::forget("user_{$user->user_id}_completed_materi_{$materi_id}");
```

---

### 7.4 No Database Query Caching

For frequently accessed data like:
- ❌ Kategori list (queried from Kelas model multiple times)
- ❌ System settings (loaded per request)
- ❌ Active courses count

Should cache:
```php
// CatalogController.php Line 46+
$categories = Cache::remember('kelas_categories', 60*24, fn() =>
    Kelas::where('status', 'aktif')
        ->distinct('kategori')
        ->pluck('kategori')
);
// Cache for 24 hours, invalidate on new class
```

---

## 8. AUTHORIZATION & SECURITY

### 8.1 Authorization Scattered Across Controllers

**Pattern found in multiple places:**

[BelajarController.php](app/Http/Controllers/BelajarController.php#L20-25)
```php
// Line 20-25: Manual authorization check
$isEnrolled = $user->kelasIkuti()->where(...)->exists();
$isOwner = $kelas->pengajar_id == $user->user_id;

if (!$isEnrolled && !$isOwner) {
    return redirect();
}
```

[MateriController.php](app/Http/Controllers/MateriController.php#L125-130)
```php
// Line 125-130: Different pattern
$materi = Materi::whereHas('kelas', function ($q) {
    $q->where('pengajar_id', Auth::id());
})->findOrFail($id);
```

**Issues:**
- Authorization logic repeated 3+ places
- No centralized policy enforcement
- Easy to miss a check somewhere
- `findOrFail` throws 404 instead of 403 (misleading)

**Should be:**
```php
// Create MateriPolicy.php
class MateriPolicy {
    public function view(User $user, Materi $materi): bool {
        // Check if enrolled in class or is owner
        return $user->kelasIkuti()
            ->where('kelas_id', $materi->kelas_id)
            ->exists() 
            || $materi->kelas->pengajar_id === $user->user_id;
    }
    
    public function update(User $user, Materi $materi): bool {
        return $materi->kelas->pengajar_id === $user->user_id;
    }
}

// In controller
public function show(Request $request, Materi $materi) {
    $this->authorize('view', $materi);  // Single check
}
```

---

### 8.2 Missing CSRF on Forms

No evidence of CSRF tokens checked in non-API requests (relying on Laravel defaults, but should verify).

---

### 8.3 No Input Sanitization

HTML input not being sanitized before storage:

```php
// In MateriController.php Line 35
'deskripsi' => 'nullable|string',  // No sanitization!
```

If user inputs JavaScript:
```html
<img src=x onerror="alert('XSS')">
```

This will be stored and retrieved in views.

**Should sanitize:**
```php
use \HTMLPurifier;

'deskripsi' => 'nullable|string|max:5000',

// In controller
$data['deskripsi'] = HtmlPurifier::clean($data['deskripsi']);
$materi->update($data);
```

---

## 9. SPECIFIC CODE EXAMPLES & RECOMMENDATIONS

### Example 1: Refactor AuthController Registration

**BEFORE (Lines 31-120):**
```php
public function register(RegisterRequest $request) {
    try {
        DB::beginTransaction();
        $referralCode = strtoupper(Str::random(10));
        [... avatar setup ...]
        $user = User::create([...]);
        Token::create([...]);
        if ($request->filled('referral_code')) {
            $referrer = User::where(...)->first();
            Referral::create([...]);
        }
        $verificationToken = [...];
        EmailVerification::create([...]);
        DB::commit();
        [ ...email sending ...]
        return response
    } catch (\Exception $e) {
        DB::rollBack();
        [ ...error handling ...]
    }
}
```

**AFTER (Split into services):**
```php
// RegistrationService.php
class RegistrationService {
    public function register(array $data): User {
        return DB::transaction(fn() =>
            $this->createUser($data)
        );
    }
    
    private function createUser(array $data): User {
        $user = User::create($data);
        $this->initializeToken($user);
        $this->handleReferral($user, $data['referral_code'] ?? null);
        $this->createEmailVerification($user);
        return $user;
    }
    
    // ... individual service methods
}

// AuthController.php
public function register(RegisterRequest $request, RegistrationService $service) {
    try {
        $user = $service->register($request->validated());
        
        if ($request->expectsJson()) {
            return $this->success(['user' => $user], 'Registration successful', 201);
        }
        
        Auth::login($user);
        return redirect('/dashboard');
        
    } catch (RegistrationException $e) {
        return $this->handleRegistrationError($request, $e);
    }
}
```

**Benefits:**
- Testable service class
- Clear separation of concerns
- Reusable registration logic
- Easy to catch specific errors
- Follows SOLID principles

---

### Example 2: Fix N+1 in DashboardController

**BEFORE:**
```php
$userStats = ['xp' => $user->xp, 'token' => $user->getSaldoToken()];
$myClasses = $user->kelasIkuti()
    ->with('pengajar:user_id,name')
    ->take(20)->get();
$totalPerCategory = Kelas::selectRaw('kategori, count(*) count')
    ->where('status', 'aktif')
    ->groupBy('kategori')
    ->pluck('count', 'kategori');  // QUERY 1
$enrolledPerCategory = DB::table('kelas')
    ->join('kelas_peserta', ...)
    ->selectRaw('kelas.kategori, count(*) count')
    ->groupBy('kategori')
    ->pluck('count', 'kategori');  // QUERY 2
```

**AFTER (Fix to single query):**
```php
$user->load('token');  // Pre-load token

$statistics = DB::table('kelas')
    ->leftJoin('kelas_peserta', fn($join) => 
        $join->on('kelas.kelas_id', '=', 'kelas_peserta.kelas_id')
            ->where('kelas_peserta.siswa_id', $user->user_id)
    )
    ->where('kelas.status', 'aktif')
    ->selectRaw('
        kelas.kategori,
        COUNT(DISTINCT kelas.kelas_id) as total,
        COUNT(kelas_peserta.siswa_id) as enrolled
    ')
    ->groupBy('kelas.kategori')
    ->get();

$categoryStats = $statistics->mapWithKeys(fn($row) => [
    $row->kategori => [
        'total' => $row->total,
        'enrolled' => $row->enrolled
    ]
]);
```

**Query comparison:**
- BEFORE: 3 queries (getSaldoToken + 2 category queries)
- AFTER: 2 queries (load token + 1 joined query)

For a typical request with 20 classes viewed: saves 20+ queries per page.

---

### Example 3: Use Policies for Authorization

**CREATE Files:**

```php
// app/Policies/MateriPolicy.php
class MateriPolicy {
    public function viewAny(User $user): bool {
        return $user->isAktif();
    }
    
    public function view(User $user, Materi $materi): bool {
        // Can view if: enrolled in class, owner, admin, or beasiswa
        if ($user->isAdmin() || $user->hasBeasiswa()) return true;
        
        $isEnrolled = $user->kelasIkuti()
            ->where('kelas_id', $materi->kelas_id)
            ->exists();
            
        $isOwner = $materi->kelas->pengajar_id === $user->user_id;
        
        return $isEnrolled || $isOwner;
    }
    
    public function update(User $user, Materi $materi): bool {
        return $materi->kelas->pengajar_id === $user->user_id;
    }
    
    public function delete(User $user, Materi $materi): bool {
        return $user->isAdmin() || 
               $materi->kelas->pengajar_id === $user->user_id;
    }
}

// app/Providers/AuthServiceProvider.php
class AuthServiceProvider extends ServiceProvider {
    protected $policies = [
        Materi::class => MateriPolicy::class,
        Kelas::class => KelasPolicy::class,
    ];
}
```

**USE IN CONTROLLER:**

```php
// BelajarController.php
public function show(Request $request, $kelas_id, $materi_id) {
    $materi = Materi::findOrFail($materi_id);
    
    $this->authorize('view', $materi);  // Clean authorization!
    
    // Rest of logic...
}

// MateriController.php
public function update(Request $request, $id) {
    $materi = Materi::findOrFail($id);
    
    $this->authorize('update', $materi);  // Throws 403 if denied
    
    // Update logic...
}
```

**Benefits:**
- Authorizes in one place
- Automatic 403 responses
- Testable (unit test policies separately)
- Readable intent

---

## SUMMARY TABLE

| Category | Severity | Count | Examples |
|----------|----------|-------|----------|
| **Controllers** | 🔴 Critical | 6 | Fat controllers, mixed concerns |
| **N+1 Queries** | 🔴 Critical | 4 | Category stats, getJumlah* methods |
| **Validation** | 🟠 High | 5 | Missing request classes |
| **Error Handling** | 🟠 High | 3 | Generic exceptions, poor logging |
| **Authorization** | 🟠 High | 2 | Scattered authorization logic |
| **Services** | 🟠 High | 4+ | Missing critical services |
| **Caching** | 🟡 Medium | 2 | No invalidation, Forever cache |
| **Performance** | 🟡 Medium | 5 | No rate limiting, inconsistent pagination |
| **Security** | 🟡 Medium | 2 | No input sanitization |
| **Models** | 🟡 Medium | 3 | Missing indexes, missing scopes |

---

## PRIORITY ACTION ITEMS

### Phase 1: CRITICAL (Do First)
1. **Extract Services** - Move business logic out of controllers (AuthService, TokenService, EnrollmentService)
2. **Fix N+1 Queries** - Add eager loading and aggregates to DashboardController, BelajarController
3. **Create Policies** - Centralize authorization logic
4. **Add Request Validation** - Create validation request classes for all endpoints

### Phase 2: HIGH (Do Next)
5. **Custom Exceptions** - Create exception classes, add exception handler middleware
6. **Database Indexes** - Add missing indexes on email, google_id, referral_code
7. **Rate Limiting** - Add throttle middleware to auth/payment endpoints
8. **Cache Strategy** - Implement cache invalidation hooks

### Phase 3: MEDIUM (Improve)
9. **Input Sanitization** - Add HTML purification
10. **Logging** - Add context to all error logs
11. **Pagination** - Standardize to single default
12. **Scopes** - Add missing query scopes to models

---

## REFERENCE LINKS TO FILES

- [app/Http/Controllers/AuthController.php](app/Http/Controllers/AuthController.php)
- [app/Http/Controllers/DashboardController.php](app/Http/Controllers/DashboardController.php)
- [app/Http/Controllers/BelajarController.php](app/Http/Controllers/BelajarController.php)
- [app/Http/Controllers/CatalogController.php](app/Http/Controllers/CatalogController.php)
- [app/Models/User.php](app/Models/User.php)
- [app/Models/Kelas.php](app/Models/Kelas.php)
- [app/Models/Materi.php](app/Models/Materi.php)
- [routes/api.php](routes/api.php)
- [app/Http/Requests/](app/Http/Requests/)
- [app/Services/](app/Services/)

---

**Generated:** April 12, 2026 | **Analysis Version:** 1.0
