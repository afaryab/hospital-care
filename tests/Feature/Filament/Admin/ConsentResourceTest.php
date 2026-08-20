<?php

use App\Enum\ConsentMethod;
use App\Enum\ConsentType;
use App\Filament\Admin\Resources\Consents\Pages\CreateConsent;
use App\Filament\Admin\Resources\Consents\Pages\ListConsents;
use App\Filament\Admin\Resources\Consents\Pages\ViewConsent;
use App\Models\Administrator;
use App\Models\Consent;
use App\Models\Patient;
use App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();
    Administrator::create(['user_id' => $this->user->id, 'authority' => 'administrator']);
    $this->actingAs($this->user);
});

test('consent list page renders and shows records', function () {
    $consents = Consent::factory()->count(2)->create();

    Livewire\Livewire::test(ListConsents::class)
        ->assertSuccessful()
        ->assertCanSeeTableRecords($consents);
});

test('consent create page renders', function () {
    Livewire\Livewire::test(CreateConsent::class)->assertSuccessful();
});

test('admin can create a consent and recorded_by is set automatically', function () {
    $patient = Patient::factory()->create();

    Livewire\Livewire::test(CreateConsent::class)
        ->fillForm([
            'patient_id' => $patient->id,
            'consent_type' => ConsentType::Treatment->value,
            'consent_method' => ConsentMethod::DigitalCheckbox->value,
            'consented_at' => now(),
        ])
        ->call('create')
        ->assertNotified()
        ->assertRedirect();

    $consent = Consent::where('patient_id', $patient->id)->first();

    expect($consent)->not->toBeNull()
        ->and($consent->consent_type)->toBe(ConsentType::Treatment)
        ->and($consent->consent_method)->toBe(ConsentMethod::DigitalCheckbox)
        ->and($consent->recorded_by)->toBe($this->user->id);
});

test('consent view page renders', function () {
    $consent = Consent::factory()->create();

    Livewire\Livewire::test(ViewConsent::class, ['record' => $consent->getRouteKey()])
        ->assertSuccessful();
});
