# Fix #015 — Encrypt Clinical Narrative & Certificate PII, Keyed CNIC Hash, Version-Snapshot Leak

**GitHub Issue:** [afaryab/hospital-care#66](https://github.com/afaryab/hospital-care/issues/66)
**Severity:** High
**Status:** ✅ Fixed
**Branch:** `feat/encrypt-clinical-and-certificate-pii`
**Date:** 2026-08-19

---

## For Developers

### What was wrong

1. `TreatmentRecord`'s clinical narrative fields (`chief_complaint`, `history_of_present_illness`, `diagnosis_text`, `treatment_plan`, `outcome_notes`, `referral_to`) and JSON fields (`examination_findings`, `prescriptions`, `department_specific_data`, `dental_chart`) were stored as plaintext — clinical PHI at rest, unencrypted.
2. `BirthCertificate`/`DeathCertificate` stored `mother_cnic`/`father_cnic`/`informant_cnic` and names (`child_name`, `mother_name`, `father_name`, `informant_name`) as plaintext. `ReferralCertificate.notes` (the doctor-authored referral letter body) was also plaintext.
3. `patients.cnic_hash`/`contact_hash` — the blind index used for lookup on top of the already-`SafeEncrypted` `cnic`/`contact` columns — was unsalted, unkeyed `hash('sha256', ...)`, duplicated independently across 9 call sites (`PatientObserver`, `PateintController`, `WebController`, `PatientImporter`). Given a CNIC's small keyspace, this is realistically rebuildable offline from a database dump.
4. `PatientVersion`/`TreatmentRecordVersion`/`ServiceOrderVersion` snapshot the parent record's `getOriginal()` on every update. Eloquent's `getOriginal()` applies the attribute's cast `get()` — so it returns the **decrypted** value for any `SafeEncrypted`/`encrypted:json` field — and the `snapshot` column was cast as plain `'array'`. Every edit to a Patient/TreatmentRecord/ServiceOrder was writing a full plaintext copy of otherwise-encrypted PII/PHI straight into the audit-trail table, undermining the encryption on the live column.

### What was added

**`App\Casts\SafeEncryptedJson`** — a JSON-aware sibling of the existing `SafeEncrypted` cast (same legacy-plaintext-fallback design), for array/JSON-cast columns.

**`App\Helpers\PiiHasher`** — centralizes CNIC/contact blind-index hashing as a keyed HMAC (`hash_hmac('sha256', $normalized, config('app.key'))`), replacing the 9 duplicated unsalted-SHA-256 call sites.

**Casts applied:**
- `TreatmentRecord`: `SafeEncrypted` on the 6 narrative fields, `SafeEncryptedJson` on the 4 JSON fields.
- `BirthCertificate`: `SafeEncrypted` on `child_name`, `mother_name`, `mother_cnic`, `father_name`, `father_cnic`, `place_of_birth`, `remarks`.
- `DeathCertificate`: `SafeEncrypted` on `place_of_death`, `antecedent_cause`, `informant_name`, `informant_relation`, `informant_cnic`, `remarks`.
- `ReferralCertificate`: `SafeEncrypted` on `notes` (gained a `casts()` method — didn't have one before). `receiving_facility_name` was left plaintext — a facility name, not patient PII.
- `PatientVersion`/`TreatmentRecordVersion`/`ServiceOrderVersion`: `snapshot` switched from `'array'` to `SafeEncryptedJson::class`, closing the audit-trail leak for all three.

**Migrations:**
- `2026_08_19_080403_rehash_patient_pii_with_keyed_hmac.php` — rehashes every existing `patients.cnic_hash`/`contact_hash` with the new keyed HMAC.
- `2026_08_19_080404_encrypt_existing_certificate_pii.php` — widens the birth/death certificate name/CNIC columns from `string` to `text` (an encrypted payload runs longer than the plaintext it replaces) and bulk-encrypts existing plaintext rows across all three certificate tables.
- `2026_08_19_080405_encrypt_existing_treatment_record_pii.php` — widens `examination_findings`/`prescriptions`/`department_specific_data`/`dental_chart` from MySQL's native `json` type to `longText` (a `json`-typed column rejects non-JSON ciphertext — the same reason `service_orders.notes_json` was widened in the original `2026_03_29_094318` encryption migration) and bulk-encrypts existing plaintext rows.
- The three Version tables were **not** bulk-migrated — `SafeEncryptedJson`'s legacy-plaintext fallback means existing snapshot rows keep reading correctly without a backfill, matching the same fallback philosophy `SafeEncrypted` already uses for `Patient`.

### Files changed

- `app/Casts/SafeEncryptedJson.php` — new
- `app/Helpers/PiiHasher.php` — new
- `app/Models/TreatmentRecord.php`, `BirthCertificate.php`, `DeathCertificate.php`, `ReferralCertificate.php`, `PatientVersion.php`, `TreatmentRecordVersion.php`, `ServiceOrderVersion.php`
- `app/Observers/PatientObserver.php`, `app/Http/Controllers/Api/PateintController.php`, `app/Http/Controllers/WebController.php`, `app/Filament/Imports/PatientImporter.php` — swapped raw `hash('sha256', ...)` for `PiiHasher::cnic()`/`PiiHasher::contact()`
- 3 new migrations (listed above)
- `tests/Feature/Compliance/DataEncryptionTest.php` — 7 new tests; `tests/Feature/Compliance/PiiHasherTest.php` — new; `tests/Feature/Compliance/DuplicatePatientTest.php` — updated to assert against `PiiHasher::cnic()` instead of the retired formula
- `tests/Feature/Filament/Admin/BirthCertificateResourceTest.php`, `DeathCertificateResourceTest.php` — 2 tests each updated: `assertDatabaseHas()` against now-encrypted columns doesn't match ciphertext, switched to asserting through the model (where the cast transparently decrypts)

### Tests

```bash
php -d memory_limit=1024M vendor/bin/pest --compact
```

879 tests, 0 failures (11 new).

### What is NOT yet covered

- Version-snapshot rows written **before** this fix remain plaintext in the database (not bulk-migrated, by design — see above). Anyone with direct DB access can still read pre-fix snapshot history in plaintext; only new snapshots going forward are encrypted.
- `diagnosis_code` (ICD-10 code) and `receiving_facility_name` were deliberately left plaintext — not considered identifying PHI on their own.
- API rate limiting/versioning, treatment attachment access control, and MFA enforcement are tracked in separate fixes (#59, #65, #63 respectively — see this index).

---

## For IT / DevOps

### What changed on the server

- 3 new migrations: one backfills `patients.cnic_hash`/`contact_hash`, two widen and bulk-encrypt columns on `treatment_records` and the three certificate tables. All three chunk through existing rows (200 at a time) — safe for large tables, but will take longer on hospitals with a large existing patient/treatment history.
- No new environment variables — the keyed HMAC and all new encryption reuse the existing `APP_KEY`.

### Deployment steps

1. Pull the latest code.
2. **Back up the database before migrating** — these migrations rewrite existing PII/PHI columns in place.
3. Run migrations: `php artisan migrate`.
4. Standard deploy (`docker compose up --build` or equivalent).

### How to verify after deploy

1. Open an existing treatment record, birth/death/referral certificate created before this deploy — confirm all fields still display correctly (the legacy-plaintext fallback in `SafeEncrypted`/`SafeEncryptedJson` means pre-migration data that somehow wasn't caught by the bulk-encrypt migration still reads fine either way).
2. Search for a patient by CNIC — confirm it still finds the right record (validates the rehashed `cnic_hash` migration ran correctly).
3. Spot-check via `mysql`/phpMyAdmin: `SELECT chief_complaint FROM treatment_records LIMIT 1` and the equivalent for a certificate table — should show ciphertext, not readable text.

### Rollback

Revert the application files. **Do not roll back the migrations** on a production database that has since received new writes — the down-migrations for the two bulk-encrypt migrations are no-ops by design (there's no safe automatic way to know which rows were plaintext-before vs encrypted-by-this-migration once new writes have layered on top). If a rollback is genuinely required, restore from the pre-deploy backup instead.

### Risk of this change

**Medium.** This is the first migration in this series that rewrites existing PHI columns in place at scale (chunked, but still a full-table pass on `treatment_records`, `birth_certificates`, `death_certificates`, `referral_certificates`, and `patients`). Take a backup first. The migrations are idempotent (safe to rerun — `encryptIfNeeded()` skips values that are already encrypted), so a failed/interrupted migration run can simply be retried.

---

## For Reception Staff

### Does anything look different?

**No.** Certificates, treatment records, and patient search all work exactly as before — the data is now encrypted at rest, which is invisible to normal app usage.

---

## For Hospital Administration

### Business risk mitigated

| Risk | Before fix | After fix |
|---|---|---|
| Clinical notes (chief complaint, diagnosis, treatment plan, prescriptions) readable in plaintext from a database dump/backup | Yes | No — encrypted at rest |
| Birth/death certificate parent/informant names and CNICs readable in plaintext | Yes | No — encrypted at rest |
| CNIC/contact blind index rebuildable offline without the application key | Yes (unsalted SHA-256) | No — keyed HMAC, requires `APP_KEY` |
| Every edit to a patient/treatment/service-order record writing a full plaintext copy of encrypted PII/PHI into the audit-trail table | Yes | No — audit-trail snapshots now encrypted too |

### Compliance relevance

**HIPAA & PHC Guidelines** require encryption at rest for ePHI (Section 7.1 of `.ai/hippa-compliance`) and integrity of any blind-indexing mechanism used for searchable encrypted data. This fix closes the largest remaining gap in at-rest encryption coverage identified in the compliance audit, and specifically fixes a subtle but serious issue where the system's own audit-trail (built for compliance) was itself leaking the PHI it was supposed to be tracking changes to.

### Financial impact

No cost to deploy. Downtime should be minimal, but budget for migration runtime proportional to existing data volume (patients, treatment records, and certificates), and take a database backup first as a safety net.
