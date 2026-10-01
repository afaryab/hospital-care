# Fix #023 — Service Departments Are Seeder-Managed (No Create/Delete in Admin)

**GitHub Issue:** [afaryab/hospital-care#90](https://github.com/afaryab/hospital-care/issues/90)
**Severity:** Medium
**Status:** ✅ Fixed
**Branch:** `fix/number-generators-skip-soft-deleted-rows`
**Date:** 2026-09-27

---

## For Developers

### What was wrong

`ServiceDepartmentResource` (Filament admin, modal `ManageRecords`) let admins create and delete departments. A department's `slug` is the service-order type code (`OPD`, `EMG`, …) that numbering, queues and dashboards key off, so an ad-hoc department (or a changed slug) produced orders no screen could show. Deleting a department cascades to all of its services (`services.service_department_id` is `onDelete('cascade')`).

While testing, a second bug surfaced: the `image` field was `required`, but every seeded department stores a static `/img/*.png` path the FileUpload can't load — so **no seeded department could be edited at all** (validation failed with "The image field is required").

### What was changed

- `ServiceDepartmentResource::canCreate()`, `canDelete()`, `canDeleteAny()` return `false`; header `CreateAction`, row `DeleteAction` and bulk delete removed.
- `slug` is disabled and not dehydrated on edit.
- `image` is optional on edit and only saved when a new file is uploaded (`->dehydrated(fn ($state) => filled($state))`).

Departments are now created only by `ServicesAndDepartmentsSeeder` (runs on every CLI container start via `migrate --seed`).

### Files changed

- `app/Filament/Admin/Resources/ServiceDepartments/ServiceDepartmentResource.php`
- `app/Filament/Admin/Resources/ServiceDepartments/Pages/ManageServiceDepartments.php`
- `tests/Feature/Filament/Admin/ServiceDepartmentResourceTest.php` — create tests replaced with: create hidden, delete disallowed, edit keeps slug, image upload on edit resolves to `/storage/...`.

### Notes

The data import (`ServiceDepartmentImporter`) can still create departments; it is an admin backup/restore tool and was left as is.

## For IT / DevOps

No schema change. Deploy as usual; new departments are added by editing `ServicesAndDepartmentsSeeder` and redeploying (the seeder is idempotent — `firstOrCreate` on slug).

**Rollback:** revert the two resource files.

## For Reception Staff

Nothing changes on your screens.

## For Hospital Administration

- **Risk mitigated:** accidental department deletion would have silently deleted every service (and price) in it; a mistyped slug would hide service orders from doctors.
- **Fixed:** editing a seeded department's name or print template works again.
- To add a department, ask IT to add it to the seeder.
