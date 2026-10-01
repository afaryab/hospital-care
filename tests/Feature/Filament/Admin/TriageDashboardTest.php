<?php

use App\Filament\Admin\Pages\TriageDashboard;
use App\Filament\Admin\Widgets\Triage\TriageKPIStats;
use App\Helpers\DateHelper;
use App\Models\Administrator;
use App\Models\ServiceOrder;
use App\Models\TreatmentRecord;
use App\Models\Triage;
use App\Models\TriageHistory;
use App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();
    Administrator::create(['user_id' => $this->user->id, 'authority' => 'administrator']);
    $this->actingAs($this->user);
});

test('triage dashboard page renders', function () {
    Livewire\Livewire::test(TriageDashboard::class)->assertSuccessful();
});

test('triage KPI stats reflect triage assignments in the current period', function () {
    $triage = Triage::factory()->create(['name' => 'Custom Priority Level']);
    $serviceOrder = ServiceOrder::factory()->create(['type' => 'EMG']);
    $treatmentRecord = TreatmentRecord::factory()->create(['service_order_id' => $serviceOrder->id]);

    TriageHistory::factory()->count(3)->create([
        'treatment_record_id' => $treatmentRecord->id,
        'service_order_id' => $serviceOrder->id,
        'new_triage_id' => $triage->id,
        'changed_at' => now(),
    ]);

    Livewire\Livewire::test(TriageKPIStats::class)
        ->assertSuccessful()
        ->assertSee('Custom Priority Level')
        ->assertSee('3');
});

test('applying a preset date range fills concrete start and end dates for widgets', function () {
    $page = Livewire\Livewire::test(TriageDashboard::class)
        ->callAction('filter', data: ['dateRange' => 'this_year'])
        ->assertHasNoFormErrors();

    $now = now(DateHelper::timezone());

    expect($page->get('filters'))->toMatchArray([
        'dateRange' => 'this_year',
        'startDate' => $now->copy()->startOfYear()->toDateString(),
        'endDate' => $now->copy()->endOfYear()->toDateString(),
    ]);
});

test('applying a custom date range keeps the chosen dates', function () {
    $page = Livewire\Livewire::test(TriageDashboard::class)
        ->callAction('filter', data: ['dateRange' => 'custom', 'startDate' => '2026-01-10', 'endDate' => '2026-02-05'])
        ->assertHasNoFormErrors();

    expect($page->get('filters'))->toMatchArray(['startDate' => '2026-01-10', 'endDate' => '2026-02-05']);
});
