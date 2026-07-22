<?php

use App\Filament\Admin\Resources\Triages\Pages\ManageTriages;
use App\Models\Administrator;
use App\Models\Triage;
use App\Models\User;

use function Pest\Laravel\assertDatabaseHas;

beforeEach(function () {
    $this->user = User::factory()->create();
    Administrator::create(['user_id' => $this->user->id, 'authority' => 'administrator']);
    $this->actingAs($this->user);
});

test('triage manage page renders', function () {
    Livewire\Livewire::test(ManageTriages::class)->assertSuccessful();
});

test('triage manage page shows triages', function () {
    $triages = Triage::factory()->count(3)->create();

    Livewire\Livewire::test(ManageTriages::class)->assertCanSeeTableRecords($triages);
});

test('admin can create a triage', function () {
    Livewire\Livewire::test(ManageTriages::class)
        ->callAction('create', data: [
            'name' => 'Critical',
            'color' => 'red',
            'priority' => 1,
            'description' => 'Immediate life-threatening emergency',
            'is_active' => true,
        ])
        ->assertNotified();

    assertDatabaseHas(Triage::class, [
        'name' => 'Critical',
        'color' => 'red',
        'priority' => 1,
    ]);
});

test('triage creation requires a name and color', function () {
    Livewire\Livewire::test(ManageTriages::class)
        ->callAction('create', data: ['name' => null, 'color' => null])
        ->assertHasFormErrors(['name' => 'required', 'color' => 'required']);
});
