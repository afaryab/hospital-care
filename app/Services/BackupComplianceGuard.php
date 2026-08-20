<?php

namespace App\Services;

use RuntimeException;

/**
 * HIPAA/PHC compliance requires backups to be encrypted at rest
 * (.ai/hippa-compliance §7.1, .ai/punjab-health-care-commission-guideline-
 * compliance §13.2). spatie/laravel-backup only encrypts when
 * config('backup.backup.password') is set — it's silently optional
 * otherwise. This is the single choke point that turns "optional" into
 * "required in production," wired to the backup:run command via a
 * CommandStarting listener in AppServiceProvider::boot() rather than
 * gating application boot itself — a missing backup password should stop
 * a backup from running unencrypted, not take down patient care.
 */
class BackupComplianceGuard
{
    public static function ensureProductionBackupsAreEncrypted(): void
    {
        if (! app()->environment('production')) {
            return;
        }

        if (filled(config('backup.backup.password'))) {
            return;
        }

        throw new RuntimeException(
            'Refusing to run backup:run in production without BACKUP_ARCHIVE_PASSWORD set. '.
            'Encrypted backups are required for HIPAA/PHC compliance — set BACKUP_ARCHIVE_PASSWORD '.
            'in the environment before deploying.'
        );
    }
}
