<?php

use App\Models\BirthCertificate;
use Illuminate\Validation\ValidationException;

test('birth certificate number is auto-generated in BC/YYYY/MM/NNNN format when not provided', function () {
    $certificate = BirthCertificate::factory()->create(['birth_certificate_number' => null]);

    expect($certificate->birth_certificate_number)->toMatch('/^BC\/\d{4}\/\d{2}\/\d{4}$/');
});

test('birth certificate numbers increment sequentially within the same month', function () {
    $first = BirthCertificate::factory()->create(['birth_certificate_number' => null]);
    $second = BirthCertificate::factory()->create(['birth_certificate_number' => null]);

    $firstSeq = (int) explode('/', $first->birth_certificate_number)[3];
    $secondSeq = (int) explode('/', $second->birth_certificate_number)[3];

    expect($secondSeq)->toBe($firstSeq + 1);
});

test('a birth certificate is not locked by default', function () {
    $certificate = BirthCertificate::factory()->create();

    expect($certificate->is_locked)->toBeFalse();
});

test('an unlocked birth certificate can be edited', function () {
    $certificate = BirthCertificate::factory()->create();

    $certificate->update(['child_name' => 'Updated Name']);

    expect($certificate->fresh()->child_name)->toBe('Updated Name');
});

test('a locked birth certificate cannot be edited', function () {
    $certificate = BirthCertificate::factory()->locked()->create();

    expect(fn () => $certificate->update(['child_name' => 'Changed Name']))
        ->toThrow(ValidationException::class);
});

test('locking a birth certificate itself is allowed (the transition into locked)', function () {
    $certificate = BirthCertificate::factory()->create(['is_locked' => false]);

    $certificate->update(['is_locked' => true, 'locked_at' => now()]);

    expect($certificate->fresh()->is_locked)->toBeTrue();
});

test('birth certificate cannot be hard deleted', function () {
    $certificate = BirthCertificate::factory()->create();

    expect(fn () => $certificate->forceDelete())->toThrow(RuntimeException::class);
});

test('birth certificate gets a verification token auto-assigned on create', function () {
    $certificate = BirthCertificate::factory()->create();

    expect($certificate->verification_token)->not->toBeNull()
        ->and(strlen($certificate->verification_token))->toBe(40);
});
