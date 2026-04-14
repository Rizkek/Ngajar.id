# 🔐 PHASE 1 SECURITY IMPLEMENTATION - Ngajar.ID

**Purpose**: Execute 3 CRITICAL security fixes before production launch  
**Timeline**: 6.5 hours total  
**Blocking**: Cannot go production without this  
**Status**: Ready to implement  

---

## 📋 Phase 1 Tasks Summary

| Task | Issue | Impact | Time | Risk |
|------|-------|--------|------|------|
| **1. Rate Limiting** | DOS/Brute force vulnerability | Server protection | 1.5h | HIGH |
| **2. Webhook Validation** | Payment spoofing risk | Revenue protection | 2h | CRITICAL |
| **3. Token Expiration** | Session hijacking risk | Account security | 1h | HIGH |
| **4. HTTPS + Security Headers** | Man-in-the-middle attacks | Data in transit | 1h | HIGH |  
| **5. APP_DEBUG=false** | Information disclosure | Stack traces exposed | 0.5h | MEDIUM |

**TOTAL**: 6.5 hours  
**Parallelizable**: Tasks 1, 3, 4, 5 can run in parallel  
**Critical Path**: Task 2 (Webhook validation)

---

## 🔴 TASK 1: RATE LIMITING (1.5 hours)

### Problem

```php
// ❌ CURRENT: No protection
Route::post('/login', [AuthController::class, 'login']);
Route::post('/password/forgot', [PasswordController::class, 'forgot']);
Route::get('/programs', [CatalogController::class, 'index']); // Unlimited scraping
```

**Without rate limiting**:
- Attacker: 230K login attempts/hour
- Attacker: Brute force password in minutes
- Attacker: Scrape entire catalog in seconds
- Result: Server crashes, revenue exposure

### Solution

#### Step 1: Create Rate Limit Middleware

```php
<?php

// app/Http/Middleware/ThrottleApiRequests.php

namespace App\Http\Middleware;

use Illuminate\Routing\Middleware\ThrottleRequests;

class ThrottleApiRequests extends ThrottleRequests
{
    /**
     * Get the throttle key for the request.
     * Usage: middleware('throttle:api')
     */
    protected function resolveRequestSignature($request)
    {
        // Use user ID if authenticated, otherwise IP
        if ($request->user()) {
            return $request->user()->getKey();
        }

        return $request->ip();
    }
}
```

#### Step 2: Register Throttle Limits

```php
<?php

// app/Providers/RouteServiceProvider.php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;

class RouteServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->configureRateLimiting();
        $this->routes(function () {
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));

            Route::middleware('web')
                ->group(base_path('routes/web.php'));
        });
    }

    protected function configureRateLimiting(): void
    {
        // ✅ LOGIN: 5 attempts per minute per IP
        RateLimiter::for('login', function (Request $request) {
            return Limit::perMinute(5)
                ->by($request->email ?: $request->ip())
                ->response(function () {
                    return response()->json([
                        'message' => 'Too many login attempts. Try again in 1 minute.',
                        'retry_after' => 60,
                    ], 429);
                });
        });

        // ✅ PASSWORD RESET: 3 attempts per 15 minutes per email
        RateLimiter::for('forgot-password', function (Request $request) {
            return Limit::perMinutes(15, 3)
                ->by($request->email ?: $request->ip())
                ->response(function () {
                    return response()->json([
                        'message' => 'Too many password reset attempts. Try again in 15 minutes.',
                        'retry_after' => 900,
                    ], 429);
                });
        });

        // ✅ API: 100 requests per minute per user/IP
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(100)
                ->by(optional($request->user())->id ?: $request->ip());
        });

        // ✅ PUBLIC API: 30 requests per minute (unauthenticated)
        RateLimiter::for('public', function (Request $request) {
            return Limit::perMinute(30)
                ->by($request->ip())
                ->response(function () {
                    return response()->json([
                        'message' => 'Rate limit exceeded. Maximum 30 requests per minute.',
                        'retry_after' => 60,
                    ], 429);
                });
        });

        // ✅ REGISTRATION: 5 per hour per IP
        RateLimiter::for('register', function (Request $request) {
            return Limit::perHour(5)
                ->by($request->ip())
                ->response(function () {
                    return response()->json([
                        'message' => 'Too many registration attempts. Try again later.',
                    ], 429);
                });
        });

        // ✅ WEBHOOK: 1000 per hour (payment webhooks from Midtrans/Xendit)
        RateLimiter::for('webhook', function (Request $request) {
            return Limit::perHour(1000)
                ->by($request->ip());
        });
    }
}
```

