<?php

namespace App\Policies;

use App\Models\Hazard;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class HazardPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
   public function view(User $user, Hazard $hazard): bool
    {
        // Admin selalu bisa
        if ($user->roles()->where('role_id', 1)->exists()) {
            return true;
        }

        // Penanggung jawab bisa
        if ($hazard->penanggungJawab && $user->id === $hazard->penanggungJawab->id) {
            return true;
        }

        // Pelapor bisa
        if ($hazard->pelapor && $user->id === $hazard->pelapor->id) {
            return true;
        }

        // Assigned ERM atau moderator sesuai event_type
        if ($hazard->assignedErms()->where('users.id', $user->id)->exists()) {
            return true;
        }

        if ($user->moderatorAssignments()->where('event_type_id', $hazard->event_type_id)->exists()) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Hazard $hazard): bool
    {
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Hazard $hazard): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Hazard $hazard): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Hazard $hazard): bool
    {
        return false;
    }
}
