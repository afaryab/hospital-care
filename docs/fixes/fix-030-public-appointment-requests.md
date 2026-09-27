# Fix #030 — Public Appointment Requests (Page + API)

**GitHub Issue:** [afaryab/hospital-care#97](https://github.com/afaryab/hospital-care/issues/97)
**Severity:** Feature
**Status:** ✅ Done
**Branch:** `fix/number-generators-skip-soft-deleted-rows`
**Date:** 2026-09-27

---

## For Developers

### Design

Anonymous users **request** an appointment; reception **confirms** it. Anonymous users never create patient records and never see patient data.

- **`appointment_requests` table:**
  - `reference` (UUID)
  - `name`
  - `contact` (encrypted with `SafeEncrypted`) and `contact_hash`
  - `gender`, `age_years`
  - `service_id`, `preferred_date`, `preferred_time` (morning / afternoon / evening), `notes`
  - `status` (`AppointmentRequestStatus`: pending / confirmed / rejected)
  - `patient_id`, `appointment_id`, `handled_by`, `handled_at`, `rejection_reason`
  - `ip_address`, soft deletes
  - Status changes are activity-logged.
- **Public web page:** `GET/POST /book-appointment` (Inertia `public/book-appointment`), then `/book-appointment/{reference}` shows the confirmation.
- **Public API (versioned):**
  - `GET /api/v1/public/services`
  - `POST /api/v1/public/appointment-requests` (201 `{data: {reference, status, preferred_date, preferred_time, scheduled_at}}`)
  - `GET /api/v1/public/appointment-requests/{reference}`
  - Responses never include name or phone.
- **Abuse controls:**
  - `throttle:public-booking` (5/min and 30/day per IP) on submissions.
  - A hidden `website` honeypot field (`prohibited`).
  - Date must be between today and 60 days ahead; phone must have at least 10 digits.
  - Only services with `generate_service_order = true` can be chosen.
- **Reception:** `GET /appointments/requests` (`AppointmentRequestPolicy`: receptionist or admin) lists requests and shows existing patients who share the phone number (matched by `contact_hash`).
  - **Confirm:** choose an existing patient or register a new one from the request, set service and time. `AppointmentRequestService::confirm()` books through the existing `AppointmentService::book()`, so priority mode, draft receivables and materialisation all apply.
  - **Reject:** requires a reason.
  - A request can only be handled once (row lock).
- Linked from an **Online requests** button on the appointments calendar and from the command palette.

### Files changed

- Migration: `2026_09_26_193838_create_appointment_requests_table`
- `app/Models/AppointmentRequest.php`, `app/Enum/AppointmentRequestStatus.php`, `database/factories/AppointmentRequestFactory.php`
- `app/Services/AppointmentRequestService.php`, `app/Services/BookableServices.php`, `app/Policies/AppointmentRequestPolicy.php`
- `app/Http/Requests/Appointments/{Store,Confirm,Reject}AppointmentRequestRequest.php`
- `app/Http/Controllers/PublicAppointmentController.php`, `app/Http/Controllers/Api/V1/PublicAppointmentRequestController.php`, `app/Http/Controllers/AppointmentRequestController.php`
- `routes/web.php`, `routes/api.php`, `app/Providers/AppServiceProvider.php` (rate limiter and policy)
- Frontend: `resources/js/pages/public/{book-appointment,appointment-requested}.tsx`, `resources/js/pages/appointments/requests.tsx`, `resources/js/elements/appointments/public-booking-shell.tsx`, `resources/js/pages/appointments/calendar.tsx`
- `tests/Feature/Appointments/AppointmentRequestTest.php` (10 tests)

## For IT / DevOps

- **Migration:** `php artisan migrate`, which creates `appointment_requests`. Rollback: `migrate:rollback` drops it.
- Publish `https://<host>/book-appointment` on the hospital website, or have the website or app call `/api/v1/public/...`.
- The rate limiter uses the client IP. Behind a reverse proxy, make sure trusted proxies are configured, or every visitor will share one limit.

## For Reception Staff

1. Go to **Appointments → Online requests**.
2. For each pending request, call the patient.
3. Pick the existing patient if one is suggested (same phone number); otherwise choose **Register as a new patient**.
4. Confirm the service and time, then press **Confirm & book**.

If you can't reach the patient, press **Reject** and give the reason.

## For Hospital Administration

- Patients can request visits online without calling.
- **Data protection:**
  - Online visitors can't see or create patient records.
  - Phone numbers are encrypted.
  - Every confirmation or rejection is recorded against the staff member.
  - Submissions are rate-limited against spam.
