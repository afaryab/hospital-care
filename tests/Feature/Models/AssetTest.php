<?php

use App\Enum\AssetStatus;
use App\Models\Asset;
use App\Models\AssetAssignmentLog;
use App\Models\AssetDepreciationEntry;
use App\Models\AssetMaintenanceLog;

test('asset generates ast number on create', function () {
    $asset = Asset::factory()->create();

    expect($asset->asset_number)->toStartWith('AST/');
});

test('asset status enum casts correctly', function () {
    $asset = Asset::factory()->underMaintenance()->create();

    expect($asset->status->value)->toBe(AssetStatus::UnderMaintenance->value);
});

test('asset soft deletes instead of hard deletes', function () {
    $asset = Asset::factory()->create();
    $id = $asset->id;
    $asset->delete();

    expect(Asset::find($id))->toBeNull()
        ->and(Asset::withTrashed()->find($id))->not->toBeNull();
});

test('asset has assignment logs relationship', function () {
    $asset = Asset::factory()->create();
    AssetAssignmentLog::factory()->create(['asset_id' => $asset->id]);

    expect($asset->assignmentLogs()->count())->toBe(1);
});

test('asset has maintenance logs relationship', function () {
    $asset = Asset::factory()->create();
    AssetMaintenanceLog::factory()->create(['asset_id' => $asset->id]);

    expect($asset->maintenanceLogs()->count())->toBe(1);
});

test('asset has depreciation entries relationship', function () {
    $asset = Asset::factory()->create();
    AssetDepreciationEntry::factory()->create(['asset_id' => $asset->id]);

    expect($asset->depreciationEntries()->count())->toBe(1);
});

test('two assets get unique ast numbers', function () {
    $a1 = Asset::factory()->create();
    $a2 = Asset::factory()->create();

    expect($a1->asset_number)->not->toBe($a2->asset_number);
});
