# Fix #004 — Configurable 1-Page Compact Emergency Triage Print Template

**GitHub Issue:** [afaryab/hospital-care#34](https://github.com/afaryab/hospital-care/issues/34)
**Severity:** Enhancement (P2)
**Status:** ✅ Implemented — pending review/merge ([PR #35](https://github.com/afaryab/hospital-care/pull/35))
**Branch:** `feature/service-order-print-templates`
**Date:** 2026-08-10

---

## For Developers

### What was added

Previously `ServiceOrderPdfPrintController::stream()` rendered `pdfs.serviceorder` (the 2-page "ED Clinical Performa") unconditionally for every service order, regardless of department — there was no mechanism mapping a department to a print template.

This adds:

1. `app/Enum/ServiceOrderTemplate.php` — string-backed enum with `EmergencyTriageDetailed` (existing 2-page, default) and `EmergencyTriageCompact` (new 1-page), each exposing `label()` and `view()`.
2. `service_order_template` nullable column on `service_departments`, cast to the enum on `ServiceDepartment`.
3. `resources/views/pdfs/serviceorder-triage-compact.blade.php` — a condensed single-page layout: patient info, triage note, one consent block (vs. two bilingual duplicates), vitals, exam findings, treatment/investigation, a shorter drug chart, diagnosis + disposition checkboxes, signatures.
4. `ServiceOrderPdfPrintController` resolves `$serviceOrder->service->department->service_order_template`, falling back to `ServiceOrderTemplate::default()` (the detailed template) when unset — existing departments keep their current behavior unless explicitly reconfigured.
5. Filament `ServiceDepartmentResource` form gets a "Service Order Print Template" `Select` (nullable, placeholder shows the default); the table gets a matching badge column.

```php
$template = $serviceOrder->service?->department?->service_order_template ?? ServiceOrderTemplate::default();

$html = view($template->view(), [...])->render();
```

### Files changed

- `app/Enum/ServiceOrderTemplate.php` (new)
- `database/migrations/2026_08_10_162913_add_service_order_template_to_service_departments_table.php` (new)
- `resources/views/pdfs/serviceorder-triage-compact.blade.php` (new)
- `app/Models/ServiceDepartment.php` — fillable + cast
- `app/Http/Controllers/Prints/ServiceOrderPdfPrintController.php` — template resolution
- `app/Filament/Admin/Resources/ServiceDepartments/ServiceDepartmentResource.php` — form select + table badge
- `tests/Feature/Filament/Admin/ServiceDepartmentResourceTest.php` (new)
- `tests/Feature/Models/ServiceDepartmentModelTest.php`, `tests/Feature/Prints/ServiceOrderPdfPrintTest.php` — extended

### Tests

```bash
php artisan test --compact tests/Feature/Prints/ServiceOrderPdfPrintTest.php tests/Feature/Models/ServiceDepartmentModelTest.php tests/Feature/Filament/Admin/ServiceDepartmentResourceTest.php
```

27 tests / 80 assertions, all passing. Full suite run: 650 passed, 1 skipped, 1 failed — the failure (`AbacusClosingIntegrationTest`) was confirmed pre-existing on `main` (unrelated `app:sync-old-hims` exit-code assertion), not introduced by this change.

New coverage:
- Model casts `service_order_template` to the enum; defaults to `null`
- Controller renders the detailed view when no template is configured, and the compact view when `EmergencyTriageCompact` is set (verified via `Pdf::shouldReceive('loadHTML')` argument assertions, since dompdf's binary output can't be inspected for text)
- Compact template content fidelity (patient info, triage category, prescriptions, real department name, disposition checkboxes)
- Filament create action persists the selected template; leaves it `null` when not chosen

### Manual verification

Rendered both templates through dompdf with realistic data (patient, triage, vitals, 2 prescriptions, discharge outcome) and inspected the PDF's `/Count` page-tree value directly: compact template = **1 page**, detailed template = **2 pages**.

### Known limitations / out of scope

- Only two template options exist today (detailed, compact). The enum is the extension point for future department-specific templates (e.g. dedicated OPD/Dental layouts) — the `TransactionPdfPrintController` `variant`-param pattern is a precedent if a request-time override (vs. department default) is ever needed.
- The print route/filename (`ED-Clinical-Performa-*.pdf`) is unchanged and still generic across departments — not in scope for this change.

---

## For IT / DevOps

### What changed on the server

One new migration adds a nullable `service_order_template` (VARCHAR) column to `service_departments`. No data backfill needed — existing rows default to `NULL`, which the app treats as "use the detailed template" (identical to current behavior).

### Deployment steps

1. Pull latest code
2. `docker compose up --build` (or standard deploy)
3. `php artisan migrate` — adds the new column
4. No queue/cache changes required

### How to verify after deploy

1. In `/admin` → Services → Service Departments, edit a department and confirm the "Service Order Print Template" select appears with two options plus a "Default" placeholder.
2. Set a department to "Emergency Triage - Compact (1 Page)", print a service order for that department, and confirm the PDF is one page.
3. Confirm a department left unconfigured still produces the original 2-page PDF.

### Rollback

`php artisan migrate:rollback` reverts the column (only if this migration is the most recent). Revert the code changes via git as normal — no destructive data changes are involved since the column is purely additive and nullable.

### Risk

**Low.** Additive, nullable column; default behavior (no template configured) is byte-for-byte the same code path as before this change. Only departments an admin explicitly reconfigures will see different print output.

---

## For Reception Staff

### What changed?

Nothing changes for existing departments unless an administrator configures otherwise. If your department is switched to the new "Compact" template, printed service order forms will now be **one page instead of two**, with the same core information (patient details, triage note, vitals, treatment, prescriptions, discharge disposition) laid out more tightly.

### When will you notice the difference?

Only after an administrator changes a department's print template setting in the admin panel. Ask your administrator which departments have been switched if you're unsure which layout to expect.

---

## For Hospital Administration

### Business impact

| Scenario | Before | After |
|---|---|---|
| Printing emergency/triage service orders | Always 2 pages per order, regardless of department | Configurable per department — compact 1-page option available |
| Paper/toner cost for high-volume departments | Fixed at 2 pages/order | Reducible to 1 page/order where the compact template is selected |
| Existing workflows | N/A | Unaffected unless an admin opts in per department |

### Compliance relevance

The compact template retains all PHC/HIPAA-relevant structured fields (patient demographics, triage time/category, vitals, diagnosis, prescriptions with prescriber and time, disposition/outcome, doctor signature) — it drops only redundant elements (duplicate bilingual consent boxes, extended review/follow-up scheduling rows) that aren't part of the minimum structured record. No clinical or identifying data is omitted.

### Data impact

No patient data is affected. This is a presentation/print-layout change only, controlled by a new per-department setting.
