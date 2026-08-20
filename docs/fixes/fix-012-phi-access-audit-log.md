# Fix #012 — No Genuine Per-View Audit Log of PHI Access

**GitHub Issue:** [afaryab/hospital-care#60](https://github.com/afaryab/hospital-care/issues/60)
**Severity:** High
**Status:** ✅ Fixed
**Branch:** `feat/phi-access-audit-log`
**Date:** 2026-08-19

---

## For Developers

### What was wrong

`BreachDetectionService::recordPatientAccess()` — called from `WebController::patient()` — looks like an access log at a glance (the method name implies it), but it isn't one. It increments a cache counter per user and only creates a persisted `Incident` once a threshold is crossed (default 20 views in 5 minutes). A single, ordinary view of a patient chart persisted nothing at all.

Beyond that: the Filament `ViewPatient` page had no logging call whatsoever, and neither did the PDF print controllers (`ServiceOrderPdfPrintController`, `TransactionPdfPrintController`). `spatie/laravel-activitylog`, already used on `Patient`, `Transaction`, `ServiceOrder`, and 6 other models, only logs its default create/update/delete events — "viewed" was never one of them anywhere in this codebase, on any model.

Both this app's HIPAA-inspired documentation (§8.1, "PHI access" is a mandatory log) and its PHC compliance documentation (§4.3, "every access must log user_id, patient_id, action, timestamp") were unmet in practice, despite a service whose name suggested otherwise.

### What was added

Explicit activity-log entries at every actual PHI read path found, using the same `activity()->causedBy()->performedOn()->event()->log()` mechanism already established in this app's newer DMS module:

```php
activity()
    ->causedBy($request->user())
    ->performedOn($patientData)
    ->event('viewed')
    ->log('Patient record viewed');
```

Four call sites:
- `WebController::patient()` — `viewed` (the front-desk/clinical patient-chart route)
- Filament `Patients\ViewPatient` page — `viewed` (a second, separate admin-panel read path into the same patient data — overridden `mount()`, calling `parent::mount()` first)
- `ServiceOrderPdfPrintController::stream()` — `downloaded`
- `TransactionPdfPrintController::stream()` and `download()` — `downloaded`

The existing `BreachDetectionService::recordPatientAccess()` call was left in place alongside the new logging — it's still a useful, separate anomaly-detection signal, just not a substitute for a real per-access audit trail.

Also updated `AuditLogsTable`'s event-badge color mapping to recognize `viewed`/`opened`/`downloaded` (info blue) and `shared` (primary), instead of falling through to a generic gray for every non-CRUD event. Given the whole point of this fix is a *scannable* audit trail, leaving every new entry visually indistinguishable from each other would have undercut it.

### Files changed

- `app/Http/Controllers/WebController.php`
- `app/Filament/Admin/Resources/Patients/Pages/ViewPatient.php`
- `app/Http/Controllers/Prints/ServiceOrderPdfPrintController.php`
- `app/Http/Controllers/Prints/TransactionPdfPrintController.php`
- `app/Filament/Admin/Resources/AuditLogs/Tables/AuditLogsTable.php`
- `tests/Feature/Compliance/PhiAccessAuditLogTest.php` — new

### Tests

```bash
php artisan test --compact
```

883 tests, 0 failures (4 new) — one per logging call site, each asserting a matching `Activity` row exists with the correct `causer_id`, `subject_type`/`subject_id`, and `event`.

### What is NOT yet covered

- Read-access logging on other PHI-adjacent surfaces this pass didn't touch (e.g. the department dashboards' own patient-search/queue views, report exports). Those are lower-traffic and lower-risk than the four paths fixed here, but worth a follow-up pass for completeness.
- No retention/rotation policy specific to *read* events was added beyond `config/activitylog.php`'s existing 365-day `clean_after_days` setting, which now applies to these too.

---

## For IT / DevOps

### What changed on the server

No schema changes — this uses the existing `activity_log` table. Expect a modest increase in that table's write volume and growth rate, since every patient-chart view and PDF print now writes a row (previously only create/update/delete events did).

### Deployment steps

Standard deploy — pull and redeploy. No migrations, no artisan commands beyond the normal flow.

### How to verify after deploy

1. View a patient's record, then check the Audit Logs admin page — a new `viewed` entry should appear for that patient, attributed to your account.
2. Print a service order or transaction PDF — a `downloaded` entry should appear.

### Rollback

Revert the 5 application files. No data changes to undo — this only adds log rows, it doesn't remove or alter anything.

### Risk of this change

**Very low.** Purely additive logging calls with no effect on business logic or response behavior. The only operational consideration is the incremental growth of the `activity_log` table, already bounded by the existing 365-day cleanup policy.

---

## For Reception Staff

### Does anything look different?

No. This is a behind-the-scenes compliance record — nothing about how you view patient records or print documents changes.

---

## For Hospital Administration

### Business risk mitigated

Before this fix, if a patient's record was viewed inappropriately, there was no way to answer "who looked at this, and when?" for any individual access — only a statistical anomaly detector that fired after 20 rapid views. Now every patient-chart view and clinical/financial document print is individually attributable to a specific staff account and timestamp, matching what a PHC inspection or HIPAA audit would expect to find.

### Compliance relevance

Directly closes a gap between this app's own documented HIPAA/PHC access-logging requirements and what the code actually did. This is exactly the kind of finding a compliance review exists to catch — the presence of a service named `recordPatientAccess` created the appearance of compliance without the substance of it.

### Financial impact

No cost to deploy. No downtime required.
