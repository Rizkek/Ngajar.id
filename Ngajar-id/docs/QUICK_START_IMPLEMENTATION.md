# 🚀 Ngajar.ID - Quick Start Implementation Guide

**Objective:** Get all refinements live in 4 weeks

---

## ⚡ WEEK 1: Database & Core (CRITICAL)

### **Day 1-2: Run Migrations**

```bash
# Navigate to project
cd e:\coding\Ngajar.id\Ngajar-id

# Run all pending migrations
php artisan migrate

# Verify tables created
php artisan tinker
>>> Schema::hasTable('learning_paths')
=> true
>>> Schema::hasTable('user_path_progress')
=> true
```

**Expected Output:**

- ✅ 3 new tables: `learning_paths`, `learning_path_kelas`, `user_path_progress`
- ✅ `is_free` field added to `learning_paths`
- ✅ `tipe` & `keterangan` fields added to `token_log`

---

### **Day 3: Add Learning Paths Routes**

**File:** `routes/web.php`

**Action:** Copy content from `routes/learning_paths_routes.php` and paste at **line 135** (after murid materi routes, before pengajar routes)

**Verify:**

```bash
php artisan route:list | grep learning-paths
```

**Expected:** 7 routes listed

---

### **Day 4-5: Create Sample Learning Paths**

**Option A: Via Tinker (Quick)**

```bash
php artisan tinker
```

```php
use App\Models\LearningPath;
use App\Models\Kelas;

// Create a FREE Web Development Path
$path = LearningPath::create([
    'judul' => 'Web Development Fundamentals',
    'deskripsi' => 'Belajar dasar web development dari nol',
    'kategori' => 'Programming',
    'level' => 'Beginner',
    'estimated_hours' => 40,
    'is_free' => true,
    'is_active' => true,
    'created_by' => 1 // Admin user_id
]);

// Attach existing classes to path
$kelasIds = Kelas::where('kategori', 'Programming')->take(5)->pluck('kelas_id');
foreach ($kelasIds as $index => $kelasId) {
    $path->kelas()->attach($kelasId, [
        'urutan' => $index + 1,
        'is_required' => true
    ]);
}
```

**Option B: Via Seeder (Better)**
Create file: `database/seeders/LearningPathSeeder.php`

```php
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\LearningPath;
use App\Models\Kelas;

class LearningPathSeeder extends Seeder
{
    public function run()
    {
        // Web Development Path (FREE)
        $webPath = LearningPath::create([
            'judul' => 'Web Development Fundamentals',
            'deskripsi' => 'Roadmap lengkap belajar web development dari nol hingga mahir',
            'kategori' => 'Programming',
            'level' => 'Beginner',
            'estimated_hours' => 40,
            'is_free' => true,
            'is_active' => true,
            'created_by' => 1
        ]);

        // Attach classes (adjust based on your data)
        $webKelas = Kelas::where('kategori', 'Programming')->take(5)->get();
        foreach ($webKelas as $index => $kelas) {
            $webPath->kelas()->attach($kelas->kelas_id, [
                'urutan' => $index + 1,
                'is_required' => true
            ]);
        }

        // UI/UX Path (FREE)
        $designPath = LearningPath::create([
            'judul' => 'UI/UX Design Basics',
            'deskripsi' => 'Belajar dasar-dasar desain UI/UX untuk pemula',
            'kategori' => 'Design',
            'level' => 'Beginner',
            'estimated_hours' => 30,
            'is_free' => true,
            'is_active' => true,
            'created_by' => 1
        ]);

        // Add more paths as needed...
    }
}
```

Run seeder:

```bash
php artisan db:seed --class=LearningPathSeeder
```

---

## 📝 WEEK 2: Messaging & Content

### **Day 6-7: Update Homepage**

**File:** `resources/views/welcome.blade.php`

**Changes:**

1. Hero section:
    - Change tagline to: "Belajar Gratis dari Relawan Expert"
    - CTA: "Mulai Belajar Gratis"

2. Add FREE learning paths section:

