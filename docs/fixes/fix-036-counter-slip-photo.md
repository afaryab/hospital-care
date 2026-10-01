# Fix #036: Webcam Photo of Patient or Guardian on Every Counter Slip

**GitHub Issue:** [afaryab/hospital-care#104](https://github.com/afaryab/hospital-care/issues/104)
**Severity:** Feature (P1: counter workflow)
**Status:** ✅ Done
**Branch:** `fix/number-generators-skip-soft-deleted-rows`
**Date:** 2026-10-01

---

## For Developers

### What it does

- **Where it appears:** the counter income flow (`CT-PS` routes) shows a live webcam panel, using the camera attached to the receptionist's PC through the browser's `getUserMedia`. The panel has:
  - a **Patient / Guardian** toggle (Patient by default);
  - a **Snap/Retake** button;
  - a camera picker, whose choice is remembered per browser;
  - a thumbnail of the photo waiting for the next slip.
- **Snap before a patient is chosen:** Snap is available as soon as the camera is live. A photo taken on the search/register screen is stored *unassigned* against the counter (`patient_id` null). When the receptionist then selects or creates a patient, `SlipPhoto::claimForPatient()` gives it to that patient, and auto-capture doesn't fire. Only the most recent unassigned photo from the **same counter**, taken within the last **30 minutes** (`CLAIM_WINDOW_MINUTES`), is claimed, and only if that patient has no pending photo yet.
- **Auto-capture:**
  - When the page opens for a patient (after selecting or creating one) and no photo is pending at this counter, a frame is taken automatically once the camera is live.
  - **Generate bill** also takes one if none is pending (`ensurePhoto()`). That covers a second slip for the same visit, which never goes back through patient selection.
- **Linking photos to slips:**
  - Photos are uploaded immediately as a *pending* `SlipPhoto`: this counter (closing), this patient, no `transaction_id`.
  - `WebController::transactionStore` (INCOME) attaches the latest pending photo for that patient **at the same counter**, inside the slip's DB transaction. Each photo is used for one slip only.
  - A retake soft-deletes the previous pending photo.
  - Relabelling Patient/Guardian is allowed only while the photo is still pending.
- **No camera never blocks billing.** The slip is saved without a photo and shows a **NO PHOTO** flag.
- **Kept separate from the patient profile photo (#93).**
- **Where photos are shown:** the counter (CT) view (a thumbnail on each income row, click to enlarge) and the transaction view. Both show Patient/Guardian, who captured the photo, when, and whether it was snapped or auto-captured.

### Data and access

- **Storage:**
  - Table `slip_photos`: patient, closing, transaction (nullable), subject, source, captured_by, captured_at, soft deletes.
  - The image lives in a MediaLibrary `slip-photo` collection on the **private** `local` disk.
- **Routes:**
  - `POST CT-PS/{year}/{month}/{number}/slip-photo` and `POST CT-PS/slip-photo` (capture, for a patient or unassigned; needs an open counter, returns 409 otherwise; `TransactionPolicy@create`)
  - `PATCH SLIP-PHOTO/{id}` (relabel)
  - `GET SLIP-PHOTO/{id}` (image)
- **`SlipPhotoPolicy`:** admins can view any photo. Otherwise, the capturer can view their own photo, and anyone who can view the attached slip can view its photo. Only the capturer can relabel, and only while the photo is pending.
- **Activity log:** `slip_photo_captured`, `slip_photo_assigned`, `slip_photo_relabelled` and `slip_photo_attached`.

### Also in this change (dark theme follow-up to #102)

The counter step frame (`elements/bullets-wrapper.tsx`) and the dividers on the counter income, counter view and transaction pages used a hardcoded `#06df72` green with no dark override. That was the green border showing in dark mode; it's now neutral grey in dark mode. The dark-blue `text-[#1c398e]` on the counter, patient, register, queue, service-order and receivable pages also gets `dark:text-neutral-200`.

### Files

- **New:**
  - `app/Models/SlipPhoto.php`, `app/Enum/SlipPhotoSubject.php`, `app/Enum/SlipPhotoSource.php`
  - `app/Http/Controllers/SlipPhotoController.php`, `app/Http/Requests/Counter/StoreSlipPhotoRequest.php`, `app/Policies/SlipPhotoPolicy.php`
  - `database/migrations/2026_09_30_184816_create_slip_photos_table.php`, `database/factories/SlipPhotoFactory.php`
  - `resources/js/elements/counter/slip-camera.tsx`, `resources/js/elements/counter/slip-photo-thumb.tsx`
  - `tests/Feature/Counter/SlipPhotoTest.php` (15 tests)
- **Changed:**
  - `app/Http/Controllers/WebController.php`, `app/Models/Transaction.php`, `app/Providers/AppServiceProvider.php`, `routes/web.php`
  - `resources/js/pages/counter/income.tsx`, `counter/view.tsx`, `transaction/view.tsx`
  - The dark-theme files listed above.

## For IT / DevOps

- **Deploy:** `php artisan migrate` (a new `slip_photos` table; nothing existing changes), then `npm run build`.
- **HTTPS is required for the webcam.** Browsers only allow camera access on HTTPS or `localhost`. On PCs that open the app over plain `http://<LAN-IP>`, the camera panel is **hidden** (`display: none`) and slips are flagged NO PHOTO. The panel appears automatically once the app is served over HTTPS. Serve the app over HTTPS, for example with an internal CA or a self-signed certificate trusted on the counter PCs.
- **First run:** each browser asks once for camera permission. It must be allowed for the app's address.
- **Storage:** about 50–150 KB per slip on the private disk (`storage/app/private`). Include it in backups.
- **Rollback:** `php artisan migrate:rollback --step=1` drops the table. Rolling back discards the captured photos, so back up first.

## For Reception Staff

- On the counter screen a small camera box shows the live picture. Pick **Patient** or **Guardian** for whoever is in front of the counter (Patient is already selected).
- The picture is taken **automatically** when you select or register the patient. Press **Snap** (or **Retake**) at any time, even before searching for the patient: the photo is kept for the patient you select next. If you switch Patient/Guardian afterwards, the photo is relabelled.
- Every slip takes its own picture. If the camera isn't working, the slip is still made and marked **NO PHOTO**.

## For Hospital Administration

- Every counter slip now carries a photo of who was present (patient or guardian), with who captured it and when. That's strong evidence against disputed payments and wrong-patient billing, visible from the counter and transaction screens.
- **Compliance:** photos are PHI. They're stored privately, never public, and shown only to admins, the receptionist who took them and users allowed to see that slip. Every capture, relabel and attachment is written to the activity log. They're not added to the patient's profile.
- Slips flagged **NO PHOTO** show where a camera or HTTPS setup needs attention.
