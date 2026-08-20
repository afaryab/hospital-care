# Fix #008 — Hard-Deletable Financial Records

**GitHub Issue:** [afaryab/hospital-care#52](https://github.com/afaryab/hospital-care/issues/52)
**Severity:** Critical
**Status:** ✅ Fixed
**Branch:** `fix/financial-record-soft-deletes`
**Date:** 2026-08-19

---

## For Developers

### What was wrong

`Transaction`, `Closing`, `ExpenseVoucher`, and `Receaveable` did not use the `SoftDeletes` trait, and their migrations never created a `deleted_at` column at all. Despite that, the Filament admin panel had fully working `DeleteAction`/`DeleteBulkAction` buttons wired up on all four resources. Clicking Delete performed a genuine, unrecoverable SQL `DELETE` — the row was simply gone, with only whatever `spatie/activitylog` happened to capture on the `deleted` event left behind. This violates the app's own product rule: "No model dealing with patient data, financial records, or clinical records may use hard deletes."

### What was added

```php
use HasFactory, LogsActivity, SoftDeletes;
```

on all four models, plus one migration adding `deleted_at` to `transactions`, `closings`, `expense_vouchers`, `receaveables` — the same pattern already used by `Patient`.

No Filament resource code needed to change: `DeleteAction` calls `$record->delete()`, and Eloquent automatically turns that into a soft delete once the trait is present. Verified this isn't just a model-level guarantee by adding a Livewire test (`ClosingSoftDeleteTest`) that calls the actual `DeleteAction` on the `EditClosing` page and confirms the row survives with `deleted_at` set.

### Files changed

- `app/Models/{Transaction,Closing,ExpenseVoucher,Receaveable}.php`
- `database/migrations/2026_08_18_205038_add_soft_deletes_to_financial_tables.php`
- `tests/Feature/FinancialRecordSoftDeletesTest.php` — new, one test per model
- `tests/Feature/Filament/Admin/ClosingSoftDeleteTest.php` — new, confirms the admin panel Delete button end-to-end

### Tests

```bash
php artisan test --compact
```

806 tests, 0 failures (6 new). Notably, the **full existing suite passed unchanged** — nothing in the app relied on hard-delete semantics for these four models, which is exactly what you'd hope to find when adding `SoftDeletes` to a model that should have had it from the start.

### What is NOT yet covered

- No backfill/audit of whether any records were already hard-deleted in production before this fix — that data is unrecoverable by definition, but worth a one-time check against activity log entries with `event=deleted` for these four subject types to understand historical exposure.
- Filament's `TrashedFilter` / restore UI wasn't added to these resources' tables — soft-deleted records are hidden by default (correct), but there's currently no in-panel way to view/restore them without `php artisan tinker`. Worth a small follow-up if admins need self-service restore.

---

## For IT / DevOps

### What changed on the server

New migration adds a nullable `deleted_at` column to 4 tables. No data migration, no downtime.

### Deployment steps

1. Pull the latest code.
2. `php artisan migrate` (or your standard deploy flow, which runs this automatically).

### How to verify after deploy

1. Delete a test `Closing` (or any of the four) via the admin panel.
2. Confirm it disappears from the list — but check the database directly (`SELECT * FROM closings WHERE id = ?`) and confirm the row is still there with `deleted_at` populated.

### Rollback

Revert the 4 model files and roll back the migration (`php artisan migrate:rollback`, or manually drop the 4 `deleted_at` columns). No data loss on rollback since the column is purely additive.

### Risk of this change

**Very low.** This is a purely additive schema change (a nullable column) plus a well-established Eloquent trait already used elsewhere in this codebase (`Patient`). The full test suite passed with zero required changes elsewhere in the app, meaning nothing currently depends on hard-delete behavior for these models.

---

## For Reception Staff

### Does anything look different?

No visible change to daily workflow. Deleting a record from the admin panel still works the same way from your perspective — it disappears from the list.

### What changed behind the scenes

If a transaction, closing, voucher, or receivable is ever deleted by mistake, it's no longer permanently lost — an administrator can recover it. Previously, a mistaken delete was unrecoverable.

---

## For Hospital Administration

### Business risk mitigated

| Risk | Before fix | After fix |
|---|---|---|
| Deleting a transaction that recorded a cash payment, concealing revenue | Permanent, unrecoverable, only a log entry remains | Row preserved, recoverable, matches the audit-trail requirement |
| Accidental deletion by any admin-panel user | Permanent data loss | Recoverable |

### Compliance relevance

Both `.ai/hippa-compliance` and the PHC guidelines require an append-only, tamper-resistant record for financial and clinical data. Hard-deletable financial records were a direct violation of that principle — any deleted transaction was simply gone from the system of record. This fix brings all four financial models in line with the immutability standard already applied to `Patient`.

### Financial impact

No cost to deploy. No downtime required.
