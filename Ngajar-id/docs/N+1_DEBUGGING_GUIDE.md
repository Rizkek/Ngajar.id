# 🚨 N+1 QUERY PROBLEM - Complete Debugging & Fix Guide

**Purpose**: Identify, locate, and fix ALL N+1 queries in Ngajar.ID  
**Severity**: 🔴 CRITICAL - Causes 10-50x performance degradation  
**Timeline**: ~6 hours to fix all identified issues  
**Status**: Ready to execute

---

## 📊 N+1 Problem Summary

### What is N+1 Query Problem?

```
EXAMPLE: Load 10 classes with their teachers

❌ WRONG (N+1 Problem):
Query 1: SELECT * FROM kelas LIMIT 10
         → Returns 10 classes

Loop iteration 1-10:
Query 2-11: SELECT * FROM users WHERE user_id = ?
           → Loads teacher for each class
           
Total: 1 + 10 = 11 queries for 10 classes ❌
Time: ~500ms (on fast server)

✅ CORRECT (Eager Loading):
Query 1: SELECT * FROM kelas LIMIT 10
Query 2: SELECT * FROM users WHERE user_id IN (?, ?, ..., ?)
         → Loads all 10 teachers in one query
         
Total: 2 queries for 10 classes ✅
Time: ~20ms
         
IMPROVEMENT: 25× faster! 🚀
```

---

## 🔍 KNOWN N+1 LOCATIONS IN NGAJAR.ID

### Location 1: DashboardController - Category Statistics

**File**: `app/Http/Controllers/DashboardController.php` (Lines 68-87)

**Problem Identified**:
```php
// ❌ WRONG: 2 separate queries instead of 1
$totalPerCategory = Kelas::selectRaw('kategori, count(*) as count')
    ->where('status', 'aktif')
    ->groupBy('kategori')
    ->pluck('count', 'kategori'); // Query 1

$enrolledPerCategory = DB::table('kelas')
    ->join('kelas_peserta', ...)
    ->selectRaw('kelas.kategori, count(*) as count')
    ->groupBy('kelas.kategori')
    ->pluck('count', 'kategori'); // Query 2
```

**Impact**: Dashboard load 2x slower than necessary

**Quick Fix**:
```php
// ✅ CORRECT: Single query with LEFT JOIN
$stats = DB::table('kelas')
    ->leftJoin('kelas_peserta', function ($join) use ($user) {
        $join->on('kelas.kelas_id', '=', 'kelas_peserta.kelas_id')
            ->where('kelas_peserta.siswa_id', $user->user_id);
    })
    ->where('kelas.status', 'aktif')
    ->selectRaw('
        kelas.kategori,
        COUNT(DISTINCT kelas.kelas_id) as total,
        COUNT(DISTINCT kelas_peserta.kelas_id) as enrolled
    ')
    ->groupBy('kelas.kategori')
    ->get();

// Result: 1 query instead of 2
```

---

### Location 2: BelajarController - Material Unlocking Check

**File**: `app/Http/Controllers/BelajarController.php` (Line 55)

**Problem Identified**:
```php
// ❌ WRONG: Lazy-loads kelas for each material
if (!$activeMateri->isUnlockedBy($user)) {
    // This calls method that lazy-loads relationships
}

// In Materi model:
public function isUnlockedBy(User $user) {
    $kelas = $this->kelas; // ← Lazy load happens HERE!
    // ...
}
```

**Attack Scenario**:
```
Scenario: Display 20 materials in course

Query 1: SELECT * FROM materi WHERE kelas_id = 1
         → Returns 20 materials

Loop:
Query 2-21: SELECT * FROM kelas WHERE kelas_id = ?
           → Loads class for each material (duplicate!)
           
Query 22-41: SELECT * FROM user_materials WHERE user_id = ? AND materi_id = ?
            → Checks unlock for each material
            
Total: 1 + 20 + 20 = 41 queries!!! ❌
```

