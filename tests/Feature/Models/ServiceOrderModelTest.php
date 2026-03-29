<?php

use App\Models\ServiceOrder;

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

    expect($order->patient())->toBeInstanceOf(Illuminate\Database\Eloquent\Relations\BelongsTo::class);
});

test('service order belongs to service', function () {
    $order = ServiceOrder::factory()->create();

    expect($order->service())->toBeInstanceOf(Illuminate\Database\Eloquent\Relations\BelongsTo::class);
});

test('service order belongs to creator', function () {
    $order = ServiceOrder::factory()->create();

    expect($order->creator())->toBeInstanceOf(Illuminate\Database\Eloquent\Relations\BelongsTo::class);
});

test('service order generateServiceOrderNumber returns padded number', function () {
    $number = ServiceOrder::generateServiceOrderNumber('OPD');

    expect($number)->toHaveLength(8);
});
