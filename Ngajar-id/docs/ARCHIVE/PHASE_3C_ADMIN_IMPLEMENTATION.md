# 🛠️ PHASE 3C: ADMIN ENDPOINTS - IMPLEMENTATION GUIDE

**Status**: Ready for Implementation  
**Estimated Time**: 8-10 hours  
**Started**: March 15, 2024  
**Priority**: HIGH (Core admin functionality)

---

## 📋 Quick Start Checklist

- [x] Added `ApiResponse` trait to **AdminController**
- [x] Added `ApiResponse` trait to **AdminUserController**
- [ ] Add `ApiResponse` trait to remaining admin controllers
- [ ] Implement all 40+ admin endpoints
- [ ] Test all endpoints with curl commands
- [ ] Update documentation

---

## 🎯 IMPLEMENTATION ROADMAP

### ✅ DONE - AdminController (Dashboard)

**File**: `app/Http/Controllers/AdminController.php`

| Endpoint | Method | Status | Notes |
|----------|--------|--------|-------|
| GET /api/v1/admin/dashboard | index | ✅ DONE | Dual-mode support added |

### ⏳ TODO - AdminUserController (User Management)

**File**: `app/Http/Controllers/AdminUserController.php`

#### Methods to Implement/Update:

1. **GET /api/v1/admin/users**
   - List all users with filters
   - Support STATUS, ROLE, SEARCH filters
   - Paginate 15 per page
   - Status: Create `index()` method

2. **GET /api/v1/admin/users/{id}**
   - Get specific user details
   - Include related class/earning info
   - Status: Create `show()` method

3. **PUT /api/v1/admin/users/{id}**
   - Update user info (name, email, phone)
   - Status: Create `updateApi()` or upgrade `update()` to dual-mode

4. **POST /api/v1/admin/users/{id}/status**
   - Suspend/activate user
   - Status: Update `pengajarUpdateStatus()` and `muridUpdateStatus()` to dual-mode

5. **DELETE /api/v1/admin/users/{id}**
   - Delete user account
   - Status: Create `destroy()` method

6. **GET /api/v1/admin/users/teachers/list**
   - List all teachers
   - Status: Update `pengajarIndex()` to dual-mode

7. **POST /api/v1/admin/users/{id}/verify-teacher**
   - Verify/approve teacher account
   - Status: Create new method

8. **POST /api/v1/admin/users/{id}/revoke-teacher**
   - Revoke teacher privileges
   - Status: Create new method

9. **GET /api/v1/admin/users/students/list**
   - List all students
   - Status: Create `studentIndex()` method

10. **POST /api/v1/admin/users/{id}/scholarship**
    - Grant scholarship to student
    - Status: Create new method

11. **POST /api/v1/admin/users/{id}/token**
    - Adjust student token balance
    - Status: Create new method

**Implementation Template**:
```php
public function index(Request $request)
{
    try {
        $user = $request->user(); // Get authenticated admin
        $query = User::murid()->with('token');

        // Filters
        if ($request->get('status')) {
            $query->where('status', $request->get('status'));
        }
        
        if ($request->get('search')) {
            $search = $request->get('search');
            $query->where('name', 'ILIKE', "%{$search}%")
                ->orWhere('email', 'ILIKE', "%{$search}%");
        }

        $users = $query->latest()->paginate(15);

        if ($request->expectsJson()) {
            return $this->successWithPagination(
                UserResource::collection($users),
                'Users retrieved',
                $users
            );
        }

        return view('admin.users.index', compact('users'));

    } catch (\Exception $e) {
        if ($request->expectsJson()) {
            return $this->serverError($e->getMessage());
        }
        return back()->with('error', 'Failed to load users');
    }
}
```

---

### ⏳ TODO - AdminKelasController (Class Moderation)

**File**: `app/Http/Controllers/AdminKelasController.php`

| Endpoint | Method | Status | Notes |
|----------|--------|--------|-------|
| GET /api/v1/admin/classes | index | Need Dual-mode | List all classes |
| GET /api/v1/admin/classes/{id} | show | Need Dual-mode | Class details |
| POST /api/v1/admin/classes/{id}/approve | approve | NEW | Approve class |
| POST /api/v1/admin/classes/{id}/reject | reject | NEW | Reject class |
| POST /api/v1/admin/classes/{id}/archive | archive | NEW | Archive class |
| DELETE /api/v1/admin/classes/{id} | destroy | NEW | Delete class |
| POST /api/v1/admin/classes/{id}/flag | flag | NEW | Flag for review |

**Action Items**:
1. Add `ApiResponse` trait
2. Convert existing methods to dual-mode
3. Add approval/rejection workflows
4. Add flagging system for problematic content