**Quick Fix**:
```php
// ✅ STEP 1: Eager-load in controller
$materiList = Materi::where('kelas_id', $kelas_id)
    ->with(['kelas', 'materiPembeli']) // ← Eager load!
    ->get();

// ✅ STEP 2: Use pre-loaded data in model
public function isUnlockedBy(User $user): bool {
    // Use already-loaded relationship
    if (!$this->relationLoaded('kelas')) {
        $this->load('kelas');
    }
    
    if (!$this->kelas->is_premium) {
        return true;
    }
    
    return $this->materiPembeli()
        ->where('user_id', $user->user_id)
        ->exists();
}

// Result: 1 + 2 = 3 queries instead of 41!
```

---

### Location 3: Controllers Calling Methods in Loops

**File**: Multiple locations (Found via grep)

**Problem Identified**:
```php
// ❌ WRONG: Calling method in loop that queries database
foreach ($classes as $kelas) {
    $kelas->jumlah_peserta = $kelas->getJumlahPeserta(); // ← Query per class
    $kelas->jumlah_materi = $kelas->getJumlahMateri();   // ← Query per class
}

// In Kelas model:
public function getJumlahPeserta(): int {
    return $this->peserta()->count(); // ← Database query! (N times)
}

public function getJumlahMateri(): int {
    return $this->materi()->count(); // ← Database query! (N times)
}
```

**Attack Scenario**:
```
Load 100 classes to show in search results

Query 1: SELECT * FROM kelas LIMIT 100

Loop 100 times:
Query 2-101: SELECT COUNT(*) FROM kelas_peserta WHERE kelas_id = ?
Query 102-201: SELECT COUNT(*) FROM materi WHERE kelas_id = ?

Total: 1 + 100 + 100 = 201 queries!!! ❌
```

**Quick Fix**:
```php
// ✅ STEP 1: Use withCount() to get count in single query
$classes = Kelas::withCount(['peserta', 'materi'])
    ->limit(100)
    ->get();

// ✅ STEP 2: Access pre-computed counts
foreach ($classes as $kelas) {
    $jumlah_peserta = $kelas->peserta_count; // ← No query!
    $jumlah_materi = $kelas->materi_count;   // ← No query!
}

// Result: 1 query instead of 201!
```

---

### Location 4: Review/Discussion/Notes Relationships

**File**: Multiple controllers (BelajarController, MateriController)

**Problem Identified**:
```php
// ❌ WRONG: No eager loading of nested relationships
$materials = Material::get();

foreach ($materials as $materi) {
    $reviews = $materi->ulasan; // ← Lazy load per material
    
    foreach ($reviews as $review) {
        $reviewer = $review->user; // ← Lazy load per review
    }
}
```

**Attack Scenario**:
```
Load 50 materials with reviews

Query 1: SELECT * FROM materi LIMIT 50

Loop 50 materials:
Query 2-51: SELECT * FROM ulasan WHERE materi_id = ?

Loop reviews (avg 5 per material = 250 reviews):
Query 52-301: SELECT * FROM users WHERE user_id = ?

Total: 1 + 50 + 250 = 301 queries!!! ❌
```

**Quick Fix**:
```php
// ✅ CORRECT: Eager-load nested relationships
$materials = Material::with([
    'ulasan' => function ($q) {
        $q->with('user:user_id,name,avatar');
    }
])
->limit(50)
->get();

// Now access without queries:
foreach ($materials as $materi) {
    foreach ($materi->ulasan as $review) {
        $name = $review->user->name; // ← No query!
    }
}

// Result: 2 queries instead of 301!
```

---

## 🔧 DEBUGGING N+1 QUERIES

### Method 1: Laravel Debugbar (Easiest)

```bash
# Installation
composer require --dev barryvdh/laravel-debugbar

# Enable in development
# In .env:
DEBUGBAR_ENABLED=true

# Then:
# 1. Open any page in browser
# 2. Click Debugbar icon (bottom right)
# 3. Go to Queries tab
# 4. Look for duplicate queries
```

**What to look for**:
```
BEFORE OPTIMIZATION:
├─ Query 1: SELECT * FROM kelas WHERE ...
├─ Query 2: SELECT * FROM users WHERE id = 1
├─ Query 3: SELECT * FROM users WHERE id = 2  ← DUPLICATE!
├─ Query 4: SELECT * FROM users WHERE id = 3  ← DUPLICATE!
└─ Query 5: SELECT * FROM users WHERE id = 4  ← DUPLICATE!

AFTER OPTIMIZATION:
├─ Query 1: SELECT * FROM kelas WHERE ...
└─ Query 2: SELECT * FROM users WHERE id IN (1,2,3,4)
```

