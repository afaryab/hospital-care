<?php

namespace App\Observers;

use App\Models\Patient;

class PatientObserver
{
    /**
     * Handle the Patient "creating" event.
     * This runs before the patient is saved to the database
     *
     * @param  \App\Models\Patient  $patient
     * @return void
     */
    public function creating(Patient $patient): void
    {
        \Sentry\traceMetrics()->count('patients-created', 1, ['id' => $patient->id]);
        // Only generate PS number if it's not already set
        if (empty($patient->ps_number)) {
            $patient->ps_number = $patient->generateCounterNumber();
        }
    }

    /**
     * Handle the Patient "created" event.
     *
     * @param  \App\Models\Patient  $patient
     * @return void
     */
    public function created(Patient $patient): void
    {
        // Any post-creation logic can go here
    }

    /**
     * Handle the Patient "updating" event.
     *
     * @param  \App\Models\Patient  $patient
     * @return void
     */
    public function updating(Patient $patient): void
    {
        // Prevent PS number from being manually changed after creation
        if ($patient->isDirty('ps_number') && !empty($patient->getOriginal('ps_number'))) {
            // If PS number was already set and someone is trying to change it, revert it
            $patient->ps_number = $patient->getOriginal('ps_number');
        }
    }

    /**
     * Handle the Patient "updated" event.
     *
     * @param  \App\Models\Patient  $patient
     * @return void
     */
    public function updated(Patient $patient): void
    {
        //
    }

    /**
     * Handle the Patient "deleted" event.
     *
     * @param  \App\Models\Patient  $patient
     * @return void
     */
    public function deleted(Patient $patient): void
    {
        //
    }

    /**
     * Handle the Patient "restored" event.
     *
     * @param  \App\Models\Patient  $patient
     * @return void
     */
    public function restored(Patient $patient): void
    {
        //
    }

    /**
     * Handle the Patient "force deleted" event.
     *
     * @param  \App\Models\Patient  $patient
     * @return void
     */
    public function forceDeleted(Patient $patient): void
    {
        //
    }
}