---

### ⏳ TODO - AdminMateriController (Material Moderation)

**File**: `app/Http/Controllers/AdminMateriController.php`

| Endpoint | Method | Status | Notes |
|----------|--------|--------|-------|
| GET /api/v1/admin/materials | index | NEW | List all materials |
| GET /api/v1/admin/materials/{id} | show | NEW | Material details |
| PUT /api/v1/admin/materials/{id} | update | NEW | Edit material |
| DELETE /api/v1/admin/materials/{id} | destroy | NEW | Delete material |
| POST /api/v1/admin/materials/{id}/verify | verify | NEW | Verify/approve |

**Action Items**:
1. Add `ApiResponse` trait
2. Create comprehensive index with content filtering
3. Add verification workflow

---

### ⏳ TODO - AdminDonasiController (Donation Management)

**File**: `app/Http/Controllers/AdminDonasiController.php`

| Endpoint | Method | Status | Notes |
|----------|--------|--------|-------|
| GET /api/v1/admin/donations | index | NEW | List all donations |
| GET /api/v1/admin/donations/{id} | show | NEW | Donation details |
| POST /api/v1/admin/donations/{id}/verify | verify | NEW | Verify donation |
| POST /api/v1/admin/donations/{id}/refund | refund | NEW | Process refund |
| DELETE /api/v1/admin/donations/{id} | destroy | NEW | Delete donation |

**Action Items**:
1. Add `ApiResponse` trait
2. Create list with filtering (status, date range)
3. Add refund workflow

---

### ⏳ TODO - AdminReportController (Analytics & Reports)

**File**: `app/Http/Controllers/AdminReportController.php`
**Status**: NEW FILE - NEEDS CREATION

| Endpoint | Method | Status | Notes |
|----------|--------|--------|-------|
| GET /api/v1/admin/reports/donations | donasiIndex | NEW | Donation reports |
| GET /api/v1/admin/reports/donations/export | donasiExport | NEW | Export CSV |
| GET /api/v1/admin/reports/revenue | revenueIndex | NEW | Revenue reports |
| GET /api/v1/admin/reports/revenue/export | revenueExport | NEW | Export CSV |
| GET /api/v1/admin/reports/users | usersReport | NEW | User statistics |
| GET /api/v1/admin/reports/classes | classesReport | NEW | Class statistics |
| GET /api/v1/admin/reports/engagement | engagementReport | NEW | Engagement metrics |

**Create Skeleton**:
```php
<?php

namespace App\Http\Controllers;

use App\Http\Traits\ApiResponse;
use Illuminate\Http\Request;

class AdminReportController extends Controller
{
    use ApiResponse;

    public function donasiIndex(Request $request)
    {
        try {
            // Get donation reports
            if ($request->expectsJson()) {
                return $this->success([], 'Reports retrieved');
            }
            return view('admin.reports.donations');
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return $this->serverError($e->getMessage());
            }
            return back()->with('error', 'Failed to load reports');
        }
    }

    // ... Other methods follow same pattern
}
```

---

### ⏳ TODO - AdminNotificationController (Broadcasting)

**File**: `app/Http/Controllers/AdminNotificationController.php`
**Status**: NEW FILE - NEEDS CREATION

| Endpoint | Method | Status | Notes |
|----------|--------|--------|-------|
| GET /api/v1/admin/notifications | index | NEW | List notifications |
| POST /api/v1/admin/notifications/send | send | NEW | Send to user |
| POST /api/v1/admin/notifications/broadcast | broadcast | NEW | Broadcast to all |
| GET /api/v1/admin/notifications/history | history | NEW | Notification history |

**Create Skeleton**:
```php
<?php

namespace App\Http\Controllers;

use App\Http\Traits\ApiResponse;
use Illuminate\Http\Request;

class AdminNotificationController extends Controller
{
    use ApiResponse;

    public function index(Request $request)
    {
        try {
            $notifications = collect([]); // Get from DB
            
            if ($request->expectsJson()) {
                return $this->successWithPagination($notifications, 'Notifications retrieved');
            }
            return view('admin.notifications.index');
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return $this->serverError($e->getMessage());
            }
            return back()->with('error', 'Failed to load notifications');
        }
    }

    // ... Other methods follow same pattern
}
```

---

### ⏳ TODO - AdminSettingsController (Platform Settings)

**File**: `app/Http/Controllers/AdminSettingsController.php`
**Status**: NEW FILE - NEEDS CREATION

