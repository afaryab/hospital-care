<?php

namespace App\Policies;

use App\Models\Incident;
use App\Models\User;

class IncidentPolicy
{
    public function before(User $user): ?bool
    {
        if ($user->isAdmin() || $user->hasRole('administrator')) {
            return true;
        }

        return null;
    }

    public function viewAny(User $user): bool
    {
        return $this->hasStaffAccess($user);
    }

    public function view(User $user, Incident $incident): bool
    {
        return $this->hasStaffAccess($user);
    }

    public function create(User $user): bool
    {
        return $this->hasStaffAccess($user);
    }

    protected function hasStaffAccess(User $user): bool
    {
        return $user->isAccountant()
            || $user->isReceptionist()
            || $user->isPatientManager()
            || $user->nursingStaffProfiles()->exists()
            || $user->isAnyDoctor();
    }

    /**
     * Advancing the lifecycle (classify/assign/investigate/resolve/close)
     * all go through update(). This codebase has no separate "Auditor"
     * role (PHC guideline §11.1 calls for one) to delegate this to, so
     * lifecycle management is admin-only via the before() bypass — a
     * documented mapping decision, not an oversight.
     */
    public function update(User $user, Incident $incident): bool
    {
        return false;
    }

    public function delete(User $user, Incident $incident): bool
    {
        return false;
    }
}
