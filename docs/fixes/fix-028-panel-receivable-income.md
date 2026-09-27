# Fix #028 — Administrative Transactions: Panel Receivable Income

**GitHub Issue:** [afaryab/hospital-care#95](https://github.com/afaryab/hospital-care/issues/95)
**Severity:** Feature
**Status:** ✅ Done
**Branch:** `fix/number-generators-skip-soft-deleted-rows`
**Date:** 2026-09-27

---

## For Developers

### Background

Panels (insurance / corporate) often pay by bank transfer or cash rather than cheque. Before this change, the only way to settle a receivable was the counter flow (`WebController::receaveablesPayment`), which needs an open counter and settles one receivable at a time. Panel cheques only *link* a receivable; they never settle it.

### What was changed

- **`App\Services\ReceivableSettlementService`**:
  - `settle(Receaveable, amount, attrs)` locks the receivable row and rejects payments that are ≤ 0, exceed the outstanding amount, or target a paid/cancelled/draft receivable. It creates one INCOME `Transaction` with `receaveable_id` and no `TransactionElement` (revenue was already recognised when the receivable was created), then updates the outstanding amount and status.
  - `settleForPanel(panelId, allocations, attrs)` does this for several receivables in one DB transaction, checks that each belongs to the panel, and rejects duplicates.
  - The counter's `receaveablesPayment` now uses `settle()`, so overpayments that used to be silently clamped to 0 are now rejected.
- **Filament › Administrative Transactions › Create:**
  - A new **Income Type** field: *General income* or *Panel receivable payment*.
  - For a panel payment, the admin picks the panel, then adds each receivable to settle with its own amount. The admin chooses these manually; the receivable dropdown shows patient, PS number, date and outstanding amount. A running total is shown.
  - Payment method, bank account and reference work as before.
  - `CreateAdministrativeTransaction::handleRecordCreation()` calls `settleForPanel()`. It creates **one transaction per receivable**, so `Receaveable::payments()`, reports and the service-order views keep working.
- **Edit:** changing the amount of a settlement transaction moves the receivable balance by the difference (`applyCollectedAmountDelta`), as the counter transaction editor already did.

### Files changed

`app/Services/ReceivableSettlementService.php` (new), `app/Filament/Admin/Resources/AdministrativeTransactions/Schemas/AdministrativeTransactionForm.php`, `.../Pages/CreateAdministrativeTransaction.php`, `.../Pages/EditAdministrativeTransaction.php`, `app/Http/Controllers/WebController.php`, `tests/Feature/Finance/AdministrativeTransactionTest.php` (multi-receivable settlement, overpayment blocked, wrong panel blocked, edit delta, general income unchanged).

### Not yet covered

- Admin transactions have no closing, so no Abacus journal entry is posted automatically (true for all admin transactions today).
- Deleting a settlement transaction does not restore the receivable balance.
- Marking a panel cheque "received" still does not settle receivables. It could reuse the same service.

## For IT / DevOps

No schema change.

## For Reception Staff

Nothing changes, except that the counter now refuses to collect more than a receivable's outstanding amount.

## For Hospital Administration

**Finance › Administrative Transactions › New**:

1. Choose Direction **Income**, then Income Type **Panel receivable payment**.
2. Pick the panel.
3. Add each patient receivable the transfer or cash covers, with the amount for each.
4. Choose the payment method, and the bank account for transfers.

Each receivable's balance updates immediately, and the payment shows on the patient's receivable history. Overpayments and receivables from another panel are blocked.
