<?php

use App\Models\ReferralCertificate;

test('referral certificate number is auto-generated in RF/YYYY/MM/NNNN format when not provided', function () {
    $certificate = ReferralCertificate::factory()->create(['referral_number' => null]);

    expect($certificate->referral_number)->toMatch('/^RF\/\d{4}\/\d{2}\/\d{4}$/');
});

test('referral certificate numbers increment sequentially within the same month', function () {
    $first = ReferralCertificate::factory()->create(['referral_number' => null]);
    $second = ReferralCertificate::factory()->create(['referral_number' => null]);

    $firstSeq = (int) explode('/', $first->referral_number)[3];
    $secondSeq = (int) explode('/', $second->referral_number)[3];

    expect($secondSeq)->toBe($firstSeq + 1);
});

test('referral certificate cannot be hard deleted', function () {
    $certificate = ReferralCertificate::factory()->create();

    expect(fn () => $certificate->forceDelete())->toThrow(RuntimeException::class);
});

test('sanitizeNotes strips script tags and event-handler attributes but keeps basic formatting', function () {
    $dirty = '<p onclick="alert(1)">Hello <strong>world</strong></p><script>alert(2)</script><a href="javascript:alert(3)">link</a>';

    $clean = ReferralCertificate::sanitizeNotes($dirty);

    expect($clean)->toContain('<strong>world</strong>')
        ->not->toContain('<script>')
        ->not->toContain('onclick')
        ->not->toContain('javascript:');
});

test('sanitizeNotes returns null for empty input', function () {
    expect(ReferralCertificate::sanitizeNotes(null))->toBeNull()
        ->and(ReferralCertificate::sanitizeNotes('   '))->toBeNull();
});
