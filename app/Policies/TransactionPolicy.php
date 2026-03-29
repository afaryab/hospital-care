<?php

namespace App\Policies;

use App\Models\Transaction;
use App\Models\User;

class TransactionPolicy
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
     * All authenticated staff can view transactions.
     */
    public function view(User $user, Transaction $transaction): bool
    {
        return $user->hasAnyProfile();
    }

    /**
     * Receptionists can create transactions.
     */
    public function create(User $user): bool
    {
        return $user->isReceptionist();
    }

    /**
     * Only the user who created the transaction can edit it.
     */
    public function update(User $user, Transaction $transaction): bool
    {
        return $user->isReceptionist() && $transaction->created_by === $user->id;
    }

    /**
     * Deletion is restricted to admins (handled by before()).
     */
    public function delete(User $user, Transaction $transaction): bool
    {
        return false;
    }
}
