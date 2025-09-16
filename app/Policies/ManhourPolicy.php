<?php

namespace App\Policies;

use App\Models\User;

class ManhourPolicy
{
    /**
     * Create a new policy instance.
     */
    public function create(User $user): bool
    {
        // cek admin
        $isAdmin = $user->roles()->where('role_id', 1)->exists();

        // cek contractor_user
        $isContractorUser = $user->contractors()->exists();

        return $isAdmin || $isContractorUser;
    }
}
