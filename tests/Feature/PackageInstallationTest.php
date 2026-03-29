<?php

use Illuminate\Support\Facades\Schema;

test('spatie medialibrary migration runs successfully', function () {
    expect(Schema::hasTable('media'))->toBeTrue();
});

test('spatie activitylog migration runs successfully', function () {
    expect(Schema::hasTable('activity_log'))->toBeTrue();
});

test('spatie permission migration runs successfully', function () {
    expect(Schema::hasTable('permissions'))->toBeTrue()
        ->and(Schema::hasTable('roles'))->toBeTrue()
        ->and(Schema::hasTable('model_has_permissions'))->toBeTrue()
        ->and(Schema::hasTable('model_has_roles'))->toBeTrue();
});
