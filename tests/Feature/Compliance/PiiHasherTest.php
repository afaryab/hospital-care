<?php

use App\Helpers\PiiHasher;

test('cnic hashing normalizes case and whitespace before hashing', function () {
    $canonical = PiiHasher::cnic('35202-1234567-1');

    expect(PiiHasher::cnic(' 35202-1234567-1 '))->toBe($canonical)
        ->and(PiiHasher::cnic('35202-1234567-1'))->toBe($canonical);
});

test('cnic hashing is a keyed HMAC using APP_KEY, not unsalted SHA-256', function () {
    $cnic = '35202-1234567-1';

    expect(PiiHasher::cnic($cnic))
        ->toBe(hash_hmac('sha256', $cnic, (string) config('app.key')))
        ->not->toBe(hash('sha256', $cnic));
});

test('contact hashing strips non-digit characters before hashing', function () {
    $canonical = PiiHasher::contact('03001234567');

    expect(PiiHasher::contact('0300-1234567'))->toBe($canonical)
        ->and(PiiHasher::contact('+92 300 1234567'))->not->toBe($canonical);
});

test('different cnics produce different hashes', function () {
    expect(PiiHasher::cnic('35202-1111111-1'))
        ->not->toBe(PiiHasher::cnic('35202-2222222-2'));
});
