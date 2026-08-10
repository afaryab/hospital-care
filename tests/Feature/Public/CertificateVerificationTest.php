<?php

use App\Models\BirthCertificate;
use App\Models\DeathCertificate;
use App\Models\Patient;
use App\Models\ServiceOrder;

use function Pest\Laravel\get;

test('a valid death certificate verification token shows the public page', function () {
    $patient = Patient::factory()->create(['name' => 'Verify Test Patient']);
    $serviceOrder = ServiceOrder::factory()->create(['patient_id' => $patient->id]);
    $certificate = DeathCertificate::factory()->create(['service_order_id' => $serviceOrder->id]);

    get(route('public-death-certificate', ['token' => $certificate->verification_token]))
        ->assertOk()
        ->assertSee('Verify Test Patient')
        ->assertSee($certificate->certificate_number);
});

test('an unknown death certificate verification token 404s', function () {
    get(route('public-death-certificate', ['token' => 'not-a-real-token']))
        ->assertNotFound();
});

test('the death certificate verification page does not require authentication', function () {
    $certificate = DeathCertificate::factory()->create();

    // No actingAs() call — guest request.
    get(route('public-death-certificate', ['token' => $certificate->verification_token]))
        ->assertOk();
});

test('a locked birth certificate verification token shows the public page', function () {
    $patient = Patient::factory()->create(['name' => 'Baby Verify Test']);
    $serviceOrder = ServiceOrder::factory()->create(['patient_id' => $patient->id]);
    $certificate = BirthCertificate::factory()->locked()->create([
        'service_order_id' => $serviceOrder->id,
        'child_name' => 'Baby Verify',
    ]);

    get(route('public-birth-certificate', ['token' => $certificate->verification_token]))
        ->assertOk()
        ->assertSee('Baby Verify')
        ->assertSee($certificate->birth_certificate_number);
});

test('an unlocked birth certificate verification token 404s even with a valid token', function () {
    $certificate = BirthCertificate::factory()->create(['is_locked' => false]);

    get(route('public-birth-certificate', ['token' => $certificate->verification_token]))
        ->assertNotFound();
});

test('an unknown birth certificate verification token 404s', function () {
    get(route('public-birth-certificate', ['token' => 'not-a-real-token']))
        ->assertNotFound();
});
