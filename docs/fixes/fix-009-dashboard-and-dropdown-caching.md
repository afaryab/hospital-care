# Fix #009 — Slow Admin Dashboard & Patient Dropdown

**GitHub Issue:** [afaryab/hospital-care#54](https://github.com/afaryab/hospital-care/issues/54)
**Severity:** Critical (performance)
**Status:** ✅ Fixed
**Branch:** `perf/dashboard-and-dropdown-caching`
**Date:** 2026-08-19

---

## For Developers

### What was wrong

This is the root cause of the reported "Filament admin dashboards filter drawer is loading slow" — two compounding issues:

1. `AdministrativeTransactionForm.php`'s `patient_id` field: `->options(fn () => Patient::query()->orderBy('name')->pluck('name', 'id')->toArray())`. This loads **every patient in the hospital** into the form on every render. `patients` is the largest, continuously-growing table in the system. `->searchable()` alone doesn't help here — it only filters the already-fully-fetched list client-side.

2. `AdminStatsOverview` — the widget rendered first (sort `-15`) on the default admin dashboard, meaning it's the very first thing that loads after login. It ran roughly 5 unscoped, all-time `SUM`/`COUNT` aggregate queries with zero caching, on every page load.

Both had a correct pattern already sitting elsewhere in the same codebase:
- `AdministrativeTransactionsTable.php` and `TransactionResource.php` already use `->searchable()->getSearchResultsUsing()->getOptionLabelUsing()` for the *filter* version of the same patient field.
- `HistoryOverallStats.php` already wraps a near-identical set of all-time aggregates in `Cache::remember(..., 3600, ...)`, with a comment explaining exactly why.

Neither pattern had been applied to the two places actually causing the slowness.

### What was added

**Patient dropdown**, now matching the existing filter's pattern:

```php
Select::make('patient_id')
    ->label('Patient (Optional)')
    ->searchable()
    ->getSearchResultsUsing(fn (string $search): array => Patient::query()
        ->where('name', 'like', "%{$search}%")
        ->limit(30)
        ->pluck('name', 'id')
        ->toArray())
    ->getOptionLabelUsing(fn ($value): ?string => Patient::find($value)?->name)
    ->nullable(),
```

**Dashboard totals** — extracted every *all-time* (not date-range-scoped) figure out of the six `getXxxStats()` methods into one `allTimeTotals()` method, cached for an hour:

```php
protected function allTimeTotals(): array
{
    return Cache::remember('dashboard.admin.alltime_totals', 3600, function () {
        // ... total_users, total_patients, closing_net, expense_voucher_amount, etc.
    });
}
```

Called once per `getStats()` invocation and passed down to each stat method. The date-range-scoped ("this duration") queries were deliberately left alone — those depend on whatever date range the admin has picked on the dashboard filter, so caching them under a single fixed key would show stale numbers the moment someone changes the date range.

### Files changed

- `app/Filament/Admin/Resources/AdministrativeTransactions/Schemas/AdministrativeTransactionForm.php`
- `app/Filament/Admin/Widgets/AdminStatsOverview.php`
- `tests/Feature/Finance/AdministrativeTransactionTest.php` — 2 new tests
- `tests/Feature/Filament/Admin/AdminStatsOverviewCacheTest.php` — new

### Tests

```bash
php artisan test --compact
```

804 tests, 0 failures (4 new). One of the new tests creates 20 patients and asserts none of their names appear in the create form's initial render — this is the test that would have caught the original bug had it existed before.

### What is NOT yet covered

This PR fixed the two *critical* items behind the specific user report. The broader audit found the same eager/uncached pattern repeated at lower severity across ~14 more call sites (Tasks, StockMovements, AuditLogs, PayrollPeriods, and others) — those are tracked as separate, lower-urgency follow-up work (`perf/query-and-cache-cleanup`), not fixed here.

---

## For IT / DevOps

### What changed on the server

No schema changes. No new environment variables. The new cache key `dashboard.admin.alltime_totals` uses whatever cache store is already configured (Redis in production per this app's existing setup) and expires automatically after 1 hour — no manual cache-clearing action needed, though clearing the general cache (e.g. via the existing Cache Settings admin page) will also clear it.

### Deployment steps

Standard deploy — pull and redeploy. No migrations, no artisan commands beyond the normal flow.

### How to verify after deploy

1. Open the admin dashboard — should load noticeably faster, especially on a hospital with a large patient roster.
2. Open the "New Administrative Transaction" form and try the patient field — typing a few letters of a patient's name should return matching results within a moment, without the page having felt slow to load in the first place.

### Rollback

Revert the two application files. No data changes to undo.

### Risk of this change

**Very low.** The dashboard numbers are identical either way (same queries, just fetched once per hour instead of on every request) — the only observable difference is that all-time totals may lag actual state by up to an hour, which is the explicit, already-accepted tradeoff for the sibling `HistoryOverallStats` widget in this same codebase. Anyone who wants perfectly fresh all-time totals could clear the cache manually.

---

## For Reception Staff

### Does anything look different?

If you're an admin: the dashboard and the "New Administrative Transaction" form should both feel noticeably faster, especially the patient field, which now searches as you type instead of loading every patient up front.

---

## For Hospital Administration

### Business risk mitigated

No compliance or financial risk here — this was purely a usability/performance issue. Slow admin tooling has a real cost in staff time and frustration, especially as the patient roster grows; this fix keeps that cost flat regardless of how large the hospital's patient database gets.

### Financial impact

No cost to deploy. No downtime required.
