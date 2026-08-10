<?php

namespace App\Observers;

use App\Enum\TreatmentOutcome;
use App\Models\DeathCertificate;
use App\Models\ReferralCertificate;
use App\Models\TreatmentRecord;

class TreatmentRecordObserver
{
    /**
     * Handle the TreatmentRecord "saved" event (fires on both create and update).
     */
    public function saved(TreatmentRecord $treatmentRecord): void
    {
        if ($treatmentRecord->outcome === TreatmentOutcome::Expired) {
            $this->ensureDeathCertificate($treatmentRecord);
        }

        if ($treatmentRecord->outcome === TreatmentOutcome::Referred) {
            $this->ensureReferralCertificate($treatmentRecord);
        }
    }

    private function ensureDeathCertificate(TreatmentRecord $treatmentRecord): void
    {
        if (DeathCertificate::where('service_order_id', $treatmentRecord->service_order_id)->exists()) {
            return;
        }

        $patient = $treatmentRecord->serviceOrder?->patient;

        DeathCertificate::create([
            'service_order_id' => $treatmentRecord->service_order_id,
            'date_of_death' => $treatmentRecord->outcome_at?->toDateString(),
            'time_of_death' => $treatmentRecord->outcome_at?->toTimeString(),
            'place_of_death' => $treatmentRecord->department?->name,
            'informant_name' => $patient?->guardian,
            'informant_relation' => $patient?->relation,
            'created_by' => $treatmentRecord->recorded_by,
        ]);
    }

    private function ensureReferralCertificate(TreatmentRecord $treatmentRecord): void
    {
        if (ReferralCertificate::where('service_order_id', $treatmentRecord->service_order_id)->exists()) {
            return;
        }

        ReferralCertificate::create([
            'service_order_id' => $treatmentRecord->service_order_id,
            'receiving_facility_name' => $treatmentRecord->referral_to,
            'created_by' => $treatmentRecord->recorded_by,
        ]);
    }
}