```blade
<section class="py-16 bg-gray-50">
    <div class="container mx-auto px-4">
        <h2 class="text-3xl font-bold text-center mb-12">
            Learning Paths Gratis 🎓
        </h2>

        <div class="grid md:grid-cols-3 gap-6">
            @foreach($freePaths as $path)
                <div class="bg-white rounded-xl p-6 shadow-sm">
                    <span class="inline-block px-3 py-1 bg-green-100 text-green-700 text-xs font-bold rounded-full mb-3">
                        GRATIS
                    </span>
                    <h3 class="text-xl font-bold mb-2">{{ $path->judul }}</h3>
                    <p class="text-gray-600 text-sm mb-4">{{ $path->deskripsi }}</p>
                    <a href="{{ route('learning-paths.show', $path->path_id) }}"
                       class="text-teal-600 font-semibold hover:underline">
                        Mulai Belajar →
                    </a>
                </div>
            @endforeach
        </div>
    </div>
</section>
```

**Controller Update:**

```php
// LandingController.php
public function index() {
    $freePaths = LearningPath::free()->active()->take(3)->get();
    return view('welcome', compact('freePaths'));
}
```

---

### **Day 8-9: Add FREE Badges**

**Files to Update:**

- `resources/views/murid/index.blade.php` ✅ Already done
- `resources/views/learning-paths/index.blade.php` (to be created)
- `resources/views/murid/katalog.blade.php`

**Badge Component:**

```blade
@if($item->is_free ?? true)
    <span class="inline-block px-2 py-1 bg-green-100 text-green-700 text-xs font-bold rounded">
        GRATIS
    </span>
@else
    <span class="inline-block px-2 py-1 bg-amber-100 text-amber-700 text-xs font-bold rounded">
        PREMIUM
    </span>
@endif
```

---

### **Day 10: Create Scholarship Page**

**File:** `resources/views/scholarship/apply.blade.php`

```blade
@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-12">
    <div class="max-w-2xl mx-auto bg-white rounded-xl shadow-lg p-8">
        <h1 class="text-3xl font-bold mb-4">Apply Beasiswa 🎁</h1>
        <p class="text-gray-600 mb-8">
            Kami percaya pendidikan adalah hak semua orang.
            Apply beasiswa untuk akses FULL ke semua konten premium.
        </p>

        <form action="{{ route('scholarship.submit') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="mb-4">
                <label class="block text-sm font-semibold mb-2">Status</label>
                <select name="status" class="w-full border rounded-lg px-4 py-2" required>
                    <option value="">Pilih Status</option>
                    <option value="pelajar">Pelajar</option>
                    <option value="mahasiswa">Mahasiswa</option>
                    <option value="pengangguran">Pengangguran</option>
                    <option value="lainnya">Lainnya</option>
                </select>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-semibold mb-2">
                    Alasan (100-500 karakter)
                </label>
                <textarea name="alasan" rows="5"
                          class="w-full border rounded-lg px-4 py-2"
                          minlength="100" maxlength="500" required></textarea>
            </div>

            <div class="mb-6">
                <label class="block text-sm font-semibold mb-2">
                    Upload KTM/KTP (Optional tapi direkomendasikan)
                </label>
                <input type="file" name="dokumen" accept="image/*,application/pdf"
                       class="w-full border rounded-lg px-4 py-2">
            </div>

            <button type="submit"
                    class="w-full bg-teal-600 hover:bg-teal-700 text-white font-bold py-3 rounded-lg">
                Submit Application
            </button>
        </form>
    </div>
</div>
@endsection
```

**Route:**

```php
Route::get('/scholarship/apply', [ScholarshipController::class, 'showForm'])->name('scholarship.apply');
Route::post('/scholarship/apply', [ScholarshipController::class, 'submit'])->name('scholarship.submit');
```

---

## 🎨 WEEK 3: UX Improvements

### **Day 11-12: Simplify Registration**

**File:** `resources/views/auth/register.blade.php`

**Before:**

```blade
<!-- Many fields: name, email, password, phone, address, etc -->
```

**After:**

