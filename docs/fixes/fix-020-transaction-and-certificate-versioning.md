# Fix #020 — Transaction Version History + Soft Deletes, Death/Referral Certificate Locks

**GitHub Issue:** [afaryab/hospital-care#76](https://github.com/afaryab/hospital-care/issues/76)
**Severity:** Medium
**Status:** ✅ Fixed
**Branch:** `feat/transaction-and-certificate-versioning`
**Date:** 2026-08-19

---

## For Developers

### What was wrong

- `Patient`, `ServiceOrder`, and `TreatmentRecord` all snapshot the pre-change record into a dedicated `{Model}Version` table on every update (see `2026_03_29_100950_add_immutability_and_versions_to_patient_records.php`). `Transaction` — a financial record, arguably as compliance-sensitive as any of those three — had no equivalent. It also had **no `SoftDeletes`**, meaning `Api\TransactionController::destroy()` performed a genuine hard delete, in direct violation of `.ai/product`'s "5.1 Immutable Records" / "5.2 Soft Deletes Only" rules. (That `destroy()` route currently isn't even wired up in `routes/api.php` — a separate, unrelated finding, left for the upcoming route-hygiene pass — but the model-level gap exists regardless of whether the route is reachable today.)
- `BirthCertificate` already has a full `is_locked`/`locked_at`/`locked_by` finalization lock with a Filament "Lock Certificate" action (`EditBirthCertificate.php`). `DeathCertificate` and `ReferralCertificate` had no equivalent — either could be silently edited after issuance with no record that it happened.

### What was added

