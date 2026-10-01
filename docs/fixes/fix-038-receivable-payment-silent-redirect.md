# Fix #038: Receivable Payment Silently Failed for Multi-Service Bills

**GitHub Issue:** [afaryab/hospital-care#107](https://github.com/afaryab/hospital-care/issues/107)
**Severity:** High (counter couldn't collect receivables; revenue)
**Status:** ✅ Fixed
**Branch:** `fix/receivable-payment-silent-redirect`
**Date:** 2026-10-01

---

## For Developers

### Root cause

`POST /RECEAVEABLES-PAYMENT` (`WebController::receaveablesPayment`) returned **302** without collecting anything, and showed no message:

1. **A leftover single-service check.** It rejected any receivable whose original transaction had more than one element ("Invalid receaveable transaction elements."). Since #95, `ReceivableSettlementService::settle()` records only the payment transaction and never uses the original elements, so the check was obsolete. It blocked every receivable from a bill with two or more services. A receivable without a transaction would also have crashed on `->elements`.
2. **Invisible errors.** The form shows errors only for its own fields. The server reported failures as `error`, as `amount` (settlement rejections: paid/draft/cancelled, or more than the outstanding amount) and as `receaveable_id`. Every rejection therefore looked like a silent redirect.
3. **Note dropped.** The form posts `receaveable_note`, but the controller read `note`.

### What was changed

- **The obsolete element check is removed.** Settlement works for any receivable.
- **Settlement rejections are re-reported against `amount_to_collect`,** so the form shows them under the amount field. The form also shows `receaveable_id`, `error` and `message` errors.
- **The note is validated as `receaveable_note`** (max 1000) and stored on the payment transaction's `notes`.

### Tests

`tests/Feature/Web/ReceaveablesPaymentTest.php`:
- full collection of a receivable from a two-service bill;
- partial collection, which keeps the balance and stores the note;
- overpayment and already-paid rejections, which report under `amount_to_collect`;
- no open counter, which redirects to open one.

## For IT / DevOps

Code only, no migration. Run `npm run build`. To confirm a stuck receivable was hit by cause 1:
```sql
SELECT COUNT(*) FROM transaction_elements WHERE transaction_id = (SELECT transaction_id FROM receaveables WHERE id = 796);  -- > 1 = cause 1
```

## For Reception Staff

Collecting a pending amount now works even when the original bill had several services. If a collection can't go through (for example, the amount is more than what's due, or it's already paid), the form tells you why. Notes you type are saved.

## For Hospital Administration

Outstanding receivables from multi-service bills (often the larger ones) could not be collected at the counter. That revenue can now be recorded. No past data changed.