```blade
<form method="POST" action="{{ route('register') }}">
    @csrf

    <!-- Name -->
    <div class="mb-4">
        <label>Nama Lengkap</label>
        <input type="text" name="name" required>
    </div>

    <!-- Email -->
    <div class="mb-4">
        <label>Email</label>
        <input type="email" name="email" required>
    </div>

    <!-- Password -->
    <div class="mb-4">
        <label>Password</label>
        <input type="password" name="password" required>
    </div>

    <!-- Password Confirmation -->
    <div class="mb-4">
        <label>Konfirmasi Password</label>
        <input type="password" name="password_confirmation" required>
    </div>

    <button type="submit">Daftar Gratis</button>
</form>
```

**Remove:**

- ❌ Phone number
- ❌ Address
- ❌ Birth date
- ❌ Email verification requirement (allow immediate access)

---

### **Day 13-14: Improve Token Top-Up UX**

**File:** `resources/views/murid/index.blade.php`

**Already improved in previous session ✅**

**Additional:** Add quick top-up widget in sidebar

```blade
<!-- Sidebar Quick Top-Up -->
<div class="bg-white rounded-xl p-4 shadow-sm mb-4">
    <h4 class="font-semibold mb-3">Saldo Token</h4>
    <p class="text-3xl font-bold text-teal-600 mb-3">
        {{ number_format($userStats['token_balance']) }}
    </p>
    <button onclick="openTopupModal()"
            class="w-full bg-teal-600 hover:bg-teal-700 text-white py-2 rounded-lg text-sm">
        Top Up
    </button>
</div>
```

---

### **Day 15: Add Celebration Animations**

**File:** `resources/views/components/celebration.blade.php`

```blade
<div id="celebration" class="fixed inset-0 pointer-events-none z-50 hidden">
    <div class="absolute inset-0 flex items-center justify-center">
        <div class="bg-white rounded-2xl p-8 shadow-2xl animate-bounce">
            <div class="text-6xl mb-4 text-center">🎉</div>
            <h2 class="text-2xl font-bold text-center mb-2">Selamat!</h2>
            <p class="text-gray-600 text-center" id="celebration-message"></p>
        </div>
    </div>
</div>

<script>
function showCelebration(message) {
    const el = document.getElementById('celebration');
    document.getElementById('celebration-message').textContent = message;
    el.classList.remove('hidden');
    setTimeout(() => el.classList.add('hidden'), 3000);
}
</script>
```

**Usage:**

```javascript
// After completing a class
showCelebration("Kelas selesai! +10 XP earned");
```

---

## 🎁 WEEK 4: Scholarship & Polish

### **Day 16-17: Implement Scholarship Auto-Approval**

