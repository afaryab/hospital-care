# Fix #039: Peds Slips Silently Rejected at Counters Set Up Before PED Existed

**GitHub Issue:** [afaryab/hospital-care#108](https://github.com/afaryab/hospital-care/issues/108)
**Severity:** High (Peds billing blocked; revenue)
**Status:** ✅ Fixed
**Branch:** `fix/receivable-payment-silent-redirect`
**Date:** 2026-10-01

---

## For Developers

### Root cause

`POST /TR-CREATE` with `department_key: "PED"` returned **302** and recorded no payment.

- **The rule:** `WebController::transactionStore` enforces `Reception::allowed_departments`.
- **What changed:** the Paeds M.O services used to be billed under **OPD**. #98 (`2026_09_26_194405_move_paeds_services_to_ped_department`) moved them to the new **PED** department.
- **The effect:** counters whose allowed list was set before that (for example `["OPD", …]`) lost the ability to bill them. Every Peds slip was rejected with "This reception is not allowed to process transactions for the PED department."
- **Why nobody saw it:** the bill screen only rendered field errors, so the message was never shown. The generic "An error occurred…" from the save step was invisible too, and its exception was logged without a stack trace (or, for voucher payments, not at all).

### What was changed

- **Migration `2026_10_01_184057_add_ped_to_receptions_allowing_opd`:**
  - Receptions whose `allowed_departments` include `OPD` but not `PED` get `PED` added. Unrestricted receptions (no list) and receptions without OPD are untouched.
  - The change is recorded in `activity_log` (event `add_ped_to_receptions_allowing_opd`, with the reception IDs).
  - `down()` removes PED only from those receptions. The log entry stays, because the audit trail is append-only.
- **Bill screen (`counter/income.tsx`):** `message` and `error` errors now appear as a toast and as a red notice under **Generate Bill**.
- **Logging:** the three transaction catch blocks now `report($e)`, which records the full exception (and sends it to Sentry if configured) instead of a bare `Log::error` message.

### Tests

`tests/Feature/Counter/PedsBillingTest.php`:
- an unrestricted counter can bill Peds;
- an OPD-only counter is rejected with the reason in `message`;
- the migration fixes exactly the right counters, the slip then succeeds, and rollback reverts only what the migration changed.

## For IT / DevOps

- **Deploy:** `php artisan migrate`, then `npm run build`.
- **Immediate workaround without deploying:** Admin → Receptions → edit the counter → add **Peds** to *Allowed departments*.
- **To check which counters are affected:**
  ```sql
  SELECT id, name, allowed_departments FROM receptions WHERE JSON_CONTAINS(allowed_departments, '"OPD"') AND NOT JSON_CONTAINS(allowed_departments, '"PED"');
  ```

## For Reception Staff

Peds slips go through again at counters that could bill OPD. If a slip is ever refused, the screen now says why instead of doing nothing.

## For Hospital Administration

- Peds visits billed since the Peds department was added may have been refused at the counter. Ask reception whether any were collected outside the system.
- Counters keep exactly the departments they had, with Peds added only where OPD was already allowed. Each change is in the activity log.
