<?php

use App\Models\Patient;
use App\Models\ServiceOrder;
use Illuminate\Support\Facades\DB;

test('patient cnic contact and address are encrypted in database', function () {
    $patient = Patient::factory()->create([
        'cnic' => '35202-1234567-1',
        'contact' => '03001234567',
        'address' => 'Main Street 12',
    ]);

    $raw = DB::table('patients')->where('id', $patient->id)->first(['cnic', 'contact', 'address']);

    expect($raw->cnic)->not->toBe('35202-1234567-1')
        ->and($raw->contact)->not->toBe('03001234567')
        ->and($raw->address)->not->toBe('Main Street 12');
});

test('encrypted patient fields are transparently decrypted through model access', function () {
    $patient = Patient::factory()->create([
        'cnic' => '35202-1111111-1',
        'contact' => '03111234567',
        'address' => 'Street 42',
    ]);

    $patient->refresh();

    expect($patient->cnic)->toBe('35202-1111111-1')
        ->and($patient->contact)->toBe('03111234567')
        ->and($patient->address)->toBe('Street 42');
});

test('cnic duplicate check works with cnic hash', function () {
    $cnic = '35202-2222222-1';

    $patient = Patient::factory()->create([
        'cnic' => $cnic,
    ]);

    $hash = hash('sha256', strtoupper(trim($cnic)));

    expect($patient->cnic_hash)->toBe($hash)
        ->and(Patient::query()->where('cnic_hash', $hash)->count())->toBe(1);
});

test('service order notes_json is encrypted at rest', function () {
    $notes = [
        'complaint' => 'Severe pain',
        'diagnosis' => 'Observation pending',
    ];

    $serviceOrder = ServiceOrder::factory()->create([
        'notes_json' => $notes,
    ]);

    $rawNotes = DB::table('service_orders')->where('id', $serviceOrder->id)->value('notes_json');

    expect($rawNotes)->toBeString()
        ->and($rawNotes)->not->toContain('Severe pain')
        ->and($serviceOrder->fresh()->notes_json)->toBe($notes);
});
