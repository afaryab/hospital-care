<?php

namespace App\Policies;

use App\Models\Patient;
use App\Models\User;

class PatientPolicy
{
    /**
     * Admins bypass all policy checks.
     */
    public function before(User $user): ?bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return null;
    }

    /**
     * All authenticated staff can view patient records.
     */
    public function view(User $user, Patient $patient): bool
    {
        return $user->hasAnyProfile();
    }

    /**
     * Receptionists and patient managers can register new patients.
     */
    public function create(User $user): bool
    {
        return $user->isReceptionist() || $user->isPatientManager();
    }

    /**
     * Receptionists and patient managers can update patient records.
     */
    public function update(User $user, Patient $patient): bool
    {
        return $user->isReceptionist() || $user->isPatientManager();
    }

    /**
     * Patient deletion is restricted to admins (handled by before()).
     */
    public function delete(User $user, Patient $patient): bool
    {
        return false;
    }
}
