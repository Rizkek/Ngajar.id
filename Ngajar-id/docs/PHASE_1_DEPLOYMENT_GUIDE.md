# 🚀 PHASE 1 DEPLOYMENT - Quick Commands

**Total Time**: 30-60 minutes  
**Status**: All code implemented, ready to deploy  
**Can Go Live**: YES  

---

## 📋 Step-by-Step Deployment

### Step 1: Run Migration (5 minutes)

```bash
# Run the new migration to add token expiration columns
php artisan migrate

# Verify migration worked
php artisan migrate:status
```

**Expected Output**:
```
Migrated: 2026_04_12_000000_add_token_expiration_to_personal_access_tokens
```

---

### Step 2: Register SecurityHeaders Middleware (5 minutes)

Since Laravel 11 doesn't use Kernel.php, add to **bootstrap/app.php**:

```php
<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // ✅ PHASE 1: Add global middleware
        $middleware->append(\App\Http\Middleware\SecurityHeaders::class); // Security headers on all responses
        $middleware->append(\App\Http\Middleware\CheckTokenExpiration::class); // Check token expiration
        
        // existing middleware...
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
```

---

### Step 3: Clear Config Cache (2 minutes)

```bash
# Clear all caches
php artisan config:clear
php artisan route:clear
php artisan cache:clear

# Rebuild cache for production performance
php artisan config:cache
php artisan route:cache
```

---

### Step 4: Test Rate Limiting (5 minutes)

**Test 1: Login rate limit (5 attempts/minute)**

```bash
# Run this 6 times quickly
for i in {1..6}; do
  echo "Attempt $i:"
  curl -X POST http://localhost:8000/api/v1/auth/login \
    -H "Content-Type: application/json" \
    -d '{"email":"test@example.com","password":"wrong"}'
  echo ""
  sleep 1
done

# Expected: Attempts 1-5 succeed (401 invalid credentials), 6th returns 429 (rate limited)
```

**Successful Response** (attempt 1-5):
```json
{
  "success": false,
  "message": "Email atau password salah."
}
```

**Rate Limited Response** (attempt 6):
```json
{
  "message": "Too many login attempts. Try again in 1 minute.",
  "retry_after": 60
}

HTTP 429 Too Many Requests
```

---

### Step 5: Test Webhook Validation (5 minutes)

**Test: Invalid signature should be rejected**

```bash
# Send webhook with WRONG signature - should get 401
curl -X POST http://localhost:8000/api/v1/webhook/midtrans \
  -H "Content-Type: application/json" \
  -H "X-Override-Notification: invalid-signature-here" \
  -d '{
    "order_id": "ORDER-123",
    "transaction_id": "txn-456",
    "gross_amount": 100000,
    "transaction_status": "settlement"
  }'

# Expected response: 401 Unauthorized
```

**Response**:
```json
{
  "success": false,
  "message": "Invalid signature"
}

HTTP 401 Unauthorized
```

**Test: Correct signature should process**

```bash
# For testing, you'll need actual Midtrans/Xendit webhook with valid signature
# Or temporarily disable signature check for testing
```

---

### Step 6: Test Token Expiration (5 minutes)

**Test: Token expires after 24 hours**

```bash
# 1. Login to get token
curl -X POST http://localhost:8000/api/v1/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email":"user@example.com",
    "password":"password123"
  }'

# Expected response includes: token, expires_in (86400 seconds = 24 hours)
# {
#   "success": true,
#   "token": "YOUR_TOKEN_HERE",
#   "expires_in": 86400
# }

# 2. Use token to access protected route
curl -X GET http://localhost:8000/api/v1/user/dashboard \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"

# Should succeed (200 OK)

# 3. Manually test expiration (optional - for development):
# Update DB: UPDATE personal_access_tokens SET expires_at = NOW() - INTERVAL 1 DAY WHERE name = 'api-token'

# 4. Try using expired token
curl -X GET http://localhost:8000/api/v1/user/dashboard \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"

# Expected: 401 with "Your session has expired"
```

---

### Step 7: Test Security Headers (5 minutes)