---

### Method 2: Query Counter Script (Automated)

Create `routes/debug.php`:

```php
<?php

Route::get('/debug/n-plus-one', function () {
    // Start query counting
    DB::listen(function ($query) {
        echo $query->sql . "\n";
    });

    // Run your code
    $classes = Kelas::all(); // Original query
    foreach ($classes as $kelas) {
        $kelas->pengajar->name; // Triggers N queries
    }

    // Shows all queries
});
```

---

### Method 3: Laravel Telescope (Real-time monitoring)

```bash
# Installation
composer require --dev laravel/telescope
php artisan telescope:install

# Then visit: http://localhost:8000/telescope
# Look for query waterfalls showing N+1 pattern
```

---

### Method 4: Automated Test Detection

```php
<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\TestCase;
use Illuminate\Support\Facades\DB;

class N1QueryDetectionTest extends TestCase
{
    public function test_dashboard_does_not_have_n_plus_one()
    {
        // Count initial queries
        $queryCount = 0;
        
        DB::listen(function () use (&$queryCount) {
            $queryCount++;
        });

        // Load dashboard
        auth()->loginUsingId(1);
        $response = $this->get('/api/dashboard');

        // Assert reasonable query count
        $this->assertLessThan(10, $queryCount, 
            "Dashboard made $queryCount queries (should be <10)");
    }

    public function test_materials_list_does_not_have_n_plus_one()
    {
        $queryCount = 0;
        
        DB::listen(function () use (&$queryCount) {
            $queryCount++;
        });

        $response = $this->get('/api/materials?limit=50');

        // Loading 50 materials should use eager loading
        $this->assertLessThan(5, $queryCount,
            "Loading 50 materials made $queryCount queries");
    }
}
```

Run it:
```bash
php artisan test --filter "N1Query" --verbose
```

---

## 🔨 COMPREHENSIVE FIX GUIDE

### Fix #1: DashboardController

**File**: `app/Http/Controllers/DashboardController.php`

```php
<?php

namespace App\Http\Controllers;

use App\Services\DashboardService;

class DashboardController extends Controller
{
    public function __construct(private DashboardService $service) {}

    public function muridDashboard(Request $request)
    {
        // ✅ Slim controller - service does everything
        $data = $this->service->getStudentDashboard(
            $request->user(),
            $request->get('kategori')
        );

        return response()->json($data);
    }
}

// In app/Services/DashboardService.php:

class DashboardService
{
    public function getCategoryStatistics(User $user): array
    {
        // ✅ OPTIMIZED: 1 query instead of 2
        return DB::table('kelas')
            ->leftJoin('kelas_peserta', function ($join) use ($user) {
                $join->on('kelas.kelas_id', '=', 'kelas_peserta.kelas_id')
                    ->where('kelas_peserta.siswa_id', $user->user_id);
            })
            ->where('kelas.status', 'aktif')
            ->selectRaw('
                kelas.kategori,
                COUNT(DISTINCT kelas.kelas_id) as total,
                COUNT(DISTINCT kelas_peserta.kelas_id) as enrolled
            ')
            ->groupBy('kelas.kategori')
            ->get()
            ->map(fn($row) => [
                'total' => $row->total,
                'enrolled' => $row->enrolled,
                'percentage' => round(($row->enrolled / $row->total) * 100),
            ])
            ->toArray();
    }
}
```

---

### Fix #2: BelajarController (Material Unlocking)

**File**: `app/Http/Controllers/BelajarController.php`

