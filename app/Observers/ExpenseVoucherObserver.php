<?php

namespace App\Observers;

use App\Models\ExpenseVoucher;

class ExpenseVoucherObserver
{
    /**
     * Handle the ExpenseVoucher "creating" event.
     * This runs before the expenseVoucher is saved to the database
     */
    public function creating(ExpenseVoucher $expenseVoucher): void
    {
        // Only generate PS number if it's not already set
        if (empty($expenseVoucher->vc_number)) {
            $expenseVoucher->vc_number = $expenseVoucher->generateExpenseVoucherNumber();
        }
    }

    /**
     * Handle the ExpenseVoucher "created" event.
     */
    public function created(ExpenseVoucher $expenseVoucher): void
    {
        // if (empty($expenseVoucher->vc_number)) {
        //     $expenseVoucher->vc_number = $expenseVoucher->generateExpenseVoucherNumber();
        // }
    }

    /**
     * Handle the ExpenseVoucher "updating" event.
     * Runs before the SQL update — allows us to revert the vc_number change.
     */
    public function updating(ExpenseVoucher $expenseVoucher): void
    {
        // Prevent VC number from being manually changed after creation
        if ($expenseVoucher->isDirty('vc_number') && ! empty($expenseVoucher->getOriginal('vc_number'))) {
            $expenseVoucher->vc_number = $expenseVoucher->getOriginal('vc_number');
        }
    }

    /**
     * Handle the ExpenseVoucher "updated" event.
     */
    public function updated(ExpenseVoucher $expenseVoucher): void
    {
        // If amount is changed, store orinal amount in edited_amount field
        if ($expenseVoucher->isDirty('amount')) {
            $expenseVoucher->edited_amount = $expenseVoucher->getOriginal('amount');
            $expenseVoucher->saveQuietly(); // Save without triggering observer again
        }

    }

    /**
     * Handle the ExpenseVoucher "deleted" event.
     */
    public function deleted(ExpenseVoucher $expenseVoucher): void
    {
        //
    }

    /**
     * Handle the ExpenseVoucher "restored" event.
     */
    public function restored(ExpenseVoucher $expenseVoucher): void
    {
        //
    }

    /**
     * Handle the ExpenseVoucher "force deleted" event.
     */
    public function forceDeleted(ExpenseVoucher $expenseVoucher): void
    {
        //
    }
}
