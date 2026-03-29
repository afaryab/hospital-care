<?php

use App\Filament\Admin\Resources\Panels\Pages\CreatePanel;
use App\Filament\Admin\Resources\Panels\Pages\EditPanel;
use App\Filament\Admin\Resources\Panels\Pages\ListPanels;
use App\Models\Administrator;
use App\Models\Panel;
use App\Models\Receaveable;
use App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();
    Administrator::create(['user_id' => $this->user->id, 'authority' => 'administrator']);
    $this->actingAs($this->user);
});

test('admin can list panels', function () {
    $panels = Panel::factory()->count(2)->create();

    Livewire\Livewire::test(ListPanels::class)
        ->assertSuccessful()
        ->assertCanSeeTableRecords($panels);
});

test('admin can create a panel with name and code', function () {
    Livewire\Livewire::test(CreatePanel::class)
        ->fillForm([
            'name' => 'Corporate Panel',
            'code' => 'CP01',
            'is_active' => true,
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $this->assertDatabaseHas('panels', [
        'name' => 'Corporate Panel',
        'code' => 'CP01',
        'is_active' => 1,
    ]);
});

test('admin can edit panel and toggle active state', function () {
    $panel = Panel::factory()->create(['is_active' => true]);

    Livewire\Livewire::test(EditPanel::class, ['record' => $panel->getRouteKey()])
        ->fillForm([
            'name' => $panel->name,
            'code' => $panel->code,
            'is_active' => false,
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($panel->fresh()->is_active)->toBeFalse();
});

test('panel list shows associated pending receivables count', function () {
    $panel = Panel::factory()->create();

    Receaveable::factory()->count(2)->create([
        'panel_id' => $panel->id,
        'status' => 'PENDING',
    ]);

    Receaveable::factory()->create([
        'panel_id' => $panel->id,
        'status' => 'PAID',
    ]);

    Livewire\Livewire::test(ListPanels::class)
        ->assertSuccessful()
        ->assertCanSeeTableRecords([$panel])
        ->assertSee('2');
});

test('duplicate panel code is rejected', function () {
    Panel::factory()->create([
        'code' => 'DUPL1',
    ]);

    Livewire\Livewire::test(CreatePanel::class)
        ->fillForm([
            'name' => 'Duplicate Code Panel',
            'code' => 'DUPL1',
            'is_active' => true,
        ])
        ->call('create')
        ->assertHasFormErrors(['code' => 'unique']);
});
