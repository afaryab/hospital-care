# Fix #019 — Require Encrypted Backups in Production, 6-Year Retention

**GitHub Issue:** [afaryab/hospital-care#74](https://github.com/afaryab/hospital-care/issues/74)
**Severity:** Medium
**Status:** ✅ Fixed
**Branch:** `chore/backup-hardening`
**Date:** 2026-08-19

---

## For Developers

### What was wrong

The backup system itself was already real and working — `spatie/laravel-backup` is installed, `config/backup.php` is fully configured, and `backup:run`/`backup:clean`/`backup:monitor` are scheduled daily in `routes/console.php`. Two specific gaps against HIPAA §7.1 and PHC guideline §13.1:

1. `config/backup.php:188` reads `'password' => env('BACKUP_ARCHIVE_PASSWORD')` and `'encryption' => 'default'` — the plumbing for AES encryption exists, but `BACKUP_ARCHIVE_PASSWORD` was never defined in `.env.example`, undocumented, and nothing enforced it being set. Spatie's package silently skips encryption when the password is empty. **Every environment that hasn't manually set this env var was producing unencrypted zip backups of full patient/financial data.**
2. `config/backup.php`'s `keep_yearly_backups_for_years` was Spatie's stock default of `2` — well short of the 6-year minimum both compliance documents specify.

### What was added

**`App\Services\BackupComplianceGuard::ensureProductionBackupsAreEncrypted()`** — a small, directly-testable static method: no-ops outside `APP_ENV=production`; in production, throws a `RuntimeException` if `config('backup.backup.password')` is empty. Wired via a `CommandStarting` event listener in `AppServiceProvider::boot()`, scoped to only the `backup:run` command:

```php
Event::listen(CommandStarting::class, function (CommandStarting $event): void {
    if ($event->command === 'backup:run') {
        BackupComplianceGuard::ensureProductionBackupsAreEncrypted();
    }
});
```

**Deliberately scoped to the backup command, not application boot.** A missing backup password should stop a backup from running unencrypted — it should not take down patient registration/treatment for the whole hospital. This is the same reasoning already applied to the consent-gate work (fix #017): a hard failure mode needs to be scoped to the specific risky operation, not the entire application.

**Retention**: `keep_yearly_backups_for_years` raised to `6` (env-overridable via `BACKUP_KEEP_YEARLY_YEARS`), and the storage-cleanup ceiling (`delete_oldest_backups_when_using_more_megabytes_than`) raised from Spatie's stock 5000MB to 20000MB (`BACKUP_MAX_STORAGE_MEGABYTES`) — at the old ceiling, cleanup could start deleting yearly backups well before 6 years actually elapsed, silently undermining the retention promise regardless of the years setting.

**`config/activitylog.php`'s `clean_after_days`** corrected from 365 to 2190 (6 years, env-overridable via `ACTIVITYLOG_CLEAN_AFTER_DAYS`) — `activity_log` is this app's audit trail and falls under the same retention requirement. **This is a config-value correction only** — `activitylog:clean` is not scheduled anywhere in `routes/console.php` today (confirmed), so this doesn't newly start deleting anything; it just makes the default correct for if/when a hospital operator decides to enable pruning. Actively turning on deletion of currently-indefinitely-retained audit data is a separate, deliberate decision this fix does not make.

**`.env.example`** documents `BACKUP_ARCHIVE_PASSWORD` (with generation instructions and a note that losing it makes existing backups unrecoverable), `BACKUP_KEEP_YEARLY_YEARS`, and `BACKUP_MAX_STORAGE_MEGABYTES`.

**Drive-by cleanup**: removed the dead `use ZipArchive;` from `tests/Feature/Compliance/BackupTest.php` (a no-op `use` for a global class, firing a harmless-but-noisy PHP warning on every test run).

### Files changed

- `app/Services/BackupComplianceGuard.php` — new
- `app/Providers/AppServiceProvider.php` — wires the `CommandStarting` listener
- `config/backup.php` — `keep_yearly_backups_for_years`, `delete_oldest_backups_when_using_more_megabytes_than` now env-driven with compliant defaults
- `config/activitylog.php` — `clean_after_days` corrected default
- `.env.example` — documents the 3 new env vars
- `tests/Feature/Compliance/BackupComplianceGuardTest.php` — new; `BackupTest.php` — dead import removed, 1 new retention-assertion test

### Tests

```bash
php -d memory_limit=1024M vendor/bin/pest --compact
```

883 tests, 0 failures (4 new).

Note on testing the guard: `Illuminate\Foundation\Console\Kernel` only re-routes Symfony's `CommandStarting` event when `! $this->app->runningUnitTests()` — meaning the event never actually fires during `Artisan::call(...)` in the test suite, by Laravel design. `BackupComplianceGuard` is tested directly as a static method instead (faking `$this->app['env']` and `config('backup.backup.password')`), which is both the only way to exercise it under test and arguably better isolation than an end-to-end command-dispatch test would have been.

### What is NOT yet covered

- `activitylog:clean` is still not scheduled — audit logs are retained indefinitely today (not a compliance violation, since indefinite retention already exceeds the 6-year minimum, but worth a deliberate decision later if storage growth becomes a concern).
- No automated backup-restore drill/test exists (HIPAA §15/PHC §13.2 call for tested disaster recovery, not just backups existing) — restore testing remains a manual/operational process, not something this fix automates.
- `DmsClassification.retention_years` (per-document-classification retention metadata) remains unenforced — nothing reads it to actually archive/purge documents. Out of scope for backup-specific hardening; flagged as a separate follow-up if document-level retention enforcement becomes a priority.

---

## For IT / DevOps

### What changed on the server

- No schema changes, no migration.
- **Action required for any production deployment**: set `BACKUP_ARCHIVE_PASSWORD` in your environment before this deploys, or `backup:run` will start failing (by design — see below). Generate one with `php artisan tinker --execute "echo Str::random(32);"` and store it in your secrets manager, not in `.env` itself if that file is ever committed or shared.
- Optional: `BACKUP_KEEP_YEARLY_YEARS` and `BACKUP_MAX_STORAGE_MEGABYTES` if the compliant defaults (6 years, 20GB) don't match your hospital's actual storage budget — but changing them away from the defaults should be a documented decision, not a reflex.

### Deployment steps

1. **Before deploying**: set `BACKUP_ARCHIVE_PASSWORD` in the production environment. **Store it somewhere durable and separate from the app** — losing it makes every existing encrypted backup unrecoverable.
2. Pull the latest code, standard deploy (`docker compose up --build` or equivalent). No migrations to run.
3. Verify: run `php artisan backup:run` manually once post-deploy and confirm it completes (exit code 0) rather than throwing the `BackupComplianceGuard` error.

### How to verify after deploy

1. `php artisan backup:run` completes successfully.
2. Download the resulting backup zip and confirm it prompts for a password when opened (proof of encryption) — or check the backup destination disk directly and confirm the zip can't be opened without the password.
3. `php artisan tinker --execute "echo config('backup.cleanup.default_strategy.keep_yearly_backups_for_years');"` → should print `6` (or your documented override).

### Rollback

Revert the application files. No data changes to undo — this fix only changes config defaults and adds a pre-flight guard; no backups are deleted, no data is migrated.

### Risk of this change

**Medium for production deploys specifically, low everywhere else.** Any hospital that deploys this to a production environment (`APP_ENV=production`) without first setting `BACKUP_ARCHIVE_PASSWORD` will see their scheduled `backup:run` start failing loudly instead of silently producing unencrypted backups — **this is the intended behavior**, but it means backups genuinely stop working until the password is set, which is why the deployment step above is called out explicitly rather than left to be discovered from a failed cron log. Non-production environments (local/staging/testing) are entirely unaffected — the guard only activates under `APP_ENV=production`.

---

## For Reception Staff

### Does anything look different?

**No.** This is an infrastructure-level change with no visible effect on daily workflows.

---

## For Hospital Administration

### Business risk mitigated

| Risk | Before fix | After fix |
|---|---|---|
| Database backups (full patient/financial records) stored unencrypted at rest | Yes, unless someone had manually configured a password (undocumented) | No — production refuses to run an unencrypted backup |
| Backup retention (2 years) falling short of the legally-relevant 6-year record-keeping window | Yes | No — 6 years by default |
| No documentation telling deployers a backup password was even expected | Correct — nothing existed | `.env.example` now documents it with generation instructions |

### Compliance relevance

**HIPAA §7.1 (Encryption)** and **PHC guideline §13.1 (Data Retention)** both directly addressed. This closes a real, silent gap: a hospital that deployed this software without separately researching Spatie's backup-encryption option would have had every database backup — full patient names, CNICs, financial records — sitting unencrypted, potentially on a shared or cloud storage disk, with 2-year retention rather than the compliance-required 6.

### Financial impact

No cost to deploy. **Action required before production deploy**: generate and securely store `BACKUP_ARCHIVE_PASSWORD` — this is a one-time operational step, not a recurring cost. Storage costs may increase modestly from the extended retention (2→6 years) and raised cleanup ceiling; size this against your actual backup volume rather than assuming the defaults are free.