| Endpoint | Method | Status | Notes |
|----------|--------|--------|-------|
| GET /api/v1/admin/settings | index | NEW | Get all settings |
| POST /api/v1/admin/settings/general | updateGeneral | NEW | Update general |
| POST /api/v1/admin/settings/social | updateSocial | NEW | Update social |
| POST /api/v1/admin/settings/payment | updatePayment | NEW | Update payment |
| POST /api/v1/admin/settings/rules | updateRules | NEW | Update rules |

---

### ⏳ TODO - AdminLearningPathController (Learning Paths)

**File**: `app/Http/Controllers/AdminLearningPathController.php`

**Existing Methods to Update**:
- Add `ApiResponse` trait
- Convert all methods to dual-mode
- Ensure proper authorization checks

---

## 🔑 KEY IMPLEMENTATION NOTES

### 1. Authorization  
Always check admin role:
```php
if (!$request->user()->isAdmin()) {
    return $this->forbidden('Admin access required');
}
```

### 2. Error Handling Pattern
```php
try {
    // Business logic
    if ($request->expectsJson()) {
        return $this->success($data, 'Message');
    }
    return view(...);
} catch (\Exception $e) {
    \Log::error('Error: ' . $e->getMessage());
    if ($request->expectsJson()) {
        return $this->serverError($e->getMessage());
    }
    return back()->with('error', 'Message');
}
```

### 3. Response Formats

**List Endpoint**:
```php
return $this->successWithPagination(
    ResourceClass::collection($items),
    'Message',
    $items // pagination object
);
```

**Create/Update**:
```php
return $this->success(
    ResourceClass::make($item),
    'Message',
    201 // for create
);
```

### 4. Filtering Best Practices
- Always validate filter inputs
- Use `in_array()` for status filters
- Use `whereDate()` for date ranges
- Support `q` parameter for search

---

## 📊 PROGRESS TRACKING

**Controllers with ApiResponse**: 3/10
- ✅ AdminController
- ✅ AdminUserController (trait only)
- ⏳ AdminKelasController
- ⏳ AdminMateriController
- ⏳ AdminDonasiController
- ⏳ AdminReportController (NEW)
- ⏳ AdminNotificationController (NEW)
- ⏳ AdminSettingsController (NEW)
- ⏳ AdminLearningPathController
- ⏳ AdminKategoriController

**Endpoints Implemented**: ~5/40+
- ✅ GET /api/v1/admin/dashboard
- ⏳ GET /api/v1/admin/users
- ⏳ GET /api/v1/admin/users/{id}
- ⏳ ... and 37+ more

---

## 🚀 NEXT STEPS

1. **Update remaining controllers** with ApiResponse trait (10 min each)
2. **Implement stub methods** with error handling (20 min each)
3. **Add business logic** for each method (30-60 min each)
4. **Test with curl commands** (see below)
5. **Update documentation** when complete

---

## 📝 CURL TEST COMMANDS TEMPLATE

```bash
# Get admin dashboard
curl -X GET http://localhost:8000/api/v1/admin/dashboard \
  -H "Authorization: Bearer YOUR_TOKEN"

# Get all users
curl -X GET "http://localhost:8000/api/v1/admin/users?page=1&limit=15" \
  -H "Authorization: Bearer YOUR_TOKEN"

# Suspend user
curl -X POST http://localhost:8000/api/v1/admin/users/2/status \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"status": "nonaktif"}'

# Approve class
curl -X POST http://localhost:8000/api/v1/admin/classes/5/approve \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json"

# Process donation refund
curl -X POST http://localhost:8000/api/v1/admin/donations/3/refund \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"reason": "Accidental donation"}'
```

---

## 📈 ESTIMATED TIME BREAKDOWN

| Task | Hours | Status |
|------|-------|--------|
| Add ApiResponse to 7 controllers | 1.5 | ⏳ Ready |
| Create 3 new admin controllers | 1.5 | ⏳ Ready |
| Implement basic CRUD for all | 4 | ⏳ Ready |
| Add advanced features (filtering, reports) | 2 | ⏳ Ready |
| Testing & debugging | 1 | ⏳ Ready |
| **TOTAL** | **10 hours** | |

---

## ✅ COMPLETION CHECKLIST

When Phase 3C is complete, verify:
- [ ] All 40+ admin endpoints return JSON with ApiResponse format
- [ ] All endpoints support role-based access (admin only)
- [ ] All endpoints have proper error handling
- [ ] All endpoints tested with curl commands
- [ ] API documentation updated
- [ ] Dual-mode support (web + API) for all methods
- [ ] Database transactions for critical operations
- [ ] Logging for all admin actions
- [ ] Rate limiting considered for admin endpoints

---

**Ready to implement Phase 3C! 🚀**
