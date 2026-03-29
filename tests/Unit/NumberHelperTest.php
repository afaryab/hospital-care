<?php

use App\Helpers\NumberHelper;

// --- moneyfy ---

test('moneyfy formats thousands as K', function () {
    expect(NumberHelper::moneyfy(1000))->toBe('1K')
        ->and(NumberHelper::moneyfy(1500))->toBe('1.5K')
        ->and(NumberHelper::moneyfy(999_999))->toBe('1000K');
});

test('moneyfy formats millions as M', function () {
    expect(NumberHelper::moneyfy(1_000_000))->toBe('1M')
        ->and(NumberHelper::moneyfy(2_500_000))->toBe('2.5M')
        ->and(NumberHelper::moneyfy(999_999_999))->toBe('1000M');
});

test('moneyfy formats billions as B', function () {
    expect(NumberHelper::moneyfy(1_000_000_000))->toBe('1B')
        ->and(NumberHelper::moneyfy(1_500_000_000))->toBe('1.5B');
});

test('moneyfy formats trillions as T', function () {
    expect(NumberHelper::moneyfy(1_000_000_000_000))->toBe('1T')
        ->and(NumberHelper::moneyfy(2_000_000_000_000))->toBe('2T');
});

test('moneyfy returns raw number below 1000', function () {
    expect(NumberHelper::moneyfy(0))->toBe('0')
        ->and(NumberHelper::moneyfy(500))->toBe('500')
        ->and(NumberHelper::moneyfy(999))->toBe('999');
});

// --- formatCurrency ---

test('formatCurrency formats with two decimal places', function () {
    expect(NumberHelper::formatCurrency(1500))->toBe('1,500.00')
        ->and(NumberHelper::formatCurrency(1500, 'PKR '))->toBe('PKR 1,500.00');
});

test('formatCurrency uses abbreviated format when flag is true', function () {
    expect(NumberHelper::formatCurrency(1500, 'PKR ', true))->toBe('PKR 1.5K')
        ->and(NumberHelper::formatCurrency(2_000_000, 'PKR ', true))->toBe('PKR 2M');
});

test('formatCurrency with no currency prefix', function () {
    expect(NumberHelper::formatCurrency(500, '', false))->toBe('500.00');
});

// --- formatPercentage ---

test('formatPercentage converts decimal to percentage', function () {
    expect(NumberHelper::formatPercentage(0.75))->toBe('75%')
        ->and(NumberHelper::formatPercentage(1.0))->toBe('100%')
        ->and(NumberHelper::formatPercentage(0.0))->toBe('0%');
});

test('formatPercentage respects decimals parameter', function () {
    expect(NumberHelper::formatPercentage(0.751, 1))->toBe('75.1%')
        ->and(NumberHelper::formatPercentage(0.7555, 2))->toBe('75.55%');
});
