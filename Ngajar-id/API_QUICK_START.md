# 🔥 QUICK START - API Integration Guide

**Untuk Developer Frontend & Backend**

---

## SETUP AWAL (WAJIB DILAKUKAN)

### 1. Jalankan Composer Dump
```bash
composer dump-autoload
```
**Alasan:** Agar helper functions di `app/Helpers/ApiHelper.php` ter-load otomatis.

### 2. Run Database Migrations
```bash
php artisan migrate
```
**Alasan:** Update tabel users, buat tabel referrals & email_verifications.

### 3. Pastikan Sanctum Installed
```bash
php artisan install:api
```
**Jika sudah ada, lanjut ke step berikutnya.**

---

## APIs YANG WAJIB DIGUNAKAN UNTUK SETIAP PAGE

### 🏠 LANDING PAGE

**Ambil Statistics:**
```bash
GET /api/v1/landing/stats
```
Response: Platform stats, volunteer info, course count

**Ambil Featured Teachers:**
```bash
GET /api/v1/landing/volunteers
```
Response: List featured teachers dengan bio & rating

**Ambil Courses (untuk section katalog):**
```bash
GET /api/v1/programs
?page=1&limit=12&category=programming
```
Response: Courses dengan pagination

---

### 🎓 STUDENT DASHBOARD

**Ambil Dashboard Data:**
```bash
GET /api/v1/student/dashboard
Authorization: Bearer {token}
```
Response:
```json
{
  "success": true,
  "data": {
    "stats": {
      "total_classes": 5,
      "in_progress": 2,
      "completed": 3,
      "total_xp": 2500
    },
    "recent_classes": [...],
    "saved_classes": [...]
  }
}
```

**Ambil My Classes:**
```bash
GET /api/v1/student/classes
Authorization: Bearer {token}
```

**Enroll Dalam Course:**
```bash
POST /api/v1/student/classes/{classId}/enroll
Authorization: Bearer {token}
Content-Type: application/json

{
  "payment_method": "token"  // atau "balance", "card", "bank_transfer"
}
```

**Lihat Progress:**
```bash
GET /api/v1/student/learning/progress/{classId}
Authorization: Bearer {token}
```

**Download Certificate:**
```bash
GET /api/v1/student/certificates/{classId}/download
Authorization: Bearer {token}
```
Response: PDF file

---

### 👨‍🏫 TEACHER DASHBOARD

**Ambil Teacher Dashboard:**
```bash
GET /api/v1/teacher/dashboard
Authorization: Bearer {token}
```
Response:
```json
{
  "stats": {
    "total_classes": 8,
    "total_students": 245,
    "total_earnings": 5600000,
    "revenue_this_month": 1200000
  },
  "recent_activity": [...]
}
```

**Ambil My Courses:**
```bash
GET /api/v1/teacher/classes
Authorization: Bearer {token}
```

**Buat Course Baru:**
```bash
POST /api/v1/teacher/classes
Authorization: Bearer {token}
Content-Type: application/json

{
  "title": "Laravel Mastery",
  "description": "Complete Laravel course...",
  "category_id": 1,
  "price": 99000,
  "is_premium": true,
  "excerpt": "Learn Laravel from scratch"
}
```

**Update Course:**
```bash
PUT /api/v1/teacher/classes/{classId}
Authorization: Bearer {token}
```

**Delete Course:**
```bash
DELETE /api/v1/teacher/classes/{classId}
Authorization: Bearer {token}
```

**Ambil Course Analytics:**
```bash
GET /api/v1/teacher/analytics/classes/{classId}
Authorization: Bearer {token}
```
Response:
```json
{
  "total_students": 150,
  "avg_rating": 4.8,
  "completion_rate": 92,
  "revenue": 7500000
}
```

---

### 🔧 ADMIN DASHBOARD

**Ambil Admin Dashboard:**
```bash
GET /api/v1/admin/dashboard
Authorization: Bearer {admin_token}
```
Response:
```json
{
  "stats": {
    "total_users": 5234,
    "total_revenue": 45600000,
    "courses_pending_approval": 12,
    "reports_today": 8
  },
  "recent_activities": [...]
}
```