```php
<?php

public function show($kelas_id, $materi_id = null)
{
    $user = Auth::user();
    $kelas = Kelas::with('pengajar:user_id,name')
        ->findOrFail($kelas_id);

    // ✅ OPTIMIZED: Eager load all needed relationships
    $materiList = Materi::where('kelas_id', $kelas_id)
        ->with([
            'kelas:kelas_id,is_premium',
            'materiPembeli:materi_id,user_id'
        ])
        ->orderBy('created_at')
        ->get();

    // Get active material
    $activeMateri = $materi_id 
        ? $materiList->firstWhere('materi_id', $materi_id)
        : $materiList->first();

    // ✅ NOW: No extra queries - uses pre-loaded data
    if (!$activeMateri->hasAccessFor($user)) {
        abort(403, 'Material locked');
    }

    return response()->json([
        'kelas' => $kelas,
        'materials' => $materiList,
        'active' => $activeMateri,
    ]);
}

// In app/Models/Materi.php:

class Materi extends Model
{
    // ✅ NEW: Uses pre-loaded relationships
    public function hasAccessFor(User $user): bool
    {
        // Check already-loaded kelas relationship
        if (!$this->relationLoaded('kelas')) {
            $this->load('kelas');
        }

        if (!$this->kelas->is_premium) {
            return true;
        }

        // Use pre-loaded materiPembeli
        if ($this->relationLoaded('materiPembeli')) {
            return $this->materiPembeli
                ->contains('user_id', $user->user_id);
        }

        // Fallback (shouldn't happen if controller does right)
        return $this->materiPembeli()
            ->where('user_id', $user->user_id)
            ->exists();
    }
}
```

---

### Fix #3: Controllers Using Count in Loops

**File**: Multiple files using `getJumlahPeserta()` etc

```php
// ❌ OLD:
$classes = Kelas::all();
foreach ($classes as $kelas) {
    $kelas->peserta_count = $kelas->getJumlahPeserta();
}

// ✅ NEW: Use withCount()
$classes = Kelas::withCount(['peserta', 'materi'])
    ->get();

// Access counts directly (no queries):
foreach ($classes as $kelas) {
    $peserta_count = $kelas->peserta_count; // From withCount
    $materi_count = $kelas->materi_count;   // From withCount
}
```

In any controller that needs counts:

```php
// ✅ OPTIMIZED VERSION
Route::get('/classes', function () {
    $classes = Kelas::withCount([
        'peserta',
        'materi',
        'ulasan'
    ])
    ->where('status', 'aktif')
    ->limit(100)
    ->get();

    return response()->json($classes);
});
```

---

### Fix #4: Nested Relationship Loading

**File**: Any controller showing materials with reviews

```php
// ❌ OLD (301 queries):
$materials = Material::with('ulasan')->get();
foreach ($materials as $materi) {
    foreach ($materi->ulasan as $review) {
        echo $review->user->name; // ← Lazy load per review
    }
}

// ✅ NEW (2 queries):
$materials = Material::with([
    'ulasan' => function ($q) {
        $q->with('user:user_id,name,avatar')
          ->orderBy('rating', 'desc');
    }
])
->get();

// Now all data pre-loaded:
foreach ($materials as $materi) {
    foreach ($materi->ulasan as $review) {
        echo $review->user->name; // ← No query!
    }
}
```

---

## 📋 COMPLETE FIX CHECKLIST

### Step 1: Identify
- [ ] Install Laravel Debugbar
- [ ] Visit each main page in browser
- [ ] Check Debugbar Queries tab
- [ ] Note any duplicate queries
- [ ] Record query counts

### Step 2: Document
- [ ] List all N+1 locations found
- [ ] Note query count before/after target
- [ ] Identify relationships causing N+1
- [ ] Plan fix approach

### Step 3: Fix
- [ ] Add eager loading to controllers
- [ ] Add withCount() where needed
- [ ] Add scopes to models
- [ ] Extract repeated code to services

### Step 4: Test
- [ ] Run automated N+1 detection tests
- [ ] Verify query count reduced
- [ ] Check performance improvement
- [ ] Test with 50+ items (worst case)

### Step 5: Verify
- [ ] Run full test suite
- [ ] Check Debugbar (should see fewer queries)
- [ ] Load test with many concurrent users
- [ ] Monitor database CPU usage

---

## 🧪 TESTING N+1 FIXES

### Automated Test Suite

