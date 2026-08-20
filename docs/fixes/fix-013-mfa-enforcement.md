# Fix #013 — Two-Factor Authentication Available But Never Enforced

**GitHub Issue:** [afaryab/hospital-care#62](https://github.com/afaryab/hospital-care/issues/62)
**Severity:** High
**Status:** ✅ Fixed
**Branch:** `feat/mfa-enforcement`
**Date:** 2026-08-19

---

## For Developers

### What was wrong

`Features::twoFactorAuthentication()` was enabled in `config/fortify.php`, making two-factor authentication available for any user to opt into via `/settings/two-factor`. But neither `AdminPanelProvider` nor `AccountsPanelProvider` applied any middleware requiring it — `authMiddleware([Authenticate::class])` was the only gate on both panels. A compromised admin password alone was sufficient to reach every patient record, the full financial ledger, and user management. Both this app's HIPAA-inspired documentation (§6.1) and its PHC compliance documentation (§17) explicitly call for MFA on privileged accounts.

### What was added

A small middleware, applied to both panels' `authMiddleware`:

```php
class EnsureTwoFactorAuthenticationIsEnabled
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || $user->hasEnabledTwoFactorAuthentication()) {
            return $next($request);
        }

        if ($request->routeIs(self::EXEMPT_ROUTE_NAMES)) {
            return $next($request);
        }

        return Redirect::route('two-factor.show')
            ->with('status', 'Two-factor authentication is required for administrator and accountant accounts. Please set it up to continue.');
    }
}
```

`hasEnabledTwoFactorAuthentication()` is Fortify's own method on the `TwoFactorAuthenticatable` trait already used by `User` — it checks both `two_factor_secret` and `two_factor_confirmed_at` are set (Fortify's config requires explicit confirmation, not just a generated-but-unconfirmed secret).

**A deliberate implementation choice worth flagging**: Filament v4.9.2 — the version currently on `release-0.10.2` at the time this was written — has no built-in panel-level 2FA-requirement API. A pending dependency-update PR (#51) bumps Filament to ≥4.11.5, which does add native support for this. Rather than make this fix depend on that PR merging first (and in what order), it's implemented as a standalone middleware that works regardless of Filament version. Once #51 lands, this custom middleware could optionally be replaced by Filament's native mechanism as a follow-up — not required, just an option.

**The logout exemption matters.** The middleware explicitly allows `filament.admin.auth.logout` and `filament.accounts.auth.logout` through even for a user without 2FA enabled. Without this, an admin who hasn't set up 2FA yet would be redirected to the 2FA setup page on every request *including* their attempt to log out — a genuine trap with no escape except completing 2FA setup.

### Files changed

- `app/Http/Middleware/EnsureTwoFactorAuthenticationIsEnabled.php` — new
- `app/Providers/Filament/AdminPanelProvider.php`
- `app/Providers/Filament/AccountsPanelProvider.php`
- `tests/Feature/MfaEnforcementTest.php` — new

### Tests

```bash
php artisan test --compact
```

883 tests, 0 failures (4 new): admin without 2FA is redirected; admin with 2FA reaches the panel; accountant without 2FA is redirected; and — the one most worth having — a 2FA-less admin can still successfully log out rather than being trapped.

Notably, **the full pre-existing Filament test suite passed unchanged**, because Livewire component tests (`Livewire::test(SomeFilamentPage::class)`) mount components directly and don't traverse the HTTP router's middleware stack — so this fix had to be verified with real HTTP-level tests (`get('/admin')`, not `Livewire::test(...)`) to actually exercise the new gate.

### What is NOT yet covered

- No bulk enrollment/migration path for existing admin/accountant accounts that don't yet have 2FA set up — they'll simply be redirected to the setup page on next login, which is the intended behavior, but IT should expect this and give affected staff a heads-up rather than have them discover it cold.
- No enforcement outside the two Filament panels (e.g. doctor/nursing accounts on the Inertia frontend) — scoped intentionally to the two privileged panels named in the original finding.

---

## For IT / DevOps

### What changed on the server

No schema changes, no new environment variables.

### Deployment steps

Standard deploy — pull and redeploy.

### How to verify after deploy

1. Log in as an admin/accountant account that has never set up 2FA — confirm you're redirected to the two-factor setup screen instead of reaching the panel dashboard.
2. Set up 2FA on that account, then confirm the panel is now reachable normally.
3. Confirm "Logout" still works even before 2FA is set up (don't want anyone stuck).

### Rollback

Revert the 3 application files. No data changes to undo.

### Risk of this change

**Medium**, specifically around rollout communication rather than the code itself: any admin/accountant account currently in daily use that hasn't set up 2FA will be blocked from the panel on next login until they do. This is the intended, correct behavior, but IT should proactively notify affected staff before deploying to production rather than have someone discover it mid-shift. Consider checking `SELECT id, name FROM users WHERE two_factor_confirmed_at IS NULL` against the admin/accountant role tables beforehand to know who's affected.

---

## For Reception Staff

### Does anything look different?

Only if you're an admin or accountant: if you haven't set up two-factor authentication yet, you'll be asked to do so the next time you log into the admin or accounts panel, before you can continue. This is a one-time setup (scan a QR code with an authenticator app).

---

## For Hospital Administration

### Business risk mitigated

Previously, a single leaked or guessed admin/accountant password was sufficient to compromise the entire system — every patient record, every financial transaction, user management. Two-factor authentication now closes that single-point-of-failure for the two highest-privilege account types.

### Compliance relevance

Both `.ai/hippa-compliance` and the PHC guidelines explicitly name MFA for administrative accounts as a required control. This closes a gap where the control was configured and available but not actually mandatory — exactly the kind of finding a compliance review exists to catch.

### Financial impact

No cost to deploy. No downtime required. Each affected admin/accountant will need a few minutes to complete one-time 2FA setup on their next login.
