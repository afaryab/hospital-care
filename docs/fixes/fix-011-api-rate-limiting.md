# Fix #011 — No Rate Limiting on Any API Route

**GitHub Issue:** [afaryab/hospital-care#58](https://github.com/afaryab/hospital-care/issues/58)
**Severity:** High
**Status:** ✅ Fixed
**Branch:** `feat/api-rate-limiting`
**Date:** 2026-08-19

---

## For Developers

### What was wrong

`bootstrap/app.php`'s `$middleware->api()` configuration never called `->throttleApi()`, and no `RateLimiter::for('api', ...)` was defined anywhere in `app/Providers`. Laravel's conditional throttle middleware entry is filtered out entirely when no limiter is registered for its name, so all 55 routes in `routes/api.php` — patient search/create/edit, transaction refund, service-order status changes, bed assignment/discharge, treatment records — could be hit without limit by any authenticated token holder. This directly violated this app's own documented "rate limiting on all public endpoints" standard, and was a particular concern for the financial/clinical mutation endpoints in that list, where unlimited retries on a refund or status-change endpoint is itself a risk.

### What was added

```php
// bootstrap/app.php
$middleware->throttleApi();

// app/Providers/AppServiceProvider.php, in boot()
RateLimiter::for('api', function (Request $request) {
    return Limit::perMinute(120)->by($request->user()?->id ?: $request->ip());
});
```

120 requests/minute per authenticated user (2/sec sustained) was chosen deliberately generous — this API backs an internal staff-facing SPA with search-as-you-type UX on several department dashboards, not a rate-sensitive public API, so the limit exists to bound abuse and retry storms rather than throttle normal usage.

### A deliberately narrowed scope

The original audit finding bundled this with **API versioning** (`/api/v1/...` — currently the API is unversioned, contradicting this app's own documented standard). That part was intentionally split out after checking with the user: versioning every route path is a breaking change that touches roughly 20 auto-generated Wayfinder TypeScript files in `resources/js/actions/` (regeneratable, but still a real-diff, real-rebuild change) and potentially any external integration a self-hosted deployment of this software might have running against it — something that can't be verified from the codebase alone. That's tracked as separate future work, not silently dropped.

### Files changed

- `bootstrap/app.php`
- `app/Providers/AppServiceProvider.php`
- `tests/Feature/Api/ApiRateLimitingTest.php` — new

### Tests

```bash
php artisan test --compact
```

880 tests, 0 failures (1 new). The new test sends 120 requests to a cheap read-only endpoint (all succeed) followed by a 121st (asserts a 429) — confirming the limiter is actually wired up and enforced, not just configured and silently inert (the exact failure mode this fix addresses).

### What is NOT yet covered

- API versioning (`/api/v1/...`) — deliberately deferred, see above.
- Per-route limit tuning — every API route currently shares the same 120/min budget. A future pass could give the genuinely sensitive mutation endpoints (refund, discharge) a tighter limit independent of the general search/read traffic.

---

## For IT / DevOps

### What changed on the server

No schema changes, no new environment variables. The rate limiter uses whatever cache store is already configured (Redis in production) to track request counts per user.

### Deployment steps

Standard deploy — pull and redeploy.

### How to verify after deploy

Send more than 120 requests to any `/api/...` endpoint within a minute as the same authenticated user — the 121st request onward should return HTTP 429 with a `Retry-After` header, then succeed again once the minute window rolls over.

### Rollback

Revert the two application files. No data changes to undo.

### Risk of this change

**Low.** 120/min is generous relative to any normal staff workflow — even rapid search-as-you-type on a department dashboard is unlikely to sustain 2 requests/second for a full minute. If any workflow does hit the limit in practice (unexpected polling behavior, a runaway frontend retry loop), that's worth investigating as a bug in its own right, not raising the limit blindly.

---

## For Reception Staff

### Does anything look different?

No. Normal usage — even fast typing in search fields — stays well under the new limit. You'd only ever see a rate-limit message if something was making requests unusually fast, which would itself be worth reporting to IT.

---

## For Hospital Administration

### Business risk mitigated

Without a rate limiter, any single compromised or malicious authenticated account (or a bug causing runaway retries) could hammer the API — including endpoints that refund transactions, change patient care status, or discharge patients — with no bound at all. This closes that gap.

### Compliance relevance

Rate limiting on API access is explicitly named in this app's own compliance-adjacent product documentation ("rate limiting on all public endpoints") as a required control. This brings the implementation in line with that already-stated standard.

### Financial impact

No cost to deploy. No downtime required.
