<?php

use App\Filament\Admin\Resources\BirthCertificates\Pages\CreateBirthCertificate;
use App\Filament\Admin\Resources\BirthCertificates\Pages\EditBirthCertificate;
use App\Filament\Admin\Resources\BirthCertificates\Pages\ListBirthCertificates;
use App\Models\Administrator;
use App\Models\BirthCertificate;
use App\Models\ServiceOrder;
use App\Models\User;

use function Pest\Laravel\assertDatabaseHas;

beforeEach(function () {
    $this->user = User::factory()->create();
    Administrator::create(['user_id' => $this->user->id, 'authority' => 'administrator']);
    $this->actingAs($this->user);
});

test('birth certificate list page renders and shows records', function () {
    $certificates = BirthCertificate::factory()->count(2)->create();

    Livewire\Livewire::test(ListBirthCertificates::class)
        ->assertSuccessful()
        ->assertCanSeeTableRecords($certificates);
});

test('admin can create a birth certificate for a service order', function () {
    $serviceOrder = ServiceOrder::factory()->create();

    Livewire\Livewire::test(CreateBirthCertificate::class)
        ->fillForm([
            'service_order_id' => $serviceOrder->id,
            'child_name' => 'Baby Ahmed',
            'gender' => 'm',
            'mother_name' => 'Sadia Ahmed',
        ])
        ->call('create')
        ->assertNotified()
        ->assertRedirect();

    assertDatabaseHas(BirthCertificate::class, [
        'service_order_id' => $serviceOrder->id,
        'child_name' => 'Baby Ahmed',
        'is_locked' => false,
    ]);
});

test('admin can update an unlocked birth certificate', function () {
    $certificate = BirthCertificate::factory()->create();

    Livewire\Livewire::test(EditBirthCertificate::class, ['record' => $certificate->getRouteKey()])
        ->fillForm(['child_name' => 'Updated Name'])
        ->call('save')
        ->assertNotified();

    assertDatabaseHas(BirthCertificate::class, [
        'id' => $certificate->id,
        'child_name' => 'Updated Name',
    ]);
});

test('the lock action is visible for an unlocked certificate and hidden for a locked one', function () {
    $unlocked = BirthCertificate::factory()->create();
    $locked = BirthCertificate::factory()->locked()->create();

    Livewire\Livewire::test(EditBirthCertificate::class, ['record' => $unlocked->getRouteKey()])
        ->assertActionVisible('lock');

    Livewire\Livewire::test(EditBirthCertificate::class, ['record' => $locked->getRouteKey()])
        ->assertActionHidden('lock');
});

test('admin can lock a birth certificate via the lock action', function () {
    $certificate = BirthCertificate::factory()->create();

    Livewire\Livewire::test(EditBirthCertificate::class, ['record' => $certificate->getRouteKey()])
        ->callAction('lock')
        ->assertNotified();

    $certificate->refresh();
    expect($certificate->is_locked)->toBeTrue()
        ->and($certificate->locked_at)->not->toBeNull()
        ->and($certificate->locked_by)->toBe($this->user->id);
});
