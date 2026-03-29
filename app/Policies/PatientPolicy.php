<?php

namespace App\Policies;

use App\Models\Patient;
use App\Models\User;

class PatientPolicy
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
        return $user->can('patient.view') || $user->hasAnyProfile();
    }

    public function view(User $user, Patient $patient): bool
    {
        return $user->can('patient.view') || $user->hasAnyProfile();
    }

    public function create(User $user): bool
    {
        return $user->can('patient.create') || $user->isReceptionist() || $user->isPatientManager();
    }

    public function update(User $user, Patient $patient): bool
    {
        return $user->can('patient.edit') || $user->isReceptionist() || $user->isPatientManager();
    }

    public function delete(User $user, Patient $patient): bool
    {
        return $user->can('patient.delete');
    }
}
