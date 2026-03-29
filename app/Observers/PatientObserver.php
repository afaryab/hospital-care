<?php

namespace App\Observers;

use App\Models\Patient;

class PatientObserver
{
    /**
     * Handle the Patient "creating" event.
     * This runs before the patient is saved to the database
     */
    public function creating(Patient $patient): void
    {
        \Sentry\traceMetrics()->count('patients-created', 1, ['id' => $patient->id]);
        // Only generate PS number if it's not already set
        if (empty($patient->ps_number)) {
            $patient->ps_number = $patient->generateCounterNumber();
        }

        // Compute blind index for CNIC deduplication (stored unhashed for searchability)
        if (! empty($patient->cnic)) {
            $patient->cnic_hash = hash('sha256', strtoupper(trim($patient->cnic)));
        }

        if (! empty($patient->contact)) {
            $normalizedContact = preg_replace('/\D+/', '', $patient->contact);
            $patient->contact_hash = $normalizedContact ? hash('sha256', $normalizedContact) : null;
        }
    }

    /**
     * Handle the Patient "created" event.
     */
    public function created(Patient $patient): void
    {
        // Any post-creation logic can go here
    }

    /**
     * Handle the Patient "updating" event.
     */
    public function updating(Patient $patient): void
    {
        // Prevent PS number from being manually changed after creation
        if ($patient->isDirty('ps_number') && ! empty($patient->getOriginal('ps_number'))) {
            // If PS number was already set and someone is trying to change it, revert it
            $patient->ps_number = $patient->getOriginal('ps_number');
        }

        // Recompute CNIC hash if CNIC is changing
        if ($patient->isDirty('cnic') && ! empty($patient->cnic)) {
            $patient->cnic_hash = hash('sha256', strtoupper(trim($patient->cnic)));
        }

        if ($patient->isDirty('contact')) {
            $normalizedContact = preg_replace('/\D+/', '', (string) $patient->contact);
            $patient->contact_hash = $normalizedContact ? hash('sha256', $normalizedContact) : null;
        }
    }

    /**
     * Handle the Patient "updated" event.
     */
    public function updated(Patient $patient): void
    {
        //
    }

    /**
     * Handle the Patient "deleted" event.
     */
    public function deleted(Patient $patient): void
    {
        //
    }

    /**
     * Handle the Patient "restored" event.
     */
    public function restored(Patient $patient): void
    {
        //
    }

    /**
     * Handle the Patient "force deleted" event.
     */
    public function forceDeleted(Patient $patient): void
    {
        //
    }
}
