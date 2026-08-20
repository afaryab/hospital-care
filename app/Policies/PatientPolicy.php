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
        return $this->hasBroadAccess($user);
    }

    /**
     * Receptionist/patient-manager/accountant keep broad access — that's a
     * legitimate front-desk and billing need. Doctors are scoped to
     * patients they've actually treated (via ServiceOrder.doctor_id),
     * closing the gap where any staff profile could view any patient.
     *
     * Nursing staff are left on broad access for now: there's no
     * ward/department assignment column in the schema to scope them by,
     * and locking them out with no replacement mechanism would break their
     * workflow outright. Tracked as follow-up work, not silently ignored.
     */
    public function view(User $user, Patient $patient): bool
    {
        if ($this->hasBroadAccess($user)) {
            return true;
        }

        if ($user->isAnyDoctor()) {
            return $patient->treatments()->where('doctor_id', $user->id)->exists();
        }

        return false;
    }

    protected function hasBroadAccess(User $user): bool
    {
        return $user->isReceptionist()
            || $user->isPatientManager()
            || $user->isAccountant()
            || $user->nursingStaffProfiles()->exists();
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
