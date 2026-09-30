# Fix #035: Record Edits Failed Because Encrypted Version Snapshots Were Rejected by MySQL

**GitHub Issue:** [afaryab/hospital-care#103](https://github.com/afaryab/hospital-care/issues/103)
**Severity:** High (blocks record edits; audit trail)
**Status:** ✅ Fixed
**Branch:** `fix/number-generators-skip-soft-deleted-rows`
**Date:** 2026-09-30

---

## For Developers

### Root cause

`be4a1b9` (#66) moved `PatientVersion`, `TreatmentRecordVersion` and `ServiceOrderVersion` `snapshot` onto the `SafeEncryptedJson` cast, so that the audit trail stopped storing decrypted PII/PHI. That cast writes Laravel ciphertext, which is base64 and not JSON. The three `snapshot` columns, however, were created as native MySQL `json` in `2026_03_29_100950_add_immutability_and_versions_to_patient_records.php` and were never widened. `treatment_records` had already been widened to `longText` for exactly this reason, in `2026_08_19_080405`.

MySQL validates `json` columns and rejected every write:

```
SQLSTATE[22032]: 3140 Invalid JSON text: "Invalid value." at position 0 in value for column 'patient_versions.snapshot'
```

The tests run on SQLite, which does not validate JSON, so the suite stayed green.

### What was changed

New migration `2026_09_30_141636_widen_and_encrypt_version_snapshots.php`:

- It changes `patient_versions.snapshot`, `treatment_record_versions.snapshot` and `service_order_versions.snapshot` to `longText`.
- It encrypts existing snapshots that are still plain JSON. They were written before #66 and hold PII/PHI.
  - Only values that are valid JSON get encrypted. Anything else is already ciphertext, possibly under an older key, and is left untouched, so nothing can be double-encrypted.
  - It's idempotent and works in chunks of 200.
- `down()` is a real rollback. It first checks that **every** snapshot decrypts to valid JSON and aborts with no changes if any doesn't. Then it decrypts them and restores the `json` columns. Rolling back re-exposes PII/PHI and brings the edit failure back, so use it only in an emergency.

`transaction_versions.snapshot` stays `json`: `TransactionVersion` uses a plain `array` cast, so it is unaffected.

### Tests

`tests/Feature/Compliance/DataEncryptionTest.php` has a new test, "version snapshot columns are widened and legacy plaintext snapshots get encrypted", with one dataset case per table. It seeds a plaintext snapshot, runs the migration twice (proving it's idempotent), and asserts that the column isn't `json`, the raw value has no plaintext, and the value decrypts back to the original. Three more tests prove that:
- ciphertext from a foreign key is never double-encrypted;
- rollback restores plain JSON;
- rollback aborts with no changes when a snapshot can't be decrypted.

The encryption and immutability test files pass: 23 tests, 63 assertions. On local MySQL, `migrate` → `rollback` → `migrate` round-trips correctly: `json` with plain JSON, then `longtext` with ciphertext that decrypts through the model.

## For IT / DevOps

- **Before deploying:**
  - Take a full database backup.
  - Confirm `APP_KEY`, plus `APP_PREVIOUS_KEYS` if the key was ever rotated, is the production key and is backed up **outside** the database backups. Without it, encrypted snapshots, like every other encrypted field since #66, can't be read.
- **Deploy:** `php artisan migrate --force`. It alters three columns (MySQL copies each table, briefly blocking writes to it) and encrypts legacy rows. Runtime grows with the number of version rows; locally it took well under a second.
- **Rollback:** `php artisan migrate:rollback --step=1` decrypts the snapshots and restores `json`. It refuses to run, changing nothing, if any snapshot can't be decrypted with the current keys. After a rollback, edits fail again, so only roll back together with the code.
- **Affected releases:** v0.10.2 to v0.10.4 on MySQL, where patient, treatment and service-order edits fail until this migration runs.

## For Reception Staff

Editing a patient's details (and, for doctors, a treatment record or service order) could fail with an error and lose the change. That no longer happens. Nothing else changes.

## For Hospital Administration

- **Operations:** since the August encryption update, every edit to a patient, treatment record or service order failed on the production database, so corrections couldn't be saved. Edits work again.
- **Compliance (PHC §5 medico-legal integrity, HIPAA audit controls):** each edit again records a version of the previous data, so the change history is complete. Older history entries that still held CNIC, contact and clinical details in plain text are now encrypted at rest, as HIPAA §7 requires.
