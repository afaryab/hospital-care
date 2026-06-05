<?php

use App\Helpers\DateHelper;
use App\Models\HospitalSetting;
use Carbon\Carbon;

beforeEach(function () {
    DateHelper::flushTimezoneCache();
});

afterEach(function () {
    DateHelper::flushTimezoneCache();
    Carbon::setTestNow();
});

test('pdf generated_at timestamp renders in the hospital timezone, not UTC', function () {
    HospitalSetting::set('hospital_timezone', 'Asia/Karachi');
    DateHelper::flushTimezoneCache();

    // 22:30 UTC on the 22nd is already 03:30 on the 23rd in Asia/Karachi (+05:00).
    Carbon::setTestNow(Carbon::parse('2026-05-22 22:30:00', 'UTC'));

    // This is the exact expression the Abacus accounting PDFs use for "Generated on".
    $rendered = DateHelper::pdfFormat(now(), 'Y-m-d H:i:s');

    expect($rendered)->toBe('2026-05-23 03:30:00');
});

test('pdf dates follow the configured hospital timezone setting', function () {
    Carbon::setTestNow(Carbon::parse('2026-05-22 22:30:00', 'UTC'));

    HospitalSetting::set('hospital_timezone', 'UTC');
    DateHelper::flushTimezoneCache();
    expect(DateHelper::pdfFormat(now(), 'Y-m-d H:i'))->toBe('2026-05-22 22:30');

    HospitalSetting::set('hospital_timezone', 'Asia/Karachi');
    DateHelper::flushTimezoneCache();
    expect(DateHelper::pdfFormat(now(), 'Y-m-d H:i'))->toBe('2026-05-23 03:30');
});

test('pdf timezone falls back to Asia/Karachi when the setting is absent', function () {
    expect(DateHelper::timezone())->toBe('Asia/Karachi');
});
