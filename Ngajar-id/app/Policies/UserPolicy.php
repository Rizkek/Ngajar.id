<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    /**
     * Determine if user can view own profile
     */
    public function view(User $user, User $target): bool
    {
        return $user->user_id === $target->user_id || $user->role === 'admin';
    }

    /**
     * Determine if user can update own profile
     */
    public function update(User $user, User $target): bool
    {
        return $user->user_id === $target->user_id || $user->role === 'admin';
    }

    /**
     * Admin can manage any user
     */
    public function delete(User $user, User $target): bool
    {
        return $user->role === 'admin';
    }
}
