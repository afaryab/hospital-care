<?php

namespace App\Policies;

use App\Models\ExpenseVoucher;
use App\Models\User;

class ExpenseVoucherPolicy
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
        return $user->can('expense_voucher.view') || $user->isAccountant() || $user->isReceptionist();
    }

    public function view(User $user, ExpenseVoucher $expenseVoucher): bool
    {
        return $user->can('expense_voucher.view') || $user->isAccountant() || $user->isReceptionist();
    }

    public function create(User $user): bool
    {
        return $user->can('expense_voucher.create') || $user->isAccountant();
    }

    public function update(User $user, ExpenseVoucher $expenseVoucher): bool
    {
        return $user->can('expense_voucher.edit') || $user->isAccountant();
    }

    public function delete(User $user, ExpenseVoucher $expenseVoucher): bool
    {
        return $user->can('expense_voucher.delete');
    }
}
