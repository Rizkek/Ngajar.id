# Ngajar.ID Laravel Architecture Analysis

## 1. STATE MANAGEMENT PATTERNS

### Services Layer
The project uses a **Services pattern** for business logic encapsulation and external integrations:

**Located in:** `app/Services/`

#### Services Implemented:
1. **MidtransService** - Payment gateway integration
   - Manages payment snap token creation for donations
   - Handles SSL verification for local development
   - Reads configuration from database `Setting` model with fallback to `.env`
   - Implements transaction details management with customer information

2. **XenditService** - Alternative payment gateway
   - Creates invoices for donations
   - Uses Basic Auth with API keys
   - Manages redirect URLs for payment success/failure flows
   - Centered around external HTTP communication

3. **SupabaseStorageService** - File storage management
   - Uploads files to Supabase cloud storage
   - Generates unique filenames to prevent conflicts
   - Returns public URLs for uploaded files
   - Handles exceptions and logging

**Service Pattern Usage:**
- Services are dependency-injected into controllers
- Encapsulate external API integration logic
- Database settings can override configuration values
- Each service has a single responsibility (SRP)

### Events & Listeners (Event-Driven Architecture)

**Location:** `app/Events/` and `app/Listeners/`

#### Implemented Events:
1. **MateriCompleted Event**
   - Dispatched when a user completes material/course
   - Carries User and Materi data
   - Uses Dispatchable, InteractsWithSockets, SerializesModels traits

#### Listeners:
1. **AwardXpToUser Listener** - Triggered by MateriCompleted
   - Awards 50 XP per completed material
   - Implements leveling system: Level = floor(XP / 500) + 1
   - Triggers level-up notifications
   - Awards achievements at milestone levels (e.g., "High Achiever" at level 5)
   - Logs user progression events

**Event Flow:**
```
User Completes Materi → MateriCompleted Event Fired
                    → AwardXpToUser Listener Executes
                    → XP Awarded → Level Calculated → Level-Up Check
```

### Repository Pattern
**Current Status:** NOT ACTIVELY IMPLEMENTED
- The project uses Eloquent ORM directly in controllers
- No separate Repository layers or interfaces
- Direct Model interactions in Business Logic

**Recommendation for future:** Consider implementing Repository pattern for:
- Database abstraction
- Easier testing with mock repositories
- Centralized query logic

---

## 2. RENDERING PATTERNS

### API Resources (JSON Transformation)
**Located in:** `app/Http/Resources/`

#### Resource Classes Implemented:

1. **UserResource**
   - Transforms User model to API JSON response
   - Returns: id, name, email, phone, role, status, avatar (with asset path), bio, xp, level
   - Includes email verification status as boolean
   - ISO8601 formatted timestamps

2. **KelasResource**
   - Transforms Kelas (Class) model
   - Includes teacher relationship (nested UserResource)
   - Uses `whenLoaded()` for optional relationships
   - Uses `whenCounted()` for relationship counts
   - Returns: title, description, thumbnail, price (both regular and token), rating, category, level

3. **MateriResource** - API transformation for course materials
4. **DonasiResource** - API transformation for donations

**Pattern Usage:**
```php
// In Controller:
return KelasResource::collection($kelas);
return new UserResource($user);
```

**Features:**
- Automatic URL formatting for assets (storage paths)
- Lazy-loaded relationships to prevent N+1 queries
- Conditional data inclusion based on context
- Consistent API response structure

### Blade Templates
**Located in:** `resources/views/`

**Blade Usage Areas:**
- Authentication views (login, register, email verification)
- Email templates (Mailable classes with Blade)
- Web routes rendering

**Example:**
- `emails.verify-email` - Email verification with custom verification URL
- `emails.welcome` - Welcome email for new users

---

## 3. MIDDLEWARE & AUTHENTICATION SETUP

### Authentication Configuration
**Config File:** `config/auth.php`

```
Guard: 'web' (session-based)
Provider: Eloquent User model
Password Reset Table: password_reset_tokens
Token Expiry: 60 minutes
Throttle: 60 seconds between reset requests
```

### Middleware Stack

#### Custom Middleware:

1. **CheckRole Middleware** (`app/Http/Middleware/CheckRole.php`)
   - Role-based access control
   - Supports multiple roles: murid (student), pengajar (teacher), admin
   - Returns JSON responses with appropriate HTTP status codes
   - Usage: `middleware('role:murid')`
   - Returns 401 if no user authenticated
   - Returns 403 if user role not in allowed roles

#### CSRF Protection Configuration
**In:** `bootstrap/app.php`

```php
$middleware->validateCsrfTokens(except: [
    '/donasi/webhook',      // Donation webhook endpoint
    '/topup/callback',      // Topup payment callback
]);
```

- **Enabled by default** for all web routes
- Exceptions for payment gateway callbacks (webhooks)
- Sanctum package supports API token authentication

### Authentication Guards

