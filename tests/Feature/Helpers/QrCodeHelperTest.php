<?php

use App\Helpers\QrCodeHelper;
use App\Models\HospitalSetting;

test('verificationUrl uses the configured certificate_verification_domain when set', function () {
    HospitalSetting::set('certificate_verification_domain', 'https://verify.example.com');

    expect(QrCodeHelper::verificationUrl('v/dc/abc123'))
        ->toBe('https://verify.example.com/v/dc/abc123');
});

test('verificationUrl strips a trailing slash from the configured domain', function () {
    HospitalSetting::set('certificate_verification_domain', 'https://verify.example.com/');

    expect(QrCodeHelper::verificationUrl('v/dc/abc123'))
        ->toBe('https://verify.example.com/v/dc/abc123');
});

test('verificationUrl falls back to the app URL when no domain is configured', function () {
    expect(QrCodeHelper::verificationUrl('v/dc/abc123'))
        ->toBe(rtrim(config('app.url'), '/').'/v/dc/abc123');
});

test('verificationUrl falls back to the app URL when the configured domain is an empty string', function () {
    HospitalSetting::set('certificate_verification_domain', '');

    expect(QrCodeHelper::verificationUrl('v/dc/abc123'))
        ->toBe(rtrim(config('app.url'), '/').'/v/dc/abc123');
});

test('dataUri produces an embeddable base64 PNG image', function () {
    $dataUri = QrCodeHelper::dataUri('https://example.com/v/dc/abc123', 48);

    expect($dataUri)->toStartWith('data:image/png;base64,');
});
