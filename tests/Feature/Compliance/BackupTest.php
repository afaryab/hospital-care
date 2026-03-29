<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Spatie\Backup\Events\UnhealthyBackupWasFound;
use Spatie\Backup\Notifications\EventHandler;
use Spatie\Backup\Notifications\Notifiable;
use Spatie\Backup\Notifications\Notifications\UnhealthyBackupWasFoundNotification;
use Symfony\Component\Process\ExecutableFinder;
use ZipArchive;

beforeEach(function () {
    config([
        'backup_test' => [
            'backup' => [
                'name' => 'hospital-care-test-backup',
                'source' => [
                    'files' => [
                        'include' => [base_path('tests')],
                        'exclude' => [],
                        'follow_links' => false,
                        'ignore_unreadable_directories' => false,
                        'relative_path' => null,
                    ],
                    'databases' => [],
                ],
                'destination' => [
                    'disks' => ['local'],
                ],
            ],
            'monitor_backups' => [
                [
                    'name' => 'hospital-care-test-backup',
                    'disks' => ['local'],
                    'health_checks' => [
                        \Spatie\Backup\Tasks\Monitor\HealthChecks\MaximumAgeInDays::class => 1,
                    ],
                ],
            ],
            'cleanup' => [
                'strategy' => \Spatie\Backup\Tasks\Cleanup\Strategies\DefaultStrategy::class,
                'default_strategy' => [
                    'keep_all_backups_for_days' => 7,
                    'keep_daily_backups_for_days' => 7,
                    'keep_weekly_backups_for_weeks' => 4,
                    'keep_monthly_backups_for_months' => 12,
                    'keep_yearly_backups_for_years' => 1,
                    'delete_oldest_backups_when_using_more_megabytes_than' => null,
                ],
            ],
        ],
    ]);

    Storage::disk('local')->deleteDirectory('hospital-care-test-backup');
});

test('backup command runs successfully', function () {
    $exitCode = Artisan::call('backup:run', [
        '--only-files' => true,
        '--disable-notifications' => true,
        '--config' => 'backup_test',
    ]);

    expect($exitCode)->toBe(0);
});

test('backup creates a zip file with a database dump', function () {
    if (! (new ExecutableFinder)->find('sqlite3')) {
        $this->markTestSkipped('sqlite3 binary not available for sqlite database dumps.');
    }

    $sqlitePath = storage_path('framework/testing/backup-test.sqlite');

    if (file_exists($sqlitePath)) {
        unlink($sqlitePath);
    }

    touch($sqlitePath);

    config([
        'database.connections.sqlite_backup_test' => [
            'driver' => 'sqlite',
            'database' => $sqlitePath,
            'prefix' => '',
        ],
        'backup_test_db' => [
            'backup' => [
                'name' => 'hospital-care-test-backup',
                'source' => [
                    'files' => [
                        'include' => [base_path('tests')],
                        'exclude' => [],
                        'follow_links' => false,
                        'ignore_unreadable_directories' => false,
                        'relative_path' => null,
                    ],
                    'databases' => ['sqlite_backup_test'],
                ],
                'destination' => [
                    'disks' => ['local'],
                ],
            ],
            'monitor_backups' => [
                [
                    'name' => 'hospital-care-test-backup',
                    'disks' => ['local'],
                    'health_checks' => [
                        \Spatie\Backup\Tasks\Monitor\HealthChecks\MaximumAgeInDays::class => 1,
                    ],
                ],
            ],
            'cleanup' => [
                'strategy' => \Spatie\Backup\Tasks\Cleanup\Strategies\DefaultStrategy::class,
                'default_strategy' => [
                    'keep_all_backups_for_days' => 7,
                    'keep_daily_backups_for_days' => 7,
                    'keep_weekly_backups_for_weeks' => 4,
                    'keep_monthly_backups_for_months' => 12,
                    'keep_yearly_backups_for_years' => 1,
                    'delete_oldest_backups_when_using_more_megabytes_than' => null,
                ],
            ],
        ],
    ]);

    \Illuminate\Support\Facades\DB::connection('sqlite_backup_test')
        ->statement('CREATE TABLE IF NOT EXISTS backup_probe (id INTEGER PRIMARY KEY, name TEXT)');

    $filename = 'backup-with-db-'.Str::uuid().'.zip';

    $exitCode = Artisan::call('backup:run', [
        '--filename' => $filename,
        '--disable-notifications' => true,
        '--config' => 'backup_test_db',
    ]);

    expect($exitCode)->toBe(0);

    $disk = Storage::disk('local');
    $backupPath = 'hospital-care-test-backup/'.$filename;

    expect($disk->exists($backupPath))->toBeTrue();

    $tmpZipPath = storage_path('framework/testing/'.$filename);
    file_put_contents($tmpZipPath, $disk->get($backupPath));

    $zip = new ZipArchive;
    $opened = $zip->open($tmpZipPath);

    expect($opened)->toBeTrue();

    $hasDatabaseDump = false;

    for ($i = 0; $i < $zip->numFiles; $i++) {
        $name = $zip->getNameIndex($i);

        if (is_string($name) && str_contains($name, 'db-dumps/')) {
            $hasDatabaseDump = true;
            break;
        }
    }

    $zip->close();

    expect($hasDatabaseDump)->toBeTrue();

    if (file_exists($tmpZipPath)) {
        unlink($tmpZipPath);
    }

    if (file_exists($sqlitePath)) {
        unlink($sqlitePath);
    }
});

test('backup retention policy cleans old backups', function () {
    expect(config('backup.cleanup.default_strategy.keep_daily_backups_for_days'))->toBe(7)
        ->and(config('backup.cleanup.default_strategy.keep_weekly_backups_for_weeks'))->toBe(4)
        ->and(config('backup.cleanup.default_strategy.keep_monthly_backups_for_months'))->toBe(12);

    $exitCode = Artisan::call('backup:clean', [
        '--disable-notifications' => true,
        '--config' => 'backup_test',
    ]);

    expect($exitCode)->toBe(0);
});

test('backup health check notifies on failure', function () {
    Notification::fake();
    EventHandler::enable();

    Event::dispatch(new UnhealthyBackupWasFound(
        diskName: 'local',
        backupName: 'hospital-care-test-backup',
        failureMessages: collect([
            [
                'check' => 'MaximumAgeInDays',
                'message' => 'No backup present',
            ],
        ]),
    ));

    Notification::assertSentTo(
        app(Notifiable::class),
        UnhealthyBackupWasFoundNotification::class
    );
});
