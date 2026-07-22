<?php

use App\Filament\Admin\Resources\Drugs\Pages\CreateDrug;
use App\Filament\Admin\Resources\Drugs\Pages\EditDrug;
use App\Filament\Admin\Resources\Drugs\Pages\ListDrugs;
use App\Models\Administrator;
use App\Models\Drug;
use App\Models\DrugCategory;
use App\Models\User;

use function Pest\Laravel\assertDatabaseHas;

beforeEach(function () {
    $this->user = User::factory()->create();
    Administrator::create(['user_id' => $this->user->id, 'authority' => 'administrator']);
    $this->actingAs($this->user);
});

test('drug list page renders', function () {
    Livewire\Livewire::test(ListDrugs::class)->assertSuccessful();
});

test('drug list page shows drugs', function () {
    $drugs = Drug::factory()->count(3)->create();

    Livewire\Livewire::test(ListDrugs::class)->assertCanSeeTableRecords($drugs);
});

test('drug create page renders', function () {
    Livewire\Livewire::test(CreateDrug::class)->assertSuccessful();
});

test('admin can create a drug', function () {
    $category = DrugCategory::factory()->create();

    Livewire\Livewire::test(CreateDrug::class)
        ->fillForm([
            'name' => 'Amoxicillin',
            'generic_name' => 'Amoxicillin Trihydrate',
            'type' => 'Capsule',
            'drug_category_id' => $category->id,
            'strength' => '500mg',
            'default_dose' => '1 capsule',
            'default_frequency' => 'TDS',
            'default_route' => 'Oral',
            'is_active' => true,
        ])
        ->call('create')
        ->assertNotified()
        ->assertRedirect();

    assertDatabaseHas(Drug::class, [
        'name' => 'Amoxicillin',
        'generic_name' => 'Amoxicillin Trihydrate',
        'drug_category_id' => $category->id,
    ]);
});

test('drug creation requires a name', function () {
    Livewire\Livewire::test(CreateDrug::class)
        ->fillForm(['name' => null])
        ->call('create')
        ->assertHasFormErrors(['name' => 'required'])
        ->assertNotNotified();
});

test('drug edit page renders', function () {
    $drug = Drug::factory()->create();

    Livewire\Livewire::test(EditDrug::class, ['record' => $drug->getRouteKey()])->assertSuccessful();
});

test('admin can update a drug', function () {
    $drug = Drug::factory()->create(['name' => 'Old Name']);

    Livewire\Livewire::test(EditDrug::class, ['record' => $drug->getRouteKey()])
        ->fillForm(['name' => 'New Name'])
        ->call('save')
        ->assertNotified();

    assertDatabaseHas(Drug::class, [
        'id' => $drug->id,
        'name' => 'New Name',
    ]);
});
