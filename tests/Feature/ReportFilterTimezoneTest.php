<?php

use App\Helpers\DateHelper;
use App\Models\HospitalSetting;
use Carbon\Carbon;

beforeEach(function () {
    HospitalSetting::set('hospital_timezone', 'Asia/Karachi');
    DateHelper::flushTimezoneCache();
});

afterEach(function () {
    DateHelper::flushTimezoneCache();
    Carbon::setTestNow();
});

test('dayStartUtc converts a hospital-local day to the correct UTC instant', function () {
    // Start of 23 May in Asia/Karachi (+05:00) is 22 May 19:00 UTC.
    expect(DateHelper::dayStartUtc('2026-05-23')->toDateTimeString())->toBe('2026-05-22 19:00:00');
});

test('dayEndUtc converts a hospital-local day to the correct UTC instant', function () {
    // End of 23 May in Asia/Karachi (+05:00) is 23 May 18:59:59 UTC.
    expect(DateHelper::dayEndUtc('2026-05-23')->toDateTimeString())->toBe('2026-05-23 18:59:59');
});

test('a record just after local midnight is inside the selected hospital day', function () {
    // 22 May 21:00 UTC == 23 May 02:00 in Asia/Karachi.
    $createdAt = Carbon::parse('2026-05-22 21:00:00', 'UTC');

    expect($createdAt->between(
        DateHelper::dayStartUtc('2026-05-23'),
        DateHelper::dayEndUtc('2026-05-23'),
    ))->toBeTrue();
});

test('a record just before next local midnight is excluded from the selected hospital day', function () {
    // 23 May 20:00 UTC == 24 May 01:00 in Asia/Karachi, so it belongs to the 24th.
    $createdAt = Carbon::parse('2026-05-23 20:00:00', 'UTC');

    expect($createdAt->between(
        DateHelper::dayStartUtc('2026-05-23'),
        DateHelper::dayEndUtc('2026-05-23'),
    ))->toBeFalse();
});

test('the boundary helpers honour a changed hospital timezone', function () {
    HospitalSetting::set('hospital_timezone', 'UTC');
    DateHelper::flushTimezoneCache();

    expect(DateHelper::dayStartUtc('2026-05-23')->toDateTimeString())->toBe('2026-05-23 00:00:00')
        ->and(DateHelper::dayEndUtc('2026-05-23')->toDateTimeString())->toBe('2026-05-23 23:59:59');
});

test('todayRangeUtc spans the hospital-local day in UTC', function () {
    // 01:00 on 23 May in Asia/Karachi is still 22 May 20:00 UTC.
    Carbon::setTestNow(Carbon::parse('2026-05-22 20:00:00', 'UTC'));

    [$start, $end] = DateHelper::todayRangeUtc();

    expect($start->toDateTimeString())->toBe('2026-05-22 19:00:00')
        ->and($end->toDateTimeString())->toBe('2026-05-23 18:59:59');
});
