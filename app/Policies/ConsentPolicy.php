<?php

namespace App\Policies;

use App\Models\Consent;
use App\Models\User;

class ConsentPolicy
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
        return $this->hasClinicalOrFrontDeskAccess($user);
    }

    public function view(User $user, Consent $consent): bool
    {
        return $this->hasClinicalOrFrontDeskAccess($user);
    }

    /**
     * Broader than PatientPolicy's doctor-scoping: a doctor needs to check
     * or record consent for a patient before it's established whether
     * they're "the" treating doctor for that encounter, so consent access
     * isn't scoped to an existing doctor_id assignment the way patient
     * record access is.
     */
    protected function hasClinicalOrFrontDeskAccess(User $user): bool
    {
        return $user->isReceptionist()
            || $user->isPatientManager()
            || $user->isAccountant()
            || $user->nursingStaffProfiles()->exists()
            || $user->isAnyDoctor();
    }

    public function create(User $user): bool
    {
        return $this->hasClinicalOrFrontDeskAccess($user);
    }

    /**
     * Consent records are append-only, matching the project's Immutable
     * Records rule: a mistake is corrected by recording a new consent
     * entry (with a note), not by silently editing history. Only the
     * admin before() bypass can update/delete.
     */
    public function update(User $user, Consent $consent): bool
    {
        return false;
    }

    public function delete(User $user, Consent $consent): bool
    {
        return false;
    }
}