- **Web Guard:** Session-based authentication for web routes
- **Sanctum:** Token-based authentication for API routes (Laravel Sanctum ^4.2)
- Supports social authentication via Socialite (Google)

---

## 4. SECURITY IMPLEMENTATIONS

### Input Validation

#### Request Classes (Form Validation)
**Location:** `app/Http/Requests/`

1. **LoginRequest**
   - Validates: email (required, valid format), password (required)
   - Custom Indonesian error messages

2. **RegisterRequest**
   - Validates:
     - name: required, max 100 chars
     - email: required, unique, max 100 chars
     - password: required, min 8 chars, confirmed
     - role: required, only 'murid' or 'pengajar' (admin removed for security)
     - phone: optional, regex validation for Indonesian numbers
     - avatar: optional, image only, max 2048 KB
     - referral_code: optional, must exist in users table
     - terms: must be accepted
     - email_notifications: optional boolean
   - Custom Indonesian error messages

3. **StoreKelasRequest, UpdateKelasRequest** - Class management validation

### Authorization (Policies)

**Location:** `app/Policies/`

#### KelasPolicy
```php
- viewAny(): Always allowed
- view(): Always allowed
- create(): Only pengajar (teachers) can create
- update(): Only original creator (pengajar_id matches user_id)
- delete(): Only original creator
```

**Uses Gate authorization:** `$this->authorize('update', $kelas);`

### Password Security

1. **Password Hashing:** Laravel's built-in `Hash::make()` (Bcrypt)
2. **Password Reset:**
   - Token-based system in `password_reset_tokens` table
   - 60-minute expiry
   - 60-second throttle between requests

### Session Security

**Sessions Table Structure:**
- Stores: id, user_id, ip_address, user_agent, payload, last_activity
- Tracks IP and user agent for anomaly detection

### Database Security

#### Data Integrity Features:
1. **Foreign Keys:** Relationships enforced at database level
2. **Unique Constraints:** Email uniqueness enforced
3. **Indexes:** Performance and query optimization indexes
4. **Timestamps:** `created_at`, `updated_at` for audit trails

#### User Model Security:
- Password hidden from serialization
- Remember token stored securely
- Achievements stored as JSON array (with casting)
- Email verification tracking

### Email Verification
**EmailVerification Model:**
- Separate verification tokens table
- Links users to verification codes
- Used in AuthController for registration flow

### Referral System
**Referral Model:**
- Referral codes generated on registration (10-char random uppercase)
- Referenced users during registration with validation
- Cannot register with non-existent referral code

---

## 5. DEPENDENCIES IN COMPOSER.JSON

### Core Framework & Authentication
```json
"laravel/framework": "^12.0"        - Latest Laravel framework
"laravel/sanctum": "^4.2"            - API token authentication
"laravel/socialite": "^5.24"         - Social auth (Google)
"laravel/tinker": "^2.10.1"          - REPL for development
```

### Payment Gateways
```json
"midtrans/midtrans-php": "^2.6"      - Midtrans PHP SDK for payment processing
```
*Note: Xendit integration exists in services but not in composer.json (likely uses HTTP client)*

### Development & Testing
```json
"phpunit/phpunit": "^11.5.3"         - Unit & feature testing framework
"mockery/mockery": "^1.6"            - Mocking library for tests
"fakerphp/faker": "^1.23"            - Fake data generation for seeders
"laravel/sail": "^1.41"              - Docker support
"laravel/pail": "^1.2.2"             - Log monitoring in development
"laravel/pint": "^1.24"              - PHP code style fixer
"nunomaduro/collision": "^8.6"       - Enhanced error reporting
```

### Build & Frontend
- Asset build pipeline via `npm` and `vite` (JS)
- Tailwind CSS for styling (see tailwind.config.js)

### Security & Performance
```json
"php": "^8.2"                        - Requires PHP 8.2+
- Built-in Laravel security middleware
- HTTPS enforcement in production (AppServiceProvider)
- Lazy loading prevention in production
```

### Autoload Configuration
```php
Files auto-loaded: app/Helpers/ApiHelper.php
- PSR-4 autoloading for app namespace
```

---

## 6. API RESPONSE PATTERNS

### ApiResponse Trait
**Location:** `app/Http/Traits/ApiResponse.php`

Provides standardized JSON response methods:

```php
- success($data, $message, $statusCode) → 200
- successWithPagination($data, $message) → Includes pagination metadata
- error($message, $errors[], $statusCode) → 400/500
- notFound($message) → 404
- unauthorized($message) → 401
- forbidden($message) → 403
- validationError($errors[]) → Validation-specific response
```

### Response Structure
```json
{
    "success": true/false,
    "message": "string",
    "data": {},
    "pagination": { "current_page", "last_page", "per_page", "total" },
    "errors": []
}
```

---

## 7. API ROUTE STRUCTURE

**File:** `routes/api.php`

