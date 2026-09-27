# Fix #025 — Patient Register Defaults to the Current Month

**GitHub Issue:** [afaryab/hospital-care#92](https://github.com/afaryab/hospital-care/issues/92)
**Severity:** Low
**Status:** ✅ Fixed
**Branch:** `fix/number-generators-skip-soft-deleted-rows`
**Date:** 2026-09-27

---

## For Developers

### What was changed

- `WebController::register()` — bare `/PS` (no year, month, `search`, `contact` or `all`) redirects to `/PS/{current Y}/{current m}` in the hospital timezone. `?all=1` keeps the full list.
- Ordering is `created_at DESC, id DESC` so rows with the same timestamp have a stable order across pages.
- `yearSelected`/`monthSelected` are always strings (`'0'` = All). This fixes the frontend building `/PS/false/05` when a month was picked with no year.
- `register.tsx` — the "All" option navigates to `/PS?all=1`; pagination carries `all=1`; the department links under each patient are built from the real departments (the old hard-coded list had wrong codes `EMR`, `XRY`, `RAD` and missed Peds).

### Files changed

- `app/Http/Controllers/WebController.php`, `resources/js/pages/register.tsx`
- Tests: `tests/Feature/Web/PatientsRegisterFilterTest.php` (default redirect, `all=1`, newest-first with tie-breaker); `WebRoutesTest` and `WebControllerCachingTest` updated for the redirect.

## For IT / DevOps

No schema change. Frontend rebuild required (`npm run build`, done by the Docker image build).

## For Reception Staff

- Opening **Register** now shows this month's patients, newest first.
- Choose **All** in Year to see every patient; searching by name/PS/phone still searches all periods.

## For Hospital Administration

Faster register loads (one month instead of every patient) and consistent ordering. No compliance impact.
