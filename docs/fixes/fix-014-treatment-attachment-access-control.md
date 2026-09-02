# Fix #014 — Treatment Attachments Had No Auth and Sat on the Public Disk

**GitHub Issue:** [afaryab/hospital-care#64](https://github.com/afaryab/hospital-care/issues/64)
**Severity:** High
**Status:** ✅ Fixed
**Branch:** `fix/treatment-attachment-access-control`
**Date:** 2026-08-19

---

## For Developers

### What was wrong

`Api\DepartmentController` handles X-ray, ultrasound, dental, lab, and emergency treatment attachments (photos, scans, PDFs) for every department:

- `uploadAttachment()` stored files on the **`public`** disk (`storage/app/public`, symlinked and served directly by the webserver) and had **no `authorize()` call at all** — any authenticated user, regardless of role or department assignment, could attach a file to any service order.
- The stored `url` accessor (`TreatmentAttachment::getUrlAttribute()`) returned a bare public-disk URL — once known or guessed, the file was viewable by anyone with network access, logged in or not, with no access record.
- `deleteAttachment()` took a route-bound `TreatmentAttachment` with **no ownership/department check** — any authenticated user could delete any department's attachments by ID, since the route only required `auth:sanctum`.

### What was added

- **`uploadAttachment()`** now calls `$this->authorize('update', $serviceOrder)` before accepting a file — same `ServiceOrderPolicy` scoping (broad roles + assigned `doctor_id`) already used by `saveTreatmentRecord()`.
- **`deleteAttachment()`** now resolves the attachment's parent service order (`$attachment->treatmentRecord->serviceOrder`) and calls `$this->authorize('update', ...)` before deleting.
- **New `showAttachment()`** action + route (`GET /api/attachments/{attachment}`, name `api-attachments-show`) streams the file inline after an `$this->authorize('view', ...)` check — replaces direct public-disk URLs.
- Files are now stored on the **`local`** (private) disk instead of `public`.
- `TreatmentAttachment::getUrlAttribute()` now returns `route('api-attachments-show', $this->id)` instead of a public storage URL. The frontend (`TreatmentAttachments` component) already consumed `attachment.url` as an opaque string, so no frontend changes were needed.
- **Data migration** (`2026_08_19_074002_move_treatment_attachments_to_private_disk.php`) moves any already-uploaded files from the public disk to the private disk on existing deployments, without touching `file_path` values (the relative path is unchanged — only which disk it resolves against changes).

### Files changed

- `app/Http/Controllers/Api/DepartmentController.php` — `uploadAttachment()`, new `showAttachment()`, `deleteAttachment()`
- `app/Models/TreatmentAttachment.php` — `getUrlAttribute()`
- `routes/api.php` — new `api-attachments-show` route
- `database/migrations/2026_08_19_074002_move_treatment_attachments_to_private_disk.php` — new
- `tests/Feature/Api/TreatmentAttachmentAccessControlTest.php` — new, 8 tests
- `tests/Feature/Api/DepartmentControllerTriageTest.php` — 2 existing tests updated: they exercised an XRAY service order with no `doctor_id` assigned, uploaded/deleted by an unrelated EmergencyDoctor account. That path is now correctly rejected (403), so the tests were updated to assign the acting doctor to the service order, matching the pattern already used elsewhere in that file.

### Tests

```bash
php -d memory_limit=1024M vendor/bin/pest --compact
```

876 tests, 0 failures (8 new).

---

## For IT / DevOps

### What changed on the server

- New migration moves existing attachment files (if any) from `storage/app/public/treatment-attachments/...` to `storage/app/private/treatment-attachments/...`. No schema/column changes.
- No new environment variables.

### Deployment steps

1. Pull the latest code.
2. Run migrations: `php artisan migrate`.
3. Standard deploy (`docker compose up --build` or equivalent).

### How to verify after deploy

1. Log in as a doctor/technician assigned to a service order; upload an attachment; confirm it appears and opens.
2. Log in as a doctor/technician **not** assigned to that order; confirm both uploading and viewing that attachment now return 403.
3. Confirm attachment thumbnails/previews still render for authorized users (the `<img>`/`<a>` tags now hit an authenticated route instead of a static public URL, but the browser session cookie carries the auth automatically).
4. If the hospital has pre-existing attachments, spot-check that `storage/app/public/treatment-attachments/` is now empty and the files are viewable through the app (confirms the migration moved them).

### Rollback

Revert the application files listed above. If migrations already ran, `php artisan migrate:rollback` will move files back to the public disk before the down-migration for the code revert.

### Risk of this change

**Low-to-medium.** Existing attachment URLs cached in a browser or bookmarked will stop working (expected — that access path is what's being closed). Legitimate in-app viewing continues to work unchanged. Watch for 403s on attachment upload/view from doctor accounts after deploy — same signal as other `doctor_id`-scoped fixes: check the record's assigned doctor.

---

## For Reception Staff

### Does anything look different?

**No.** Attachment upload, viewing, and deletion inside the app work exactly as before for accounts that are actually assigned to the relevant service order.

---

## For Hospital Administration

### Business risk mitigated

| Risk | Before fix | After fix |
|---|---|---|
| X-ray/ultrasound/lab images reachable by anyone who knows or guesses the URL, no login required | Yes — public disk, no auth | No — private disk, authenticated + authorized route only |
| Any logged-in staff account (any role, any department) deleting any other department's clinical attachments | Yes | Restricted to the record's assigned doctor/broad-access roles |
| Any logged-in staff account attaching files to a service order they have no relationship to | Yes | Blocked |

### Compliance relevance

**PHC Guidelines & HIPAA:** clinical images (X-rays, ultrasounds, dental photos) are PHI and must be access-controlled and non-publicly-reachable. Storing them on a public, symlinked disk with no auth was a direct exposure of PHI to anyone with network access; this fix closes that gap and applies the same clinical-relationship scoping used elsewhere in the system (see fix #006, `docs/fixes/fix-006-patient-record-access-control.md`).

### Financial impact

No cost to deploy. No downtime required beyond the standard migration step.
