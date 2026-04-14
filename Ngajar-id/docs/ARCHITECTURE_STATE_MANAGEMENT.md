# 🏗️ Laravel Architecture & State Management Guide - Ngajar.ID

**Document Purpose**: Comprehensive guide on state management, data flow, and rendering patterns in Laravel (equivalent to JS framework patterns)

**Last Updated**: March 15, 2026

---

## 📚 Table of Contents

1. [State Management Comparison](#state-management-comparison)
2. [Current Architecture](#current-architecture)
3. [Data Flow Patterns](#data-flow-patterns)
4. [Rendering Patterns](#rendering-patterns)
5. [Production Readiness Assessment](#production-readiness-assessment)
6. [Improvement Recommendations](#improvement-recommendations)

---

## 🔄 State Management Comparison

### JavaScript Frameworks (Vue/React)
```
User Action → State Change → Re-render → DOM Update
```

**Patterns Used:**
- Redux/Vuex/Pinia: Centralized state
- Props/Emit: Component communication
- Lifecycle hooks: Side effects
- Computed/Derived state: Selectors

### Laravel Architecture (Equivalent)
```
HTTP Request → Service Layer → Update Model → Database → Response
```

**Patterns Used:**
- **Services**: Business logic (like Vuex actions)
- **Events**: Side effects & cascading updates (like Redux thunks)
- **Models**: State container (like Redux store)
- **Database**: Persistent state (like LocalStorage)
- **API Resources**: Data transformation & serialization (like selectors)

---

## 🏛️ Current Architecture

### 1. **Service Layer** (Business Logic)

Located: `app/Services/`

#### What We Have:
```
✅ MidtransService    - Payment processing (external integration)
✅ XenditService      - Alternative payments (HTTP client)
✅ SupabaseStorageService - File storage integration
```

#### How It Works:
```php
// Example: User enrollment in class
class EnrollmentService {
    public function enrollStudent($userId, $classId, $paymentMethod) {
        // Business logic (equivalent to Redux action)
        $class = Kelas::findOrFail($classId);
        
        if ($paymentMethod === 'token') {
            $this->deductTokens($userId, $class->price_token);
        }
        
        // Create enrollment
        Enrollment::create(['user_id' => $userId, 'class_id' => $classId]);
        
        // Trigger side effect (event dispatch)
        event(new UserEnrolledInClass($userId, $classId));
        
        return true;
    }
}
```

**Current Status**: ⚠️ Partially implemented
- Only payment services implemented
- Business logic scattered in controllers
- **Needs**: UserService, EnrollmentService, ProgressService, etc.

---

### 2. **Event-Driven Architecture** (Side Effects)

Located: `app/Events/`, `app/Listeners/`

#### Current Implementation:

**✅ MateriCompleted Event**
```php
// Trigger when user completes material
event(new MateriCompleted($user, $material, $class));

// Listener: AwardXpToUser fires automatically
class AwardXpToUser {
    public function handle(MateriCompleted $event) {
        // 1. Award XP
        $event->user->addXp($event->material->xp_value);
        
        // 2. Check level up
        if ($event->user->canLevelUp()) {
            $event->user->levelUp();
        }
        
        // 3. Check achievement
        // 4. Send email
    }
}
```

**Event-Driven Pattern Benefits:**
- ✅ Decouples business logic
- ✅ Enables complex workflows
- ✅ Easy to add features without modifying core
- Equivalent to Redux middleware + side effects

**Current Status**: ⚠️ Only 1 event implemented
- **Needs**: 
  - UserRegistered → Send welcome email + referral bonus
  - ClassCreated → Notify admin for approval
  - ReviewSubmitted → Update teacher rating
  - DonationReceived → Send thank you email
  - CertificateIssued → Notify student

---

### 3. **Model Layer** (State Container)

Located: `app/Models/`

#### Models as State:
```php
// Model = Unified source of truth (like Redux store)
class User extends Model {
    // State properties
    protected $attributes = [
        'name', 'email', 'password', 'role', 'xp', 'level'
    ];
    
    // Relationships (normalized state)
    public function classes() {
        return $this->belongsToMany(Kelas::class, 'enrollment');
    }
    
    // Computed properties (like Redux selectors)
    public function getTotalXpAttribute() {
        return $this->xp; // Could compute from activities
    }
    
    // Actions (like Redux mutations)
    public function addXp($amount) {
        $this->xp += $amount;
        $this->save();
    }
}
```

**Current Status**: ✅ Well-structured
- 15+ models properly defined
- Relationships clearly mapped
- Already normalized (no data duplication)
- Accessors/Mutators implemented
- Casting properly configured

---

### 4. **API Resources** (Data Transformation/Selectors)

Located: `app/Http/Resources/`

#### Equivalent to Redux Selectors:
```php
// Transform state into presentation format
class UserResource extends JsonResource {
    public function toArray($request) {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'level' => $this->level,
            'xp' => $this->xp,
            // Data selector (only expose what's needed)
            'profile' => [
                'bio' => $this->bio,
                'avatar' => $this->avatar_url,
            ],
        ];
    }
}
```

**Current Status**: ✅ Implemented
- UserResource, KelasResource, MateriResource, DonasiResource present
- Proper pagination support
- Resource collections for lists

**Needs**: Expand to all models for consistency

---

## 📊 Data Flow Patterns

### Pattern 1: User Registration (Complete Flow)

```
┌─────────────────────────────────────────────────────────┐
│                  CLIENT REQUEST                         │
│  POST /api/register {name, email, password, terms}     │
└────────────────────┬────────────────────────────────────┘
                     │
┌────────────────────▼────────────────────────────────────┐
│              CONTROLLER LAYER                           │
│  AuthController::register(RegisterRequest $request)    │
│  - Validates input (FormRequest)                        │
│  - Calls business logic (Service)                       │
└────────────────────┬────────────────────────────────────┘
                     │
┌────────────────────▼────────────────────────────────────┐
│              SERVICE LAYER                              │
│  RegistrationService::register()                        │
│  - Hash password                                        │
│  - Create user in database                              │
│  - Generate verification token                          │
│  - Dispatch event: UserRegistered                       │
└────────────────────┬────────────────────────────────────┘
                     │
         ┌───────────┴──────────────┐
         │                          │
    ┌────▼──────┐          ┌────────▼──────┐
    │ Database  │          │ Event Queue   │
    │ Store: ✅ │          │               │
    └───────────┘          └────────┬──────┘
                          (Async listeners)
                           │
         ┌─────────────────┼──────────────────┐
         │                 │                  │
    ┌────▼─────┐  ┌────────▼────┐  ┌─────────▼──┐
    │Send Email │  │Award Bonus  │  │Log Activity│
    │Listener   │  │Listener     │  │Listener    │
    └───────────┘  └─────────────┘  └────────────┘
         │
┌────────▼────────────────────────────────────────────────┐
│              RESPONSE LAYER                             │
│  return ApiResponse::success([                          │
│    'user' => UserResource::make($user),                │
│    'token' => $token                                    │
│  ]);                                                    │
└─────────────────────────────────────────────────────────┘
         │
┌────────▼────────────────────────────────────────────────┐
│                 CLIENT                                  │
│  {success: true, data: {user: {...}, token: "..."}}   │
└─────────────────────────────────────────────────────────┘
```

**Current Status**: ✅ Mostly implemented
- Controllers present, services partially
- Events exist but need expansion
- Database transactions working

---

### Pattern 2: Material Completion (Event-Driven)

```
┌──────────────────────────┐
│ Student clicks Complete  │
└────────┬─────────────────┘
         │
┌────────▼──────────────────────┐
│ BelajarController::           │
│ completeMaterial($id)         │
└────────┬──────────────────────┘
         │
┌────────▼──────────────────────────────┐
│ Mark material as completed             │
│ Dispatch event: MateriCompleted       │
└────────┬──────────────────────────────┘
         │
         └──────┬─────────────────┬────────────────┐
                │                 │                │
         ┌──────▼────────┐  ┌─────▼──────┐  ┌────▼────────┐
         │AwardXpListener│  │Check Cert  │  │Notify Class │
         │- Add XP (✅) │  │- Check if  │  │- Broadcast  │
         │- Level up   │  │  completed │  │  to teacher │
         │             │  │  (emit)    │  │             │
         └─────────────┘  └────────────┘  └─────────────┘
                │
               ✅ XP Awarded → User State Updated
               ✅ DB Saved → Persistent
               ✅ Email Sent → User Notified
```

**Current Status**: ✅ Working well
- Only 1 listener (needs more)
- Pattern is correct for expansion

---

## 🎨 Rendering Patterns

### Pattern 1: Blade Template Rendering (Web)

**File**: `resources/views/belajar/show.blade.php`

```blade
<!-- Server-rendered state (equivalent to component props) -->
@extends('layouts.app')

@section('content')
    <!-- State binding -->
    <h1>{{ $kelas->title }}</h1>
    
    <!-- Conditional rendering -->
    @if(auth()->user()->isEnrolled($kelas->id))
        <div class="materials">
            @foreach($kelas->materials as $material)
                <div class="material-card">
                    <!-- Showing state -->
                    <h3>{{ $material->title }}</h3>
                    
                    <!-- Computed property binding -->
                    @if($material->completed)
                        <span class="badge badge-success">Completed</span>
                    @else
                        <button @click="toggleComplete">Mark Complete</button>
                    @endif
                </div>
            @endforeach
        </div>
    @else
        <button onclick="enrollClass({{$kelas->id}})">Enroll Now</button>
    @endif
@endsection
```

**Rendering Type**: Server-side rendering (SSR)
- ✅ Good for SEO
- ✅ Simpler authentication checks
- ⚠️ Requires page reload for state changes (consider AJAX)

---

### Pattern 2: API Response Rendering (SPA/Mobile)

**Endpoint**: `GET /api/v1/student/learning/materials/1`

```php
// Controller returns API Resource (like JSON serializer)
public function getMaterial($id) {
    $material = Material::findOrFail($id);
    
    // Transform state into JSON
    return $this->success(
        new MateriResource($material),
        'Material retrieved'
    );
}

// Resource transforms state
class MateriResource extends JsonResource {
    public function toArray($request) {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'type' => $this->type,
            'completed' => $this->userCompleted(),
            'locked' => $this->isLocked(),
            'content' => $this->getContentUrl(),
        ];
    }
}

// Returns: {"success": true, "data": {...}}
```

**Rendering Type**: Client-side rendering (CSR)
- ✅ Mobile app support
- ✅ Real-time updates
- ✅ REST API pattern

**Current Status**: ✅ Properly implemented with ApiResponse trait

---

## 📊 Production Readiness Assessment

### ✅ **Strengths**

| Aspect | Status | Details |
|--------|--------|---------|
| **Database Design** | ✅ Excellent | Normalized, properly indexed, migrations versioned |
| **API Structure** | ✅ Excellent | RESTful, consistent response format, 100+ endpoints |
| **Authentication** | ✅ Good | Session + Sanctum tokens, social auth via Socialite |
| **Authorization** | ✅ Good | Policies, middleware role checks |
| **Validation** | ✅ Good | FormRequest classes, custom rules |
| **Error Handling** | ✅ Good | Try-catch in controllers, proper error responses |
| **Testing Setup** | ✅ Configured | PHPUnit, Mockery, Faker ready |
| **Email System** | ✅ Configured | Queue jobs for async emails |
| **File Storage** | ✅ Configured | Supabase integration for scalability |

---

### ⚠️ **Areas Needing Improvement**

| Area | Issue | Impact | Priority |
|------|-------|--------|----------|
| **Service Layer** | Scattered business logic | Hard to test, maintainability | HIGH |
| **Repository Pattern** | Not used | Direct DB queries in controllers | HIGH |
| **Event System** | Only 1 event utilized | Missing workflows | MEDIUM |
| **Middleware** | No request rate limiting | Vulnerable to abuse | HIGH |
| **Input Sanitization** | Basic validation only | XSS/SQL injection risk | HIGH |
| **API Documentation** | Manual (no auto-generation) | Hard to maintain | MEDIUM |
| **Caching** | Not implemented | Performance issues at scale | MEDIUM |
| **Database Query** | N+1 queries possible | Slow page loads | MEDIUM |
| **Logging** | Basic Laravel logging | Hard to track issues in prod | MEDIUM |
| **Monitoring** | Not set up | No alerts for errors | MEDIUM |

---

## 🔧 Improvement Recommendations

### Priority 1: Security & Performance (CRITICAL)

#### 1.1 Implement Service Layer Properly
```php
// Create: app/Services/StudentService.php
class StudentService {
    public function enrollInClass(User $user, Kelas $kelas, $paymentMethod) {
        return DB::transaction(function () use ($user, $kelas, $paymentMethod) {
            // Validate enrollment eligibility
            $this->validateEnrollment($user, $kelas);
            
            // Process payment
            if ($paymentMethod === 'token') {
                $this->deductTokens($user, $kelas->price_token);
            }
            
            // Create enrollment
            $enrollment = $user->classes()->attach($kelas->id, [
                'enrolled_at' => now(),
                'status' => 'active'
            ]);
            
            // Dispatch event for side effects
            event(new UserEnrolledInClass($user, $kelas));
            
            return $enrollment;
        });
    }
}
```

#### 1.2 Move Controllers to Slim Handlers
```php
// BEFORE: Fat controller
class BelajarController extends Controller {
    public function complete($id) {
        $material = Material::findOrFail($id);
        $user = auth()->user();
        
        // All logic in controller ❌
        $user->materials()->attach($material->id, ['completed_at' => now()]);
        $user->addXp($material->xp);
        // ... more logic
    }
}

// AFTER: Slim controller using service
class BelajarController extends Controller {
    public function __construct(private StudentService $service) {}
    
    public function complete($id) {
        $this->service->completeMaterial(auth()->user(), $id);
        return $this->success(null, 'Material completed');
    }
}
```

#### 1.3 Implement Repository Pattern
```php
// app/Repositories/MaterialRepository.php
class MaterialRepository {
    public function getCompletedByUser(User $user, $limit = 10) {
        return Material::whereHas('users', fn($q) => 
            $q->where('user_id', $user->id)
        )->with(['class', 'user'])->paginate($limit);
    }
}

// Usage in Service
class StudentService {
    public function __construct(private MaterialRepository $repo) {}
    
    public function getMyCompletedMaterials() {
        return $this->repo->getCompletedByUser(auth()->user());
    }
}
```

---

### Priority 2: API Improvements

#### 2.1 Add More Events
```php
// app/Events/UserRegistered.php
class UserRegistered {
    public function __construct(public User $user) {}
}

// app/Listeners/SendWelcomeEmail.php
class SendWelcomeEmail {
    public function handle(UserRegistered $event) {
        Mail::queue(new WelcomeEmail($event->user));
    }
}

// app/Listeners/AwardReferralBonus.php
class AwardReferralBonus {
    public function handle(UserRegistered $event) {
        if ($event->user->referral_code) {
            // Award bonus to referrer
        }
    }
}

// Register in: app/Providers/EventServiceProvider.php
protected $listen = [
    UserRegistered::class => [
        SendWelcomeEmail::class,
        AwardReferralBonus::class,
    ],
];
```

---

### Priority 3: Performance & Monitoring

#### 3.1 Add Eager Loading to Prevent N+1
```php
// BAD: N+1 queries
$classes = Kelas::all();
foreach ($classes as $class) {
    echo $class->user->name; // 1 + N queries ❌
}

// GOOD: Eager loading
$classes = Kelas::with('user', 'materials', 'reviews')->get();
foreach ($classes as $class) {
    echo $class->user->name; // 2 queries total ✅
}
```

#### 3.2 Add Query Caching
```php
// Cache expensive queries
$stats = Cache::remember('landing_stats', 3600, function () {
    return [
        'total_users' => User::count(),
        'total_classes' => Kelas::count(),
        // ...
    ];
});

// Invalidate on changes
event(new ClassCreated($class)); // Clear cache in listener
```

#### 3.3 Add Logging for Production Debugging
```php
// app/Services/StudentService.php
use Psr\Log\LoggerInterface;

class StudentService {
    public function __construct(private LoggerInterface $logger) {}
    
    public function completeMaterial(User $user, $materialId) {
        try {
            $this->logger->info('User completing material', [
                'user_id' => $user->id,
                'material_id' => $materialId,
            ]);
            
            // ... logic
        } catch (Exception $e) {
            $this->logger->error('Material completion failed', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }
}
```

---

## 🚀 Production Readiness Checklist

```
✅ DATABASE
  [x] Migrations in version control
  [x] Relationships properly defined
  [x] Indexes on foreign keys
  [x] Constraints enforced
  
✅ API
  [x] RESTful endpoints
  [x] Consistent response format
  [x] Pagination implemented
  [x] Error handling
  
⚠️ ARCHITECTURE
  [ ] Service layer fully extracted (50%) - NEEDS WORK
  [ ] Repository pattern (0%) - NEEDS IMPLEMENTATION
  [ ] Event system expanded (15%) - NEEDS WORK
  
✅ SECURITY
  [x] CSRF protection on forms
  [x] Authentication checks
  [x] Password hashing (bcrypt)
  [ ] Rate limiting (0%) - NEEDS IMPLEMENTATION
  [ ] Input sanitization (basic only)
  
⚠️ PERFORMANCE
  [ ] Query optimization (partial)
  [ ] Caching strategy (0%)
  [ ] CDN for static files (not set up)
  
⚠️ MONITORING
  [ ] Error tracking (basic Laravel logging only)
  [ ] Performance monitoring (not set up)
  [ ] Email alerts (not configured)
```

---

## 📝 Summary

### Can We Go to Production Now?

**Answer: 80% Ready, with caveats**

✅ **CAN GO TO PRODUCTION IF:**
- Expected traffic: < 10K users
- Operations team monitors logs daily
- No complex state management needed
- Simple workflows acceptable

⚠️ **ISSUES TO DO FIRST:**
1. Implement rate limiting (security)
2. Extract service layer fully (maintainability)
3. Add input sanitization (security)
4. Set up error tracking (debugging)
5. Expand event system (user experience)

---

**Recommendation**: 
- Deploy to production with monitoring
- Phase improvements over 2-3 sprints
- Add security hardening before scaling
- Complete service layer refactor before 20K users
