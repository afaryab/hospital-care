<?php

use App\Models\Closing;
use App\Models\Reception;
use App\Models\ServiceOrder;
use App\Models\User;
use Carbon\Carbon;

// ─── Closing ────────────────────────────────────────────────────────────────

test('closing number follows CT/YYYY/MM/NNNN format', function () {
    $number = Closing::generateCounterNumber();

    $now = Carbon::now();
    expect($number)->toBe("CT/{$now->format('Y')}/{$now->format('m')}/0001");
});

test('closing numbers are sequential and unique', function () {
    $user = User::factory()->create();
    $reception = Reception::create([
        'name' => 'Main Reception',
        'is_allowed_to_pay_voucher' => 0,
        'is_allowed_to_pay_from_petty_cash' => 0,
    ]);

    $numbers = [];

    for ($i = 0; $i < 5; $i++) {
        $number = Closing::generateCounterNumber();
        Closing::create([
            'ct_number' => $number,
            'reception_id' => $reception->id,
            'receptionist_id' => $user->id,
        ]);
        $numbers[] = $number;
    }

    expect($numbers)->toHaveCount(5)
        ->and(array_unique($numbers))->toHaveCount(5);

    $now = Carbon::now();
    expect($numbers[0])->toBe("CT/{$now->format('Y')}/{$now->format('m')}/0001");
    expect($numbers[4])->toBe("CT/{$now->format('Y')}/{$now->format('m')}/0005");
});

test('closing number sequence restarts each month', function () {
    $user = User::factory()->create();
    $reception = Reception::create([
        'name' => 'Main Reception',
        'is_allowed_to_pay_voucher' => 0,
        'is_allowed_to_pay_from_petty_cash' => 0,
    ]);

    // Seed a closing from last month
    Carbon::setTestNow(Carbon::now()->subMonth());
    $lastMonth = Closing::generateCounterNumber();
    Closing::create([
        'ct_number' => $lastMonth,
        'reception_id' => $reception->id,
        'receptionist_id' => $user->id,
    ]);

    // This month should restart at 0001
    Carbon::setTestNow(Carbon::now()->addMonth());
    $thisMonth = Closing::generateCounterNumber();

    $now = Carbon::now();
    expect($thisMonth)->toBe("CT/{$now->format('Y')}/{$now->format('m')}/0001");
});

// ─── ServiceOrder ────────────────────────────────────────────────────────────

test('service order numbers are unique for the same type within a month', function () {
    $user = User::factory()->create();

    $numbers = [];

    for ($i = 0; $i < 5; $i++) {
        $number = ServiceOrder::generateServiceOrderNumber('OPD');
        ServiceOrder::create([
            'type' => 'OPD',
            'so_number' => "PS/2026/01/{$number}/OPD/{$number}",
            'so_short' => "OPD-MONTH-{$i}",
            'created_by' => $user->id,
            'payee_type' => User::class,
            'payee_id' => $user->id,
        ]);
        $numbers[] = $number;
    }

    expect($numbers)->toHaveCount(5)
        ->and(array_unique($numbers))->toHaveCount(5);
});

test('service order numbers are independent per type', function () {
    $user = User::factory()->create();

    $opdNumber = ServiceOrder::generateServiceOrderNumber('OPD');
    ServiceOrder::create([
        'type' => 'OPD',
        'so_number' => "PS/2026/01/{$opdNumber}/OPD/{$opdNumber}",
        'so_short' => 'OPD-001',
        'created_by' => $user->id,
        'payee_type' => User::class,
        'payee_id' => $user->id,
    ]);

    // LAB type should start its own sequence from 0001
    $labNumber = ServiceOrder::generateServiceOrderNumber('LAB');

    expect($labNumber)->toBe('00000001');
});

test('service order monthly sequence restarts next month', function () {
    $user = User::factory()->create();

    Carbon::setTestNow(Carbon::now()->subMonth());
    $lastMonthNumber = ServiceOrder::generateServiceOrderNumber('OPD');
    ServiceOrder::create([
        'type' => 'OPD',
        'so_number' => "PS/2025/12/{$lastMonthNumber}/OPD/{$lastMonthNumber}",
        'so_short' => 'OPD-LAST',
        'created_by' => $user->id,
        'payee_type' => User::class,
        'payee_id' => $user->id,
    ]);

    Carbon::setTestNow(Carbon::now()->addMonth());
    $thisMonthNumber = ServiceOrder::generateServiceOrderNumber('OPD');

    expect($thisMonthNumber)->toBe('00000001');
});

test('short service order numbers are unique regardless of month', function () {
    $user = User::factory()->create();

    $numbers = [];

    for ($i = 0; $i < 5; $i++) {
        $number = ServiceOrder::generateShortServiceOrderNumber('OPD');
        ServiceOrder::create([
            'type' => 'OPD',
            'so_number' => "PS/2026/01/{$number}/OPD/short-{$i}",
            'so_short' => "SHORT-{$number}",
            'created_by' => $user->id,
            'payee_type' => User::class,
            'payee_id' => $user->id,
        ]);
        $numbers[] = $number;
    }

    expect($numbers)->toHaveCount(5)
        ->and(array_unique($numbers))->toHaveCount(5);
});
