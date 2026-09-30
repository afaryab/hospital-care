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
- It encrypts any existing snapshot that is still plaintext. Those were written before #66 and still hold PII/PHI.
  - It's idempotent: values that already decrypt are left alone, and it works in chunks of 200.
- `down()` is a no-op, because encrypted snapshots can't go back into a `json` column.

`transaction_versions.snapshot` stays `json`: `TransactionVersion` uses a plain `array` cast, so it is unaffected.

### Tests

`tests/Feature/Compliance/DataEncryptionTest.php` has a new test, "version snapshot columns are widened and legacy plaintext snapshots get encrypted", with one dataset case per table. It seeds a plaintext snapshot, runs the migration twice (proving it's idempotent), and asserts three things:
- the column isn't `json`;
- the raw value contains no plaintext;
- the value decrypts back to the original.

The encryption and immutability test files pass: 20 tests, 58 assertions. On local MySQL, all three columns are now `longtext` and no plaintext patient snapshots remain.

## For IT / DevOps

- **Deploy:** `php artisan migrate --force`. The migration alters three columns and re-encrypts legacy rows; runtime scales with the number of version rows (about 140 ms locally).
- **Back up before migrating.** The migration is not reversible. `down()` does nothing, and restoring the old `json` type would need the snapshots decrypted first.
- `APP_KEY` must be the production key when the migration runs, because it encrypts with it.

## For Reception Staff

Editing a patient's details (and, for doctors, a treatment record or service order) could fail with an error and lose the change. That no longer happens. Nothing else changes.

## For Hospital Administration

- **Operations:** since the August encryption update, every edit to a patient, treatment record or service order failed on the production database, so corrections couldn't be saved. Edits work again.
- **Compliance (PHC §5 medico-legal integrity, HIPAA audit controls):** each edit again records a version of the previous data, so the change history is complete. Older history entries that still held CNIC, contact and clinical details in plain text are now encrypted at rest, as HIPAA §7 requires.