### Route Organization:
```
/api/
├── Public Routes
│   ├── /auth/register
│   ├── /auth/login
│   ├── /auth/verify-email/{token}
│   └── /auth/forgot-password
│
├── Authenticated Routes (auth:sanctum)
│   ├── /profile - User profile
│   ├── /student (role:murid)
│   │   ├── /kelas - Browse & enroll
│   │   ├── /materi - Access materials
│   │   ├── /token - Token management
│   │   └── /donasi - Donations
│   │
│   ├── /teacher (role:pengajar)
│   │   ├── /kelas - Create & manage classes
│   │   ├── /materi - Create materials
│   │   └── /earnings - Revenue tracking
│   │
│   └── /admin (role:admin)
│       ├── /users - User management
│       ├── /kelas - Class moderation
│       ├── /reports - Analytics
│       ├── /settings - Global settings
│       └── /notifications - Broadcast
```

---

## 8. DATA FLOW ARCHITECTURE

### Registration Flow
```
1. POST /register (RegisterRequest validation)
2. Hash password
3. Generate referral code
4. Handle avatar upload (Supabase if configured)
5. Create User with role (murid/pengajar only)
6. Create Token record with 0 balance
7. Create EmailVerification token
8. Queue VerifyEmail Mailable
9. Return user data with JWT/Sanctum token
```

### Course Completion Flow
```
1. User completes material
2. MateriCompleted event dispatched
3. AwardXpToUser listener executed
4. XP added to user
5. Level check and upgrade if threshold exceeded
6. Achievement check (Level 5 → "High Achiever")
7. Log event for analytics
```

### Payment Flow
```
1. User initiates donation/topup
2. Create Topup/Donasi record
3. Generate transaction via Service (Midtrans/Xendit)
4. Callback webhook (CSRF exempt)
5. Update transaction status
6. Award tokens if successful
7. Log transaction
```

---

## 9. CURRENT ARCHITECTURAL STRENGTHS

✅ **Well-organized folder structure** following Laravel conventions
✅ **Service layer** for external integrations (Midtrans, Xendit, Supabase)
✅ **Events & Listeners** for gamification (XP, achievements, level-up)
✅ **API Resources** for consistent JSON transformation
✅ **Request validation** with custom messages
✅ **Role-based middleware** for access control
✅ **CSRF protection** with webhook exceptions
✅ **Password security** with Bcrypt hashing
✅ **Email verification** system implemented
✅ **Referral system** with validation
✅ **Token system** for premium features
✅ **Larson Sanctum** for API authentication
✅ **Social authentication** support (Socialite)
✅ **Lazy loading prevention** in production

---

## 10. POTENTIAL IMPROVEMENTS

🔧 **Repository Pattern** - Implement for better testability and abstraction
🔧 **Query Builder Optimization** - Some queries may need eager loading review
🔧 **Rate Limiting** - Implement per-endpoint rate limiting beyond CSRF throttle
🔧 **API Versioning** - Consider `/api/v1/` structure for future compatibility
🔧 **Soft Deletes** - Consider SoftDelete trait for data retention
🔧 **Audit Trail** - Implement action logging for admin activities
🔧 **Input Sanitization** - Add additional input sanitization beyond validation
🔧 **Encryption** - Consider encrypting sensitive data at rest
🔧 **Two-Factor Authentication** - Add 2FA for admin accounts
🔧 **API Documentation** - Add automated OpenAPI/Swagger documentation

---

## 11. SECURITY CHECKLIST

| Item | Status | Details |
|------|--------|---------|
| CSRF Protection | ✅ Active | Configured globally with webhook exceptions |
| Authentication | ✅ Implemented | Session-based + Sanctum tokens |
| Authorization | ✅ Implemented | Role middleware + Policies |
| Input Validation | ✅ Implemented | Request classes with rules |
| Password Hashing | ✅ Implemented | Bcrypt via Hash facade |
| Email Verification | ✅ Implemented | Token-based system |
| HTTPS Enforcement | ✅ Active | In production (AppServiceProvider) |
| SQL Injection | ✅ Protected | Eloquent ORM parameterized queries |
| N+1 Query Prevention | ✅ Active | Lazy loading disabled in production |
| Role-Based Access | ✅ Implemented | CheckRole middleware |
| Session Tracking | ✅ Implemented | IP and User-Agent logged |
| Token Expiry | ✅ Active | 60-minute password reset tokens |
| XSS Protection | ✅ Active | Blade escaping by default |
| Sensitive Data Hiding | ✅ Configured | Password hidden from serialization |

---

## 12. TECHNOLOGY STACK SUMMARY

| Layer | Technology |
|-------|------------|
| Framework | Laravel 12.0 |
| Authentication | Sanctum 4.2, Socialite 5.24 |
| Database ORM | Eloquent |
| Frontend | Blade templates, Vue/Inertia (via Vite) |
| Styling | Tailwind CSS |
| Payment Gateways | Midtrans, Xendit |
| File Storage | Supabase |
| Build Tool | Vite + npm |
| Testing | PHPUnit 11.5.3 |
| Database | PostgreSQL (migration references) |
| Caching | Redis (configured) |

