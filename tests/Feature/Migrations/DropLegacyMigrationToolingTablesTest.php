<?php

use Illuminate\Support\Facades\Schema;

test('legacy migration tooling tables are dropped', function () {
    expect(Schema::hasTable('migration_logs'))->toBeFalse()
        ->and(Schema::hasTable('upgrade_processes'))->toBeFalse();
});

test('dropping legacy migration tooling tables can be rolled back', function () {
    $migration = require database_path('migrations/2026_09_26_190705_drop_legacy_migration_tooling_tables.php');

    $migration->down();

    expect(Schema::hasTable('migration_logs'))->toBeTrue()
        ->and(Schema::hasTable('upgrade_processes'))->toBeTrue();

    $migration->up();

    expect(Schema::hasTable('migration_logs'))->toBeFalse()
        ->and(Schema::hasTable('upgrade_processes'))->toBeFalse();
});
