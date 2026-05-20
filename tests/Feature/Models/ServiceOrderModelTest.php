<?php

use App\Models\ServiceOrder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

test('service order so_number_parts parses correctly', function () {
    $order = ServiceOrder::factory()->make(['so_number' => 'PS/2026/03/0001/OPD/00000001']);

    expect($order->so_number_parts)->toBe([
        'year' => '2026',
        'month' => '03',
        'number' => '0001',
        'departmentKey' => 'OPD',
        'serviceNumber' => '00000001',
    ]);
});

test('service order so_number_parts returns null when so_number is empty', function () {
    $order = ServiceOrder::factory()->make(['so_number' => null]);

    expect($order->so_number_parts)->toBeNull();
});

test('service order year month number attributes derive from so_number', function () {
    $order = ServiceOrder::factory()->make(['so_number' => 'PS/2026/03/0007/OPD/00000001']);

    expect($order->year)->toBe('2026')
        ->and($order->month)->toBe('03')
        ->and($order->number)->toBe('0007')
        ->and($order->departmentKey)->toBe('OPD')
        ->and($order->serviceNumber)->toBe('00000001');
});

test('service order belongs to patient', function () {
    $order = ServiceOrder::factory()->create();

    expect($order->patient())->toBeInstanceOf(BelongsTo::class);
});

test('service order belongs to service', function () {
    $order = ServiceOrder::factory()->create();

    expect($order->service())->toBeInstanceOf(BelongsTo::class);
});

test('service order belongs to creator', function () {
    $order = ServiceOrder::factory()->create();

    expect($order->creator())->toBeInstanceOf(BelongsTo::class);
});

test('service order generateServiceOrderNumber returns padded number', function () {
    $number = ServiceOrder::generateServiceOrderNumber('OPD');

    expect($number)->toHaveLength(8);
});

test('service order generateServiceOrderNumber uses highest sequence per type and ignores gaps', function () {
    $now = now();
    $ps = sprintf('PS/%s/%s/0001', $now->format('Y'), $now->format('m'));

    ServiceOrder::factory()->create([
        'type' => 'OPD',
        'so_number' => $ps.'/OPD/00000001',
    ]);
    ServiceOrder::factory()->create([
        'type' => 'OPD',
        'so_number' => $ps.'/OPD/00000099',
    ]);
    // A different type must not influence OPD's sequence.
    ServiceOrder::factory()->create([
        'type' => 'LAB',
        'so_number' => $ps.'/LAB/00000500',
    ]);

    expect(ServiceOrder::generateServiceOrderNumber('OPD'))->toBe('00000100');
});

test('service order generateShortServiceOrderNumber uses highest sequence per type and ignores gaps', function () {
    ServiceOrder::factory()->create([
        'type' => 'OPD',
        'so_short' => 'OPD/00000001',
    ]);
    ServiceOrder::factory()->create([
        'type' => 'OPD',
        'so_short' => 'OPD/00000042',
    ]);
    ServiceOrder::factory()->create([
        'type' => 'LAB',
        'so_short' => 'LAB/00000800',
    ]);

    expect(ServiceOrder::generateShortServiceOrderNumber('OPD'))->toBe('00000043');
});

test('service order generateToken returns Ymd prefix with 4-digit sequence starting at 0001', function () {
    $token = ServiceOrder::generateToken(doctorId: 1, serviceId: 1);

    expect($token)->toBe(now()->format('Ymd').'0001');
});

test('service order generateToken increments per doctor independently', function () {
    $prefix = now()->format('Ymd');

    ServiceOrder::factory()->create([
        'doctor_id' => 1,
        'token' => $prefix.'0001',
    ]);
    ServiceOrder::factory()->create([
        'doctor_id' => 1,
        'token' => $prefix.'0002',
    ]);

    expect(ServiceOrder::generateToken(doctorId: 1, serviceId: 7))->toBe($prefix.'0003')
        ->and(ServiceOrder::generateToken(doctorId: 2, serviceId: 7))->toBe($prefix.'0001');
});

test('service order generateToken scopes by service when no doctor is set', function () {
    $prefix = now()->format('Ymd');

    ServiceOrder::factory()->create([
        'doctor_id' => null,
        'service_id' => 10,
        'token' => $prefix.'0001',
    ]);
    ServiceOrder::factory()->create([
        'doctor_id' => null,
        'service_id' => 10,
        'token' => $prefix.'0002',
    ]);
    // Different service — independent sequence.
    ServiceOrder::factory()->create([
        'doctor_id' => null,
        'service_id' => 99,
        'token' => $prefix.'0005',
    ]);

    expect(ServiceOrder::generateToken(doctorId: null, serviceId: 10))->toBe($prefix.'0003')
        ->and(ServiceOrder::generateToken(doctorId: null, serviceId: 99))->toBe($prefix.'0006');
});

test('service order generateToken ignores tokens from previous days', function () {
    $today = now()->format('Ymd');
    $yesterday = now()->subDay()->format('Ymd');

    ServiceOrder::factory()->create([
        'doctor_id' => 1,
        'token' => $yesterday.'0099',
        'created_at' => now()->subDay(),
    ]);

    expect(ServiceOrder::generateToken(doctorId: 1, serviceId: 1))->toBe($today.'0001');
});
