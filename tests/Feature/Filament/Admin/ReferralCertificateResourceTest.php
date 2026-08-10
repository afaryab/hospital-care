<?php

use App\Filament\Admin\Resources\ReferralCertificates\Pages\CreateReferralCertificate;
use App\Filament\Admin\Resources\ReferralCertificates\Pages\EditReferralCertificate;
use App\Filament\Admin\Resources\ReferralCertificates\Pages\ListReferralCertificates;
use App\Models\Administrator;
use App\Models\ReferralCertificate;
use App\Models\ServiceOrder;
use App\Models\User;

use function Pest\Laravel\assertDatabaseHas;

beforeEach(function () {
    $this->user = User::factory()->create();
    Administrator::create(['user_id' => $this->user->id, 'authority' => 'administrator']);
    $this->actingAs($this->user);
});

test('referral certificate list page renders and shows records', function () {
    $certificates = ReferralCertificate::factory()->count(2)->create();

    Livewire\Livewire::test(ListReferralCertificates::class)
        ->assertSuccessful()
        ->assertCanSeeTableRecords($certificates);
});

test('admin can create a referral certificate for a service order', function () {
    $serviceOrder = ServiceOrder::factory()->create();

    Livewire\Livewire::test(CreateReferralCertificate::class)
        ->fillForm([
            'service_order_id' => $serviceOrder->id,
            'receiving_facility_name' => 'City General Hospital',
        ])
        ->call('create')
        ->assertNotified()
        ->assertRedirect();

    assertDatabaseHas(ReferralCertificate::class, [
        'service_order_id' => $serviceOrder->id,
        'receiving_facility_name' => 'City General Hospital',
    ]);
});

test('admin can update a referral certificate', function () {
    $certificate = ReferralCertificate::factory()->create();

    Livewire\Livewire::test(EditReferralCertificate::class, ['record' => $certificate->getRouteKey()])
        ->fillForm(['receiving_facility_name' => 'Updated Hospital'])
        ->call('save')
        ->assertNotified();

    assertDatabaseHas(ReferralCertificate::class, [
        'id' => $certificate->id,
        'receiving_facility_name' => 'Updated Hospital',
    ]);
});
