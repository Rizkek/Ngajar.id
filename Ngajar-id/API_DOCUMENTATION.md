# 📚 Ngajar.ID API v1 - Complete Documentation

**Base URL**: `http://localhost:8000/api/v1`  
**Version**: 1.0.0  
**Last Updated**: March 15, 2026  
**Status**: 75% Complete (100+ endpoints implemented)

---

## 📋 Table of Contents

1. [Quick Start](#quick-start)
2. [Authentication](#authentication)
3. [Response Format](#response-format)
4. [Error Handling](#error-handling)
5. [Public Endpoints](#public-endpoints)
6. [Protected Endpoints](#protected-endpoints)
7. [Implementation Status](#implementation-status)
8. [Query Parameters](#query-parameters)
9. [Usage Examples](#usage-examples)

---

## ⚡ Quick Start

### Prerequisites
```bash
# 1. Ensure Composer autoload is updated
composer dump-autoload

# 2. Run database migrations
php artisan migrate

# 3. Verify Sanctum is installed
php artisan install:api
```

### Setup Steps

**Step 1: Register with Referral Code (Optional)**
```bash
POST /api/v1/register
{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "password123",
  "password_confirmation": "password123",
  "phone": "+62812345678",
  "role": "murid",
  "referral_code": "ABC123DEF4G",
  "terms": true
}
```

**Step 2: Login to Get Token**
```bash
POST /api/v1/login
{
  "email": "john@example.com",
  "password": "password123"
}
# Response includes: token, user details
```

**Step 3: Use Bearer Token for Protected Routes**
```bash
GET /api/v1/student/dashboard
Authorization: Bearer {your_token}
```

---

## 🔐 Authentication

### Register
**POST** `/register`

Create new user account.

**Request:**
```json
{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "password123",
  "password_confirmation": "password123",
  "phone": "+62812345678",
  "role": "murid",
  "referral_code": "ABC123DEF4G",
  "terms": true
}
```

**Response:** 201 Created
```json
{
  "success": true,
  "message": "Registration successful",
  "data": {
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com",
    "role": "murid"
  }
}
```

---

### Login  
**POST** `/login`

Authenticate user.

**Request:**
```json
{
  "email": "john@example.com",
  "password": "password123"
}
```

**Response:** 200 OK
```json
{
  "success": true,
  "message": "Login successful",
  "data": {
    "token": "token_string_here",
    "user": { ... }
  }
}
```

---

### Verify Email
**POST** `/verify-email/{token}`

Verify email address with token.

**Response:** 200 OK
```json
{
  "success": true,
  "message": "Email verified successfully"
}
```

---

### Get Current User
**GET** `/user`

Get authenticated user profile.

**Headers:** `Authorization: Bearer {token}`

**Response:** 200 OK
```json
{
  "success": true,
  "data": {
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com",
    "phone": "+62812345678",
    "role": "murid",
    "xp": 150,
    "level": 5
  }
}
```

---

## Landing Page API

### Get Statistics
**GET** `/landing/stats`

Get platform statistics for landing page.

**Response:** 200 OK
```json
{
  "success": true,
  "data": {
    "total_students": 1524,
    "total_teachers": 48,
    "total_classes": 156,
    "total_materials": 1250,
    "total_donations": 50000000,
    "average_rating": 4.8
  }
}
```

---

### Get Volunteers/Mentors
**GET** `/landing/volunteers`

Get featured volunteer teachers.

**Response:** 200 OK
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "name": "Pak Ahmad",
      "bio": "Teacher bio",
      "avatar": "avatar_url",
      "rating": 4.9,
      "students": 250,
      "classes": 5
    }
  ]
}
```

---

### Get Landing Page Info
**GET** `/landing/info`

Get landing page content and team information.

**Response:** 200 OK
```json
{
  "success": true,
  "data": {
    "about": "Platform description",
    "mission": "Our mission...",
    "team": [ ... ]
  }
}
```

---

## Student API

All endpoints require `Authorization: Bearer {token}` header.

### Student Dashboard
**GET** `/student/dashboard`

Get student dashboard with stats and recommendations.

**Response:** 200 OK
```json
{
  "success": true,
  "data": {
    "user": { ... },
    "stats": {
      "total_classes": 5,
      "completed_classes": 2,
      "in_progress": 3,
      "total_xp": 450,
      "current_level": 8
    },
    "recent_classes": [ ... ],
    "recommended_classes": [ ... ]
  }
}
```

---

### Get My Classes
**GET** `/student/classes`

Get list of enrolled classes.

**Query Parameters:**
- `status=active|completed|archived` - Filter by status
- `search=search_term` - Search by title
- `page=1` - Pagination
- `per_page=10` - Items per page

**Response:** 200 OK
```json
{
  "success": true,
  "data": [ ... ],
  "pagination": {
    "current_page": 1,
    "last_page": 5,
    "per_page": 10,
    "total": 48
  }
}
```

---

### Enroll in Class
**POST** `/student/classes/{id}/enroll`

Enroll in a class.

**Request:**
```json
{
  "payment_method": "token" // or "cash"
}
```

**Response:** 200 OK
```json
{
  "success": true,
  "message": "Enrollment successful"
}
```

---

### Get Class Materials
**GET** `/student/learning/materials/{classId}`

Get all materials for a class.

**Response:** 200 OK
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "title": "Introduction",
      "type": "video",
      "duration": "15:30",
      "completed": false,
      "is_premium": false
    }
  ]
}
```

---

### Complete Material
**POST** `/student/learning/materials/{id}/complete`

Mark material as completed.

**Response:** 200 OK
```json
{
  "success": true,
  "message": "Material completed",
  "data": {
    "xp_earned": 50,
    "new_total_xp": 500
  }
}
```

---

### Get My Progress
**GET** `/student/learning/progress`

Get overall learning progress.

**Response:** 200 OK
```json
{
  "success": true,
  "data": {
    "total_classes": 5,
    "completed_classes": 2,
    "in_progress": 3,
    "completion_rate": 40,
    "this_week_xp": 150
  }
}
```

---

### Submit Class Review
**POST** `/student/reviews/classes/{id}`

Submit a review for a class.

**Request:**
```json
{
  "rating": 5,
  "message": "Great class!"
}
```

**Response:** 201 Created
```json
{
  "success": true,
  "message": "Review submitted"
}
```

---

### Get My Certificates
**GET** `/student/certificates`

Get list of earned certificates.

**Response:** 200 OK
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "class_title": "Python Basics",
      "issued_date": "2026-03-15",
      "certificate_url": "url_to_pdf"
    }
  ]
}
```

---

### Download Certificate
**GET** `/student/certificates/{id}`

Download certificate PDF.

**Response:** 200 OK (PDF file)

---

### Get Token Balance
**GET** `/student/token/balance`

Get current token balance.

**Response:** 200 OK
```json
{
  "success": true,
  "data": {
    "current_balance": 1500,
    "total_earned": 5000,
    "total_spent": 3500
  }
}
```

---

### Get Learning Paths
**GET** `/student/learning-paths`

Get list of learning paths enrolled.

**Response:** 200 OK
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "title": "Complete Web Developer",
      "progress": 45,
      "courses_completed": 3,
      "total_courses": 7
    }
  ]
}
```

---

## Teacher API

All endpoints require `Authorization: Bearer {token}` header and `role=pengajar`.

### Teacher Dashboard
**GET** `/teacher/dashboard`

Get teacher dashboard with statistics.

**Response:** 200 OK
```json
{
  "success": true,
  "data": {
    "user": { ... },
    "stats": {
      "total_classes": 5,
      "total_students": 250,
      "total_materials": 45,
      "total_earnings": 5000000
    },
    "recent_activities": [ ... ],
    "top_classes": [ ... ]
  }
}
```

---

### Get My Classes
**GET** `/teacher/classes`

Get list of teacher's classes.

**Response:** 200 OK
```json
{
  "success": true,
  "data": [ ... ],
  "pagination": { ... }
}
```

---

### Create Class
**POST** `/teacher/classes`

Create a new class.

**Request:**
```json
{
  "title": "Python for Beginners",
  "description": "Learn Python basics...",
  "category": "programming",
  "level": "beginner",
  "price": 100000,
  "price_token": 500,
  "thumbnail": "file_upload"
}
```

**Response:** 201 Created
```json
{
  "success": true,
  "message": "Class created successfully",
  "data": { ... }
}
```

---

### Update Class
**PUT** `/teacher/classes/{id}`

Update class details.

**Request:** Similar to Create Class

**Response:** 200 OK

---

### Delete Class
**DELETE** `/teacher/classes/{id}`

Delete a class.

**Response:** 200 OK
```json
{
  "success": true,
  "message": "Class deleted successfully"
}
```

---

### Get Class Students
**GET** `/teacher/classes/{id}/students`

Get list of enrolled students.

**Response:** 200 OK
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "name": "John Doe",
      "email": "john@example.com",
      "enrolled_date": "2026-03-10",
      "progress": 45
    }
  ]
}
```

---

### Get Class Analytics
**GET** `/teacher/classes/{id}/stats`

Get detailed class statistics.

**Response:** 200 OK
```json
{
  "success": true,
  "data": {
    "total_students": 25,
    "completed": 10,
    "in_progress": 15,
    "average_rating": 4.7,
    "total_revenue": 2500000
  }
}
```

---

### Create Material
**POST** `/teacher/materials`

Create new material/lesson.

**Request:**
```json
{
  "class_id": 1,
  "title": "Introduction",
  "description": "Material description",
  "type": "video",
  "file": "file_upload",
  "is_premium": false,
  "price_token": 0,
  "duration": "15:30"
}
```

**Response:** 201 Created

---

### Get Class Materials
**GET** `/teacher/materials/class/{classId}`

Get materials for a class.

**Response:** 200 OK

---

### Generate Certificates
**POST** `/teacher/certificates/class/{classId}/generate`

Generate and issue certificates to students.

**Request:**
```json
{
  "student_ids": [1, 2, 3]
}
```

**Response:** 200 OK
```json
{
  "success": true,
  "message": "3 certificates generated"
}
```

---

### Get Earnings
**GET** `/teacher/earnings`

Get total earnings summary.

**Response:** 200 OK
```json
{
  "success": true,
  "data": {
    "total_earned": 5000000,
    "this_month": 500000,
    "pending_withdrawal": 100000,
    "withdrawn": 4900000
  }
}
```

---

### Get Earnings History
**GET** `/teacher/earnings/history`

Get detailed earnings history.

**Query Parameters:**
- `start_date=2026-03-01`
- `end_date=2026-03-31`
- `page=1`

**Response:** 200 OK
```json
{
  "success": true,
  "data": [ ... ],
  "pagination": { ... }
}
```

---

## Admin API

All endpoints require `Authorization: Bearer {token}` header and `role=admin`.

### Admin Dashboard
**GET** `/admin/dashboard`

Get admin dashboard with overall statistics.

**Response:** 200 OK
```json
{
  "success": true,
  "data": {
    "total_users": 1524,
    "total_teachers": 48,
    "total_students": 1476,
    "total_classes": 156,
    "total_revenue": 50000000,
    "new_users_today": 12
  }
}
```

---

### Get Users
**GET** `/admin/users`

Get all users.

**Query Parameters:**
- `role=murid|pengajar|admin`
- `status=aktif|nonaktif`
- `search=search_term`
- `page=1`

**Response:** 200 OK
```json
{
  "success": true,
  "data": [ ... ],
  "pagination": { ... }
}
```

---

### Get User Details
**GET** `/admin/users/{id}`

Get detailed user information.

**Response:** 200 OK
```json
{
  "success": true,
  "data": { ... }
}
```

---

### Update User Status
**POST** `/admin/users/{id}/status`

Change user status.

**Request:**
```json
{
  "status": "aktif" // or "nonaktif"
}
```

**Response:** 200 OK

---

### Delete User
**DELETE** `/admin/users/{id}`

Delete a user account.

**Response:** 200 OK

---

### Get Classes
**GET** `/admin/classes`

Get all classes with moderation status.

**Query Parameters:**
- `status=pending|approved|rejected|archived`
- `search=search_term`
- `page=1`

**Response:** 200 OK

---

### Approve Class
**POST** `/admin/classes/{id}/approve`

Approve a pending class.

**Response:** 200 OK

---

### Reject Class
**POST** `/admin/classes/{id}/reject`

Reject a class.

**Request:**
```json
{
  "reason": "Reason for rejection"
}
```

**Response:** 200 OK

---

### Get Donations
**GET** `/admin/donations`

Get all donations.

**Query Parameters:**
- `status=pending|success|failed`
- `page=1`

**Response:** 200 OK

---

### Refund Donation
**POST** `/admin/donations/{id}/refund`

Process refund for donation.

**Request:**
```json
{
  "reason": "Refund reason"
}
```

**Response:** 200 OK

---

### Get Reports
**GET** `/admin/reports/donations`

Get donation reports.

**Query Parameters:**
- `start_date=2026-03-01`
- `end_date=2026-03-31`

**Response:** 200 OK

---

### Export Reports
**GET** `/admin/reports/donations/export`

Export reports as CSV.

**Response:** 200 OK (CSV file)

---

## Error Handling

### Error Response Format
```json
{
  "success": false,
  "message": "Error message",
  "errors": {
    "field1": ["Error message"],
    "field2": ["Error message"]
  }
}
```

### HTTP Status Codes

| Code | Meaning |
|------|---------|
| 200 | OK - Success |
| 201 | Created - Resource created |
| 400 | Bad Request - Invalid input |
| 401 | Unauthorized - No token |
| 403 | Forbidden - No permission |
| 404 | Not Found - Resource not found |
| 422 | Unprocessable Entity - Validation error |
| 500 | Server Error |

---

## Response Format

### Success Response
```json
{
  "success": true,
  "message": "Operation successful",
  "data": { ... }
}
```

### Paginated Response
```json
{
  "success": true,
  "data": [ ... ],
  "pagination": {
    "current_page": 1,
    "last_page": 5,
    "per_page": 10,
    "total": 48
  }
}
```

### Error Response
```json
{
  "success": false,
  "message": "Error message",
  "errors": { ... }
}
```

---

## Authentication

Include token in Authorization header:
```
Authorization: Bearer {token}
```

Or in query parameter:
```
?token={token}
```

---

## 📊 Query Parameters Reference

| Endpoint | Parameter | Type | Description |
|----------|-----------|------|-------------|
| `/programs` | `search` | string | Search in title/description |
| `/programs` | `category` | string | Filter by category |
| `/programs` | `page` | int | Page number |
| `/programs` | `limit` | int | Items per page |
| `/mentors` | `search` | string | Search by name/email |
| `/mentors` | `rating` | float | Minimum rating filter |
| `/mentors` | `page` | int | Page number |
| `/student/classes` | `status` | string | active\|completed\|archived |
| `/student/classes` | `search` | string | Search by title |
| `/student/token/history` | `type` | string | penggunaan\|pendapatan\|komisi\|topup |
| `/admin/users` | `role` | string | murid\|pengajar\|admin |
| `/admin/users` | `status` | string | aktif\|suspended\|unverified |
| `/admin/classes` | `status` | string | pending\|approved\|rejected |

---

## 💻 Usage Examples

### cURL (Windows CMD)

**Register:**
```cmd
curl -X POST http://localhost:8000/api/v1/register ^
  -H "Content-Type: application/json" ^
  -d "{\"name\":\"Test User\",\"email\":\"test@example.com\",\"password\":\"password123\",\"password_confirmation\":\"password123\",\"role\":\"murid\"}"
