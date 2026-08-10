<?php

use App\Enum\DeathCertificateManner;
use App\Models\DeathCertificate;

test('death certificate number is auto-generated in DC/YYYY/MM/NNNN format when not provided', function () {
    $certificate = DeathCertificate::factory()->create(['certificate_number' => null]);

    expect($certificate->certificate_number)->toMatch('/^DC\/\d{4}\/\d{2}\/\d{4}$/');
});

test('death certificate numbers increment sequentially within the same month', function () {
    $first = DeathCertificate::factory()->create(['certificate_number' => null]);
    $second = DeathCertificate::factory()->create(['certificate_number' => null]);

    $firstSeq = (int) explode('/', $first->certificate_number)[3];
    $secondSeq = (int) explode('/', $second->certificate_number)[3];

    expect($secondSeq)->toBe($firstSeq + 1);
});

test('death certificate casts manner_of_death to the enum', function () {
    $certificate = DeathCertificate::factory()->create([
        'manner_of_death' => DeathCertificateManner::Natural,
    ]);

    expect($certificate->fresh()->manner_of_death)->toBe(DeathCertificateManner::Natural);
});

test('death certificate cannot be hard deleted', function () {
    $certificate = DeathCertificate::factory()->create();

    expect(fn () => $certificate->forceDelete())->toThrow(RuntimeException::class);
});

test('death certificate soft deletes normally', function () {
    $certificate = DeathCertificate::factory()->create();

    $certificate->delete();

    expect($certificate->trashed())->toBeTrue()
        ->and(DeathCertificate::withTrashed()->find($certificate->id))->not->toBeNull();
});

test('death certificate gets a verification token auto-assigned on create', function () {
    $certificate = DeathCertificate::factory()->create();

    expect($certificate->verification_token)->not->toBeNull()
        ->and(strlen($certificate->verification_token))->toBe(40);
});

test('death certificate verification tokens are unique', function () {
    $first = DeathCertificate::factory()->create();
    $second = DeathCertificate::factory()->create();

    expect($first->verification_token)->not->toBe($second->verification_token);
});
