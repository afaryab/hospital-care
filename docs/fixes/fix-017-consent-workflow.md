# Fix #017 — Consent Create/View UI, Policy, and an Opt-In Treatment Gate

**GitHub Issue:** [afaryab/hospital-care#70](https://github.com/afaryab/hospital-care/issues/70)
**Severity:** Medium
**Status:** ✅ Fixed
**Branch:** `feat/consent-workflow`
**Date:** 2026-08-19

---

## For Developers

### What was wrong

- `ConsentResource` had an empty `form()` (`return $schema->components([]);`) and only registered an `index` page — there was no way to actually create a consent record through the admin UI.
- `Consent` had no policy registered — no authorization gate on who could view/create consent records.
- Nothing anywhere required a consent record to exist before treatment could be documented. PHC guideline §7 requires consent type, timestamp, and capture method to be recorded and linked to the patient record.

### What was added

**Enums** — `App\Enum\ConsentType` (Treatment/Procedure/DataSharing) and `App\Enum\ConsentMethod` (DigitalCheckbox/PaperSigned/VerbalRecorded), replacing the plain magic strings the model/factory/table previously used independently.

**Real Filament UI**: `ConsentResource` now has a working `form()` (patient + optional service-order lazy-search selects, type/method selects, consented-at picker, notes), a `ConsentInfolist`, and `create`/`view` pages (`CreateConsent` auto-sets `recorded_by` from the acting user; `ListConsents` gained a "New Consent" header action). **Deliberately no `edit` page** — consent records are append-only, matching this codebase's existing Immutable Records pattern (`TreatmentRecord.is_finalized`, `BirthCertificate.is_locked`): a correction is a new consent entry with a note, not a silent edit to history.

**`ConsentPolicy`** (new, registered in `AppServiceProvider`) — admin `before()` bypass; `view`/`viewAny`/`create` open to any clinical or front-desk role (receptionist, patient manager, accountant, nursing staff, **or any doctor** — broader than `PatientPolicy`'s doctor-scoping, since a doctor needs to check/record consent for a patient before it's established whether they're "the" treating doctor for that encounter); `update`/`delete` always `false` except via the admin bypass.

**The gate — opt-in, off by default.** `TreatmentRecord::booted()` gained a `static::creating()` hook that, only when `HospitalSetting::get('require_consent_before_treatment', false)` is true, blocks creation with a `ValidationException` (`consent` key) unless a `treatment`-type `Consent` row exists for the service order's patient. A new toggle on the Hospital Settings page controls this, with an explicit warning in its helper text.

**This was a deliberate scope decision, not an oversight**: a hard-enforced gate at the model layer, active immediately on deploy, would block treatment for every existing patient with no historical consent record — a real patient-safety risk for a live hospital, not something to silently force on. The full workflow (UI, policy, model wiring, gate logic) is fully built and functional; whether/when to actually flip it on per-deployment is left to the hospital, after backfilling consent for existing patients if they choose to enable it.

### Files changed

- `app/Enum/ConsentType.php`, `ConsentMethod.php` — new
- `app/Models/Consent.php` — enum casts, `SoftDeletes` + `LogsActivity` (consistent with other clinical/compliance models), hard-delete guard
- `app/Models/Patient.php` — new `consents(): HasMany`
- `app/Models/TreatmentRecord.php` — the opt-in gate in `static::creating()`
- `app/Filament/Admin/Resources/Consents/ConsentResource.php`, `Pages/CreateConsent.php`, `Pages/ViewConsent.php`, `Pages/ListConsents.php`, `Schemas/ConsentInfolist.php`
- `app/Policies/ConsentPolicy.php` — new
- `app/Providers/AppServiceProvider.php` — registers `ConsentPolicy`
- `app/Filament/Admin/Pages/HospitalSettings.php` — new toggle
- `database/factories/ConsentFactory.php` — enum-based, new `treatment()` state
- `database/migrations/2026_08_19_105003_add_soft_deletes_to_consents_table.php` — new
- Tests: `tests/Feature/Compliance/ConsentGateTest.php`, `tests/Feature/Policies/ConsentPolicyTest.php`, `tests/Feature/Filament/Admin/ConsentResourceTest.php` — new; `HospitalSettingsTest.php` — 1 new test

### Tests

```bash
php -d memory_limit=1024M vendor/bin/pest --compact
```

886 tests, 0 failures (17 new).

### What is NOT yet covered

- No consent-recording UI on the reception/doctor-facing Inertia frontend — consent is recorded through the admin Filament panel only. A self-service "capture consent at check-in" reception-facing flow is a reasonable follow-up, not built here.
- The gate only checks for the existence of *any* `treatment`-type consent for the patient — it doesn't distinguish per-encounter or per-procedure consent, and doesn't expire old consent. Sufficient for the "was any consent ever recorded" compliance baseline the audit flagged; finer-grained consent lifecycle (expiry, per-procedure) is a larger feature.
- No backfill tooling for existing patients is included — a hospital that wants to enable the gate needs to record consent for their existing patient base first (through the new UI, one at a time, or via a future bulk-import if that becomes a real need).

---

## For IT / DevOps

### What changed on the server

- One migration adds `deleted_at` to `consents`. No other schema changes.
- No new environment variables. The gate is controlled entirely through the Hospital Settings admin page (`HospitalSetting` key `require_consent_before_treatment`), **defaults to off**.

### Deployment steps

1. Pull the latest code.
2. Run migrations: `php artisan migrate`.
3. Standard deploy (`docker compose up --build` or equivalent).
4. **Leave the "Require Recorded Consent Before Treatment" toggle off** unless the hospital has explicitly decided to enforce it and has a plan for existing patients without a recorded consent.

### How to verify after deploy

1. Admin → Compliance → Consents → confirm the list page loads and "New Consent" creates a record correctly.
2. Confirm treatment recording (any department) still works normally with the gate off (default).
3. Only if intentionally testing the gate: toggle it on in Hospital Settings, confirm treatment save now requires a recorded consent, then toggle back off before going live unless that's the actual intent.

### Rollback

Revert the application files. The migration only adds a nullable `deleted_at` column — safe to leave in place even on rollback.

### Risk of this change

**Low as shipped** (gate defaults off, so behavior for every existing deployment is unchanged until an admin explicitly opts in). **The risk moves entirely to whoever flips the toggle** — flag this clearly to any hospital administrator who wants to enable it: every patient without a recorded "treatment" consent will be blocked from receiving any documented treatment until one is recorded for them.

---

## For Reception Staff

### Does anything look different?

**A new "Consents" section appears under Compliance in the admin panel**, where consent can now actually be recorded (previously the page existed but had no working form). Day-to-day patient registration and treatment recording are unaffected — the new gate is off by default.

---

## For Hospital Administration

### Business risk mitigated

| Risk | Before fix | After fix |
|---|---|---|
| No way to record consent digitally at all | Consent page existed but had no working create form | Full create/view workflow, enum-validated type/method, audit-logged |
| No authorization control on who can record/view consent | No policy registered | `ConsentPolicy` — clinical/front-desk roles only |
| No way to require consent before treatment, for hospitals that want to enforce it | Not possible | Available via a settings toggle — **you decide when, after your existing patients are ready** |

### Compliance relevance

**PHC Guideline §7 (Consent Management)** requires consent type, timestamp, and capture method to be recorded and linked to the patient record. This fix makes that actually possible for the first time. Whether to *enforce* it as a hard gate is a deployment decision left to each hospital — turning on the toggle is a meaningful operational step (see IT/DevOps section) and should be planned, not flipped casually.

### Financial impact

No cost to deploy. No downtime required.
