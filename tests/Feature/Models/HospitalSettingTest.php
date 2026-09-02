<?php

use App\Models\HospitalSetting;
use Illuminate\Support\Facades\Storage;

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

test('logoUrl returns null when no logo has been uploaded', function () {
    expect(HospitalSetting::logoUrl())->toBeNull();
});

test('logoUrl resolves a stored disk path to a public storage URL', function () {
    Storage::fake('public');
    HospitalSetting::set('hospital_logo', 'hospital-settings/logo.png');

    expect(HospitalSetting::logoUrl())->toBe(Storage::disk('public')->url('hospital-settings/logo.png'));
});

test('logoUrl passes through an already-absolute URL unchanged', function () {
    HospitalSetting::set('hospital_logo', 'https://cdn.example.com/logo.png');

    expect(HospitalSetting::logoUrl())->toBe('https://cdn.example.com/logo.png');
});

test('name falls back to the app name when no hospital name is set', function () {
    expect(HospitalSetting::name())->toBe(config('app.name'));
});

test('name returns the configured hospital name once set', function () {
    HospitalSetting::set('hospital_name', 'City Care Hospital');

    expect(HospitalSetting::name())->toBe('City Care Hospital');
});
