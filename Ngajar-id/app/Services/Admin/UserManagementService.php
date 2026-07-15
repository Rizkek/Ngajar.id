<?php

namespace App\Services\Admin;

use App\Models\User;
use App\Models\Token;
use App\Models\TokenLog;
use Exception;
use Illuminate\Pagination\LengthAwarePaginator;

class UserManagementService
{
    /**
     * List all users with optional filters.
     */
    public function listUsers(array $filters = [], int $perPage = 15, ?string $role = null): LengthAwarePaginator
    {
        $query = User::query()
            ->withCount('kelasIkuti', 'kelasAjar')
            ->with('tokens');

        if ($role) {
            $query->where('role', $role);
        }

        if (!empty($filters['role']) && $filters['role'] !== 'all') {
            $query->where('role', $filters['role']);
        }

        if (!empty($filters['status']) && $filters['status'] !== 'all') {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $sortBy = $filters['sort'] ?? 'created_at';
        $sortDir = $filters['direction'] ?? 'desc';
        $query->orderBy($sortBy, $sortDir);

        return $query->paginate($perPage);
    }

    /**
     * Get a single user with details.
     */
    public function getUser(int $userId): User
    {
        return User::with([
            'kelasIkuti',
            'kelasAjar',
            'token',
            'tokens',
        ])->findOrFail($userId);
    }

    /**
     * Update a user profile.
     */
    public function updateUser(int $userId, array $data): User
    {
        $user = User::findOrFail($userId);
        $user->update($data);
        return $user->fresh();
    }

    /**
     * Update user status (aktif, nonaktif, suspended).
     */
    public function updateStatus(int $userId, string $status, ?string $reason = null): User
    {
        $user = User::findOrFail($userId);
        $user->update(['status' => $status]);
        return $user->fresh();
    }

    /**
     * Permanently delete a user.
     */
    public function deleteUser(int $userId): void
    {
        $user = User::findOrFail($userId);
        $user->delete();
    }

    /**
     * Verify a teacher (set status to 'aktif').
     */
    public function verifyTeacher(int $userId): User
    {
        $user = User::where('role', 'pengajar')->findOrFail($userId);
        $user->update(['status' => 'aktif']);
        return $user->fresh();
    }

    /**
     * Revoke a teacher (set status to 'nonaktif').
     */
    public function revokeTeacher(int $userId): User
    {
        $user = User::where('role', 'pengajar')->findOrFail($userId);
        $user->update(['status' => 'nonaktif']);
        return $user->fresh();
    }

    /**
     * Grant or revoke scholarship for a student.
     */
    public function setScholarship(int $userId, bool $isBeasiswa): User
    {
        $user = User::where('role', 'murid')->findOrFail($userId);
        $user->update(['is_beasiswa' => $isBeasiswa]);
        return $user->fresh();
    }

    /**
     * Adjust token balance for a student.
     * Action: 'add' | 'subtract'
     */
    public function adjustToken(int $userId, string $action, int $amount, ?string $reason = null): array
    {
        return \Illuminate\Support\Facades\DB::transaction(function () use ($userId, $action, $amount, $reason) {
            $user = User::where('role', 'murid')->findOrFail($userId);

            $token = Token::firstOrCreate(
                ['user_id' => $user->user_id],
                ['jumlah' => 0, 'last_update' => now()]
            );

            if ($action === 'add') {
                $token->increment('jumlah', $amount);
            } else {
                if ($token->jumlah < $amount) {
                    throw new Exception('Insufficient token balance');
                }
                $token->decrement('jumlah', $amount);
            }

            $token->update(['last_update' => now()]);

            // Log the adjustment
            TokenLog::create([
                'user_id' => $user->user_id,
                'jumlah' => $amount,
                'aksi' => $action === 'add' ? 'tambah' : 'kurang',
                'tipe' => 'penyesuaian_admin',
                'keterangan' => $reason ?? "Token disesuaikan oleh Admin ({$action})",
                'tanggal' => now(),
            ]);

            return [
                'user_id' => $user->user_id,
                'action' => $action,
                'amount' => $amount,
                'new_balance' => $token->jumlah,
            ];
        });
    }
}