```

**Login:**
```cmd
curl -X POST http://localhost:8000/api/v1/login ^
  -H "Content-Type: application/json" ^
  -d "{\"email\":\"test@example.com\",\"password\":\"password123\"}"
```

**Get Dashboard (with token):**
```cmd
curl http://localhost:8000/api/v1/student/dashboard ^
  -H "Authorization: Bearer YOUR_TOKEN_HERE"
```

### JavaScript (Fetch)

```javascript
// Login & Get Token
const loginResponse = await fetch("http://localhost:8000/api/v1/login", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({
        email: "test@example.com",
        password: "password123"
    })
});

const loginData = await loginResponse.json();
const token = loginData.data.token;

// Use Protected Endpoint
const dashResponse = await fetch("http://localhost:8000/api/v1/student/dashboard", {
    headers: {
        "Authorization": `Bearer ${token}`,
        "Content-Type": "application/json"
    }
});

const dashboard = await dashResponse.json();
console.log(dashboard);
```

---

## ✅ Implementation Status

| Phase | Category | Endpoints | Status | Notes |
|-------|----------|-----------|--------|-------|
| 3A | Landing | 3 | ✅ Complete | stats, volunteers, info |
| 3B | Programs | 4 | ✅ Complete | index, show, reviews, materials |
| 3D | Student | 30+ | ✅ Complete | dashboard, classes, materials, progress |
| 3E | Teacher | 40+ | ✅ Complete | dashboard, classes, materials, earnings |
| 3F | Mentors | 4 | ✅ Complete | index, show, classes, reviews |
| 3H | Donations | 4 | ✅ Complete | index, stats, recent, store |
| 3G | Learning Paths | 8 | ⚠️ Partial | Basic setup done |
| 3C | Admin | 40+ | ⏳ In Progress | dashboard, users, classes, reports |
| **TOTAL** | **All** | **100+** | **75% Complete** | Mostly production-ready |

### Quick Test

Get started with these endpoints:
```bash
# Public endpoints (no auth needed)
curl http://localhost:8000/api/v1/landing/stats
curl http://localhost:8000/api/v1/programs
curl http://localhost:8000/api/v1/mentors

# After login (use Bearer token)
curl http://localhost:8000/api/v1/student/dashboard \
  -H "Authorization: Bearer {token}"
```

---

## 🔗 Related Documentation

- **Quick Start Guide**: See [API_QUICK_START.md](API_QUICK_START.md) for setup instructions
- **Fixes & Solutions**: 
  - [Jitsi Live Class Fix](docs/JITSI_FIX.md)
  - [Logout 419 Error Fix](docs/LOGOUT_FIX.md)
  - [Category Integration](docs/KATEGORI_INTEGRASI.md)
- **Payment Integration**: [Midtrans Setup](docs/MIDTRANS_SETUP.md)
- **Archived Documentation**: [docs/ARCHIVE/](docs/ARCHIVE/)

---

## Versioning

Current version: **v1**  
Future versions will be available at `/api/v2`, etc.

---

**Last Updated:** March 15, 2026  
**Maintained By:** Ngajar.ID Dev Team  
**Support:** halo@ngajar.id
