# Fix #022 — Number Generators Ignored Soft-Deleted Rows

**GitHub Issue:** [afaryab/hospital-care#88](https://github.com/afaryab/hospital-care/issues/88)
**Severity:** Critical
**Status:** ✅ Fixed
**Branch:** `fix/number-generators-skip-soft-deleted-rows`
**Date:** 2026-09-09

---

## For Developers

### What was wrong

Production, 2026-09-09 09:04:52:

```
SQLSTATE[23000]: Integrity constraint violation: 1062 Duplicate entry 'TR/2026/09/09/0087'
for key 'transactions.transactions_tr_number_unique'
```

An admin had soft-deleted transaction `TR/2026/09/09/0087` — the highest-numbered transaction of the day. From that moment, **every** attempt to record a new transaction that day failed with the same error. This was not a one-off collision; it was a lockout for the rest of the period.

### Root cause

`Transaction::generateTransactionNumber()` computes the next sequence as `max(existing) + 1`, querying via `self::where('tr_number', 'like', 'TR/2026/09/09/%')`. Since Fix #008 (issue #52, August 2026) added `SoftDeletes` to `Transaction`, that query is silently narrowed by the SoftDeletes global scope to non-deleted rows.

A soft-deleted row is still a row: it keeps its `tr_number` and still occupies the unique index. So after `0087` was trashed the generator saw a max of `0086`, produced `0087`, and the insert collided with the trashed row. Because the trashed row remained the true max, the next attempt produced `0087` again, and so on.

The May 2026 change (`385ec15`) that moved these generators from `count()` to max-sequence explicitly targeted "gaps from deleted records" — but at that time deletes were *hard* deletes, which remove the row from the unique index. Fix #008 made deletes soft three months later, which changed the failure mode from "gap" to "phantom occupant", and no test covered a soft-deleted max.

**On "we have a command for this":** the only repair command in the repo is `app:fix-closing-ct-numbers`, which renumbers CT numbers after an old-HIMS sync. Nothing ever existed for TR numbers. The May fix was a model change, and so is this one — no command is needed now either (see "No data repair needed" below).

### What was changed

Added `withTrashed()` to every number-generator query on a `SoftDeletes` model, so trashed rows still reserve their number:

| Model | Method | Column | Unique index | Period |
|---|---|---|---|---|
| `Transaction` | `generateTransactionNumber` | `tr_number` | yes | day |
| `Patient` | `generateCounterNumber` | `ps_number` | **no** (plain index) | month |
| `Closing` | `generateCounterNumber` | `ct_number` | yes | month |
| `ExpenseVoucher` | `generateExpenseVoucherNumber` | `vc_number` | yes | month |
| `ServiceOrder` | `generateServiceOrderNumber` | `so_number` | yes | month |
| `ServiceOrder` | `generateShortServiceOrderNumber` | `so_short` | yes | all-time |
| `ServiceOrder` | `generateToken` | `token` | no | day (per doctor/service) |
| `Task` | `generateTaskNumber` | `task_number` | yes | month |
| `Asset` | `generateAssetNumber` | `asset_number` | yes | year |

```php
// Before
$existingNumbers = self::where('tr_number', 'like', "{$prefix}%")
    ->lockForUpdate()
    ->pluck('tr_number');

// After
$existingNumbers = self::withTrashed()
    ->where('tr_number', 'like', "{$prefix}%")
    ->lockForUpdate()
    ->pluck('tr_number');
```

**Note the `Patient` row:** `ps_number` has no unique index, so the same bug there would not have crashed — it would have **silently registered a new patient under a PS number already held by a trashed patient**. That is worse than a 500. Worth a follow-up unique index once production data is verified clean (see below).

**Already correct, untouched:** `Appointment`, `BirthCertificate`, `DeathCertificate`, `ReferralCertificate` — all written after Fix #008 and already use `withTrashed()`.

**Deliberately not changed:**
- `PurchaseOrder::generatePoNumber()` — no `SoftDeletes` on that model, so a delete does remove the row from the unique index. Its `count()`-based sequence has a separate, pre-existing gap weakness, unrelated to this bug.
- `SyncOldHIMS`'s private TR counter (`Transaction::where(...)->count()`, around line 1602) — legacy import path only. Same blind spot in principle; flagged below rather than touched, since that command has its own test surface and cursor logic.

### No data repair needed

The trashed `TR/2026/09/09/0087` row already holds the true max for the day. Once this fix is deployed, the next generated number is `0088`. No renumbering, no command, no manual database edit. **Do not rename or hard-delete the trashed row** — it is part of the audit trail and `TransactionObserver` blocks force-deletes anyway.

### Files changed

- `app/Models/Transaction.php`, `Patient.php`, `Closing.php`, `ExpenseVoucher.php`, `ServiceOrder.php`, `Task.php`, `Asset.php` — `withTrashed()` on the generator query
- `tests/Feature/Models/TransactionModelTest.php`, `PatientModelTest.php`, `ClosingModelTest.php`, `ServiceOrderModelTest.php`, `ExpenseVoucherModelTest.php`, `TaskTest.php`, `AssetTest.php` — 9 new regression tests

### Tests

```bash
php artisan config:clear   # see note below
php -d memory_limit=1024M vendor/bin/pest --compact tests/Feature/Models tests/Feature/NumberGenerationTest.php
```

186 tests pass (179 + 7), 9 new. Each new test creates the highest-numbered record of the period, soft-deletes it, and asserts the next generated number is `max + 1` rather than the deleted number — e.g. create `0086` and `0087`, trash `0087`, expect `0088`.

> **SQLite caveat** (same as Fix #001): `FOR UPDATE` is a no-op in the test database. Tests verify the sequence logic, not the lock.

> **Local gotcha:** a stale `bootstrap/cache/config.php` (left by `php artisan config:cache`, which the Docker entrypoints run) makes the test suite ignore phpunit.xml's SQLite override and try to reach the Docker `db` host — every test fails with `getaddrinfo for db failed`. Run `php artisan config:clear` before testing outside Docker.

### What is NOT yet covered

- **`patients.ps_number` has no unique index.** Add one (`->unique()`) after a one-time production check that no duplicates already exist: `SELECT ps_number, COUNT(*) FROM patients GROUP BY ps_number HAVING COUNT(*) > 1`.
- **`SyncOldHIMS` TR counter** still uses `count()` without `withTrashed()`. If a synced-in transaction is later soft-deleted and another sync runs for the same day, the same collision can occur on the import path.
- **`Task`, `Asset`, `PurchaseOrder` remain `count()`-based** rather than max-sequence. With `withTrashed()` and soft deletes, `count == max` in practice, but a legacy import with gaps would break them. Converting them to the `maxSequence` pattern used by `Transaction` is a small, safe follow-up.
- Filament `TrashedFilter` / restore UI is still absent on the financial resources (carried over from Fix #008's notes) — an admin cannot see or restore the trashed `0087` without `tinker`.

---

## For IT / DevOps

### What changed on the server

- No schema changes, no migrations, no environment variables. PHP model files only.

### Deployment steps

1. Pull the latest code.
2. `docker compose up --build` (or your standard deployment command).
3. No artisan commands required.

**Deploy urgency:** until this is deployed, no new transaction can be recorded on any day where the most recent transaction has been deleted. If reception is blocked today, the deploy *is* the fix — there is nothing to run afterwards.

### How to verify after deploy

1. Record any transaction. Its number should be the next after the highest of the day *including* the deleted one — for 2026-09-09 that is `TR/2026/09/09/0088`.
2. Optional check in the `cli` container:
   ```bash
   php artisan tinker --execute "echo App\Models\Transaction::generateTransactionNumber();"
   ```
   should print `0088` (it only reads; it does not consume the number).

### Interim workaround if the deploy cannot happen promptly

Temporarily restore the trashed record so the old code can see it:

```bash
php artisan tinker --execute "App\Models\Transaction::withTrashed()->where('tr_number','TR/2026/09/09/0087')->restore();"
```

The next transaction will then get `0088`, after which the record can be deleted again from the admin panel. **Caveat:** while restored, the record counts toward the open counter's totals again, so do this only as a bridge to the deploy and re-delete promptly.

### Rollback

Revert the seven model files. This reintroduces the bug, so only roll back if the deploy itself is broken.

### Risk of this change

**Very low.** The change adds trashed rows to a read-only query whose sole purpose is to compute the next sequence. It does not alter which records are visible anywhere else in the application.

---

## For Reception Staff

### Does anything look different?

**No.** If you saw an error when saving a transaction today after an admin deleted one, that stops as soon as IT deploys this fix. Transaction numbers simply continue from where they left off.

The deleted transaction's number is **never reused** — that is deliberate. A number that appeared on a printed receipt must never appear on a different receipt.

---

## For Hospital Administration

### Business risk mitigated

| Risk | Before fix | After fix |
|---|---|---|
| Deleting the most recent transaction halted all income recording for the rest of the day | Yes — happened in production 2026-09-09 | No |
| Same lockout for closings, vouchers, service orders, tasks, assets after deleting the most recent one | Yes (latent) | No |
| A new patient silently registered under a PS number already belonging to a deleted patient | Possible (no unique index on `ps_number`) | No |

### Compliance relevance

Record numbers are identity. Under PHC and HIPAA-style record-integrity expectations, a number that already exists on a deleted-but-retained record must not be reissued to a different patient or payment — doing so creates an audit-trail ambiguity where two records share one identifier. This fix makes that guarantee hold across every numbered record type in the system.

### Financial impact

No cost to deploy. No downtime. Income recording resumes on the affected day the moment the fix is live.