**`transaction_versions`** table and `TransactionVersion` model, structurally identical to `patient_versions`/`service_order_versions`/`treatment_record_versions`. Wired into the existing `TransactionObserver::updating()` (Transaction's lifecycle logic already lives in an Observer, not a model `booted()` hook — unlike the other three, which are model-driven — so the snapshot call was added there to stay consistent with how this specific model already does things, rather than introducing a second, competing hook location). Quiet writes (`recalculatePayment()`'s `updateQuietly()`, the observer's own `edited_amount` tracking via `saveQuietly()`) bypass observers entirely, so only genuine edits are captured — matching the existing precedent's behavior for the other three models.

**`SoftDeletes`** added to `Transaction`, plus a `deleting()` guard in `TransactionObserver` that blocks `forceDelete()`, mirroring `Patient`/`TreatmentRecord`/`BirthCertificate`'s identical pattern.

**`is_locked`/`locked_at`/`locked_by`** added to `DeathCertificate` and `ReferralCertificate`, with the identical `static::updating()` guard `BirthCertificate` already uses (rejects any edit once `is_locked` is set), plus a matching "Lock Certificate" header action added to `EditDeathCertificate`/`EditReferralCertificate`.

### Files changed

- `database/migrations/2026_08_19_112901_add_transaction_versions_and_soft_deletes.php`, `2026_08_19_112902_add_finalization_lock_to_death_and_referral_certificates.php` — new
- `app/Models/TransactionVersion.php` — new
- `app/Models/Transaction.php` — `SoftDeletes`, `versions(): HasMany`
- `app/Observers/TransactionObserver.php` — snapshot-on-update, hard-delete guard
- `app/Models/DeathCertificate.php`, `ReferralCertificate.php` — lock fields, casts, guard, `lockedBy()` relation
- `app/Filament/Admin/Resources/DeathCertificates/Pages/EditDeathCertificate.php`, `ReferralCertificates/Pages/EditReferralCertificate.php` — lock action
- `database/factories/DeathCertificateFactory.php`, `ReferralCertificateFactory.php` — `locked()` state
- Tests: `tests/Feature/Compliance/TransactionVersioningTest.php`, `CertificateFinalizationLockTest.php` — new; `Filament/Admin/DeathCertificateResourceTest.php`, `ReferralCertificateResourceTest.php` — 2 new tests each

### Tests

```bash
php -d memory_limit=1024M vendor/bin/pest --compact
```

892 tests, 0 failures (19 new).

**Migration verification note**: the two new migrations were exercised repeatedly and successfully via the full Pest suite (`RefreshDatabase` runs every migration file, including both new ones, against a real SQLite database on every test run) — this is genuine end-to-end verification, not just a syntax check. A separate live-MySQL run (the usual extra step for this series of fixes) wasn't completed this round: this machine had a second, unrelated Docker Compose project actively holding the same default host ports (3306, 6379) for its own MySQL/Redis, and forcing a takeover would have disrupted that concurrent work. The migrations use the same patterns (identical column types, identical `Schema::table()`/`softDeletes()`/`constrained()` calls) as the precedent migration they're modeled on, which has already run cleanly in production-equivalent MySQL as part of earlier work in this series.

### What is NOT yet covered

- `TransactionController::destroy()` still isn't reachable from any route — the soft-delete/hard-delete-guard fix here makes the *model* safe regardless, but the missing route registration is a separate finding for the upcoming route-table-hygiene pass.
- No Filament UI surfaces `Transaction`'s new version history yet (no "view history" page/tab) — the data is captured and queryable (`Transaction::versions()`), but there's no admin-facing view of it, matching the current state of `Patient`/`ServiceOrder`/`TreatmentRecord` versions (none of those have a dedicated history UI either — this fix keeps `Transaction` at parity with the existing pattern, not ahead of it).

---

## For IT / DevOps

### What changed on the server

- Two migrations: one creates `transaction_versions` and adds `deleted_at` to `transactions`; the other adds 3 columns (`is_locked`, `locked_at`, `locked_by`) to `death_certificates` and `referral_certificates` each. No data migration/backfill needed — all new columns are nullable or default to a safe value (`is_locked` defaults `false`).

### Deployment steps

1. Pull the latest code.
2. Run migrations: `php artisan migrate`.
3. Standard deploy (`docker compose up --build` or equivalent).

### How to verify after deploy

1. Edit and save an existing transaction (if reachable through your workflow) — confirm it still saves correctly, and check `transaction_versions` has a new row.
2. Open a death or referral certificate's edit page — confirm a "Lock Certificate" button now appears, and that locking it correctly blocks further edits.
3. Confirm normal transaction creation/payment flows are unaffected — this fix doesn't change any existing transaction behavior, only adds tracking and a delete-safety net.

### Rollback

Revert the application files. `php artisan migrate:rollback` cleanly drops the new tables/columns — no data loss beyond the version history itself, which is new data anyway.

### Risk of this change

**Low.** Purely additive — no existing Transaction/certificate behavior changes for records that are never edited after this deploys. The one behavior change with real effect: transaction deletion (if that route is ever wired up) becomes a soft delete instead of a hard delete, which is the intended fix, not a regression.

---

## For Reception Staff

### Does anything look different?

**A "Lock Certificate" button now appears on death and referral certificate edit pages** (matching the existing birth certificate behavior). Locking a certificate is permanent — once locked, it can't be edited further, so only lock a certificate once it's finalized and correct.

---

## For Hospital Administration

### Business risk mitigated

| Risk | Before fix | After fix |
|---|---|---|
| Transaction (financial record) hard-deleted with no trace, if that path is ever used | Yes | No — soft delete only, hard delete blocked |
| No audit trail of who changed a transaction's amount/details and what it was before | Only `edited_amount` (previous amount only, single field) | Full pre-change snapshot on every edit |
| Death/referral certificates silently editable after issuance | Yes | No — same finalization-lock protection birth certificates already have |

### Compliance relevance

Extends the same "Immutable Records" and "Data Integrity" principles (`.ai/product` §5.1–5.2) already applied to Patient, Service Order, and Treatment Record to Transaction and the two remaining certificate types — closing a gap where financial records and two of three certificate types were less protected than clinical records already are.

### Financial impact

No cost to deploy. No downtime required.
