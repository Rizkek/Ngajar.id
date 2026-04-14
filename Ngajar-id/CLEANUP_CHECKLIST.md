# QUICK CLEANUP CHECKLIST

## 🔴 IMMEDIATE DELETE (No Dependencies)

- [ ] `app/Services/CategoryService.php` - 0 usages
- [ ] `app/Services/DashboardService.php` - 0 usages  
- [ ] `app/Services/SupabaseStorageService.php` - 0 usages
- [ ] `app/Http/Controllers/ProgressController.php` - 0 route references
- [ ] `app/Http/Controllers/RecommendationController.php` - 0 route references
- [ ] `comprehensive_review.php` - Debug file
- [ ] `dashboard_summary.php` - Debug file
- [ ] `review_system.php` - Debug file
- [ ] `test_material_quick.php` - Test file
- [ ] `test_student_setup.php` - Test file
- [ ] `test_teacher_setup.php` - Test file
- [ ] `verify_seeding.php` - Test file
- [ ] `get_admin_token.php` - Utility file

## 🟠 DELETE AFTER VERIFICATION

- [ ] `app/Http/Controllers/SearchController.php` (verify `Api/V1/SearchController` is complete)
- [ ] `app/Http/Controllers/NotificationController.php` (verify `Api/V1/NotificationController` handles all)

## 🟡 REVIEW & DECIDE

- [ ] `database/seeders/StudentSeeder.php` - Consolidate into `ContentSeeder`
- [ ] `database/seeders/AdditionalPengajarSeeder.php` - Consolidate into `ContentSeeder`
- [ ] `database/seeders/EnhancedDataSeeder.php` - Consolidate into `ContentSeeder`
- [ ] Audit web routes in `resources/views/` directories:
  - `admin/`
  - `donasi/`
  - `learning-paths/`
  - `murid/`
  - `pengajar/`
  - `live-class/`
  - `messages/`
  - `profile/`

## 🟢 REFACTORING (Later)

- [ ] Split hybrid controllers:
  - `BelajarController.php` (web + api)
  - `CatalogController.php` (web + api)
  - `KelasController.php` (web + api)
  - `MateriController.php` (overlaps with MaterialUploadController)

- [ ] Move Admin controllers to `Api/V1/Admin/` namespace:
  - `AdminUserController.php`
  - `AdminKelasController.php`
  - `AdminMateriController.php`
  - `AdminDonasiController.php`
  - `AdminReportController.php`
  - `AdminNotificationController.php`
  - `AdminSettingsController.php`
  - `AdminLearningPathController.php`

## VERIFICATION COMMANDS

```bash
# Check if CategoryService is used anywhere
grep -r "CategoryService" app/ routes/ --include="*.php" | grep -v "app/Services/CategoryService.php"

# Check if DashboardService is used
grep -r "DashboardService" app/ routes/ --include="*.php" | grep -v "app/Services/DashboardService.php"

# Check if SupabaseStorageService is used
grep -r "SupabaseStorageService" app/ routes/ --include="*.php" | grep -v "app/Services/SupabaseStorageService.php"

# Check ProgressController routes
grep -r "ProgressController" routes/ --include="*.php"

# Check RecommendationController routes
grep -r "RecommendationController" routes/ --include="*.php"
```

**Last Updated:** April 14, 2026

