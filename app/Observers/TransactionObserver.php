<?php

namespace App\Observers;

use App\Models\ServiceOrder;
use App\Models\Transaction;

class TransactionObserver
{
    /**
     * Handle the Transaction "creating" event.
     * This runs before the transaction is saved to the database
     */
    public function creating(Transaction $transaction): void
    {
        \Sentry\traceMetrics()->count('transaction-created', 1, ['id' => $transaction->id]);
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
     * Handle the Transaction "updating" event.
     * Runs before the SQL update — allows us to revert the tr_number change.
     */
    public function updating(Transaction $transaction): void
    {
        // Prevent TR number from being manually changed after creation
        if ($transaction->isDirty('tr_number') && ! empty($transaction->getOriginal('tr_number'))) {
            $transaction->tr_number = $transaction->getOriginal('tr_number');
        }
    }

    /**
     * Handle the Transaction "updated" event.
     */
    public function updated(Transaction $transaction): void
    {
        // If amount is changed, store original amount in edited_amount field
        if ($transaction->isDirty('amount')) {
            $transaction->edited_amount = $transaction->getOriginal('amount');
            $transaction->saveQuietly(); // Save without triggering observer again
            $transaction->updateCounter();
        }

        // When a transaction is marked as refunded, update linked service orders
        if ($transaction->isDirty('is_refunded') && $transaction->is_refunded) {
            $serviceOrderIds = $transaction->elements()
                ->whereNotNull('service_order_id')
                ->pluck('service_order_id')
                ->unique()
                ->filter();

            if ($serviceOrderIds->isNotEmpty()) {
                ServiceOrder::whereIn('id', $serviceOrderIds)
                    ->update(['status' => 'refunded']);
            }
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
