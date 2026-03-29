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
        return $user->can('service_order.view') || $user->hasAnyProfile();
    }

    public function view(User $user, ServiceOrder $serviceOrder): bool
    {
        return $user->can('service_order.view') || $user->hasAnyProfile();
    }

    public function create(User $user): bool
    {
        return $user->can('service_order.create') || $user->isReceptionist();
    }

    public function update(User $user, ServiceOrder $serviceOrder): bool
    {
        return $user->can('service_order.edit') || $user->isAnyDoctor() || $user->isReceptionist();
    }

    public function delete(User $user, ServiceOrder $serviceOrder): bool
    {
        return $user->can('service_order.delete');
    }
}
