<?php

use App\Filament\Admin\Resources\ServiceOrders\Pages\ListServiceOrders;
use App\Filament\Admin\Resources\ServiceOrders\Pages\ViewServiceOrder;
use App\Models\Administrator;
use App\Models\ServiceOrder;
use App\Models\TreatmentRecord;
use App\Models\Triage;
use App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();
    Administrator::create(['user_id' => $this->user->id, 'authority' => 'administrator']);
    $this->actingAs($this->user);
});

test('service order list page renders', function () {
    Livewire\Livewire::test(ListServiceOrders::class)->assertSuccessful();
});

test('service order view page renders', function () {
    $serviceOrder = ServiceOrder::factory()->create();
    Livewire\Livewire::test(ViewServiceOrder::class, ['record' => $serviceOrder->getRouteKey()])->assertSuccessful();
});

test('service order list can be filtered by triage', function () {
    $triage = Triage::factory()->create();

    $matching = ServiceOrder::factory()->create(['so_number' => 'PS/2026/01/0001/EMG/01']);
    TreatmentRecord::factory()->create(['service_order_id' => $matching->id, 'triage_id' => $triage->id]);

    $other = ServiceOrder::factory()->create(['so_number' => 'PS/2026/01/0002/EMG/01']);
    TreatmentRecord::factory()->create(['service_order_id' => $other->id]);

    Livewire\Livewire::test(ListServiceOrders::class)
        ->call('loadTable')
        ->assertCanSeeTableRecords([$matching, $other])
        ->filterTable('triage', $triage->id)
        ->assertCanSeeTableRecords([$matching])
        ->assertCanNotSeeTableRecords([$other]);
});

test('visiting the service order list with a triage query param pre-applies the filter', function () {
    $triage = Triage::factory()->create();

    $matching = ServiceOrder::factory()->create(['so_number' => 'PS/2026/01/0003/EMG/01']);
    TreatmentRecord::factory()->create(['service_order_id' => $matching->id, 'triage_id' => $triage->id]);

    $other = ServiceOrder::factory()->create(['so_number' => 'PS/2026/01/0004/EMG/01']);
    TreatmentRecord::factory()->create(['service_order_id' => $other->id]);

    Livewire\Livewire::withQueryParams(['triage' => (string) $triage->id])
        ->test(ListServiceOrders::class)
        ->assertSuccessful()
        ->call('loadTable')
        ->assertCanSeeTableRecords([$matching])
        ->assertCanNotSeeTableRecords([$other]);
});
