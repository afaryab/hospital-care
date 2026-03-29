<?php

namespace App\Policies;

use App\Models\Closing;
use App\Models\User;

class ClosingPolicy
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
        return $user->can('closing.view') || $user->isAccountant() || $user->isReceptionist();
    }

    public function view(User $user, Closing $closing): bool
    {
        return $user->can('closing.view') || $user->isAccountant() || $user->isReceptionist();
    }

    public function create(User $user): bool
    {
        return $user->can('closing.create') || $user->isReceptionist();
    }

    public function update(User $user, Closing $closing): bool
    {
        return $user->can('closing.edit')
            || ($user->isReceptionist() && $closing->receptionist_id === $user->id);
    }

    public function delete(User $user, Closing $closing): bool
    {
        return $user->can('closing.delete');
    }
}
