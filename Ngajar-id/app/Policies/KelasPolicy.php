<?php

namespace App\Policies;

use App\Models\Kelas;
use App\Models\User;

class KelasPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Kelas $kelas): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->isPengajar();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Kelas $kelas): bool
    {
        // Hanya pembuat kelas yang boleh diedit
        return $user->user_id === $kelas->pengajar_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Kelas $kelas): bool
    {
        return $user->user_id === $kelas->pengajar_id;
    }
}