#### Step 3: Apply Rate Limiting to Routes

```php
<?php

// routes/api.php

// ✅ Auth routes with strict limits
Route::middleware(['throttle:login'])->group(function () {
    Route::post('/auth/login', [AuthController::class, 'login']);
    Route::post('/auth/register', [AuthController::class, 'register']);
});

Route::middleware(['throttle:forgot-password'])->group(function () {
    Route::post('/password/forgot', [PasswordController::class, 'forgot']);
    Route::post('/password/reset', [PasswordController::class, 'reset']);
});

// ✅ API routes with moderate limits
Route::middleware(['auth:sanctum', 'throttle:api'])->group(function () {
    Route::get('/me', [UserController::class, 'me']);
    Route::get('/programs', [CatalogController::class, 'index']);
    // ... more routes
});

// ✅ Webhook routes with permissive limits (external service)
Route::middleware(['throttle:webhook'])->group(function () {
    Route::post('/webhook/midtrans', [WebhookController::class, 'midtrans']);
    Route::post('/webhook/xendit', [WebhookController::class, 'xendit']);
});

// ✅ Public routes with strict limits
Route::middleware(['throttle:public'])->group(function () {
    Route::get('/public/categories', [CatalogController::class, 'categories']);
});
```

#### Step 4: Monitor Rate Limiting

```php
<?php

// app/Http/Middleware/LogRateLimitExceeded.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LogRateLimitExceeded
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // Log when rate limit is exceeded
        if ($response->status() === 429) {
            Log::warning('Rate limit exceeded', [
                'ip' => $request->ip(),
                'user_id' => optional($request->user())->id,
                'endpoint' => $request->path(),
                'method' => $request->method(),
            ]);

            // Optional: Send alert to admin if many attempts
            if (rand(1, 10) === 1) { // Log 10% to prevent log spam
                \App\Models\SecurityAlert::create([
                    'type' => 'rate_limit_exceeded',
                    'ip_address' => $request->ip(),
                    'endpoint' => $request->path(),
                    'detected_at' => now(),
                ]);
            }
        }

        return $response;
    }
}
```

### Testing Rate Limiting

```bash
# Test login rate limiting
for i in {1..6}; do
  curl -X POST http://localhost:8000/api/auth/login \
    -H "Content-Type: application/json" \
    -d '{"email":"test@test.com","password":"wrong"}' \
    -w "\nStatus: %{http_code}\n"
  echo "Attempt $i"
done

# Should succeed 5 times, 6th returns 429
```

---

## 🔴 TASK 2: WEBHOOK SIGNATURE VALIDATION (2 hours)

### Problem

```php
// ❌ DANGEROUS - No validation!
Route::post('/webhook/payment', function (Request $request) {
    $data = json_decode($request->getContent());
    
    // If attacker sends:
    // POST /webhook/payment
    // {"order_id": 123, "status": "success", "amount": 1000}
    // 
    // We mark order as paid without verifying it came from Midtrans!
    
    Payment::where('order_id', $data->order_id)
        ->update(['status' => 'success']);
});
```

