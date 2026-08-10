<?php

use App\Enum\DeathCertificateManner;
use App\Filament\Admin\Resources\DeathCertificates\Pages\CreateDeathCertificate;
use App\Filament\Admin\Resources\DeathCertificates\Pages\EditDeathCertificate;
use App\Filament\Admin\Resources\DeathCertificates\Pages\ListDeathCertificates;
use App\Models\Administrator;
use App\Models\DeathCertificate;
use App\Models\ServiceOrder;
use App\Models\User;

use function Pest\Laravel\assertDatabaseHas;

beforeEach(function () {
    $this->user = User::factory()->create();
    Administrator::create(['user_id' => $this->user->id, 'authority' => 'administrator']);
    $this->actingAs($this->user);
});

test('death certificate list page renders and shows records', function () {
    $certificates = DeathCertificate::factory()->count(2)->create();

    Livewire\Livewire::test(ListDeathCertificates::class)
        ->assertSuccessful()
        ->assertCanSeeTableRecords($certificates);
});

test('admin can create a death certificate for a service order', function () {
    $serviceOrder = ServiceOrder::factory()->create();

    Livewire\Livewire::test(CreateDeathCertificate::class)
        ->fillForm([
            'service_order_id' => $serviceOrder->id,
            'place_of_death' => 'Emergency Ward',
            'manner_of_death' => DeathCertificateManner::Natural->value,
            'informant_name' => 'Muhammad Ali',
        ])
        ->call('create')
        ->assertNotified()
        ->assertRedirect();

    assertDatabaseHas(DeathCertificate::class, [
        'service_order_id' => $serviceOrder->id,
        'place_of_death' => 'Emergency Ward',
        'manner_of_death' => DeathCertificateManner::Natural->value,
    ]);
});

test('admin can update a death certificate', function () {
    $certificate = DeathCertificate::factory()->create();

    Livewire\Livewire::test(EditDeathCertificate::class, ['record' => $certificate->getRouteKey()])
        ->fillForm(['antecedent_cause' => 'Chronic renal failure'])
        ->call('save')
        ->assertNotified();

    assertDatabaseHas(DeathCertificate::class, [
        'id' => $certificate->id,
        'antecedent_cause' => 'Chronic renal failure',
    ]);
});