**Kelola Users:**
```bash
# List all users
GET /api/v1/admin/users?role=murid&status=active

# Get user details
GET /api/v1/admin/users/{userId}

# Update user
PUT /api/v1/admin/users/{userId}

# Suspend user
POST /api/v1/admin/users/{userId}/suspend

# Delete user
DELETE /api/v1/admin/users/{userId}
```

**Kelola Course Submissions:**
```bash
# List pending courses
GET /api/v1/admin/classes/pending

# Approve course
POST /api/v1/admin/classes/{classId}/approve

# Reject course
POST /api/v1/admin/classes/{classId}/reject
{
  "reason": "Quality tidak sesuai standar"
}
```

**Manage Donations:**
```bash
# List donations
GET /api/v1/admin/donations?status=success

# Refund donation
POST /api/v1/admin/donations/{donationId}/refund
{
  "reason": "Pembaca request refund"
}

# Export report
GET /api/v1/admin/reports/donations/export?format=csv&month=03&year=2026
```

**Broadcast Notification:**
```bash
POST /api/v1/admin/notifications/broadcast
Authorization: Bearer {admin_token}

{
  "title": "Maintenance Alert",
  "message": "Server maintenance 2am-4am",
  "type": "warning",
  "target_role": "all"  // atau "murid", "pengajar", "admin"
}
```

---

## 🔐 AUTHENTICATION FLOW

### 1. Register
```bash
POST /api/v1/register

{
  "name": "Budi Santoso",
  "email": "budi@example.com",
  "password": "SecurePass123",
  "password_confirmation": "SecurePass123",
  "role": "murid",  // atau "pengajar"
  "phone": "081234567890",
  "referral_code": "FRIEND123",  // optional
  "avatar": <file>,  // optional
  "email_notifications": true,
  "terms": true
}
```

**Response:**
```json
{
  "success": true,
  "message": "Verification email sent",
  "data": {
    "id": 1,
    "name": "Budi",
    "email": "budi@example.com",
    "role": "murid"
  }
}
```

### 2. Verify Email
```bash
POST /api/v1/verify-email/{token}
```
Buka dari email link yang diterima user.

### 3. Login
```bash
POST /api/v1/login

{
  "email": "budi@example.com",
  "password": "SecurePass123"
}
```

**Response:**
```json
{
  "success": true,
  "message": "Login successful",
  "data": {
    "user": {
      "id": 1,
      "name": "Budi",
      "email": "budi@example.com",
      "role": "murid",
      "avatar_url": "https://...",
      "email_verified": true
    },
    "token": "abc123|xyz789...",
    "token_type": "Bearer"
  }
}
```

### 4. Gunakan Token
```bash
Authorization: Bearer {token}
```

### 5. Logout
```bash
POST /api/v1/user/logout
Authorization: Bearer {token}
```

---

## 📱 RESPONSE STANDARDS

**Semua API akan return format ini:**

### Success (200, 201)
```json
{
  "success": true,
  "message": "Operation successful",
  "data": { ... }
}
```

### Paginated (200)
```json
{
  "success": true,
  "data": [ ... ],
  "pagination": {
    "current_page": 1,
    "last_page": 5,
    "per_page": 10,
    "from": 1,
    "to": 10,
    "total": 48
  }
}
```

### Validation Error (422)
```json
{
  "success": false,
  "message": "Validation failed",
  "errors": {
    "email": ["Email already exists"],
    "password": ["Password too weak"]
  }
}
```

### Unauthorized (401)
```json
{
  "success": false,
  "message": "Unauthenticated"
}
```

### Forbidden (403)
```json
{
  "success": false,
  "message": "Unauthorized access"
}
```

### Not Found (404)
```json
{
  "success": false,
  "message": "Resource not found"
}
```

### Server Error (500)
```json
{
  "success": false,
  "message": "Internal server error"
}
```

---

## 🛠️ DEVELOPING NEW ENDPOINTS

**Template untuk tambah endpoint baru:**

