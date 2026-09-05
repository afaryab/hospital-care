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
     * access. Doctors get full access too — any doctor/provider type may
     * need to view a service order outside their own assignment (covering
     * shifts, ward rounds, referrals), and there's no scoping column that
     * reliably captures "who's actually treating this patient right now"
     * (see ServiceOrder.doctor_id vs IndDoctorController's unscoped queue).
     */
    public function view(User $user, ServiceOrder $serviceOrder): bool
    {
        return $this->hasBroadAccess($user) || $user->isAnyDoctor();
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

    public function update(User $user, ServiceOrder $serviceOrder): bool
    {
        return $user->can('service_order.edit') || $this->hasBroadAccess($user) || $user->isAnyDoctor();
    }

    public function delete(User $user, ServiceOrder $serviceOrder): bool
    {
        return $user->can('service_order.delete');
    }
}
