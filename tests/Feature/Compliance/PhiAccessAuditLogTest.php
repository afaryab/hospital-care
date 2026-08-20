<?php

use App\Filament\Admin\Resources\Patients\Pages\ViewPatient;
use App\Models\Patient;
use App\Models\ServiceOrder;
use App\Models\Transaction;
use Spatie\Activitylog\Models\Activity;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

afterEach(function () {
    Activity::query()->delete();
});

test('viewing a patient chart via the front-desk route logs a viewed event', function () {
    $admin = adminUser();
    actingAs($admin);

    $patient = Patient::factory()->create(['ps_number' => 'PS/2026/03/0001']);

    get(route('patients-register-ps-number', ['year' => 2026, 'month' => '03', 'number' => '0001']));

    $activity = Activity::query()
        ->where('subject_type', Patient::class)
        ->where('subject_id', $patient->id)
        ->where('event', 'viewed')
        ->first();

    expect($activity)->not->toBeNull()
        ->and($activity->causer_id)->toBe($admin->id);
});

test('viewing a patient in the Filament admin panel logs a viewed event', function () {
    $admin = adminUser();
    actingAs($admin);

    $patient = Patient::factory()->create();

    Livewire\Livewire::test(
        ViewPatient::class,
        ['record' => $patient->getRouteKey()]
    );

    $activity = Activity::query()
        ->where('subject_type', Patient::class)
        ->where('subject_id', $patient->id)
        ->where('event', 'viewed')
        ->first();

    expect($activity)->not->toBeNull();
});

test('printing a service order pdf logs a downloaded event', function () {
    $admin = adminUser();
    actingAs($admin);

    $serviceOrder = ServiceOrder::factory()->create(['type' => 'EMG']);

    get(route('print-serviceorder', ['id' => $serviceOrder->id]));

    $activity = Activity::query()
        ->where('subject_type', ServiceOrder::class)
        ->where('subject_id', $serviceOrder->id)
        ->where('event', 'downloaded')
        ->first();

    expect($activity)->not->toBeNull()
        ->and($activity->causer_id)->toBe($admin->id);
});

test('streaming and downloading a transaction pdf logs a downloaded event each time', function () {
    $admin = adminUser();
    actingAs($admin);

    $transaction = Transaction::factory()->create(['tr_number' => 'TR/2026/03/15/0001']);

    get(route('print-transaction', ['year' => 2026, 'month' => '03', 'day' => '15', 'number' => '0001']));
    get(route('download-transaction', ['year' => 2026, 'month' => '03', 'day' => '15', 'number' => '0001']));

    $count = Activity::query()
        ->where('subject_type', Transaction::class)
        ->where('subject_id', $transaction->id)
        ->where('event', 'downloaded')
        ->count();

    expect($count)->toBe(2);
});
