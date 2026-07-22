<?php

use App\Filament\Admin\Resources\DrugCategories\Pages\ManageDrugCategories;
use App\Models\Administrator;
use App\Models\DrugCategory;
use App\Models\User;

use function Pest\Laravel\assertDatabaseHas;

beforeEach(function () {
    $this->user = User::factory()->create();
    Administrator::create(['user_id' => $this->user->id, 'authority' => 'administrator']);
    $this->actingAs($this->user);
});

test('drug category manage page renders', function () {
    Livewire\Livewire::test(ManageDrugCategories::class)->assertSuccessful();
});

test('drug category manage page shows categories', function () {
    $categories = DrugCategory::factory()->count(3)->create();

    Livewire\Livewire::test(ManageDrugCategories::class)->assertCanSeeTableRecords($categories);
});

test('admin can create a drug category', function () {
    Livewire\Livewire::test(ManageDrugCategories::class)
        ->callAction('create', data: [
            'name' => 'Antibiotics',
            'description' => 'Anti-bacterial medications',
        ])
        ->assertNotified();

    assertDatabaseHas(DrugCategory::class, [
        'name' => 'Antibiotics',
        'description' => 'Anti-bacterial medications',
    ]);
});

test('drug category creation requires a name', function () {
    Livewire\Livewire::test(ManageDrugCategories::class)
        ->callAction('create', data: ['name' => null])
        ->assertHasActionErrors(['name' => 'required']);
});
