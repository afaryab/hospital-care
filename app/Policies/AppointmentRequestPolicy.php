<?php

namespace App\Policies;

use App\Models\AppointmentRequest;
use App\Models\User;

class AppointmentRequestPolicy
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
        return $user->isReceptionist();
    }

    public function update(User $user, AppointmentRequest $appointmentRequest): bool
    {
        return $user->isReceptionist();
    }
}
