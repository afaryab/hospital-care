# Fix #026 — Patient Photo Capture (Webcam or Upload)

**GitHub Issue:** [afaryab/hospital-care#93](https://github.com/afaryab/hospital-care/issues/93)
**Severity:** Feature
**Status:** ✅ Done
**Branch:** `fix/number-generators-skip-soft-deleted-rows`
**Date:** 2026-09-27

---

## For Developers

### Design

- `Patient` implements `HasMedia` (Spatie MediaLibrary, already installed) with a `photos` collection on the **private `local` disk**, since a patient photo is PHI. Every capture is kept; `Patient::currentPhoto()` returns the newest, so earlier photos stay auditable. No change to the `patients` table.
- Routes follow the record-number URL pattern:
  - `POST /PS/{year}/{month}/{number}/photo` → `patient-photo-store` (`PatientPolicy::update`, `StorePatientPhotoRequest`: jpg/png/webp, max 5 MB, `source` = camera|upload)
  - `GET /PS/{year}/{month}/{number}/photo` → `patient-photo-show` (`PatientPolicy::view`, streamed with `Cache-Control: private`)
  - Both are registered before the `{departmentKey}` route so `photo` is not read as a department.
- Every change writes an activity-log entry `photo_updated` with the new and previous media ids, plus the source.
- `WebController::patient()` passes `patientPhotoUrl` (cache-busted with `?v={mediaId}`) and `canUpdatePatient`.
- Frontend: `resources/js/elements/patient/patient-photo-dialog.tsx`:
  - **Webcam** tab: **Start camera** opens a live preview (`getUserMedia`), there is a camera picker when several are attached, **Snap** captures a JPEG from a canvas, and **Retake** / **Save photo** follow.
  - **Upload** tab: a file picker.
  - Camera tracks are stopped whenever the dialog closes.
- `PatientMiniCard` accepts `photoUrl` and `onPhotoClick`. Only the patient profile page passes them, so other screens are unchanged.

### Files changed

`app/Models/Patient.php`, `app/Http/Controllers/PatientPhotoController.php`, `app/Http/Requests/Patients/StorePatientPhotoRequest.php`, `app/Http/Controllers/WebController.php`, `routes/web.php`, `resources/js/elements/patient/patient-photo-dialog.tsx`, `resources/js/elements/patient/mini-card.tsx`, `resources/js/pages/patient.tsx`, `tests/Feature/Web/PatientPhotoTest.php` (6 tests: webcam save + audit log, history kept, 403 without update rights, non-image rejected, served only to viewers, page props).

## For IT / DevOps

- **Browsers allow webcam access only over HTTPS (or `localhost`).** On plain `http://<LAN-IP>` the Webcam tab shows a message and staff must use Upload. To use webcams at reception, serve the app over HTTPS (e.g. an internal certificate on the reverse proxy).
- Photos are written to `storage/app/private/{media-id}/…` and are included in the normal backup of `storage/app`.
- No migration (MediaLibrary's `media` table already exists).

## For Reception Staff

On a patient's profile, click the patient picture:

1. Press **Start camera**. The live picture appears.
2. Ask the patient to look at the camera and press **Snap**.
3. Press **Retake** if needed, then **Save photo**.

You can also switch to **Upload** and pick an image file. If the webcam tab says camera access is blocked, tell IT (the site needs HTTPS).

## For Hospital Administration

- Photo identification reduces wrong-patient errors at reception and in clinics.
- PHI handling: photos are private files served only to staff allowed to view the patient, every change is audit-logged with who and when, and earlier photos are kept (never overwritten).
