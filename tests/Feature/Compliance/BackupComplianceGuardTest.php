<?php

use App\Services\BackupComplianceGuard;

test('does nothing outside production, regardless of password', function () {
    $this->app['env'] = 'testing';
    config(['backup.backup.password' => null]);

    BackupComplianceGuard::ensureProductionBackupsAreEncrypted();

    expect(true)->toBeTrue();
});

test('throws in production when no backup password is configured', function () {
    $this->app['env'] = 'production';
    config(['backup.backup.password' => null]);

    expect(fn () => BackupComplianceGuard::ensureProductionBackupsAreEncrypted())
        ->toThrow(RuntimeException::class, 'BACKUP_ARCHIVE_PASSWORD');
});

test('does not throw in production once a backup password is configured', function () {
    $this->app['env'] = 'production';
    config(['backup.backup.password' => 'a-real-secret']);

    BackupComplianceGuard::ensureProductionBackupsAreEncrypted();

    expect(true)->toBeTrue();
});
