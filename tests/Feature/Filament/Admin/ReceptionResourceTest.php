<?php

use App\Models\Reception;
use App\Models\Closing;
use App\Models\User;
use App\Models\Administrator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;

uses(RefreshDatabase::class);

test('admin can merge receptions and related closings are updated', function () {
    $adminUser = User::factory()->create();
    Administrator::factory()->create(['user_id' => $adminUser->id]);
    actingAs($adminUser);

    $primary = Reception::factory()->create(['name' => 'Primary Reception']);
    $secondary = Reception::factory()->create(['name' => 'Secondary Reception']);
    $closing1 = Closing::factory()->create(['reception_id' => $primary->id]);
    $closing2 = Closing::factory()->create(['reception_id' => $secondary->id]);

    Livewire\Livewire::test(\App\Filament\Admin\Resources\Receptions\Pages\ManageReceptions::class)
        ->callTableBulkAction('merge', [$primary->id, $secondary->id], [
            'primary_reception_id' => $primary->id,
        ])
        ->assertNotified();

    // Secondary should be deleted
    assertDatabaseMissing('receptions', ['id' => $secondary->id]);
    // Closings should now point to primary
    assertDatabaseHas('closings', ['id' => $closing2->id, 'reception_id' => $primary->id]);
});