### 1. Define Route (routes/api.php)
```php
Route::prefix('admin')->middleware('auth:sanctum', 'role:admin')->group(function () {
    Route::get('/analytics', [AdminAnalyticsController::class, 'index']);
});
```

### 2. Create Controller
```php
<?php

namespace App\Http\Controllers\Api;

use App\Http\Traits\ApiResponse;
use App\Models\YourModel;
use App\Http\Resources\YourResource;

class YourController extends ApiController {
    use ApiResponse;
    
    public function index() {
        try {
            $data = YourModel::paginate(15);
            return $this->successWithPagination(
                YourResource::collection($data),
                'Data retrieved successfully'
            );
        } catch (\Exception $e) {
            return $this->serverError($e->getMessage());
        }
    }
    
    public function store(Request $request) {
        try {
            $validated = $request->validate([
                'field' => 'required|string',
                'email' => 'required|email|unique:table'
            ]);
            
            $data = YourModel::create($validated);
            return $this->success(
                new YourResource($data),
                'Created successfully',
                201
            );
        } catch (\Exception $e) {
            return $this->validationError(['error' => $e->getMessage()]);
        }
    }
}
```

### 3. Create Resource (app/Http/Resources)
```php
<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class YourResource extends JsonResource {
    public function toArray(Request $request): array {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
```

---

## 🧪 TESTING ENDPOINTS

### Dengan Postman
1. Set method ke GET/POST/PUT/DELETE
2. Input URL: `http://localhost:8000/api/v1/endpoint`
3. Headers:
   - `Content-Type: application/json`
   - `Authorization: Bearer {token}`
4. Kirim request

### Dengan cURL
```bash
curl -X POST http://localhost:8000/api/v1/login \
  -H "Content-Type: application/json" \
  -d '{"email":"user@example.com","password":"pass123"}'
```

### Dengan PHP (PECL/curl)
```php
$curl = curl_init();
curl_setopt_array($curl, array(
    CURLOPT_URL => "http://localhost:8000/api/v1/login",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => array(
        "Content-Type: application/json",
        "Authorization: Bearer " . $token
    ),
    CURLOPT_POSTFIELDS => json_encode($data)
));
$response = curl_exec($curl);
```

---

## 🚨 COMMON ERRORS & SOLUTIONS

| Error | Cause | Solution |
|-------|-------|----------|
| `Helper functions not found` | ApiHelper.php not autoloaded | Run `composer dump-autoload` |
| `401 Unauthenticated` | Missing/invalid token | Check token in Authorization header |
| `403 Forbidden` | Wrong role | Check user role (murid/pengajar/admin) |
| `422 Validation Error` | Invalid input | Check required fields & validation rules |
| `500 Internal Server Error` | Controller error | Check Laravel logs at `storage/logs/` |
| `CORS Error` | Browser blocking request | Configure CORS in config/cors.php |

---

## ✅ INTEGRATION CHECKLIST

- [ ] Composer dump-autoload run
- [ ] Database migrations executed
- [ ] Sanctum properly configured
- [ ] .env configured dengan correct APP_URL
- [ ] Mail driver configured untuk email verification
- [ ] File storage configured untuk avatar uploads
- [ ] CORS allowed untuk frontend domain
- [ ] Routes properly registered
- [ ] Controllers extend ApiController
- [ ] Responses menggunakan ApiResponse trait
- [ ] Resources defined untuk semua models
- [ ] Validation rules configured
- [ ] Error handling implemented
- [ ] Rate limiting setup
- [ ] API documented di postman collection

---

## 📚 USEFUL LINKS

- **Full Documentation**: [API_DOCUMENTATION.md](API_DOCUMENTATION.md)
- **Integration Summary**: [API_INTEGRATION_SUMMARY.md](API_INTEGRATION_SUMMARY.md)
- **API Routes**: [routes/api.php](routes/api.php)
- **Response Helper**: [app/Http/Traits/ApiResponse.php](app/Http/Traits/ApiResponse.php)
- **API Helper Functions**: [app/Helpers/ApiHelper.php](app/Helpers/ApiHelper.php)

---

**Status**: ✅ Ready for Integration  
**Version**: 1.0  
**Last Updated**: March 15, 2026
