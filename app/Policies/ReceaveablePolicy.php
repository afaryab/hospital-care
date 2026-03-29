<?php

namespace App\Policies;

use App\Models\Receaveable;
use App\Models\User;

class ReceaveablePolicy
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
        return $user->can('receaveable.view') || $user->isAccountant() || $user->isReceptionist();
    }

    public function view(User $user, Receaveable $receaveable): bool
    {
        return $user->can('receaveable.view') || $user->isAccountant() || $user->isReceptionist();
    }

    public function create(User $user): bool
    {
        return $user->can('receaveable.create') || $user->isAccountant() || $user->isReceptionist();
    }

    public function update(User $user, Receaveable $receaveable): bool
    {
        return $user->can('receaveable.edit') || $user->isAccountant();
    }

    public function delete(User $user, Receaveable $receaveable): bool
    {
        return $user->can('receaveable.delete');
    }
}
