<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponse;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use App\Services\Admin\UserManagementService;

class AdminUserController extends Controller
{
    use ApiResponse;

    protected $userService;

    public function __construct(UserManagementService $userService)
    {
        $this->userService = $userService;
    }

    /** GET /admin/users */
    public function index(Request $request)
    {
        try {
            $data = $this->userService->listUsers(
                $request->only(['role', 'status', 'search', 'sort', 'direction']),
                $request->get('per_page', 15)
            );

            return $this->successWithPagination(UserResource::collection($data), 'Users retrieved successfully');
            
        } catch (\Exception $e) {
            return $this->serverError($e->getMessage());
            
        }
    }

    /** GET /admin/users/{id} */
    public function show(Request $request, $id)
    {
        try {
            $user = $this->userService->getUser($id);
            return $this->success(new UserResource($user), 'User retrieved successfully');
            
        } catch (\Exception $e) {
            return $this->serverError($e->getMessage());
            
        }
    }

    /** PUT /admin/users/{id} */
    public function update(Request $request, $id)
    {
        try {
            $data = $request->validate([
                'name' => 'nullable|string|max:255',
                'email' => 'nullable|email|unique:users,email,' . $id . ',user_id',
                'role' => 'nullable|in:murid,pengajar,admin',
            ]);

            $user = $this->userService->updateUser($id, array_filter($data));

            return $this->success(new UserResource($user), 'User updated successfully');
            
        } catch (\Exception $e) {
            return $this->serverError($e->getMessage());
            
        }
    }

    /** POST /admin/users/{id}/status */
    public function updateStatus(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'status' => 'required|in:aktif,nonaktif,suspended',
                'reason' => 'nullable|string|max:255',
            ]);

            $user = $this->userService->updateStatus($id, $validated['status'], $validated['reason'] ?? null);

            return $this->success(new UserResource($user), 'Status updated successfully');
            
        } catch (\Exception $e) {
            return $this->serverError($e->getMessage());
            
        }
    }

    /** DELETE /admin/users/{id} */
    public function destroy(Request $request, $id)
    {
        try {
            $this->userService->deleteUser($id);
            return $this->success(null, 'User deleted successfully');
            
        } catch (\Exception $e) {
            return $this->serverError($e->getMessage());
            
        }
    }

    /** GET /admin/users/teachers */
    public function teacherIndex(Request $request)
    {
        try {
            $data = $this->userService->listUsers(
                $request->only(['status', 'search']),
                $request->get('per_page', 15),
                'pengajar'
            );

            return $this->successWithPagination(UserResource::collection($data), 'Teachers retrieved successfully');
            
        } catch (\Exception $e) {
            return $this->serverError($e->getMessage());
            
        }
    }

    /** POST /admin/users/{id}/verify-teacher */
    public function verifyTeacher(Request $request, $id)
    {
        try {
            $user = $this->userService->verifyTeacher($id);
            return $this->success(new UserResource($user), 'Teacher verified successfully');
            
        } catch (\Exception $e) {
            return $this->serverError($e->getMessage());
            
        }
    }

    /** POST /admin/users/{id}/revoke-teacher */
    public function revokeTeacher(Request $request, $id)
    {
        try {
            $user = $this->userService->revokeTeacher($id);
            return $this->success(new UserResource($user), 'Teacher revoked successfully');
            
        } catch (\Exception $e) {
            return $this->serverError($e->getMessage());
            
        }
    }

    /** GET /admin/users/students */
    public function studentIndex(Request $request)
    {
        try {
            $data = $this->userService->listUsers(
                $request->only(['status', 'search']),
                $request->get('per_page', 15),
                'murid'
            );

            return $this->successWithPagination(UserResource::collection($data), 'Students retrieved successfully');
            
        } catch (\Exception $e) {
            return $this->serverError($e->getMessage());
            
        }
    }

    /** POST /admin/users/{id}/scholarship */
    public function grantScholarship(Request $request, $id)
    {
        try {
            $validated = $request->validate(['is_beasiswa' => 'required|boolean']);
            $user = $this->userService->setScholarship($id, $validated['is_beasiswa']);
            return $this->success(new UserResource($user), 'Scholarship status updated successfully');
            
        } catch (\Exception $e) {
            return $this->serverError($e->getMessage());
            
        }
    }

    /** POST /admin/users/{id}/token */
    public function adjustToken(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'amount' => 'required|integer|min:1',
                'action' => 'required|in:add,subtract',
                'reason' => 'nullable|string|max:255',
            ]);

            $result = $this->userService->adjustToken($id, $validated['action'], $validated['amount'], $validated['reason'] ?? null);

            return $this->success($result, 'Token adjusted successfully');
            
        } catch (\Exception $e) {
            return $this->serverError($e->getMessage());
            
        }
    }
}