```bash
# Check if security headers are present in response
curl -I https://your-domain.com/api/v1/user/dashboard

# Expected headers:
# X-Content-Type-Options: nosniff
# X-Frame-Options: DENY
# X-XSS-Protection: 1; mode=block
# Strict-Transport-Security: max-age=31536000; includeSubDomains
# Content-Security-Policy: default-src 'self'...
# Referrer-Policy: strict-origin-when-cross-origin
# Permissions-Policy: geolocation=(), microphone=(), camera=()...
```

---

### Step 8: Environment Configuration for Production (2 minutes)

Update **.env** before deploying to production:

```bash
# Change from:
APP_ENV=local
APP_DEBUG=true

# To:
APP_ENV=production
APP_DEBUG=false
```

---

## ✅ Verification Checklist

- [ ] Migration ran successfully
- [ ] Middleware registered in bootstrap/app.php
- [ ] Config cache cleared
- [ ] Rate limiting works (5th attempt succeeds, 6th blocked)
- [ ] Webhook signature validation works (invalid signature = 401)
- [ ] Tokens created with 24-hour expiration
- [ ] Security headers present in responses
- [ ] APP_DEBUG=false in production .env

---

## 📊 Performance Impact

After Phase 1 deployment:

```
BEFORE Phase 1:
- No rate limiting           → Vulnerable to brute force/DoS
- No webhook validation      → Revenue loss from fraud
- No token expiration        → Stolen tokens = permanent access
- No security headers        → Vulnerable to attacks
- APP_DEBUG=true in prod    → Stack traces exposed

AFTER Phase 1:
✅ Rate limiting active       → Protected from brute force/DoS
✅ Webhook signature check    → Prevents payment fraud
✅ Token expiration in 24h    → Compromised account has limited window
✅ Security headers active    → MIME sniffing, clickjacking prevented
✅ APP_DEBUG=false           → No stack traces exposed
```

---

## 🚀 Deploy to Production

```bash
# 1. Create feature branch
git checkout -b phase-1-security

# 2. Commit all changes
git add .
git commit -m "Phase 1 Security: Rate limiting, webhook validation, token expiration, security headers"

# 3. Push to GitHub
git push origin phase-1-security

# 4. Create Pull Request
# → Review code
# → Run tests
# → Approve

# 5. Merge to main
git checkout main
git pull origin main
git merge phase-1-security

# 6. Deploy to production
# (Use your deployment process - Heroku, DigitalOcean, AWS, etc)

# 7. On production server:
php artisan migrate              # Run migrations
php artisan config:cache        # Cache config
php artisan route:cache         # Cache routes
```

---

## 🧪 Run Final Tests

```bash
# Run full test suite
php artisan test

# Or specific security tests (if you create them)
php artisan test --filter Security

# Check Laravel logs for any errors
tail -f storage/logs/laravel.log
```

---

## ⚠️ Rollback Plan (If needed)

```bash
# Rollback migration
php artisan migrate:rollback --step=1

# Remove middleware from bootstrap/app.php

# Redeploy previous version
```

---

## 📞 Troubleshooting

### Problem: Webhook keeps failing with "Invalid signature"

**Solution**:
1. Verify MIDTRANS_SERVER_KEY in .env matches your Midtrans account
2. Check if Midtrans is sending X-Override-Notification header
3. For Xendit, check XENDIT_WEBHOOK_TOKEN

### Problem: Users getting "Token expired" immediately

**Solution**:
1. Check server time is correct: `date`
2. Verify expires_at in DB is in future: `SELECT * FROM personal_access_tokens`
3. Check timezone setting in Laravel config

### Problem: Rate limiting too strict/loose

**Solution**:
1. Adjust limits in `app/Providers/AppServiceProvider.php`
2. Change numbers in `RateLimiter::for()` definitions
3. Re-run `php artisan config:cache`

---

## ✅ PHASE 1 COMPLETE!

After completing these steps, you have:

✅ Rate limiting on all auth endpoints  
✅ Webhook signature validation  
✅ Token expiration after 24 hours  
✅ Security headers on all responses  
✅ Debug info disabled in production  

**Next**: Sprint 1 Architecture (when ready)

---

**Timeline**: 30-60 minutes  
**Complexity**: Low-Medium  
**Risk**: Low (all backwards compatible, can rollback easily)  

**Status**: 🟢 READY FOR PRODUCTION
