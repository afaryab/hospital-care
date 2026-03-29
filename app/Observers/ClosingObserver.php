<?php

namespace App\Observers;

use App\Models\Closing;

class ClosingObserver
{
    /**
     * Handle the Closing "creating" event.
     * This runs before the closing is saved to the database
     */
    public function creating(Closing $closing): void
    {
        // Only generate PS number if it's not already set
        if (empty($closing->ct_number)) {
            $closing->ct_number = $closing->generateCounterNumber();
        }
    }

    /**
     * Handle the Closing "created" event.
     */
    public function created(Closing $closing): void
    {
        // if (empty($closing->ct_number)) {
        //     $closing->ct_number = $closing->generateCounterNumber();
        // }
    }

    /**
     * Handle the Closing "updating" event.
     * Runs before the SQL update — allows us to revert the ct_number change.
     */
    public function updating(Closing $closing): void
    {
        // Prevent CT number from being manually changed after creation
        if ($closing->isDirty('ct_number') && ! empty($closing->getOriginal('ct_number'))) {
            $closing->ct_number = $closing->getOriginal('ct_number');
        }
    }

    /**
     * Handle the Closing "updated" event.
     */
    public function updated(Closing $closing): void
    {
        //
    }

    /**
     * Handle the Closing "deleted" event.
     */
    public function deleted(Closing $closing): void
    {
        //
    }

    /**
     * Handle the Closing "restored" event.
     */
    public function restored(Closing $closing): void
    {
        //
    }

    /**
     * Handle the Closing "force deleted" event.
     */
    public function forceDeleted(Closing $closing): void
    {
        //
    }
}