**Create Controller:** `app/Http/Controllers/ScholarshipController.php`

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class ScholarshipController extends Controller
{
    public function showForm()
    {
        return view('scholarship.apply');
    }

    public function submit(Request $request)
    {
        $request->validate([
            'status' => 'required',
            'alasan' => 'required|min:100|max:500',
            'dokumen' => 'nullable|file|mimes:jpg,png,pdf|max:2048'
        ]);

        $user = auth()->user();

        // Auto-approve for students with document
        $autoApprove = false;
        if (in_array($request->status, ['pelajar', 'mahasiswa']) && $request->hasFile('dokumen')) {
            $autoApprove = true;
        }

        // Auto-approve for top 10% leaderboard
        // (implement leaderboard logic here)

        if ($autoApprove) {
            $user->update(['is_beasiswa' => true]);
            return redirect()->route('dashboard')
                ->with('success', 'Beasiswa disetujui! Selamat belajar!');
        }

        // Otherwise, manual review
        // TODO: Create scholarship_applications table
        return redirect()->route('dashboard')
            ->with('info', 'Aplikasi diterima! Kami akan review dalam 24-48 jam.');
    }
}
```

---

### **Day 18-19: Create Learning Paths Views**

**File:** `resources/views/learning-paths/index.blade.php`

```blade
@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-12">
    <h1 class="text-4xl font-bold mb-8">Learning Paths</h1>

    <!-- Filters -->
    <div class="flex gap-4 mb-8">
        <a href="{{ route('learning-paths.index') }}"
           class="px-4 py-2 rounded-lg {{ !request('kategori') ? 'bg-teal-600 text-white' : 'bg-gray-100' }}">
            Semua
        </a>
        @foreach($categories as $cat)
            <a href="{{ route('learning-paths.index', ['kategori' => $cat]) }}"
               class="px-4 py-2 rounded-lg {{ request('kategori') == $cat ? 'bg-teal-600 text-white' : 'bg-gray-100' }}">
                {{ $cat }}
            </a>
        @endforeach
    </div>

    <!-- Paths Grid -->
    <div class="grid md:grid-cols-3 gap-6">
        @foreach($paths as $path)
            <div class="bg-white rounded-xl p-6 shadow-sm hover:shadow-lg transition">
                @if($path->is_free)
                    <span class="inline-block px-3 py-1 bg-green-100 text-green-700 text-xs font-bold rounded-full mb-3">
                        GRATIS
                    </span>
                @else
                    <span class="inline-block px-3 py-1 bg-amber-100 text-amber-700 text-xs font-bold rounded-full mb-3">
                        PREMIUM
                    </span>
                @endif

                <h3 class="text-xl font-bold mb-2">{{ $path->judul }}</h3>
                <p class="text-gray-600 text-sm mb-4">{{ Str::limit($path->deskripsi, 100) }}</p>

                <div class="flex items-center gap-4 text-sm text-gray-500 mb-4">
                    <span>📚 {{ $path->kelas->count() }} kelas</span>
                    <span>⏱️ {{ $path->estimated_hours }}h</span>
                    <span>👥 {{ $path->total_enrolled }}</span>
                </div>

                <a href="{{ route('learning-paths.show', $path->path_id) }}"
                   class="block w-full text-center bg-teal-600 hover:bg-teal-700 text-white py-2 rounded-lg">
                    Lihat Detail
                </a>
            </div>
        @endforeach
    </div>
</div>
@endsection
```

---

### **Day 20: Final Testing & Launch**

**Checklist:**

- [ ] All migrations run successfully
- [ ] Learning paths routes working
- [ ] Can enroll to free path
- [ ] Can enroll to premium path (with tokens)
- [ ] Scholarship application works
- [ ] Token top-up works
- [ ] FREE badges visible everywhere
- [ ] Homepage updated with new messaging
- [ ] Registration simplified (3 fields)
- [ ] Celebration animations working

**Test User Journey:**

1. Register (3 fields only)
2. Browse free learning paths
3. Enroll to free path
4. Complete first class
5. See celebration animation
6. View premium path
7. Apply scholarship OR top-up tokens
8. Enroll to premium path
9. Complete path
10. Download certificate

---

## 📊 Post-Launch Monitoring

### **Week 5+: Track Metrics**

**Daily:**

- New registrations
- Free vs premium enrollments
- Scholarship applications

**Weekly:**

- Retention rates (Day 1, Week 1)
- Conversion rates (free → premium)
- Scholarship approval rate

**Monthly:**

- NPS survey
- User interviews
- A/B tests

---

## 🆘 Troubleshooting

### **Migration Errors:**

```bash
# Rollback last migration
php artisan migrate:rollback

# Fresh migrate (CAUTION: deletes data)
php artisan migrate:fresh
```

### **Route Not Found:**

```bash
# Clear route cache
php artisan route:clear

# List all routes
php artisan route:list
```

### **View Not Found:**

```bash
# Clear view cache
php artisan view:clear
```

---

## 📞 Need Help?

**Reference Documents:**

- `docs/ALIGNMENT_REFINEMENT_SUMMARY.md` - Overview
- `docs/FREE_VS_PREMIUM_POLICY.md` - Balance guidelines
- `docs/POSITIONING_MESSAGING_GUIDE.md` - Messaging
- `docs/USER_JOURNEY_SIMPLIFIED.md` - UX guide

---

**Let's make education accessible for all! 🚀🎓**
