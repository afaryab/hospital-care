<?php

namespace App\Policies;

use App\Models\ServiceOrder;
use App\Models\User;

class ServiceOrderPolicy
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
     * See PatientPolicy for the same reasoning: broad roles keep full
     * access, doctors are scoped to orders they're the assigned doctor_id
     * for. Nursing staff kept broad for the same reason (no scoping column
     * exists yet).
     */
    public function view(User $user, ServiceOrder $serviceOrder): bool
    {
        if ($this->hasBroadAccess($user)) {
            return true;
        }

        if ($user->isAnyDoctor()) {
            return (int) $serviceOrder->doctor_id === $user->id;
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
        return $user->can('service_order.create') || $user->isReceptionist();
    }

    /**
     * Was previously "any doctor can update any service order" — the
     * clinical-write IDOR. Doctors are now restricted to orders they're
     * actually assigned to.
     */
    public function update(User $user, ServiceOrder $serviceOrder): bool
    {
        if ($user->can('service_order.edit') || $this->hasBroadAccess($user)) {
            return true;
        }

        if ($user->isAnyDoctor()) {
            return (int) $serviceOrder->doctor_id === $user->id;
        }

        return false;
    }

    public function delete(User $user, ServiceOrder $serviceOrder): bool
    {
        return $user->can('service_order.delete');
    }
}
