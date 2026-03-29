<?php

use App\Models\HospitalSetting;

test('hospital setting can be created and retrieved by key', function () {
    HospitalSetting::factory()->create(['key' => 'hospital_name', 'value' => 'Test Hospital']);

    expect(HospitalSetting::where('key', 'hospital_name')->value('value'))->toBe('Test Hospital');
});

test('hospital setting get helper returns value for existing key', function () {
    HospitalSetting::factory()->create(['key' => 'phone', 'value' => '03001234567']);

    expect(HospitalSetting::get('phone'))->toBe('03001234567');
});

test('hospital setting get helper returns null for missing key', function () {
    expect(HospitalSetting::get('nonexistent_key'))->toBeNull();
});

test('hospital setting set helper creates new key', function () {
    HospitalSetting::set('website', 'https://hospital.test');

    expect(HospitalSetting::get('website'))->toBe('https://hospital.test');
});

test('hospital setting set helper updates existing key', function () {
    HospitalSetting::factory()->create(['key' => 'email', 'value' => 'old@test.com']);
    HospitalSetting::set('email', 'new@test.com');

    expect(HospitalSetting::get('email'))->toBe('new@test.com')
        ->and(HospitalSetting::where('key', 'email')->count())->toBe(1);
});