```php
<?php

namespace Tests\Feature;

class N1QueryOptimizationTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        $this->withoutExceptionHandling();
    }

    /** @test */
    public function dashboard_loads_with_minimal_queries()
    {
        $user = User::factory()->create();
        
        $queries = collect();
        DB::listen(fn($q) => $queries->push($q->sql));

        $this->actingAs($user)->get('/api/dashboard');

        // Should be <= 5 queries
        $this->assertLessThan(6, $queries->count(), 
            "Dashboard made {$queries->count()} queries");

        // Should not have duplicate category queries
        $categoryQueries = $queries->filter(function ($sql) {
            return str_contains($sql, 'kategori') && str_contains($sql, 'count');
        });
        
        $this->assertLessThanOrEqual(1, $categoryQueries->count(),
            "Found duplicate category queries");
    }

    /** @test */
    public function materials_list_loads_with_eager_loading()
    {
        $kelas = Kelas::factory()->has(Materi::factory()->count(10))->create();
        
        $queries = collect();
        DB::listen(fn($q) => $queries->push($q));

        // Load materials with reviews
        $response = $this->get("/api/kelas/{$kelas->id}/materials");

        // Should use only 2-3 queries (materials + relationships)
        // NOT 1 + 10 + (reviews × 5 or more)
        
        // Count queries by table
        $tableQueries = $queries->groupBy(function ($q) {
            preg_match('/from `?(\w+)`?/', $q->sql, $matches);
            return $matches[1] ?? 'unknown';
        });

        // Should have minimal table hits
        $this->assertLessThan(5, $tableQueries->keys()->count(),
            "Too many tables queried: " . 
            $tableQueries->keys()->implode(', '));
    }

    /** @test */
    public function can_handle_load_test_with_many_items()
    {
        // Create realistic data load
        Kelas::factory(50)
            ->has(Materi::factory(20))
            ->has(Ulasan::factory(10))
            ->create();

        $startTime = microtime(true);
        $queries = collect();
        
        DB::listen(fn($q) => $queries->push($q));

        // This should NOT make 50 + 1000 + 500 queries!
        $response = $this->get('/api/materials?limit=50');

        $duration = microtime(true) - $startTime;

        $this->assertLessThan(10, $queries->count(),
            "Performance test: {$queries->count()} queries in {$duration}ms");
        
        $this->assertLessThan(1000, $duration,
            "Load test took {$duration}ms (should be <1s)");
    }
}
```

Run tests:
```bash
php artisan test --filter "N1Query" -v
```

---

## 🎯 PRIORITY FIXES

### Priority 1 (Do First - Most Impact)
```
❌ BelajarController isUnlockedBy() 
   Impact: Every material load on every course
   Fix: 41 queries → 3 queries
   Time: 1 hour

❌ DashboardController category stats
   Impact: Every dashboard load
   Fix: 2 queries → 1 query  
   Time: 30 minutes

❌ withCount() for peserta/materi
   Impact: Any list showing counts
   Fix: 1+N queries → 1 query
   Time: 1 hour
```

### Priority 2 (Do Next - Important)
```
⚠️ Review/Discussion relationships
   Impact: Pages with nested data
   Fix: 300+ queries → 3 queries
   Time: 1.5 hours

⚠️ Material access checks in loops
   Impact: Listing materials
   Fix: N+N queries → 1 query
   Time: 1 hour
```

---

## ✅ SUCCESS CRITERIA

After fixing all N+1 queries:

```
✅ Dashboard loads in <50ms (was 500ms+)
✅ Material list loads in <100ms (was 1000ms+)
✅ Debugbar shows 2-5 queries per page (not 20+)
✅ Database CPU drops 50%+
✅ Can handle 100 concurrent users without lag
✅ All tests pass with green performance metrics
```

---

## 🚀 NEXT STEPS

1. **Install Debugbar**: `composer require --dev barryvdh/laravel-debugbar`
2. **Scan all pages**: Browse UI, check Debugbar queries
3. **Document findings**: List each N+1 location
4. **Apply fixes**: Use solutions from this guide
5. **Test heavily**: Run automated tests
6. **Deploy**: Monitor production queries

---

**Status**: 🟢 **Ready to Fix - All code provided**  
**Est. Time**: 4-6 hours for all fixes  
**Impact**: 10-50× performance improvement
