<?php

namespace App\Policies;

use App\Models\Closing;
use App\Models\User;

class ClosingPolicy
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
     * Admins, accountants, and receptionists can view any closing.
     */
    public function view(User $user, Closing $closing): bool
    {
        return $user->isAccountant() || $user->isReceptionist();
    }

    /**
     * Receptionists can create closings.
     */
    public function create(User $user): bool
    {
        return $user->isReceptionist();
    }

    /**
     * Only the receptionist who owns the closing can update it.
     */
    public function update(User $user, Closing $closing): bool
    {
        return $user->isReceptionist() && $closing->receptionist_id === $user->id;
    }

    /**
     * Closing deletion is restricted to admins (handled by before()).
     */
    public function delete(User $user, Closing $closing): bool
    {
        return false;
    }
}
