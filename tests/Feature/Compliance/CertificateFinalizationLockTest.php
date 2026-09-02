<?php

use App\Models\DeathCertificate;
use App\Models\ReferralCertificate;
use Illuminate\Validation\ValidationException;

test('a locked death certificate cannot be modified', function () {
    $certificate = DeathCertificate::factory()->locked()->create();

    expect(fn () => $certificate->update(['antecedent_cause' => 'Changed']))
        ->toThrow(ValidationException::class);
});

test('an unlocked death certificate can be modified freely', function () {
    $certificate = DeathCertificate::factory()->create();

    $certificate->update(['antecedent_cause' => 'Renal failure']);

    expect($certificate->fresh()->antecedent_cause)->toBe('Renal failure');
});

test('a locked referral certificate cannot be modified', function () {
    $certificate = ReferralCertificate::factory()->locked()->create();

    expect(fn () => $certificate->update(['receiving_facility_name' => 'Changed Hospital']))
        ->toThrow(ValidationException::class);
});

test('an unlocked referral certificate can be modified freely', function () {
    $certificate = ReferralCertificate::factory()->create();

    $certificate->update(['receiving_facility_name' => 'City General Hospital']);

    expect($certificate->fresh()->receiving_facility_name)->toBe('City General Hospital');
});
