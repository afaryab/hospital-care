# Fix #033 — Emergency Triage Note Print Rework (and BP Never Being Saved)

**GitHub Issue:** [afaryab/hospital-care#100](https://github.com/afaryab/hospital-care/issues/100)
**Severity:** High (clinical data loss for blood pressure)
**Status:** ✅ Fixed
**Branch:** `fix/number-generators-skip-soft-deleted-rows`
**Date:** 2026-09-28

---

## For Developers

### Bugs found

- **Blood pressure was never stored, in any department.** The OPD, IND and department APIs create `VitalSign` rows with `bp_systolic`/`bp_diastolic`, but the columns are `blood_pressure_systolic`/`_diastolic`. Eloquent silently dropped the unknown keys. `VitalSign` now has two-way `bp_systolic`/`bp_diastolic` attribute aliases (fillable and appended), which fixes every caller and every form without touching them.
- **Missing fields:** GCS and BSL weren't captured anywhere, so they printed blank.
- **Airway/Breathing:** the print showed the literal text "Clear Y/N", although the Emergency form already records Airway and Breathing as examination findings.
- **MR#:** the print showed the service-order number as the MR#.
- **Dispositions:** Admitted and LAMA couldn't be recorded, so their boxes could never be ticked.
- **Footer:** page numbers read "1 / 0", because dompdf can't resolve the CSS `counter(pages)`.

### Template lookup

`ServiceOrderPdfPrintController::resolveView()` now tries, in order:

1. `pdfs/service/{service slug}.blade.php`
2. `pdfs/service_department/{department slug}.blade.php`
3. the department's configured `ServiceOrderTemplate`
4. the default template.

Slugs are lower-cased, and any run of non-alphanumeric characters becomes `_` (so `EMG` → `emg`, `M.O_MOR` → `m_o_mor`). The new triage layout is `pdfs/service_department/emg.blade.php`, so it applies to Emergency only. The Detailed and Compact templates are unchanged.

### New triage layout

A4, one page, greyscale only. It is built with tables because dompdf doesn't support flex or grid. Top to bottom:

- **Title:** "EMERGENCY · TRIAGE NOTE" badge, with MR# (PS number) and ER Visit# shown once each.
- **Patient grid:** Age / Sex prints as `18 Y · Male` (unit Y, M or D), via `PatientDisplay`.
- **Presenting Complaints and General Examination:** Airway, Breathing and the other findings as recorded. The header "Complain" line was removed.
- **Vitals tiles:** BP, Pulse, RR, Temp, SpO₂, GCS, BSL, Weight. A missing value prints *Not recorded* in grey italics.
- **Past History:** HTN, DM, Asthma, IHD as Yes / No / —, plus an Allergies line.
- **Treatment Given in ER:** a Drug · Dose · Route table.
- **Investigations Advised.**
- **Discharge Medicines:**
  - columns: Qty/dose (from the dosage form), Morning + Noon + Night pattern, When to take, Duration; drug names wrap instead of being cut off;
  - the legend `1 + 1 + 1 = Morning + Noon + Night`;
  - "Prescribed <date time> · <Doctor>" shown once in the section bar.
- **Diagnosis:** ICD-coded diagnoses print as `Description (CODE)`, followed by any free-text diagnosis.
- **Advice / Follow-up.**
- **Dispositions:** aligned checkboxes, with the recorded outcome pre-ticked.
- **Consent and four signature lines.**
- **Page x of y** in the footer.

### Data changes

| Data | Where |
|---|---|
| GCS, BSL | new `vital_signs.gcs`, `vital_signs.blood_glucose` columns |
| HTN / DM / Asthma / IHD, allergies | new **patient-level** columns `patients.history_*` (nullable boolean) and `patients.allergies` (encrypted); saved from the Emergency form through `past_history` |
| Investigations advised, advice, admitted-to | `treatment_records.department_specific_data` (existing encrypted JSON) |
| ER vs discharge medicines, dosage form | `given_in_er` and `form` on each prescription row. Older rows without the flag count as "given in ER" if they have a `given_at` time. The form is filled from the drug picker, with a fallback lookup of `drugs.type`. |
| Admitted, LAMA | new `TreatmentOutcome` cases (string column, no migration) |

- **Frequency mapping:** the mapping (TDS→1+1+1, BD→1+0+1, OD→1+0+0, HS→0+0+1, QID→"4 times/day", …) and the dosage-form units live in `config/prescriptions.php`, read by `App\Helpers\PrescriptionFormatter`.
- **Page numbering:** `App\Helpers\PdfPageNumbers` draws "Page x of y" through a dompdf `end_document` callback. This fixes the footer on every service-order PDF, not just Emergency.

### Emergency form

The Emergency form (`showEmergencyDetails`) adds:
- GCS and BSL inputs;
- a **Past History, Investigations & Advice** section;
- a **Given in ER** checkbox on each prescription row.

The discharge dialog adds **Admitted** (with an "admitted to" field) and **Left Against Medical Advice**. Other departments' forms are unchanged.

### Files changed

- Migrations: `2026_09_28_022904_add_gcs_and_blood_glucose_to_vital_signs_table`, `2026_09_28_022904_add_past_history_to_patients_table`
- Models and enums: `app/Models/{VitalSign,Patient}.php`, `app/Enum/TreatmentOutcome.php`
- Config and helpers: `config/prescriptions.php`, `app/Helpers/{PrescriptionFormatter,PatientDisplay,PdfPageNumbers}.php`
- Controllers: `app/Http/Controllers/Api/DepartmentController.php`, `app/Http/Controllers/EmergencyDoctorController.php`, `app/Http/Controllers/Prints/ServiceOrderPdfPrintController.php`
- Views: `resources/views/pdfs/service_department/emg.blade.php` (new), `resources/views/pdfs/partials/letterhead-footer.blade.php`
- Frontend: `resources/js/elements/dept-portal/{DeptPatientForm,DischargeDialog}.tsx`, `resources/js/pages/emg/patient.tsx`
- Tests: `tests/Feature/Prints/EmergencyTriageNoteTest.php` (16 tests); the PDF-facade mocks in `ServiceOrderPdfPrintTest` now allow `setCallbacks`.

### Not covered

- Blood pressure readings entered before this fix were never stored and can't be recovered.
- Diagnosis abbreviations are expanded only for ICD-coded diagnoses, as agreed. Free-text diagnoses print exactly as typed.

## For IT / DevOps

- **Migrations:** `php artisan migrate` adds two nullable columns to `vital_signs` and five to `patients`. Both are reversible with `migrate:rollback`.
- **Frontend:** rebuild with `npm run build` (done by the Docker image build).
- **Frequency codes:** new codes (for example `Q6H`) can be added to `config/prescriptions.php` without code changes. Run `php artisan config:clear` after editing it if the config is cached.

## For Reception Staff

Nothing changes at the counter.

## For Hospital Administration

- **Clinical safety:** blood pressure is now actually saved in every department. Until this fix, every BP entered was silently discarded.
- **Printed Emergency note:**
  - one clear page that shows exactly what was recorded, marking gaps as "Not recorded" instead of leaving them blank;
  - GCS, BSL, SpO₂ and weight are now included;
  - the patient's past history and allergies follow them to every future visit.
- **Patient instructions:** discharge medicines print as plain instructions (`1 + 0 + 1`, "Morning, Night") that patients and attendants can follow without knowing codes like BD or TDS.
