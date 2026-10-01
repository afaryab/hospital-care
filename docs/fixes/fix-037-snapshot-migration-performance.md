# Fix #037: Snapshot Encryption Migration Blocked Production Deploys for Hours

**GitHub Issue:** [afaryab/hospital-care#105](https://github.com/afaryab/hospital-care/issues/105)
**Severity:** High (blocks production deployment)
**Status:** ✅ Fixed
**Branch:** `fix/version-snapshot-migration-performance`
**Date:** 2026-10-01

---

## For Developers

### Root cause

`2026_09_30_141636_widen_and_encrypt_version_snapshots` (#103, fix-035) did two things in one migration:

1. **Changed `snapshot` from `json` to `longtext`** on the three version tables. On MySQL that's a full table copy (ALGORITHM=COPY), which blocks writes to each table while it runs.
2. **Encrypted every legacy plaintext snapshot with one `UPDATE` per row.** MySQL migrations aren't wrapped in a transaction (no transactional DDL), so every row was its own commit and redo-log flush.

On a database with about 6 million treatment records, step 2 runs for hours. Every migration queued behind it waits that long.

### What was changed

- **The migration now only widens the columns.** It also skips any table whose `snapshot` column is no longer `json`, so re-running after an interrupted deploy costs nothing for tables already converted.
- **The encryption pass is now `php artisan versions:encrypt-snapshots`** (`App\Console\Commands\EncryptVersionSnapshots`, backed by `App\Services\VersionSnapshotEncryption`). It:
  - walks rows by primary key and commits **once per batch** (default 1,000 rows);
  - only encrypts values that are valid JSON, so ciphertext under an older key is never re-encrypted;
  - is idempotent and resumable, and safe while the app is live;
  - has `--table=…`, `--batch=…` and `--dry-run` options.
- **`down()`** uses the same service: it checks that every snapshot decrypts, decrypts in batches, and restores `json`. It aborts, changing nothing, if any row can't be decrypted.
- **The app doesn't depend on the backfill.** `SafeEncryptedJson` already reads plain JSON as well as ciphertext, so the app is correct before, during and after the command. New versions are written encrypted.

### Measured on MySQL 8 (local Docker, synthetic rows of ~470 bytes)

| Step | Rows | Time | Per row | Extrapolated to 6M |
|---|---|---|---|---|
| Migration (table copy only) | 200,000 | 4 s | — | about 2 min (depends on hardware and row size) |
| Re-run on already-converted table | 200,000 | 9 ms | — | instant |
| Old behaviour (one commit per row) | 20,000 | 85 s | ~4.2 ms | about 7 h, and it blocked the deploy |
| `versions:encrypt-snapshots` (batches of 1,000) | 200,000 | 107 s | ~0.53 ms | about 55 min, while the app keeps running |

### Tests

`tests/Feature/Compliance/DataEncryptionTest.php` covers:
- the command encrypts on all three tables, and is idempotent;
- batches, and a dry run that changes nothing;
- unknown tables are rejected;
- foreign-key ciphertext is never double-encrypted;
- the migration no longer rewrites rows;
- rollback round-trips;
- rollback aborts with no changes when a row can't be decrypted.

## For IT / DevOps

### If the old migration is running in production right now

1. **Find it:** `SHOW FULL PROCESSLIST;`
   - State **"copy to tmp table"** / an `ALTER` means a table copy is in progress.
   - Single-row `update … _versions` statements mean the encryption pass is running.
2. **Stop it:** kill the `php artisan migrate` process **and** `KILL <id>;` its MySQL connection. MySQL can keep running a statement after the client goes away.
   - If it's stopped during a table copy, MySQL discards the copy and the table keeps its old type.
   - If it's stopped during encryption, already-encrypted rows stay encrypted.
   - Nothing is lost either way.
3. **Deploy this fix**, then run `php artisan migrate`.
   - The snapshot migration only copies tables that are still `json`, and skips the ones already converted. The migrations after it then run normally.
   - Large tables are still copied once, which blocks writes to that table only. For very large `*_versions` tables, run it in a quiet window or use `pt-online-schema-change` / `gh-ost`.
4. **Encrypt the remaining plaintext rows while the system runs.** In the CLI container, use `nohup` or `screen`:
   ```bash
   php artisan versions:encrypt-snapshots --dry-run    # how many are left
   php artisan versions:encrypt-snapshots              # encrypt (resumable)
   ```

### Other notes

- **Back up first**, and keep `APP_KEY` (and any `APP_PREVIOUS_KEYS`) backed up **outside** the database backups.
- **Rollback:** `php artisan migrate:rollback` for this migration decrypts the snapshots and restores `json`. It refuses to run if any snapshot can't be decrypted.

## For Reception Staff

Nothing changes for you. This only affects how the update is installed.

## For Hospital Administration

- **Operations:** the database update that encrypts old change-history records no longer holds up the rest of the release. Urgent fixes queued behind it go out in minutes instead of waiting hours.
- **Compliance:** older history entries still get encrypted, as a background job that runs while the hospital keeps working. Until it finishes, those older entries stay readable in the database only by staff with database access, the same as before the update.
