<?php

use App\Models\ServiceOrder;
use App\Models\Transaction;
use App\Services\DataPortability\RecordTypeRegistry;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Imports\Importer;

test('every registered entry has a valid model, importer, and exporter', function () {
    $entries = RecordTypeRegistry::all();

    expect($entries)->not->toBeEmpty();

    foreach ($entries as $key => $entry) {
        expect($entry)->toHaveKeys(['label', 'model', 'importer', 'exporter'])
            ->and(class_exists($entry['model']))->toBeTrue("model missing for [{$key}]")
            ->and(is_subclass_of($entry['importer'], Importer::class))->toBeTrue("importer invalid for [{$key}]")
            ->and(is_subclass_of($entry['exporter'], Exporter::class))->toBeTrue("exporter invalid for [{$key}]")
            ->and($entry['importer']::getModel())->toBe($entry['model'])
            ->and($entry['exporter']::getModel())->toBe($entry['model']);
    }
});

test('Transaction and ServiceOrder are deliberately not registered', function () {
    $models = collect(RecordTypeRegistry::all())->pluck('model');

    expect($models)->not->toContain(Transaction::class)
        ->and($models)->not->toContain(ServiceOrder::class);
});

test('options returns a key-to-label map matching all entries', function () {
    $options = RecordTypeRegistry::options();
    $entries = RecordTypeRegistry::all();

    expect($options)->toHaveCount(count($entries));

    foreach ($entries as $key => $entry) {
        expect($options[$key])->toBe($entry['label']);
    }
});

test('find returns the matching entry or null', function () {
    expect(RecordTypeRegistry::find('services'))->not->toBeNull()
        ->and(RecordTypeRegistry::find('services')['label'])->toBe('Services')
        ->and(RecordTypeRegistry::find('not-a-real-type'))->toBeNull();
});
