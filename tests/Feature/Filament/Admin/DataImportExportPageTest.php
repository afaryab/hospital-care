<?php

use App\Filament\Admin\Pages\DataImportExport;
use App\Models\Administrator;
use App\Models\User;

beforeEach(function () {
    $this->admin = User::factory()->create();
    Administrator::create(['user_id' => $this->admin->id, 'authority' => 'administrator']);
    $this->actingAs($this->admin);
});

test('the page renders for an admin with no record type selected', function () {
    Livewire\Livewire::test(DataImportExport::class)
        ->assertSuccessful()
        ->assertActionDoesNotExist('clearAllCaches'); // sanity: no unrelated actions leak in
});

test('a non-admin cannot access the page', function () {
    $receptionist = User::factory()->create();
    $this->actingAs($receptionist);

    expect(DataImportExport::canAccess())->toBeFalse();
});

test('selecting a record type reveals its import and export actions', function () {
    Livewire\Livewire::test(DataImportExport::class)
        ->set('recordType', 'services')
        ->assertActionExists('import')
        ->assertActionExists('export')
        ->assertSee('Import Services')
        ->assertSee('Export Services');
});

test('no import or export action is shown until a record type is selected', function () {
    Livewire\Livewire::test(DataImportExport::class)
        ->assertActionDoesNotExist('import')
        ->assertActionDoesNotExist('export');
});
