# Fix #006 — Broken Access Control: Patient/Service-Order/Transaction Records

**GitHub Issue:** [afaryab/hospital-care#48](https://github.com/afaryab/hospital-care/issues/48)
**Severity:** Critical
**Status:** ✅ Fixed
**Branch:** `fix/patient-record-access-control`
**Date:** 2026-08-19

---

## For Developers

### What was wrong

`PatientPolicy`, `ServiceOrderPolicy`, and `TransactionPolicy` all gated `view` (and `ServiceOrderPolicy::update`) with:

```php
$user->can('patient.view') || $user->hasAnyProfile();
```

`hasAnyProfile()` is true for every staff account regardless of role, and the seeded Spatie permissions (`patient.view`, `service_order.view`) are also granted broadly to every clinical role in `RolesAndPermissionsSeeder`. The OR-condition meant these policies never actually restricted reads by relationship — only `before()`'s admin bypass did any real gating. A receptionist, a single-department doctor, or an X-ray technician could read or print any patient's full clinical and financial record, and any doctor could write diagnosis/prescriptions to any service order, not just their own.

On top of the policy gap, several endpoints never called `authorize()` at all:

- `WebController::patient()` — the `PS/{year}/{month}/{number}` route family
- `WebController::updateServiceOrderStatus()`
- `Prints\ServiceOrderPdfPrintController::stream()` — resolved by raw incrementing `id`
- `Prints\TransactionPdfPrintController::stream()`/`download()`
- `Api\{Opd,Ind,Department}Controller::saveTreatmentRecord()`

### What was added

**Scoped policies** — receptionist/patient-manager/accountant/nursing-staff keep broad access (legitimate front-desk, billing, and ward-charting need); doctors are scoped to records they're the assigned doctor for:

```php
// PatientPolicy::view()
if ($this->hasBroadAccess($user)) {
    return true;
}
if ($user->isAnyDoctor()) {
    return $patient->treatments()->where('doctor_id', $user->id)->exists();
}
return false;
```

Same shape in `ServiceOrderPolicy` (via `ServiceOrder.doctor_id` directly) and `TransactionPolicy` (via `TransactionElement.doctor_id`).

**`authorize()` calls added** to every endpoint listed above.

**Test helper added** — `tests/Pest.php` now has `adminUser()` (creates a user with an Administrator profile), used across the suite wherever a test needs "just let me hit this route" without exercising authorization itself.

### Files changed

- `app/Policies/PatientPolicy.php`, `ServiceOrderPolicy.php`, `TransactionPolicy.php`
- `app/Http/Controllers/WebController.php`
- `app/Http/Controllers/Prints/ServiceOrderPdfPrintController.php`, `TransactionPdfPrintController.php`
- `app/Http/Controllers/Api/OpdController.php`, `IndController.php`, `DepartmentController.php`
- `tests/Pest.php` — new `adminUser()` helper
- `tests/Feature/AccessControlHardeningTest.php` — new, covers all 4 previously-unauthorized endpoints
- `tests/Feature/Policies/ServiceOrderPolicyTest.php` — new
- 6 existing test files updated: `AuthorizationTest.php`, `Compliance/RBACTest.php`, `Policies/TransactionPolicyTest.php`, `Prints/ServiceOrderPdfPrintTest.php`, `Web/WebControllerPatientTest.php`, `Api/{DepartmentControllerDischargeTest,DepartmentControllerTriageTest,TreatmentRecordIcd10ValidationTest}.php` — either given an explicit `doctor_id` assignment to reflect real scoped access, or swapped a bare test user for `adminUser()` where the test was about content/rendering rather than authorization

### Tests

```bash
php artisan test --compact
```

819 tests, 0 failures (17 new).

### What is NOT yet covered

- **Nursing staff** have no ward/department assignment column in the schema, so they keep broad access to `Patient`/`ServiceOrder` for now rather than being silently locked out with no replacement mechanism. Needs a schema addition (a ward/department assignment on the nursing profile) before this can be tightened.
- Treatment attachments (X-ray/ultrasound images) still sit on the public disk with no auth mediation — separate finding, tracked for a follow-up branch.
- API rate limiting and versioning — separate findings, tracked for follow-up branches.

---

## For IT / DevOps

### What changed on the server

No database schema changes. No migrations. No new environment variables.

PHP source files updated: 3 policy classes, `WebController.php`, 2 print controllers, 3 API department controllers.

### Deployment steps

1. Pull the latest code.
2. Standard deploy (`docker compose up --build` or equivalent).
3. No artisan commands required.

### How to verify after deploy

1. Log in as a doctor who has never treated a given patient; try opening that patient's record via `PS/{year}/{month}/{number}` — should return 403.
2. Log in as a receptionist; the same URL should work normally for any patient.
3. Log in as a doctor assigned to a service order; confirm printing that order's PDF still works.

### Rollback

Revert the 6 application files listed above. No data or schema changes to undo.

### Risk of this change

**Low-to-medium.** The change adds restrictions, not new features — previously-unrestricted reads/writes are now gated by actual clinical relationship. The main operational risk is a legitimate doctor being blocked from a patient/order they should have access to but that isn't correctly linked via `doctor_id` in the data (e.g. a service order created under a different doctor's account by mistake). Watch for unexpected 403s from doctor accounts after deploy — that's the signal to check the underlying `doctor_id` assignment on the record in question, not to revert this fix.

---

## For Reception Staff

### Does anything look different?

**No.** Receptionists, patient managers, and accountants keep full lookup access to any patient, transaction, or service order — this fix does not change your daily workflow.

### What should you do if a doctor reports "403 Forbidden"?

This means the doctor tried to open a patient or print a document they aren't the assigned doctor for. Check that the service order/transaction is correctly assigned to the right doctor. If it looks correctly assigned and they're still blocked, contact IT.

---

## For Hospital Administration

### Business risk mitigated

| Risk | Before fix | After fix |
|---|---|---|
| Any staff account reading any patient's full chart | Any logged-in account, any role | Restricted to front-desk/billing roles and the patient's actual treating doctor(s) |
| Any doctor writing diagnosis/prescriptions to any patient's order | No ownership check at all | Restricted to the assigned doctor |
| Death/birth/referral certificates reachable by ID guessing | No auth check on the print route | Same clinical-relationship scoping applied |
| Doctor A discharging/finalizing Doctor B's emergency case | Possible | Blocked |

### Compliance relevance

**PHC Guidelines & HIPAA:** both require role-based, least-privilege access to patient information — staff should only see data relevant to their actual clinical or operational role. The previous behavior (any staff profile granting access to every patient) was a direct violation of that principle across the entire clinical and financial record surface. This fix closes the gap for the highest-traffic access paths (patient chart view, print/download, and clinical write). Nursing-staff scoping remains a documented follow-up, not yet closed.

### Who was exposed

Every hospital running this software, on every patient, transaction, and service order in the system, to every logged-in staff account regardless of role. This fix closes that exposure for receptionist/doctor/technician-class accounts; admin, accountant, patient-manager, and nursing-staff accounts retain broad access by design.

### Financial impact

No cost to deploy. No downtime required.
