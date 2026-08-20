# Fix #018 — Incident Manual Reporting and a Full Classify→Close Lifecycle

**GitHub Issue:** [afaryab/hospital-care#72](https://github.com/afaryab/hospital-care/issues/72)
**Severity:** Medium
**Status:** ✅ Fixed
**Branch:** `feat/incident-lifecycle`
**Date:** 2026-08-19

---

## For Developers

### What was wrong

- `Incident` was written to exclusively by `BreachDetectionService` (3 automated security-detection types) — there was no way for a human to manually report an incident at all (clinical error, system failure, delay in treatment, etc. — the types PHC guideline §9.1 actually calls out).
- `status` only ever held the value `'open'` (the migration's default, the only value `BreachDetectionService` ever wrote). Nothing anywhere transitioned it — there was no lifecycle.
- `IncidentResource::form()` was empty, `canCreate()` hard-returned `false`, and only an `index` page was registered — a pure read-only audit log viewer.
- No `IncidentPolicy` existed; `Incident` wasn't registered in `AppServiceProvider`'s `Gate::policy()` list.

### What was added

**Enums**: `App\Enum\IncidentType` (the 3 existing automated types + 4 new manually-reportable PHC types: clinical error, system failure, data breach, delay in treatment), `IncidentSeverity` (critical/high/medium/low, formalizing values the table filter already listed), `IncidentStatus` (the linear lifecycle: Reported → Classified → Assigned → Investigated → Resolved → Closed, with a `next()` method that's the single source of truth for what transition is legal from any given stage).

**Lifecycle columns** (migration): `department_id`, `reported_by`, `assigned_to`, `classified_at`, `assigned_at`, `investigated_at`, `investigation_notes`, `resolved_at`, `resolution_notes`, `closed_at`, `closed_by`. The migration also remaps every existing `status = 'open'` row to `'reported'` before changing the column default, so no pre-existing incident is left holding a value the new enum doesn't recognize.

**Lifecycle enforcement — two layers, matching this codebase's established defense-in-depth style** (`TreatmentRecord.is_finalized`, `BirthCertificate.is_locked`):
1. Each of the 5 new table row actions (classify/assign/investigate/resolve/close) is only `->visible()` when the record is in the correct preceding status.
2. `Incident::booted()` gained a `static::updating()` hook that independently re-validates: a status change is only accepted if it matches `IncidentStatus::next()` for the record's current (pre-change) status — skipping a stage or moving backward throws a `ValidationException`, even if something bypasses the UI.

**Manual reporting**: a real `form()` (type/severity/department/patient/occurred-at/description), wired through `CreateIncident` (`mutateFormDataBeforeCreate` sets `reported_by` and folds the free-text description into the existing `context` JSON column rather than adding a new one), plus a `ViewIncident` infolist page showing the full record including lifecycle history.

**`IncidentPolicy`** (new, registered in `AppServiceProvider`) — admin `before()` bypass; `view`/`viewAny`/`create` open to any staff profile (accountant/receptionist/patient-manager/nursing/doctor — anyone might need to report or check on an incident); `update`/`delete` always `false` except via the admin bypass, since **lifecycle management (classify/assign/investigate/resolve/close) all route through `update()`, and this codebase has no distinct "Auditor" role** (PHC §11.1 calls for Doctor/Nurse/Receptionist/Admin/Auditor, but only 3 of those 5 have an equivalent `User` role helper today). Mapping lifecycle management to admin-only is a documented decision, not an oversight — introducing a new Auditor role/profile is a larger scope than this feature and is flagged as follow-up work.

### Files changed

- `app/Enum/IncidentType.php`, `IncidentSeverity.php`, `IncidentStatus.php` — new
- `app/Models/Incident.php` — enum casts, `LogsActivity`, new relations (`department`, `reportedBy`, `assignedTo`, `closedBy`), the lifecycle-guard `updating()` hook
- `app/Services/BreachDetectionService.php` — writes `IncidentStatus::Reported` instead of the retired `'open'` string
- `app/Filament/Admin/Resources/Incidents/IncidentResource.php`, `Tables/IncidentsTable.php`, `Schemas/IncidentForm.php`, `Schemas/IncidentInfolist.php`, `Pages/CreateIncident.php`, `Pages/ViewIncident.php`, `Pages/ListIncidents.php`
- `app/Policies/IncidentPolicy.php` — new
- `app/Providers/AppServiceProvider.php` — registers `IncidentPolicy`
- `database/factories/IncidentFactory.php` — new
- `database/migrations/2026_08_19_110553_add_lifecycle_columns_to_incidents_table.php` — new
- Tests: `tests/Feature/Compliance/IncidentLifecycleTest.php`, `tests/Feature/Policies/IncidentPolicyTest.php`, `tests/Feature/Filament/Admin/IncidentResourceTest.php` — new

### Tests

```bash
php -d memory_limit=1024M vendor/bin/pest --compact
```

886 tests, 0 failures (18 new). The existing `BreachNotificationTest.php` suite (automated detection → Incident creation → email notification) passes unchanged — the `'open'` → `IncidentStatus::Reported` swap is behavior-preserving for that flow.

### What is NOT yet covered

- No "Auditor" role — lifecycle management is admin-only. If this codebase later adds a distinct auditor/compliance-officer role, `IncidentPolicy::update()` is the one place to revisit.
- `SecurityIncidentNotification` still only fires once, at creation — lifecycle-stage notifications (e.g. notify on assignment, notify on close) are not added here; the existing notification behavior for automated detections is unchanged.
- No bulk/CSV incident import or reporting from the reception/doctor-facing Inertia frontend — incidents are reported through the admin Filament panel only.

---

## For IT / DevOps

### What changed on the server

- One migration adds 11 columns to `incidents` and remaps the sole pre-existing status value. Fast on any realistically-sized `incidents` table (it's populated only by security-detection events, not routine daily traffic).
- No new environment variables.

### Deployment steps

1. Pull the latest code.
2. Run migrations: `php artisan migrate`.
3. Standard deploy (`docker compose up --build` or equivalent).

### How to verify after deploy

1. Admin → Compliance → Incidents — confirm existing (pre-migration) incidents still display and now show status "Reported" instead of the old "open".
2. Confirm "Report Incident" creates a new incident correctly.
3. Walk one incident through Classify → Assign → Investigate → Resolve → Close and confirm each action only appears at the right stage.
4. Trigger an automated detection (e.g. repeated failed logins) and confirm it still creates an incident and sends the security-contact email as before.

### Rollback

Revert the application files. The migration's `down()` remaps `'reported'` back to `'open'` and drops the new columns — safe to run if reverting before any post-deploy lifecycle data has accumulated; if incidents have already been classified/assigned/etc., rolling back the migration will lose that lifecycle detail (the base incident record itself is preserved).

### Risk of this change

**Low.** Additive for the automated-detection path (same creation flow, just a renamed initial status value, verified against the existing test suite). The new manual-reporting and lifecycle-management surface is entirely new functionality — no existing behavior to regress.

---

## For Reception Staff

### Does anything look different?

**A new "Report Incident" button appears on the Incidents page** (Compliance section, admin panel) for any staff role — receptionists, doctors, nursing staff, and others can now file an incident report. Managing an incident through its lifecycle (classify/assign/investigate/resolve/close) remains an admin task.

---

## For Hospital Administration

### Business risk mitigated

| Risk | Before fix | After fix |
|---|---|---|
| No way to record a clinical incident (medication error, delay in treatment, etc.) at all | Only automated security events were logged | Staff can file a report for any incident type |
| No accountability trail for how an incident was handled | Status never changed from "open" | Full classify → assign → investigate → resolve → close trail, each stage timestamped and attributed |
| No authorization control on who can manage incidents | No policy registered | `IncidentPolicy` — reporting open to staff, lifecycle management restricted to admin |

### Compliance relevance

**PHC Guideline §9 (Incident Management System)** requires a defined incident lifecycle with classification, assignment, investigation, resolution, and closure — all with an audit trail. This fix builds that lifecycle for the first time. The one open item flagged above (no distinct Auditor role) is a genuine gap against §11.1's full RBAC role list — admin currently absorbs that responsibility, which is workable for a smaller hospital but worth revisiting if a dedicated compliance/audit role becomes necessary.

### Financial impact

No cost to deploy. No downtime required.
