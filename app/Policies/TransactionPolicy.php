<?php

namespace App\Policies;

use App\Models\Transaction;
use App\Models\User;

class TransactionPolicy
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
        return $user->can('transaction.view') || $user->hasAnyProfile();
    }

    public function view(User $user, Transaction $transaction): bool
    {
        return $user->can('transaction.view') || $user->hasAnyProfile();
    }

    public function create(User $user): bool
    {
        return $user->can('transaction.create') || $user->isReceptionist();
    }

    public function update(User $user, Transaction $transaction): bool
    {
        return $user->can('transaction.edit')
            || ($user->isReceptionist() && $transaction->created_by === $user->id);
    }

    public function delete(User $user, Transaction $transaction): bool
    {
        return $user->can('transaction.delete');
    }
}
