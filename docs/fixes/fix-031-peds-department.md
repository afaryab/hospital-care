# Fix #031 — New Peds (Pediatrics) Department

**GitHub Issue:** [afaryab/hospital-care#98](https://github.com/afaryab/hospital-care/issues/98)
**Severity:** Feature
**Status:** ✅ Done
**Branch:** `fix/number-generators-skip-soft-deleted-rows`
**Date:** 2026-09-27

---

## For Developers

### What was added

- **Department:** `ServicesAndDepartmentsSeeder` creates `PED` ("Peds", `/img/ped.png`, currently a copy of the OPD icon), and the three "Paeds M.O" shift services are defined under it. On existing installs, migration `2026_09_26_194405_move_paeds_services_to_ped_department` moves those services from OPD to PED. Their prices and flags are unchanged; past service orders keep their OPD numbers.
- **Reception payments:** these were already generic, driven by the department slug. PED payments create `PED/…` service orders (`so_short`) and `{PS}/PED/…` (`so_number`). `TransactionElementType::PED` was added for the Filament enum selects.
- **Peds Doctor profile:**
  - new `ped_doctors` table (migration `2026_09_26_194404`), `PedDoctor` model and factory;
  - `User::pedDoctorProfiles()`, the shared `profiles.ped_doctor` key, `isAnyDoctor()`, `cachedDoctors()` and PMDC lookup;
  - Filament user form repeater and infolist entry;
  - the "Peds Doctor" provider type on services and recitation services;
  - included in doctor pickers in reports, voucher lists and the user API;
  - the `ped_doctor` role in `RolesAndPermissionsSeeder`; the first user gets the profile; added to the staff-by-role chart.
- **Doctor workspace:** there is no copied code.
  - `OpdDoctorController` and `Api\OpdController` read a `department` route default (`OPD` or `PED`).
  - New routes: `PED`, `PED/search`, `PED/{id}` (`ped-dashboard`, `ped-search`, `ped-patient`) and `/api/ped/{search,my-queue,service-orders/{id}/treatment-record,service-orders/{id}/status}`.
  - `pages/opd/index.tsx` and `pages/opd/patient.tsx` take a `department` prop (label and URLs) instead of hard-coded OPD routes.
- **Queue display:** `/que/peds` (`hospital-ped-queue`) uses the shared outpatient queue method. The queue page shows the department label passed to it.
- **UI:** sidebar Peds dashboard and queue links; command palette entries; Peds tab and filter in service orders (and the `in:` validation list); `TreatmentFormConfig` PED defaults (same as OPD); service-order badge colour.

### Files changed (main)

- `app/Models/{PedDoctor,User}.php`, `database/factories/PedDoctorFactory.php`, two migrations
- `database/seeders/{ServicesAndDepartmentsSeeder,RolesAndPermissionsSeeder}.php`, `app/Enum/TransactionElementType.php`, `app/Helpers/TreatmentFormConfig.php`
- `app/Http/Controllers/{OpdDoctorController,WebController}.php`, `app/Http/Controllers/Api/{OpdController,UserController}.php`, `routes/{web,api}.php`
- Filament: user form and infolist, service and recitation provider types, report doctor pickers, `StaffByRoleChart`, `ServiceOrderResource`
- Frontend: `app-sidebar.tsx`, `command-palette.tsx`, `lib/{abilities,outpatient-department}.ts`, `pages/opd/{index,patient}.tsx`, `pages/hospital/opd-queue.tsx`, `pages/service-orders/index.tsx`, `elements/serviceorder/filter-and-select-serviceorder.tsx`
- `tests/Feature/Departments/PedsDepartmentTest.php` (8 tests)

### Deliberately not changed

- `app:close-old-service-orders` (scheduled every 5 seconds) closes **OPD** orders still "open" **5 minutes** after creation, though its comment says 12 hours. This looks like a bug, so it was **not** extended to PED. Confirm the intended timeout before aligning the two.
- No Peds LCD operator profile was added. Staff can open `/que/peds`, but LCD-only accounts can't be assigned to it yet.
- Paeds services keep their existing settings (no service order generated, no provider). To get them into the Peds doctors' queue, enable **Generates Service Order** and **Have service provider**, and select **Peds Doctor** as a provider on each service.

## For IT / DevOps

- **Migrations:** `create_ped_doctors_table`, then the Paeds service move. Both are reversible (`migrate:rollback` moves the services back and drops the table).
- The seeder adds the PED department on the next `migrate --seed` (CLI container start).
- If a reception counter has **Allowed departments** set, add Peds to it in Filament › Receptions, or that counter can't take Peds payments.

## For Reception Staff

- A **Peds** department card appears at the counter. Take Peds payments exactly as for OPD.
- The **Paeds M.O** shift services now appear under Peds instead of OPD.
- A **Peds** queue screen is at **Queue → Peds**.

## For Hospital Administration

- Children's visits are tracked separately from adult OPD in service orders, reports and queues.
- To give a doctor the Peds dashboard, add a **Peds Doctor Profile** (with PMDC number) to their user in Filament.
- Configure which Peds services create service orders and which doctors provide them (see the note for developers above).
