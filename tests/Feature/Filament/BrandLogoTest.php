<?php

use App\Models\HospitalSetting;
use Illuminate\Support\Facades\Storage;

test('brand logo view shows the default app logo and app name when no hospital logo is set', function () {
    $html = view('filament.partials.brand-logo')->render();

    expect($html)->toContain(asset('logo.png'))
        ->and($html)->toContain(config('app.name'))
        ->and($html)->not->toContain('bg-white');
});

test('brand logo view shows the uploaded hospital logo on a white background and the hospital name', function () {
    Storage::fake('public');
    HospitalSetting::set('hospital_logo', 'hospital-settings/logo.png');
    HospitalSetting::set('hospital_name', 'City Care Hospital');

    $html = view('filament.partials.brand-logo')->render();

    expect($html)->toContain(Storage::disk('public')->url('hospital-settings/logo.png'))
        ->and($html)->toContain('City Care Hospital')
        ->and($html)->toContain('bg-white')
        ->and($html)->not->toContain(asset('logo.png'));
});
