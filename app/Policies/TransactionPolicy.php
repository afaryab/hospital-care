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
        return $this->hasBroadAccess($user);
    }

    /**
     * See PatientPolicy for the same reasoning. Doctors are scoped to
     * transactions containing at least one element they're the assigned
     * doctor for (TransactionElement.doctor_id).
     */
    public function view(User $user, Transaction $transaction): bool
    {
        if ($this->hasBroadAccess($user)) {
            return true;
        }

        if ($user->isAnyDoctor()) {
            return $transaction->elements()->where('doctor_id', $user->id)->exists();
        }

        return false;
    }

    protected function hasBroadAccess(User $user): bool
    {
        return $user->isReceptionist() || $user->isAccountant();
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
