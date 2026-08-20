# Fix #016 — N+1 Queries, Missing Indexes, and Reference-Data Cache Bypass

**GitHub Issue:** [afaryab/hospital-care#68](https://github.com/afaryab/hospital-care/issues/68)
**Severity:** Medium
**Status:** ✅ Fixed
**Branch:** `perf/query-and-cache-cleanup`
**Date:** 2026-08-19

---

## For Developers

### What was wrong

1. **N+1 queries** in `ExpenseVouchersTable`, `ClosingsTable`, `AuditLogsTable` — none eager-loaded the relationships their columns display, so every visible row triggered a separate query for `expCategory`/`serviceOrder.service`/`payedTo`/`transaction`, `reception`, or `causer` (a `morphTo`) respectively.
2. **Missing indexes**: `activity_log.created_at`, `activity_log.causer_id`, `activity_log.event`, `expense_vouchers.created_at` — all used in default sorts or filters on tables with no other supporting index for that access pattern.
3. **~14 call sites bypassing the `Cacheable` trait**: `ServiceDepartment::all()` (×3), `Panel::all()` (×2), `PaymentMethod::all()` (×2), and filtered `ExpenseCategory::query()`/`Service::where()`/`Triage::query()` calls across `WebController` and `EmergencyDoctorController` — re-querying small, rarely-changing reference tables on every hit of high-traffic reception/doctor pages, even though `cachedAll()`/`cachedActive()` already existed on those models for exactly this purpose.
4. **Found during implementation, not in the original audit**: `User::isAdmin()` had no memoization — every Policy's `before()` hook calls it first, so a Filament table with row-level actions ran it once per row per action-visibility check. Confirmed empirically while writing the N+1 regression test for `ExpenseVouchersTable`: a 5-row table fired **49** queries, almost all of them identical `administrators` existence checks — this dwarfed the actual relationship N+1 it was masking.

### What was added

**Eager loading** — `modifyQueryUsing()` added to all 3 tables:
- `ExpenseVouchersTable`: `->with(['expCategory', 'serviceOrder.service', 'payedTo', 'transaction'])`
- `ClosingsTable`: `->with('reception')`
- `AuditLogsTable`: `->with('causer')`

**Indexes** — new migration adds `activity_log.created_at`, `activity_log.causer_id`, `activity_log.event`, `expense_vouchers.created_at`.

**Cache bypass fixes** — swapped to the existing `Cacheable` methods:
- `ServiceDepartment::all()` → `ServiceDepartment::cachedAll()`
- `Panel::all()` → `Panel::cachedActive()` (this one also has a minor behavior change — see note below)
- `PaymentMethod::all()` → `PaymentMethod::cachedAll()`
- `Service::where('service_department_id', ...)->get()` → `Service::cachedActive()->where('service_department_id', ...)->values()`
- 4× `ExpenseCategory::query()->where(...)->get()` → `ExpenseCategory::cachedAll()->where(...)->values()` (Laravel Collection's `where()`, filtering the already-cached listing in-memory instead of re-querying)
- `Triage::query()->where('is_active', true)->orderBy('priority')->get([...])` in `EmergencyDoctorController` → `Triage::cachedActive()` (already used correctly elsewhere in the codebase — this was the one inconsistent call site)

**`User::isAdmin()` memoization** — added a `protected ?bool $isAdminMemo = null;` instance property; `isAdmin()` now computes once per instance (`??=`) instead of on every call.

### A note on the `Panel::all()` → `Panel::cachedActive()` swap

`Panel::cachedActive()` filters `is_active = true`; the old `Panel::all()` call sites returned every panel including inactive ones. `Panel::cachedActive()`'s own docstring says it's "used across transaction, receivable, and cheque forms" — but grep showed it was never actually wired up anywhere except the cache-warming registry, while `WebController` used `Panel::all()` at the exact call sites that docstring describes. This is very likely the intended method that was simply never connected — showing an inactive insurance panel as selectable for a *new* transaction doesn't make sense anyway — but it is a small behavior change bundled with the caching fix, not a caching-only change, so it's called out explicitly here rather than left implicit.

### Files changed

- `app/Filament/Admin/Resources/ExpenseVouchers/Tables/ExpenseVouchersTable.php`, `Closings/Tables/ClosingsTable.php`, `AuditLogs/Tables/AuditLogsTable.php`
- `database/migrations/2026_08_19_082353_add_missing_performance_indexes.php` — new
- `app/Http/Controllers/WebController.php`, `EmergencyDoctorController.php`
- `app/Models/User.php` — `isAdmin()` memoization
- `tests/Feature/Filament/Admin/ExpenseVoucherResourceTest.php`, `ClosingResourceTest.php` — new N+1 regression tests; `AuditLogResourceTest.php` — new file
- `tests/Feature/Performance/WebControllerCachingTest.php` — new
- `tests/Feature/Models/UserModelTest.php` — 2 new tests for `isAdmin()` memoization

### Tests

```bash
php -d memory_limit=1024M vendor/bin/pest --compact
```

880 tests, 0 failures (13 new).

The N+1 regression tests assert query count stays roughly flat (within a small tolerance for incidental pagination/filter-option queries) going from 1 row to 5 rows, rather than exact equality — Filament's internal query patterns have enough legitimate small variance (e.g. one fewer query when the table happens to skip a "check for any records" branch) that exact-equality assertions were flaky; the tolerance is still far tighter than genuine N+1 growth would allow.

### What is NOT yet covered

- The `service_orders (doctor_id, type, created_at)` composite index and `closings.closed_at`/`cash_recieving_time` indexes flagged during research were deprioritized — lower query volume / smaller tables, not part of the original "4 missing indexes" scope.
- `User::isAdmin()` was the only role-helper memoized. `isAccountant()`, `isReceptionist()`, `isAnyDoctor()`, `isPatientManager()`, and others follow the identical un-memoized pattern and are called from the same Policy `before()` hooks — `isAdmin()` was prioritized because it's the first check in literally every policy, but the others are a legitimate follow-up.
- The lower-priority `AdministrativeTransactionForm`/`AdministrativeTransactionsTable` cache-bypass call sites (admin-only, lower traffic than the reception-facing pages fixed here) were not touched in this pass.

---

## For IT / DevOps

### What changed on the server

- One migration adds 4 indexes — fast, non-blocking on MySQL for tables of this size (no data rewrite, index-only).
- No new environment variables. No cache configuration changes — reuses the existing `Cacheable` trait and whatever `CACHE_STORE` is already configured.

### Deployment steps

1. Pull the latest code.
2. Run migrations: `php artisan migrate`.
3. Standard deploy (`docker compose up --build` or equivalent).

### How to verify after deploy

1. Open the admin Expense Vouchers, Closings, and Audit Log tables — should still render correctly with all relationship data visible (category names, reception names, causer names).
2. On the counter income page and expense/voucher pages, confirm departments/services/payment methods/panels/expense categories still populate correctly in dropdowns.
3. If checking query logs/slow-query log: repeated page loads of the same reception page should show far fewer queries against `service_departments`, `panels`, `payment_methods`, `expense_categories`, `triages` after the first hit (cached for 1 hour or until the underlying table is written to).

### Rollback

Revert the application files. The index migration is safe to leave in place even if the application code is rolled back (indexes don't change query results, only speed) — but `php artisan migrate:rollback` works cleanly if a full rollback is wanted.

### Risk of this change

**Low.** Eager-loading and indexing are purely additive from a correctness standpoint. The one item worth double-checking after deploy: confirm insurance panel dropdowns on the counter income/receivables pages still show every panel your hospital actually uses as selectable — if a panel was marked inactive but staff still expect to select it for new transactions, mark it active again in the Panels admin page (see the `Panel::all()` → `Panel::cachedActive()` note above).

---

## For Reception Staff

### Does anything look different?

**No**, with one narrow exception: on the income/receivables pages, the "Panel" dropdown now only shows panels marked *active*. If a panel you expect to see is missing, ask an admin to check whether it was marked inactive.

---

## For Hospital Administration

### Business risk mitigated

This is a performance fix, not a security/compliance one — but it's directly related to the earlier dashboard-slowness complaint that started this audit series (see fix #009). Reception and doctor-facing pages that are opened dozens-to-hundreds of times per day (counter income, receivables, expense entry, voucher forms, EMG patient view) were re-querying the same small, rarely-changing reference tables on every single page load; admin tables with relationship columns were running one extra query per visible row. Both compound under real daily usage, especially on hospital hardware with modest database resources.

### Compliance relevance

Not a compliance item — purely a performance/scalability fix that keeps the system responsive as patient/transaction/audit-log volume grows, which is itself an operational precondition for staff being able to do their compliance-relevant work (accurate record-keeping, timely treatment) without the system slowing them down.

### Financial impact

No cost to deploy. No downtime required — the index migration runs quickly.