**Attack Scenario**:
1. Attacker finds order ID (123) from canceled purchase
2. Attacker sends fake webhook: `{"order_id": 123, "status": "success"}`
3. System marks order as paid (it wasn't!)
4. Revenue loss: $XXX × risk level


### Solution

#### Step 1: Create Webhook Service with Signature Validation

```php
<?php

// app/Services/WebhookValidationService.php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WebhookValidationService
{
    /**
     * Validate Midtrans webhook signature
     * 
     * @param Request $request
     * @return bool
     */
    public static function validateMidtransSignature(Request $request): bool
    {
        try {
            $body = $request->getContent();
            $signature = $request->header('X-Crypt-Timestamp') . $request->header('X-Crypt-Hash');
            
            // Midtrans uses SHA256(body + secret) format
            $expectedSignature = hash('sha256', $body . env('MIDTRANS_SERVER_KEY'));
            $providedSignature = $request->header('X-Signature-Key');
            
            // Use hash_equals to prevent timing attacks
            return hash_equals($expectedSignature, $providedSignature);
            
        } catch (\Exception $e) {
            Log::error('Midtrans signature validation failed', [
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Validate Xendit webhook signature
     * 
     * @param Request $request
     * @return bool
     */
    public static function validateXenditSignature(Request $request): bool
    {
        try {
            $body = $request->getContent();
            $signature = $request->header('X-Callback-Token');
            
            // Xendit uses HMAC-SHA256
            $expectedSignature = hash_hmac('sha256', $body, env('XENDIT_WEBHOOK_VERIFICATION_TOKEN'));
            
            return hash_equals($signature, $expectedSignature);
            
        } catch (\Exception $e) {
            Log::error('Xendit signature validation failed', [
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Prevent replay attacks by checking timestamp
     */
    public static function isWithinTimeWindow(int $timestamp, int $windowSeconds = 300): bool
    {
        $now = time();
        $diff = abs($now - $timestamp);
        
        // Reject if older than 5 minutes
        if ($diff > $windowSeconds) {
            Log::warning('Webhook rejected: timestamp outside acceptable window', [
                'timestamp' => $timestamp,
                'current' => $now,
                'diff' => $diff,
            ]);
            return false;
        }
        
        return true;
    }
}
```

#### Step 2: Create Webhook Controller with Validation

```php
<?php

// app/Http/Controllers/WebhookController.php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Transaction;
use App\Services\WebhookValidationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    /**
     * Handle Midtrans payment webhook
     * 
     * POST /webhook/midtrans
     * 
     * Example payload:
     * {
     *   "transaction_id": "5e80b743-3d88-4f07-b29e-cb0f9c7f3c3d",
     *   "order_id": "ORDER-123",
     *   "gross_amount": 100000,
     *   "payment_type": "credit_card",
     *   "transaction_status": "settlement"
     * }
     */
    public function midtrans(Request $request)
    {
        // ✅ Step 1: Validate signature
        if (!WebhookValidationService::validateMidtransSignature($request)) {
            Log::error('Midtrans webhook failed signature validation', [
                'ip' => $request->ip(),
                'order_id' => $request->input('order_id'),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Invalid signature',
            ], 401);
        }

        // ✅ Step 2: Validate timestamp (prevent replay)
        $timestamp = $request->input('transaction_time');
        if (!WebhookValidationService::isWithinTimeWindow(strtotime($timestamp))) {
            return response()->json([
                'success' => false,
                'message' => 'Timestamp outside acceptable window',
            ], 400);
        }

        try {
            return DB::transaction(function () use ($request) {
                $orderId = $request->input('order_id');
                $transactionStatus = $request->input('transaction_status');
                $transactionId = $request->input('transaction_id');
                $grossAmount = $request->input('gross_amount');

                // ✅ Step 3: Find order
                $order = Order::findOrFail($orderId);

                // ✅ Step 4: Validate amount matches
                if ((int) $grossAmount !== (int) $order->total_amount) {
                    Log::error('Midtrans webhook amount mismatch', [
                        'order_id' => $orderId,
                        'expected' => $order->total_amount,
                        'received' => $grossAmount,
                    ]);

                    return response()->json([
                        'success' => false,
                        'message' => 'Amount mismatch',
                    ], 400);
                }

                // ✅ Step 5: Update order based on status
                switch ($transactionStatus) {
                    case 'settlement':
                    case 'capture':
                        $order->update([
                            'status' => 'paid',
                            'paid_at' => now(),
                            'transaction_id' => $transactionId,
                        ]);

                        // Event: Order paid (send email, grant access, etc)
                        event(new \App\Events\OrderPaid($order));

                        Log::info('Midtrans payment confirmed', [
                            'order_id' => $orderId,
                            'amount' => $grossAmount,
                        ]);
                        break;

                    case 'pending':
                        $order->update(['status' => 'pending']);
                        break;

                    case 'deny':
                    case 'cancel':
                        $order->update(['status' => 'failed']);
                        Log::warning('Midtrans payment denied', ['order_id' => $orderId]);
                        break;

                    default:
                        Log::warning('Unknown Midtrans status', [
                            'status' => $transactionStatus,
                            'order_id' => $orderId,
                        ]);
                }

                // ✅ Step 6: Create transaction record
                Transaction::create([
                    'order_id' => $orderId,
                    'external_id' => $transactionId,
                    'gateway' => 'midtrans',
                    'status' => $transactionStatus,
                    'amount' => $grossAmount,
                    'raw_response' => json_encode($request->all()),
                ]);

                return response()->json(['success' => true], 200);
            });

        } catch (\Exception $e) {
            Log::error('Midtrans webhook processing failed', [
                'error' => $e->getMessage(),
                'order_id' => $request->input('order_id'),
            ]);

            // Return 500 so Midtrans retries
            return response()->json([
                'success' => false,
                'message' => 'Processing error',
            ], 500);
        }
    }

    /**
     * Handle Xendit payment webhook
     */
    public function xendit(Request $request)
    {
        // ✅ Similar validation for Xendit
        if (!WebhookValidationService::validateXenditSignature($request)) {
            Log::error('Xendit webhook failed validation', [
                'ip' => $request->ip(),
            ]);
            return response()->json(['success' => false], 401);
        }

        try {
            // Process Xendit webhook
            $externalId = $request->input('external_id');
            $status = $request->input('status');
            $amount = $request->input('amount');

            $order = Order::where('xendit_id', $externalId)->firstOrFail();

            // Update based on status
            if ($status === 'PAID') {
                $order->update(['status' => 'paid', 'paid_at' => now()]);
                event(new \App\Events\OrderPaid($order));
            }

            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            Log::error('Xendit webhook failed', ['error' => $e->getMessage()]);
            return response()->json(['success' => false], 500);
        }
    }
}
```

#### Step 3: Register Webhook Routes

```php
<?php

// routes/api.php

Route::middleware(['throttle:webhook'])->group(function () {
    Route::post('/webhook/midtrans', [WebhookController::class, 'midtrans']);
    Route::post('/webhook/xendit', [WebhookController::class, 'xendit']);
});
```

### Testing Webhook Validation

```bash
# Test with correct signature
curl -X POST http://localhost:8000/api/webhook/midtrans \
  -H "Content-Type: application/json" \
  -H "X-Signature-Key: correct-signature" \
  -d '{
    "order_id": "ORDER-123",
    "gross_amount": 100000,
    "transaction_status": "settlement",
    "transaction_time": "'$(date -u +"%Y-%m-%d %H:%M:%S")'",
    "transaction_id": "5e80b743-3d88-4f07-b29e-cb0f9c7f3c3d"
  }'

# Should return: {"success": true}

# Test with wrong signature
curl -X POST http://localhost:8000/api/webhook/midtrans \
  -H "Content-Type: application/json" \
  -H "X-Signature-Key: wrong-signature" \
  -d '{"order_id": "ORDER-123", ...}'

# Should return 401: {"success": false, "message": "Invalid signature"}
```

---

## 🟡 TASK 3: TOKEN EXPIRATION (1 hour)

### Problem

```php
// ❌ CURRENT: Tokens never expire
$token = $user->createToken('api-token')->plainTextToken;
// Token is valid forever!
```

**Risk**:
- Stolen token = permanent access
- Old tokens never invalidated
- Compromised device = lifetime risk

### Solution

#### Step 1: Create Token Model with Expiration

```php
<?php

// app/Models/PersonalAccessToken.php

namespace App\Models;

use Laravel\Sanctum\PersonalAccessToken as SanctumPersonalAccessToken;

class PersonalAccessToken extends SanctumPersonalAccessToken
{
    protected $casts = [
        'expires_at' => 'datetime',
        'last_used_at' => 'datetime',
    ];

    /**
     * Scope: Get only valid (not expired) tokens
     */
    public function scopeValid($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('expires_at')
              ->orWhere('expires_at', '>', now());
        });
    }

    /**
     * Check if token is expired
     */
    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    /**
     * Refresh token expiration
     */
    public function refresh()
    {
        $this->update([
            'expires_at' => now()->addHours(24), // Refresh for 24 more hours
            'last_used_at' => now(),
        ]);
    }
}
```

#### Step 2: Update Middleware to Check Expiration

```php
<?php

// app/Http/Middleware/CheckTokenExpiration.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckTokenExpiration
{
    public function handle(Request $request, Closure $next)
    {
        // Get the token from request
        if ($request->user() && auth('sanctum')->check()) {
            $token = $request->user()->currentAccessToken();

            if ($token && $token->isExpired()) {
                // Token expired, revoke it
                $token->delete();

                return response()->json([
                    'message' => 'Token has expired. Please login again.',
                    'error_code' => 'TOKEN_EXPIRED',
                ], 401);
            }

            // Refresh token last_used_at
            if ($token) {
                $token->update(['last_used_at' => now()]);
            }
        }

        return $next($request);
    }
}
```

#### Step 3: Create Token Expiration Migration

```php
<?php

// database/migrations/yyyy_mm_dd_hhmmss_add_expiration_to_personal_access_tokens.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('personal_access_tokens', function (Blueprint $table) {
            $table->timestamp('expires_at')->nullable()->after('abilities');
            $table->timestamp('last_used_at')->nullable()->after('expires_at');
            $table->index('expires_at'); // Fast expiration lookups
        });
    }

    public function down(): void
    {
        Schema::table('personal_access_tokens', function (Blueprint $table) {
            $table->dropColumn(['expires_at', 'last_used_at']);
            $table->dropIndex(['expires_at']);
        });
    }
};
```

#### Step 4: Update Login to Set Expiration

```php
<?php

// app/Http/Controllers/AuthController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (!Auth::attempt($credentials)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $user = Auth::user();

        // ✅ Create token with expiration
        $token = $user->createToken('api-token', ['*'], [
            'expires_at' => now()->addHours(24), // Token valid for 24 hours
        ])->plainTextToken;

        return response()->json([
            'user' => $user,
            'token' => $token,
            'expires_in' => 86400, // 24 hours in seconds
            'token_type' => 'Bearer',
        ]);
    }

    public function logout(Request $request)
    {
        // ✅ Revoke current token
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out successfully']);
    }

    /**
     * Refresh token (user can extend session)
     */
    public function refresh(Request $request)
    {
        $user = $request->user();
        $oldToken = $user->currentAccessToken();

        // Revoke old token
        $oldToken->delete();

        // Create new token
        $newToken = $user->createToken('api-token', ['*'], [
            'expires_at' => now()->addHours(24),
        ])->plainTextToken;

        return response()->json([
            'token' => $newToken,
            'expires_in' => 86400,
        ]);
    }
}
```

#### Step 5: Add to API Routes

```php
<?php

// routes/api.php

// Apply token expiration check to auth routes
Route::middleware(['auth:sanctum', 'check-token-expiration'])->group(function () {
    Route::get('/me', [UserController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/refresh', [AuthController::class, 'refresh']);
    // ... other authenticated routes
});
```

### Command to Remove Expired Tokens (Schedule)

```php
<?php

// app/Console/Commands/DeleteExpiredTokens.php

namespace App\Console\Commands;

use App\Models\PersonalAccessToken;
use Illuminate\Console\Command;

class DeleteExpiredTokens extends Command
{
    protected $signature = 'tokens:cleanup';
    protected $description = 'Delete expired personal access tokens';

    public function handle()
    {
        $deleted = PersonalAccessToken::where('expires_at', '<', now())->delete();
        $this->info("Deleted {$deleted} expired tokens");
    }
}
```

```php
// app/Console/Kernel.php

protected function schedule(Schedule $schedule)
{
    // Run daily at 2 AM
    $schedule->command('tokens:cleanup')->dailyAt('02:00');
}
```

---

## 🟡 TASK 4: HTTPS + SECURITY HEADERS (1 hour)

### Step 1: Force HTTPS

```php
<?php

// config/app.php

'url' => env('APP_URL', 'https://ngajar.id'), // Use HTTPS

// app/Providers/AppServiceProvider.php

public function boot(): void
{
    if ($this->app->environment('production')) {
        // Force HTTPS in production
        URL::forceScheme('https');
    }
}
```

### Step 2: Add Security Headers Middleware

```php
<?php

// app/Http/Middleware/SecurityHeaders.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SecurityHeaders
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // ✅ Security headers
        $response->header('X-Content-Type-Options', 'nosniff'); // Prevent MIME sniffing
        $response->header('X-Frame-Options', 'DENY'); // Prevent clickjacking
        $response->header('X-XSS-Protection', '1; mode=block'); // XSS protection
        $response->header('Strict-Transport-Security', 'max-age=31536000; includeSubDomains'); // Force HTTPS
        $response->header('Content-Security-Policy', "default-src 'self'"); // CSP
        $response->header('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->header('Permissions-Policy', 'geolocation=(), microphone=(), camera=()');

        return $response;
    }
}
```

### Step 3: Register Middleware

```php
// app/Http/Kernel.php

protected $middleware = [
    // ...
    \App\Http\Middleware\SecurityHeaders::class,
];
```

---

## 🟡 TASK 5: APP_DEBUG=FALSE (0.5 hours)

### Environment Setup

```bash
# .env

APP_ENV=production      # Set to production
APP_DEBUG=false         # Hide debug info
APP_URL=https://ngajar.id

# Ensure these are set
DB_PASSWORD=strong_random_password
SANCTUM_STATEFUL_DOMAINS=ngajar.id

# Payment credentials
MIDTRANS_SERVER_KEY=xxx_server_key
MIDTRANS_CLIENT_KEY=xxx_client_key
XENDIT_API_KEY=xxx_api_key

# Security
WEBHOOK_SECRET=random_long_string
```

### Verification Checklist

```bash
# ✅ Verify no debug info is exposed
curl -H "X-Requested-With: XMLHttpRequest" https://ngajar.id/api/error
# Should NOT show stack trace, should return clean JSON error

# ✅ Verify HTTPS is forced
curl http://ngajar.id
# Should redirect to https://ngajar.id

# ✅ Verify Sanctum is configured
grep -r "SANCTUM_STATEFUL_DOMAINS" .env

# ✅ Verify no sensitive files are exposed
curl https://ngajar.id/.env
# Should return 404, not expose .env contents
```

---

## ✅ PHASE 1 EXECUTION CHECKLIST

### Pre-Implementation
- [ ] Create feature branch: `git checkout -b phase-1-security`
- [ ] Backup current database: `php artisan backup:run`
- [ ] Review all changes before committing

### Task 1: Rate Limiting
- [ ] Copy `ThrottleApiRequests.php` to `app/Http/Middleware/`
- [ ] Update `RouteServiceProvider.php` with rate limit configuration
- [ ] Update routes in `routes/api.php` with throttle middleware
- [ ] Copy `LogRateLimitExceeded.php` middleware
- [ ] Register middleware in `app/Http/Kernel.php`
- [ ] **Test**: `php artisan test --filter RateLimitTest`

### Task 2: Webhook Validation
- [ ] Create `WebhookValidationService.php` in `app/Services/`
- [ ] Update `WebhookController.php` with validation
- [ ] Update webhook routes with signature validation
- [ ] Create webhook test requests in `tests/Feature/`
- [ ] **Test**: `php artisan test --filter WebhookTest`

### Task 3: Token Expiration
- [ ] Create migration for token expiration columns
- [ ] Update `PersonalAccessToken.php` model
- [ ] Create `CheckTokenExpiration.php` middleware
- [ ] Update `AuthController.php` login/refresh
- [ ] Create `DeleteExpiredTokens.php` command
- [ ] **Test**: `php artisan test --filter TokenExpirationTest`

### Task 4: HTTPS + Security Headers
- [ ] Create `SecurityHeaders.php` middleware
- [ ] Register in `app/Http/Kernel.php`
- [ ] Update `config/app.php` to use HTTPS
- [ ] Update `AppServiceProvider.php`
- [ ] **Test**: Verify headers with `curl -I https://localhost:8000`

### Task 5: Environment Configuration
- [ ] Update `.env` with production values
- [ ] Set `APP_DEBUG=false`
- [ ] Verify all secrets are set
- [ ] Check `.env.example` is up to date
- [ ] **Test**: `php artisan config:clear && php artisan config:cache`

### Post-Implementation
- [ ] Run all tests: `php artisan test`
- [ ] Check log files: `tail -f storage/logs/laravel.log`
- [ ] Verify with monitoring: Check requests/second for rate limits
- [ ] Create PR for review
- [ ] Merge to main after approval

---

## 📊 Phase 1 Summary

| Task | Files | Time | Status |
|------|-------|------|--------|
| Rate Limiting | 3 files new | 1.5h | Ready |
| Webhook Validation | 1 file update | 2h | Ready |
| Token Expiration | 4 files | 1h | Ready |
| HTTPS + Headers | 2 files | 1h | Ready |
| Environment | 1 file update | 0.5h | Ready |

**Total Implementation**: 6.5 hours  
**Testing**: 1-2 hours  
**Deployment**: 1 hour  

**Grand Total**: ~8-9 hours (1 full day)

---

## 🚀 After Phase 1: What's Next

### Can Do Immediately After Phase 1
✅ Go production with confidence  
✅ Full public beta  
✅ Accept real payments  
✅ Scale to 10K users safely  

### Phase 2 (Next 2-4 weeks) - Can run while operating
- [ ] Encrypt PII (email, phone)
- [ ] Audit logging for sensitive changes
- [ ] File upload security (virus scan, etc)
- [ ] Centralized error tracking (Sentry)

### Phase 3 (Month 2-3) - Polish
- [ ] Advanced security headers
- [ ] Penetration testing
- [ ] Security compliance (SOC2)

---

**Status**: 🟢 **READY TO IMPLEMENT**  
**Priority**: 🔴 **CRITICAL - DO FIRST**  
**Blockers**: ❌ **None - all dependencies are available**

Execute Phase 1 → Then proceed to Sprint 1-3 refactoring
