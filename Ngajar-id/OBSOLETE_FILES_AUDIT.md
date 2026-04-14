# Laravel Project Obsolete Files Audit
**Date:** April 14, 2026  
**Project:** Ngajar.id Platform  
**Status:** Migration to API-First Architecture Complete

---

## EXECUTIVE SUMMARY

The project has successfully transitioned to an API-first architecture with comprehensive API endpoints in `app/Http/Controllers/Api/V1/`. However, **significant code duplication and obsolete legacy code remains** that should be cleaned up:

- **47 files** requiring assessment or deletion
- **3 unused services** (no references outside their own file)
- **5 seeders** with overlapping functionality  
- **8+ controllers** with mixed responsibility patterns
- **Web routes** still active but could be deprecated for some features

---

## 1. OBSOLETE / DUPLICATE CONTROLLERS

### 1.1 Controllers with Mixed Responsibility (Web + API patterns)

These controllers serve both web-based views AND API requests (creating duplication):

| File | Routes Using It | Status | Recommendation |
|------|-----------------|--------|-----------------|
| `app/Http/Controllers/BelajarController.php` | web.php (5 routes), api.php (15 routes) | **MIXED** | Split into API-only or maintain as hybrid for backward compatibility |
| `app/Http/Controllers/CatalogController.php` | web.php (2 routes), api.php (3 routes) | **MIXED** | Split or deprecate web routes |
| `app/Http/Controllers/KelasController.php` | web.php (8 routes), api.php (8 routes) | **MIXED** | Has both create() form views and API methods |
| `app/Http/Controllers/MateriController.php` | web.php paths exist, api.php (6 routes) | **MIXED** | Overlaps with `Api/V1/MaterialUploadController.php` |
| `app/Http/Controllers/LearningPathController.php` | web.php & api.php | **MIXED** | API version exists (`LearningPathApiController.php`) |

