# Fix #002 — Missing Authorization: Resource-Level Access Control

**GitHub Issue:** [afaryab/hospital-care#4](https://github.com/afaryab/hospital-care/issues/4)
**Severity:** Critical
**Status:** ✅ Fixed
**Branch:** `claude/wizardly-bassi`
**Date:** 2026-03-28

---

## For Developers

### What was wrong

The application had authentication (users must log in) but zero authorization (no checks on *what* a logged-in user is allowed to do). Three categories of exposure:

1. **All API routes were publicly accessible** — `routes/api.php` had no `auth` middleware. Any unauthenticated HTTP client could search patients, transactions, users, or closings.

2. **No resource-level ownership checks** — `WebController` never verified that the logged-in user had permission to view or edit a specific counter or transaction. A doctor could open another receptionist's closing statement URL and see it.

3. **No policies or gates** — `app/Policies/` did not exist. No `authorize()`, `can()`, or `abort_if()` calls appeared in any business controller. Only one Gate existed (for Laravel Pulse).

### What was added

**Role helpers on `User` model:**
```php
$user->isAdmin();          // adminProfiles()->exists()
$user->isReceptionist();   // receptionistProfiles()->exists()
$user->isAccountant();     // accountantProfiles()->exists()
$user->isAnyDoctor();      // any of 6 doctor profile types
$user->isPatientManager(); // patientManagerProfiles()->exists()
$user->hasAnyProfile();    // any of the above
```

**Three policies created:**

| Policy | `view` | `create` | `update` | `delete` |
|---|---|---|---|---|
| `ClosingPolicy` | admin / accountant / receptionist | admin / receptionist | admin OR owner receptionist | admin only |
| `TransactionPolicy` | admin / any staff | admin / receptionist | admin OR creator | admin only |
| `PatientPolicy` | admin / any staff | admin / receptionist / patient_manager | admin / receptionist / patient_manager | admin only |

All policies define `before()` — admins bypass every check unconditionally.

**Policies registered in `AppServiceProvider`:**
```php
Gate::policy(Closing::class, ClosingPolicy::class);
Gate::policy(Transaction::class, TransactionPolicy::class);
Gate::policy(Patient::class, PatientPolicy::class);
```

**API routes wrapped in auth middleware:**
```php
Route::middleware(['auth'])->group(function () {
    // all api.php routes
});
```

**`authorize()` calls added to `WebController`:**
- `counterView()` → `$this->authorize('view', $openCounter)`
- `transactionView()` → `$this->authorize('view', $transaction)`
- `transactionEdit()` → `$this->authorize('update', $transaction)`
- `transactionUpdate()` → `$this->authorize('update', $transaction)`

### Files changed

- `app/Models/User.php` — role helpers added
- `app/Policies/ClosingPolicy.php` — new
- `app/Policies/TransactionPolicy.php` — new
- `app/Policies/PatientPolicy.php` — new
- `app/Providers/AppServiceProvider.php` — policies registered
- `routes/api.php` — auth middleware applied
- `app/Http/Controllers/WebController.php` — authorize() calls added
- `tests/Feature/AuthorizationTest.php` — 18 new tests
- `tests/Feature/Api/*ApiTest.php` — `beforeEach(actingAs)` added to 4 files

### Tests

```bash
php artisan test --compact tests/Feature/AuthorizationTest.php
```

18 tests covering:
- Unauthenticated API access returns 401 (6 tests)
- ClosingPolicy: view/update/delete permissions (6 tests)
- TransactionPolicy: create/update/delete (3 tests)
- PatientPolicy: view/create/update by role (3 tests)

### What is NOT yet covered

These remain to be implemented in future work:
- Role-based middleware for route groups (doctors shouldn't reach counter routes)
- `authorize()` in Filament resources (admin panel has `canAccessPanel()` but no per-record policies)
- `authorize()` in print/report controllers

---

## For IT / DevOps

### What changed on the server

No database schema changes. No migrations. No new environment variables.

PHP source files updated:
- New `app/Policies/` directory (3 files)
- Modified `app/Models/User.php`, `app/Providers/AppServiceProvider.php`, `routes/api.php`, `app/Http/Controllers/WebController.php`

### Deployment steps

1. Pull the latest code
2. Run `docker compose up --build` (or standard deploy command)
3. No artisan commands required

### How to verify after deploy

1. Open the hospital app in a browser without logging in
2. Try `curl -X POST https://your-domain/api/patients` — should return `{"message":"Unauthenticated."}`
3. Log in as a receptionist and try to open another receptionist's counter URL — should return 403 Forbidden

### Rollback

Revert `routes/api.php`, `app/Providers/AppServiceProvider.php`, `app/Http/Controllers/WebController.php`, and delete `app/Policies/`. No data or schema changes to undo.

### Risk of this change

**Low-to-Medium.** The change adds restrictions, not new features. Previously unrestricted operations are now gated. If any frontend workflow depends on a scenario that is now forbidden (unlikely in normal use), it will receive a 403. Check browser console after deploy for unexpected 403 errors.

---

## For Reception Staff

### Does anything look different?

**Mostly no.** You can still open and close counters, register patients, and record transactions exactly as before.

### One thing that changed

Previously, you could technically visit the URL of any other receptionist's counter and see their statement. This is now blocked — you will see a "403 Forbidden" page instead. This was a privacy fix, not an intentional feature.

### What should you do if you see "403 Forbidden"?

Contact your IT admin. It may mean:
- You are trying to access a counter that belongs to a different user
- Your account does not have the right role assigned

### What should you do differently?

Nothing for your normal daily work.

---

## For Hospital Administration

### Business risk mitigated

| Risk | Before fix | After fix |
|---|---|---|
| Unauthenticated access to patient data via API | Any internet user could query | Requires logged-in session |
| Receptionist A reading Receptionist B's financial statement | Any user could view any counter URL | Blocked — own data only (admins see all) |
| Doctor editing financial transactions | No restriction | Blocked — only transaction creator or admin |
| Unauthorized patient record modification | Any logged-in user could update | Restricted to receptionist, patient manager, admin |
| API data exposure without login | Full exposure | Fully blocked |

### Compliance relevance

**PHC Guidelines:** Role-based access control is a mandatory requirement. Staff must only see data relevant to their role.

**HIPAA:** Access to patient information must be restricted to staff with a clinical or operational need. Unrestricted access to all patient records by any staff profile is a violation.

This fix brings the system substantially closer to compliance on both frameworks.

### Who was exposed

Any hospital running this software had all patient, financial, and operational data accessible to any logged-in user regardless of their role. The API was additionally accessible without any login. This fix closes both exposures.

### Financial impact

No cost to deploy. No downtime required.
