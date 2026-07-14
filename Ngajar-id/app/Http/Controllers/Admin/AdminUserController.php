<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use App\Services\Admin\UserManagementService;

class AdminUserController extends Controller
{

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

            return view('admin.users.index', compact('data'));
        } catch (\Exception $e) {

            return back()->with('error', $e->getMessage());
        }
    }

    /** GET /admin/users/{id} */
    public function show(Request $request, $id)
    {
        try {
            $user = $this->userService->getUser($id);

            return view('admin.users.show', compact('user'));
        } catch (\Exception $e) {

            return back()->with('error', $e->getMessage());
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

            return back()->with('success', 'User updated successfully');
        } catch (\Exception $e) {

            return back()->with('error', $e->getMessage());
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

            return back()->with('success', 'Status updated');
        } catch (\Exception $e) {

            return back()->with('error', $e->getMessage());
        }
    }

    /** DELETE /admin/users/{id} */
    public function destroy(Request $request, $id)
    {
        try {
            $this->userService->deleteUser($id);

            return redirect()->route('admin.users')->with('success', 'User deleted');
        } catch (\Exception $e) {

            return back()->with('error', $e->getMessage());
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

            return view('admin.pengajar.index', compact('data'));
        } catch (\Exception $e) {

            return back()->with('error', $e->getMessage());
        }
    }

    /** POST /admin/users/{id}/verify-teacher */
    public function verifyTeacher(Request $request, $id)
    {
        try {
            $user = $this->userService->verifyTeacher($id);

            return back()->with('success', 'Teacher verified successfully');
        } catch (\Exception $e) {

            return back()->with('error', $e->getMessage());
        }
    }

    /** POST /admin/users/{id}/revoke-teacher */
    public function revokeTeacher(Request $request, $id)
    {
        try {
            $user = $this->userService->revokeTeacher($id);

            return back()->with('success', 'Teacher revoked successfully');
        } catch (\Exception $e) {

            return back()->with('error', $e->getMessage());
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

            return view('admin.murid.index', compact('data'));
        } catch (\Exception $e) {

            return back()->with('error', $e->getMessage());
        }
    }

    /** POST /admin/users/{id}/scholarship */
    public function grantScholarship(Request $request, $id)
    {
        try {
            $validated = $request->validate(['is_beasiswa' => 'required|boolean']);
            $user = $this->userService->setScholarship($id, $validated['is_beasiswa']);

            return back()->with('success', 'Scholarship status updated successfully');
        } catch (\Exception $e) {

            return back()->with('error', $e->getMessage());
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

            return back()->with('success', 'Token adjusted successfully');
        } catch (\Exception $e) {

            return back()->with('error', $e->getMessage());
        }
    }
}
