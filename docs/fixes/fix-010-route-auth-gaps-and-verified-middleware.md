# Fix #010 — Unauthenticated Import Route & App-Wide Broken Email Verification Enforcement

**GitHub Issue:** [afaryab/hospital-care#56](https://github.com/afaryab/hospital-care/issues/56)
**Severity:** Critical
**Status:** ✅ Fixed
**Branch:** `fix/route-auth-gaps`
**Date:** 2026-08-19

---

## For Developers

### What was wrong

Two related findings from the route-compliance pass of a broader audit.

**1. `/import-old` required no authentication at all.** `Route::get('/import-old', [ImportController::class, 'index'])` sat above the `['auth','verified']` middleware group in `routes/web.php`. `ImportController::index()` runs unbounded (`set_time_limit(0)`, unlimited memory) bulk `updateOrCreate()` writes across `Patient`, `User`, `Transaction`, `Closing`, `Expense`, and `ExpenseVoucher` from a secondary database connection — a one-time legacy migration tool that anyone, logged in or not, could trigger repeatedly.

**2. `routes/settings.php` dropped `verified` from its middleware, and investigating why led to a much bigger discovery.** The file used `Route::middleware('auth')` only, while the rest of the app consistently uses `['auth','verified']`. Digging into *why* this one file was inconsistent turned up the real problem: `App\Models\User` never actually implemented `Illuminate\Contracts\Auth\MustVerifyEmail` — the import existed in the file but was commented out (`// use Illuminate\Contracts\Auth\MustVerifyEmail;`). Laravel's `verified` middleware checks `$user instanceof MustVerifyEmail` and **silently passes every request through** if the model doesn't implement that interface — regardless of `email_verified_at`. Fortify's `Features::emailVerification()` being enabled only wires up the verification *flow* (sending the email, the `/email/verify` routes); it does not by itself make anything require verification. This meant the `['auth','verified']` group already used throughout `routes/web.php` — protecting patient records, transactions, everything — had never actually enforced email verification, app-wide, since the day it was written.

### What was added

- Moved `/import-old` inside the `['auth','verified']` group, plus an explicit admin-only check in the controller (`abort_unless($request->user()?->isAdmin(), 403);`) — nobody but an admin has a legitimate reason to run a legacy data migration.
- `routes/settings.php` now uses `Route::middleware(['auth', 'verified'])`, matching the rest of the app.
- **The actual fix**: `User` now implements `MustVerifyEmail` via Laravel's standard trait:

```php
use Illuminate\Auth\MustVerifyEmail;
use Illuminate\Contracts\Auth\MustVerifyEmail as MustVerifyEmailContract;

class User extends Authenticatable implements FilamentUser, MustVerifyEmailContract
{
    use Cacheable, HasFactory, HasRoles, LogsActivity, MustVerifyEmail, Notifiable, TwoFactorAuthenticatable;
```

This single change makes `verified` middleware start actually functioning everywhere it's already applied in this codebase — not just in the one file the original finding pointed at.

### Files changed

- `routes/web.php` — moved `/import-old` inside the auth group
- `app/Http/Controllers/Migration/ImportController.php` — admin-only guard
- `routes/settings.php` — added `verified` to the middleware group
- `app/Models/User.php` — implements `MustVerifyEmail`
- `tests/Feature/RouteAuthGapsTest.php` — new

### Tests

```bash
php artisan test --compact
```

885 tests, 0 failures (6 new). The full existing suite — 879 tests that predate this fix — **passed unchanged**, which is exactly the signal you want when flipping on an enforcement mechanism that was previously inert: it confirms nothing in the app was accidentally depending on unverified users being able to reach protected routes.

### What is NOT yet covered

- No audit of whether any currently-registered users have `email_verified_at = null` in production. Once this deploys, any such account will be redirected to the verify-email screen on next login rather than reaching the app — expected and correct, but worth a heads-up to whoever runs this hospital's instance so they're not caught off guard.

---

## For IT / DevOps

### What changed on the server

No schema changes, no new environment variables. Purely an application-code behavior change.

### Deployment steps

Standard deploy — pull and redeploy.

### How to verify after deploy

1. Confirm `/import-old` returns a login redirect when logged out, and a 403 when logged in as a non-admin.
2. If any user account in this hospital's database has an unverified email, confirm that account gets redirected to the "verify your email" screen on next login rather than reaching the app directly. If that's unexpected for an existing account, an admin can manually mark it verified via `php artisan tinker` (`$user->markEmailAsVerified()`) or by resending/completing the verification link.

### Rollback

Revert the 4 application files. No data changes to undo — this is a pure behavior/enforcement change.

### Risk of this change

**Low-to-medium.** The full test suite passing unchanged is strong evidence this won't disrupt normal operation. The one real-world scenario worth watching: any **existing, currently-in-use** staff account that happens to have an unverified email (e.g., created before email verification was configured, or via an import/seed path that didn't set `email_verified_at`) will now be blocked at login until verified. Worth a quick database check (`SELECT COUNT(*) FROM users WHERE email_verified_at IS NULL`) before deploying to production, so IT can proactively verify any real accounts that need it rather than staff discovering it by being locked out.

---

## For Reception Staff

### Does anything look different?

If your account's email was already verified (the normal case), nothing changes for you. If you ever see a "please verify your email" screen after logging in, click the link in the verification email sent to your inbox — that's expected behavior that simply wasn't being enforced before.

---

## For Hospital Administration

### Business risk mitigated

| Risk | Before fix | After fix |
|---|---|---|
| Anyone (no account needed) triggering unbounded legacy data-migration writes | Fully exposed, no auth required | Requires admin login |
| Email verification requirement silently never enforced anywhere in the app | Any account, verified or not, had full access | Verification actually required, as originally intended when this feature was configured |

### Compliance relevance

Both `.ai/hippa-compliance` and PHC guidelines expect access controls to work as documented, not just be present in configuration. This is a case where a compliance-relevant control (email verification as part of account security) was configured correctly at the Fortify level but silently inert at the model level — exactly the kind of gap a security review exists to catch, since it wouldn't show up in a casual code read of `config/fortify.php` alone.

### Financial impact

No cost to deploy. No downtime required.
