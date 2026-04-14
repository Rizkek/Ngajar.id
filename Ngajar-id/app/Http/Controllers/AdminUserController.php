<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Token;
use App\Http\Traits\ApiResponse;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminUserController extends Controller
{
    use ApiResponse;

    /**
     * List all users (paginated)
     * GET /admin/users
     */
    public function index(Request $request)
    {
        try {
            $query = User::query()
                ->withCount('kelasIkuti', 'kelasAjar', 'donasi')
                ->with('personalAccessTokens');

            // Filter by role
            if ($request->has('role') && $request->role !== 'all') {
                $query->where('role', $request->role);
            }

            // Filter by status
            if ($request->has('status') && $request->status !== 'all') {
                $query->where('status', $request->status);
            }

            // Search by name or email
            if ($request->has('search') && $request->search) {
                $query->where(function ($q) use ($request) {
                    $q->where('name', 'like', "%{$request->search}%")
                        ->orWhere('email', 'like', "%{$request->search}%");
                });
            }

            // Sort
            $sortBy = $request->get('sort', 'created_at');
            $sortDir = $request->get('direction', 'desc');
            $query->orderBy($sortBy, $sortDir);

            $data = $query->paginate($request->get('per_page', 15));

            if ($request->expectsJson()) {
                return $this->successWithPagination(
                    UserResource::collection($data),
                    'Users retrieved successfully'
                );
            }

            return view('admin.users.index', compact('data'));
        } catch (\Exception $e) {
            \Log::error('AdminUserController@index: ' . $e->getMessage());
            if ($request->expectsJson()) {
                return $this->serverError($e->getMessage());
            }
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Get single user
     * GET /admin/users/{id}
     */
    public function show(Request $request, $id)
    {
        try {
            $user = User::with('kelasIkuti', 'kelasAjar', 'donasi', 'personalAccessTokens')
                ->findOrFail($id);

            if ($request->expectsJson()) {
                return $this->success(
                    new UserResource($user),
                    'User retrieved successfully'
                );
            }

            return view('admin.users.show', compact('user'));
        } catch (\Exception $e) {
            \Log::error('AdminUserController@show: ' . $e->getMessage());
            if ($request->expectsJson()) {
                return $this->notFound('User not found');
            }
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Update user
     * PUT /admin/users/{id}
     */
    public function update(Request $request, $id)
    {
        try {
            $user = User::findOrFail($id);

            $validated = $request->validate([
                'name' => 'nullable|string|max:255',
                'email' => 'nullable|email|unique:users,email,' . $id,
                'phone' => 'nullable|string|max:20',
                'bio' => 'nullable|string',
                'avatar' => 'nullable|url',
            ]);

            $user->update($validated);

            if ($request->expectsJson()) {
                return $this->success(
                    new UserResource($user),
                    'User updated successfully'
                );
            }

            return back()->with('success', 'User updated successfully');
        } catch (\Exception $e) {
            \Log::error('AdminUserController@update: ' . $e->getMessage());
            if ($request->expectsJson()) {
                return $this->serverError($e->getMessage());
            }
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Update user status (aktif/nonaktif)
     * POST /admin/users/{id}/status
     */
    public function updateStatus(Request $request, $id)
    {
        try {
            $user = User::findOrFail($id);

            $validated = $request->validate([
                'status' => 'required|in:aktif,nonaktif,suspended',
            ]);

            $user->update($validated);

            if ($request->expectsJson()) {
                return $this->success(
                    new UserResource($user),
                    'User status updated successfully'
                );
            }

            return back()->with('success', 'User status updated successfully');
        } catch (\Exception $e) {
            \Log::error('AdminUserController@updateStatus: ' . $e->getMessage());
            if ($request->expectsJson()) {
                return $this->serverError($e->getMessage());
            }
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Delete user
     * DELETE /admin/users/{id}
     */
    public function destroy(Request $request, $id)
    {
        try {
            $user = User::findOrFail($id);

            // Prevent deleting admin
            if ($user->role === 'admin') {
                throw new \Exception('Cannot delete admin users');
            }

            $user->delete();

            if ($request->expectsJson()) {
                return $this->success(null, 'User deleted successfully');
            }

            return back()->with('success', 'User deleted successfully');
        } catch (\Exception $e) {
            \Log::error('AdminUserController@destroy: ' . $e->getMessage());
            if ($request->expectsJson()) {
                return $this->serverError($e->getMessage());
            }
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * List teachers
     * GET /admin/users/teachers/list
     */
    public function teacherIndex(Request $request)
    {
        try {
            $query = User::where('role', 'pengajar')
                ->withCount('kelasAjar')
                ->with('personalAccessTokens');

            // Filter by status
            if ($request->has('status') && $request->status !== 'all') {
                $query->where('status', $request->status);
            }

            // Search
            if ($request->has('search') && $request->search) {
                $query->where(function ($q) use ($request) {
                    $q->where('name', 'like', "%{$request->search}%")
                        ->orWhere('email', 'like', "%{$request->search}%");
                });
            }

            $data = $query->latest()->paginate($request->get('per_page', 15));

            if ($request->expectsJson()) {
                return $this->successWithPagination(
                    UserResource::collection($data),
                    'Teachers retrieved successfully'
                );
            }

            return view('admin.teachers.index', compact('data'));
        } catch (\Exception $e) {
            \Log::error('AdminUserController@teacherIndex: ' . $e->getMessage());
            if ($request->expectsJson()) {
                return $this->serverError($e->getMessage());
            }
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Verify teacher
     * POST /admin/users/{id}/verify-teacher
     */
    public function verifyTeacher(Request $request, $id)
    {
        try {
            $user = User::where('role', 'pengajar')->findOrFail($id);

            $user->update(['status' => 'aktif']);

            if ($request->expectsJson()) {
                return $this->success(
                    new UserResource($user),
                    'Teacher verified successfully'
                );
            }

            return back()->with('success', 'Teacher verified successfully');
        } catch (\Exception $e) {
            \Log::error('AdminUserController@verifyTeacher: ' . $e->getMessage());
            if ($request->expectsJson()) {
                return $this->serverError($e->getMessage());
            }
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Revoke teacher
     * POST /admin/users/{id}/revoke-teacher
     */
    public function revokeTeacher(Request $request, $id)
    {
        try {
            $user = User::where('role', 'pengajar')->findOrFail($id);

            $user->update(['status' => 'nonaktif']);

            if ($request->expectsJson()) {
                return $this->success(
                    new UserResource($user),
                    'Teacher revoked successfully'
                );
            }

            return back()->with('success', 'Teacher revoked successfully');
        } catch (\Exception $e) {
            \Log::error('AdminUserController@revokeTeacher: ' . $e->getMessage());
            if ($request->expectsJson()) {
                return $this->serverError($e->getMessage());
            }
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * List students
     * GET /admin/users/students/list
     */
    public function studentIndex(Request $request)
    {
        try {
            $query = User::where('role', 'murid')
                ->withCount('kelasIkuti')
                ->with('personalAccessTokens');

            // Filter by status
            if ($request->has('status') && $request->status !== 'all') {
                $query->where('status', $request->status);
            }

            // Search
            if ($request->has('search') && $request->search) {
                $query->where(function ($q) use ($request) {
                    $q->where('name', 'like', "%{$request->search}%")
                        ->orWhere('email', 'like', "%{$request->search}%");
                });
            }

            $data = $query->latest()->paginate($request->get('per_page', 15));

            if ($request->expectsJson()) {
                return $this->successWithPagination(
                    UserResource::collection($data),
                    'Students retrieved successfully'
                );
            }

            return view('admin.students.index', compact('data'));
        } catch (\Exception $e) {
            \Log::error('AdminUserController@studentIndex: ' . $e->getMessage());
            if ($request->expectsJson()) {
                return $this->serverError($e->getMessage());
            }
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Grant scholarship to student
     * POST /admin/users/{id}/scholarship
     */
    public function grantScholarship(Request $request, $id)
    {
        try {
            $user = User::where('role', 'murid')->findOrFail($id);

            $validated = $request->validate([
                'is_beasiswa' => 'required|boolean',
            ]);

            $user->update($validated);

            if ($request->expectsJson()) {
                return $this->success(
                    new UserResource($user),
                    'Scholarship status updated successfully'
                );
            }

            return back()->with('success', 'Scholarship status updated successfully');
        } catch (\Exception $e) {
            \Log::error('AdminUserController@grantScholarship: ' . $e->getMessage());
            if ($request->expectsJson()) {
                return $this->serverError($e->getMessage());
            }
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Adjust token for student
     * POST /admin/users/{id}/token
     */
    public function adjustToken(Request $request, $id)
    {
        try {
            $user = User::where('role', 'murid')->findOrFail($id);

            $validated = $request->validate([
                'amount' => 'required|integer|min:1',
                'action' => 'required|in:add,subtract',
                'reason' => 'nullable|string|max:255',
            ]);

            $token = Token::firstOrCreate(
                ['user_id' => $user->id],
                ['jumlah' => 0, 'last_update' => now()]
            );

            if ($validated['action'] === 'add') {
                $token->increment('jumlah', $validated['amount']);
            } else {
                if ($token->jumlah < $validated['amount']) {
                    throw new \Exception('Insufficient token balance');
                }
                $token->decrement('jumlah', $validated['amount']);
            }

            $token->update(['last_update' => now()]);

            if ($request->expectsJson()) {
                return $this->success(
                    [
                        'user_id' => $user->id,
                        'action' => $validated['action'],
                        'amount' => $validated['amount'],
                        'new_balance' => $token->jumlah,
                    ],
                    'Token adjusted successfully'
                );
            }

            return back()->with('success', 'Token adjusted successfully');
        } catch (\Exception $e) {
            \Log::error('AdminUserController@adjustToken: ' . $e->getMessage());
            if ($request->expectsJson()) {
                return $this->serverError($e->getMessage());
            }
            return back()->with('error', $e->getMessage());
        }
    }
}