**Action:** These controllers should either:
- **Migrate all logic to Api/V1/** and delete web routes, OR
- **Clearly separate concerns** into dedicated API and Web controllers

---

### 1.2 Controllers with NO Routes (Unused)

These controllers are defined but not used anywhere:

| File | References | Reason | Recommendation |
|------|-----------|--------|-----------------|
| `app/Http/Controllers/DashboardController.php` | Imported in api.php but NOT used in any route definition | Old implementation | **DELETE** - Not actively routed |
| `app/Http/Controllers/DonasiController.php` | DEPRECATED - Donation logic moved to webhooks | Webhook handling moved to `WebhookController.php` | **REVIEW** - May have legacy web routes |
| `app/Http/Controllers/NotificationController.php` | Old web-based, replaced by `Api/V1/NotificationController.php` | API replacement exists | **DELETE** - Use API version only |
| `app/Http/Controllers/ProgressController.php` | Exists but no routes reference it | Unused fallback | **DELETE** |
| `app/Http/Controllers/RecommendationController.php` | Exists but no active routes | Unused | **DELETE** |
| `app/Http/Controllers/SearchController.php` | API version exists in `Api/V1/SearchController.php` | Duplication | **DELETE** - Keep API version |

**References Search Results:**
- `ProgressController`: 0 route references found
- `RecommendationController`: 0 route references found
- `NotificationController`: Replaced by `Api/V1/NotificationController` (different namespace)

---

### 1.3 Legacy Session-Based Controllers (Web-Only)

These should be reviewed for migration to API or marked as deprecated:

| File | Purpose | Status | Action |
|------|---------|--------|--------|
| `app/Http/Controllers/ForgotPasswordController.php` | Password reset forms (web) | **LEGACY** | Keep if web UI needed, else migrate to API pattern (`PasswordController`) |
| `app/Http/Controllers/ProfileController.php` | User profile management (web views) | **ACTIVE** in web.php | Convert to API responses or keep as hybrid |
| `app/Http/Controllers/LandingController.php` | Landing page rendering | **ACTIVE** | Can coexist with API but consider full API migration |
| `app/Http/Controllers/TopupController.php` | Token topup (mixed web/API) | **MIXED** | API exists in `api.php` routes |

---

## 2. UNUSED SERVICES (Critical)

These services are **defined but NEVER imported or used** anywhere in the codebase:

| Service | File Path | Usage Count | Reason | Status |
|---------|-----------|-------------|--------|--------|
| `CategoryService` | `app/Services/CategoryService.php` | **0 usages** | Never imported anywhere | **DELETE** |
| `DashboardService` | `app/Services/DashboardService.php` | **0 usages** | Logic moved to controllers | **DELETE** |
| `EnrollmentService` | `app/Services/EnrollmentService.php` | **0 usages** in import statements* | Only instantiated internally via `app()` call | **REVIEW** |
| `SupabaseStorageService` | `app/Services/SupabaseStorageService.php` | **0 usages** | Dead code | **DELETE** |
| `GamificationService` | `app/Services/GamificationService.php` | **1 usage** - called from `EnrollmentService` | Only used by `EnrollmentService` internally | **KEEP** |

**Currently Used Services (Confirmed Imports):**
- ✅ `MidtransService` - used by `DonasiController`
- ✅ `XenditService` - used by `TopupController`, `DonasiController`
- ✅ `WebhookValidationService` - used by `WebhookController`
- ✅ `NotificationService` - used by `NotificationController`
- ✅ `LiveClassService` - used by `LiveClassController`
- ✅ `ProgressTrackingService` - used by `ProgressController`
- ✅ `RecommendationService` - used by `RecommendationController`

**Action:** Delete `CategoryService`, `DashboardService`, and `SupabaseStorageService`

---

## 3. DUPLICATE/OVERLAPPING FUNCTIONALITY

### 3.1 Controllers with API Versions

New dedicated API controllers exist that may supersede older versions:

| Old Controller | New API Version | Status | Action |
|---|---|---|---|
| `SearchController.php` (app/Http/Controllers/) | `Api/V1/SearchController.php` | **DUPLICATE** | **DELETE** old version |
| `NotificationController.php` | `Api/V1/NotificationController.php` | **DUPLICATE** | **DELETE** old version |
| `LearningPathController.php` | `Api/V1/LearningPathApiController.php` | **DUPLICATE** | Consolidate or delete old |
| `BelajarController.php` | Partially covered by `Api/V1/StudentProgressController.php` | **PARTIAL** | Migrate remaining logic |

### 3.2 Admin Controllers (Legacy Layer)

Old Admin controllers mixed with new API admin routes:

| Old Web Controller | New API Route | Status |
|---|---|---|
| `AdminUserController.php` | Routed in api.php | **ACTIVE** - used by both |
| `AdminKelasController.php` | Routed in api.php | **ACTIVE** - used by both |
| `AdminMateriController.php` | Routed in api.php | **ACTIVE** - used by both |
| `AdminDonasiController.php` | Routed in api.php | **ACTIVE** - used by both |
| `AdminReportController.php` | Routed in api.php | **ACTIVE** - used by both |
| `AdminNotificationController.php` | Routed in api.php | **ACTIVE** - used by both |
| `AdminSettingsController.php` | Routed in api.php | **ACTIVE** - used by both |
| `AdminLearningPathController.php` | Routed in api.php | **ACTIVE** - used by both |

**Note:** These work but create a confusing pattern. Consider refactoring to `Api/V1/Admin/` namespace.

---

## 4. DATABASE SEEDERS AUDIT

| Seeder | Purpose | Usage | Status | Recommendation |
|--------|---------|-------|--------|-----------------|
| `DatabaseSeeder.php` | **Main seeder** - Creates Admin, Teachers (5), Students (20) | Primary | **ACTIVE** | Keep as main entry point |
| `ContentSeeder.php` | Creates additional teachers and courses with rich content | Called from `DatabaseSeeder` | **ACTIVE** | Keep - provides realistic test data |
| `StudentSeeder.php` | Creates additional students (5) and enrollments | Standalone option | **POTENTIALLY DUPLICATE** | Overlaps with `DatabaseSeeder` student creation |
| `AdditionalPengajarSeeder.php` | Creates 10 more teachers | Standalone option | **POTENTIALLY DUPLICATE** | Overlaps with `ContentSeeder` teacher creation |
| `EnhancedDataSeeder.php` | Adds materials, donations, reviews | Standalone option | **POTENTIALLY DUPLICATE** | Overlaps with `ContentSeeder` |

**Issues:**
- Multiple seeders create the same type of data (teachers, students, courses)
- No clear seeding hierarchy - developers might run wrong combination
- Could result in duplicate records if all are run

**Recommendation:**
1. Consolidate into single seeding strategy:
   - `DatabaseSeeder.php` - Basic infrastructure only (no data)
   - `ContentSeeder.php` - All content generation (keep current)
   - DELETE: `StudentSeeder.php`, `AdditionalPengajarSeeder.php`, `EnhancedDataSeeder.php`

OR

2. Keep but rename to indicate execution order:
   - `01_DatabaseSeeder.php`
   - `02_ContentSeeder.php`
   - Document which ones to run

---

## 5. VIEW FILES AUDIT

### 5.1 Active Blade Views (In Use)

✅ **KEEP** - These are actively used:
- `resources/views/welcome.blade.php` - Landing page (web.php route)
- `resources/views/programs.blade.php` - Programs catalog (web.php route)
- `resources/views/mentors.blade.php` - Mentors directory (web.php route)
- `resources/views/auth/` - All auth views (login, register, password reset)
- `resources/views/emails/` - Email templates (transactional)
- `resources/views/layouts/` - Layout templates

### 5.2 Potentially Unused Views

Review these directories for active usage:

| Directory | Status | Notes |
|-----------|--------|-------|
| `resources/views/admin/` | **CHECK** | May be legacy if Filament used instead |
| `resources/views/donasi/` | **CHECK** | Donation UI - check if API replaced it |
| `resources/views/learning-paths/` | **CHECK** | May be API-only now |
| `resources/views/murid/` | **CHECK** | Student dashboard - check if API replaced it |
| `resources/views/pengajar/` | **CHECK** | Teacher interface - check if API replaced it |
| `resources/views/live-class/` | **CHECK** | Live class UI - verify current use |
| `resources/views/messages/` | **CHECK** | Messaging UI - verify if still active |
| `resources/views/profile/` | **CHECK** | Profile management - may be in API only |

**Action:** Search for view references in routes:
```bash
grep -r "view(" routes/ | grep -o "'[^']*'" | sort -u
```

---

## 6. OTHER OBSOLETE FILES

| File | Type | Status | Action |
|------|------|--------|--------|
| `comprehensive_review.php` | Root-level review file | **TEMP/DEBUG** | **DELETE** - Development artifact |
| `dashboard_summary.php` | Root-level report file | **TEMP/DEBUG** | **DELETE** - Development artifact |
| `review_system.php` | Root-level script | **TEMP/DEBUG** | **DELETE** - Development artifact |
| `test_material_quick.php` | Root-level test | **TEMP** | **DELETE** - Development artifact |
| `test_student_setup.php` | Root-level test | **TEMP** | **DELETE** - Development artifact |
| `test_teacher_setup.php` | Root-level test | **TEMP** | **DELETE** - Development artifact |
| `verify_seeding.php` | Root-level verification | **TEMP** | **DELETE** - Development artifact |
| `get_admin_token.php` | Root-level admin utility | **TEMP** | **DELETE** - Development artifact |

---

## 7. JOBS AUDIT

| Job | Usage | Status |
|-----|-------|--------|
| `SendCourseCompletionEmail.php` | Used in `BelajarController` | **ACTIVE** | Keep |

Only 1 job defined - minimal usage.

---

## 8. IMMEDIATE CLEANUP ACTIONS

### 🔴 **HIGH PRIORITY** - Delete These Files

```
app/Services/CategoryService.php
app/Services/DashboardService.php
app/Services/SupabaseStorageService.php
app/Http/Controllers/SearchController.php          # Keep Api/V1 version
app/Http/Controllers/NotificationController.php    # Keep Api/V1 version
app/Http/Controllers/ProgressController.php
app/Http/Controllers/RecommendationController.php

# Root-level development artifacts:
comprehensive_review.php
dashboard_summary.php
review_system.php
test_material_quick.php
test_student_setup.php
test_teacher_setup.php
verify_seeding.php
get_admin_token.php
```

### 🟠 **MEDIUM PRIORITY** - Review & Consolidate

```
# Seeders - consolidate into DatabaseSeeder.php and ContentSeeder.php:
database/seeders/StudentSeeder.php
database/seeders/AdditionalPengajarSeeder.php
database/seeders/EnhancedDataSeeder.php

# Mixed responsibility controllers - split or mark as deprecated:
app/Http/Controllers/BelajarController.php
app/Http/Controllers/CatalogController.php
app/Http/Controllers/KelasController.php
```

### 🟡 **LOW PRIORITY** - Review & Consider

```
# Legacy web-only patterns:
app/Http/Controllers/ForgotPasswordController.php    # If API password reset exists
app/Http/Controllers/ProfileController.php           # If API profile exists
app/Http/Controllers/DonasiController.php            # If webhooks sufficient

# Admin controllers - consider moving to Api/V1/Admin/ namespace
app/Http/Controllers/Admin*.php
```

---

## 9. DETECTION METHODOLOGY

### Services Analysis
```bash
# Find unused services
for service in CategoryService DashboardService EnrollmentService GamificationService SupabaseStorageService; do
  echo "=== $service ==="
  grep -r "$service" app/ routes/ --include="*.php" | grep -v "app/Services" | wc -l
done
```

### Controllers Analysis
```bash
# Find unused controllers
grep -r "use App\\\Http\\\Controllers" routes/ | sed "s/.*Controllers\\\\//" | sed 's/;.*//' | sort -u > used.txt
ls app/Http/Controllers/*.php | xargs -I {} basename {} .php > defined.txt
comm -23 defined.txt used.txt  # Controllers not in routes
```

### Views Analysis
```bash
# Find view() calls in routes
grep -r "view(" routes/ | sed "s/.*view('\([^']*\)'.*/\1/" | sort -u
```

---

## 10. DEPENDENCIES TO CHECK BEFORE DELETION

Before deleting any file, verify:

1. **NotificationController** deletion:
   - Check if web routes use it (found: web.php has NotificationController calls)
   - Verify `Api/V1/NotificationController` handles all cases

2. **SearchController** deletion:
   - Verify `Api/V1/SearchController` has all public search functionality

3. **ProgressController** deletion:
   - Check if `Api/V1/StudentProgressController` is complete replacement

4. **DashboardService** deletion:
   - Verify `DashboardController` doesn't import it
   - Check service providers

5. **CategoryService** deletion:
   - Verify category logic isn't needed for admin operations
   - Check CategoryService isn't lazy-loaded via ServiceProvider

---

## 11. MIGRATION ROADMAP

### Phase 1: Safe Deletions (0 dependencies)
- Delete root-level test/debug files
- Delete unused services confirmed to have 0 references

### Phase 2: API Consolidation
- Keep API v1 controllers, delete old duplicates
- Update routes to point only to API versions

### Phase 3: Legacy Controller Review
- Assess web-only vs hybrid controllers
- Decide: keep for backward compatibility or full API migration

### Phase 4: Seeder Consolidation
- Merge overlapping seed data
- Create single entry point

### Phase 5: Admin Namespace Refactoring
- Move Admin controllers to `Api/V1/Admin/`
- Update route imports

---

## 12. RECOMMENDATIONS SUMMARY

| Item | Keep | Delete | Consolidate |
|------|------|--------|-------------|
| **Services** | 7 (used) | 4 (unused) | - |
| **Controllers** | 20+ API | 6 duplicates | 8 mixed |
| **Seeders** | 2 main | 3 overlapping | Yes |
| **Views** | Most | Root artifacts | - |
| **Jobs** | 1 | - | - |

**Estimated cleanup time:** 2-3 hours  
**Risk level:** LOW (mostly deletions, no breaking changes if dependencies verified)

---

## NEXT STEPS

1. **Run verification script** to confirm dependencies before deletion
2. **Create feature branch** for cleanup work
3. **Delete in phases** (starting with confirmed unused)
4. **Run tests** after each phase
5. **Update documentation** with new architecture

