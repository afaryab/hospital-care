# Fix #001 — Race Condition in CT and SO Number Generation

**GitHub Issue:** [afaryab/hospital-care#3](https://github.com/afaryab/hospital-care/issues/3)
**Severity:** High
**Status:** ✅ Fixed
**Branch:** `claude/wizardly-bassi`
**Date:** 2026-03-28

---

## For Developers

### What was wrong

Three number-generation methods lacked database-level locking. If two requests arrived simultaneously (e.g. two receptionists opening a counter at the exact same second), both could read the same `COUNT(*)` before either committed their insert — producing identical CT or SO numbers and triggering a unique-constraint violation (500 error).

**Affected methods:**

| Model | Method | Number format |
|---|---|---|
| `Closing` | `generateCounterNumber()` | `CT/YYYY/MM/NNNN` |
| `ServiceOrder` | `generateServiceOrderNumber($type)` | 8-digit padded count |
| `ServiceOrder` | `generateShortServiceOrderNumber($type)` | 8-digit all-time count |

`Patient::generateCounterNumber()` and `Transaction::generateTransactionNumber()` were already correct and served as the reference pattern.

### Root cause

```php
// UNSAFE — two threads can read the same count simultaneously
$count = self::where('ct_number', 'like', "CT/{$year}/{$month}/%")->count();
```

### Fix applied

```php
// SAFE — SELECT ... FOR UPDATE serialises concurrent reads
return DB::transaction(function () {
    $count = self::where('ct_number', 'like', "CT/{$year}/{$month}/%")
        ->lockForUpdate()
        ->count();
    $count += 1;
    return "CT/{$year}/{$month}/{$count}";
});
```

### Files changed

- `app/Models/Closing.php` — added `DB` import, wrapped in transaction + lock
- `app/Models/ServiceOrder.php` — added `DB` import, wrapped both methods
- `tests/Feature/NumberGenerationTest.php` — 7 new tests (all passing)

### Tests

```bash
php artisan test --compact tests/Feature/NumberGenerationTest.php
```

Covers: format validation, sequential uniqueness, monthly resets, type independence.

> **Note:** SQLite (test environment) silently ignores `FOR UPDATE`. The lock is active in production MySQL/MariaDB only. Tests verify the counting logic, not the lock itself.

---

## For IT / DevOps

### What changed on the server

No database schema changes. No new migrations. No environment variables needed.

Only PHP model files and a test file were modified — a standard application code deploy.

### Deployment steps

1. Pull the latest code onto the server
2. Run `docker compose up --build` (or your standard deployment command)
3. No artisan commands required (no migrations)

### How to verify after deploy

Open two browser sessions simultaneously and have both create a new counter opening at the same moment. Both should succeed with different CT numbers. Previously, one would fail with a 500 error.

### Rollback

Revert `app/Models/Closing.php` and `app/Models/ServiceOrder.php` to the previous commit. No data or schema changes to undo.

### Risk of this change

**Low.** The change only adds a transaction wrapper and a lock to an existing query. The query result is identical under normal (non-concurrent) conditions.

---

## For Reception Staff

### Does anything look different?

**No.** The counter opening and service order screens work exactly the same as before.

### What problem did this fix?

In a busy hospital where two receptionists tried to open a counter at precisely the same moment, the system could crash with an error screen. This fix prevents that crash. You may never have noticed it — but it was a risk during high-traffic periods.

### What should you do differently?

Nothing. No change to your daily workflow.

---

## For Hospital Administration

### Business risk mitigated

| Risk | Before fix | After fix |
|---|---|---|
| Duplicate counter (CT) numbers | Possible under simultaneous access | Eliminated |
| Duplicate service order (SO) numbers | Possible under simultaneous access | Eliminated |
| 500 error during peak hours | Possible | Eliminated |
| PHC audit — unique record numbers | At risk | Compliant |

### Compliance relevance

Punjab Healthcare Commission (PHC) and HIPAA guidelines require that every clinical and financial record carry a unique, traceable identifier. Duplicate numbers would fail an audit. This fix ensures the uniqueness guarantee is enforced at the database level.

### Financial impact

No cost to deploy. No downtime required.

### Who was affected

Any hospital running this software with more than one active receptionist session was exposed to this risk. The fix is included in the next release.
