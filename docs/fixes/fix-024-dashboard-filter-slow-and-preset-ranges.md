# Fix #024 — Dashboard Filter Drawer Slow; Preset Date Ranges Ignored

**GitHub Issue:** [afaryab/hospital-care#91](https://github.com/afaryab/hospital-care/issues/91)
**Severity:** Medium
**Status:** ✅ Fixed
**Branch:** `fix/number-generators-skip-soft-deleted-rows`
**Date:** 2026-09-27

---

## For Developers

### Root causes

1. **Every Livewire request ran Inertia's shared props eagerly.** `HandleInertiaRequests` is in the `web` group, so Filament's `/livewire/update` calls (opening the drawer, every field change) executed `share()`: two uncached `hospital_settings` queries, `Inspiring::quotes()`, the timezone list, and serialising the user with every profile relation.
2. **`->live()` on the Date Range select** made each dropdown change a full server round trip while the drawer was open.
3. **Preset ranges never reached the widgets.** The start/end pickers were `visible()` only for "Custom", and Filament v4 does not dehydrate hidden fields — so "This Year", "Last Month", etc. were applied as *just* `dateRange`, and every widget (which reads `startDate`/`endDate`) fell back to *this month*.
4. `TriageKPIStats` inherited Filament's default 5-second polling.

### What was changed

- `HandleInertiaRequests::share()` — `quote`, `routeName`, `auth`, `timezone`, `hospital` are now lazy closures, resolved only when an Inertia response is actually rendered.
- `HospitalSetting` uses the `Cacheable` trait: `get()` reads one cached key→value map, flushed on any save/delete.
- `HasDashboardDateFilters` — Date Range is no longer `live()`; the pickers toggle client-side with `visibleJs()`; a custom `FilterAction::action()` expands the preset into concrete `startDate`/`endDate` via the new `resolveDateFilters()`.
- `TriageKPIStats` polls every 15s (same as the triage charts).

### Files changed

- `app/Http/Middleware/HandleInertiaRequests.php`
- `app/Models/HospitalSetting.php`
- `app/Filament/Admin/Concerns/HasDashboardDateFilters.php`
- `app/Filament/Admin/Widgets/Triage/TriageKPIStats.php`
- Tests: `tests/Feature/Models/HospitalSettingTest.php` (served from cache, refreshed on write), `tests/Feature/Filament/Admin/TriageDashboardTest.php` (preset and custom ranges).

### Not yet covered

About 100 widget queries use `whereDate(created_at, …)`, which prevents index use. Converting them to `where('created_at', '>=', DateHelper::dayStartUtc(...))` (as `AdminStatsOverview` does) is the next performance step.

## For IT / DevOps

No schema change. Settings are cached in the default cache store; if a setting is ever changed by raw SQL, run `php artisan cache:clear` (or wait for the 1-hour TTL).

**Rollback:** revert the four files.

## For Reception Staff

Nothing changes.

## For Hospital Administration

- The dashboard filter opens and responds faster.
- **Correctness fix:** choosing "This Year", "Last Month" and other presets now actually changes the figures. Before this fix, every preset silently showed *this month's* numbers, so any report read from a preset range was wrong.
