<?php

namespace App\Observers;

use App\Models\Transaction;

class TransactionObserver
{
    /**
     * Handle the Transaction "creating" event.
     * This runs before the transaction is saved to the database
     *
     * @param  \App\Models\Transaction  $transaction
     * @return void
     */
    public function creating(Transaction $transaction): void
    {
        // Only generate PS number if it's not already set
        if (empty($transaction->tr_number)) {
            $transaction->tr_number = $transaction->generateTransactionNumber();
        }
    }

    /**
     * Handle the Transaction "created" event.
     */
    public function created(Transaction $transaction): void
    {
        // if (empty($transaction->tr_number)) {
        //     $transaction->tr_number = $transaction->generateTransactionNumber();
        // }

        $transaction->updateCounter();
    }

    /**
     * Handle the Transaction "updated" event.
     */
    public function updated(Transaction $transaction): void
    {
        // Prevent TR number from being manually changed after creation
        if ($transaction->isDirty('tr_number') && !empty($transaction->getOriginal('tr_number'))) {
            // If TR number was already set and someone is trying to change it, revert it
            $transaction->tr_number = $transaction->getOriginal('tr_number');
        }

        // If amount is changed, store orinal amount in edited_amount field
        if ($transaction->isDirty('amount')) {
            $transaction->edited_amount = $transaction->getOriginal('amount');
            $transaction->saveQuietly(); // Save without triggering observer again
            $transaction->updateCounter();
        }


    }

    /**
     * Handle the Transaction "deleted" event.
     */
    public function deleted(Transaction $transaction): void
    {
        //
    }

    /**
     * Handle the Transaction "restored" event.
     */
    public function restored(Transaction $transaction): void
    {
        //
    }

    /**
     * Handle the Transaction "force deleted" event.
     */
    public function forceDeleted(Transaction $transaction): void
    {
        //
    }
}
