# Abacus Double-Entry Accounting Conventions

The Abacus package (`packages/processton/abacus/`) provides a double-entry accounting system integrated as a Filament v4 plugin on the **Accounts panel** (`/accounts`).

---

## Architecture

- **Package:** `packages/processton/abacus/`
- **Panel:** Accounts (`/accounts`) via `AbacusPlugin::make()`
- **Tables:** `abacus_currencies`, `abacus_years`, `abacus_chart_of_accounts`, `abacus_incomings`, `abacus_transactions`
- **Models:** `AbacusChartOfAccount`, `AbacusIncoming`, `AbacusTransaction`, `AbacusYear`, `Currency`

---

## Core Concepts

### AbacusIncoming (Source Document)

An `AbacusIncoming` is the entry point — a source document (like a journal voucher) that gets "converted" into balanced debit/credit `AbacusTransaction` entries.

- **Always create an AbacusIncoming** when recording any financial event (closing, voucher payment, etc.)
- Link to the source record via `closing_id` FK and `reference` field (e.g., CT number)
- The `amount` field holds the gross total of the transaction

### AbacusTransaction (Double-Entry Ledger)

Each entry links to:
- `abacus_incoming_id` — the source document
- `abacus_chart_of_account_id` — which COA account
- `abacus_year_id` — the financial year book
- `entry_type` — `debit` or `credit`

**Rule:** Total debits MUST equal total credits for each incoming.

### AbacusYear (Financial Year Books)

- Pakistan financial year: **July 1 – June 30**
- If a year book doesn't exist for a given date, create one automatically
- Status: `0` = inactive, `1` = active, `2` = archived

---

## Chart of Accounts (COA) Reference

| Code | Name | Base Type | Type |
|------|------|-----------|------|
| 1110 | Cash in Hand | asset | cash |
| 1120 | Bank Accounts | asset | bank |
| 1130 | Accounts Receivable | asset | receivables |
| 4100 | Sales Revenue | income | sales |
| 4200 | Service Revenue | income | services |
| 4300 | Other Income | income | other_income |
| 5010 | Refunds | expense | refunds |
| 5020 | Discounts | expense | discounts |
| 5100 | COGS | expense | cogs |
| 5200 | Salaries & Wages | expense | salaries |
| 5300 | Rent Expense | expense | rent |
| 5400 | Utilities | expense | utilities |
| 5900 | Other Expenses | expense | misc |

---

## When to Create Abacus Incomings

**Always create an AbacusIncoming with balanced entries for:**

1. **Closing Report & Receive** — When admin reports a closing in Filament (if `abacus_auto_map_accounts` hospital setting is ON):
   - **Cash Sales**: Debit Cash in Hand (1110) / Credit Service Revenue (4200)
   - **Credit Sales (cash portion)**: Debit Cash in Hand (1110) / Credit Service Revenue (4200)
   - **Credit Sales (receivable portion)**: Debit Accounts Receivable (1130) / Credit Service Revenue (4200)
   - **Receivable Payments**: Debit Cash in Hand (1110) / Credit Accounts Receivable (1130)
   - **Regular Expenses**: Debit Other Expenses (5900) / Credit Cash in Hand (1110)
   - **Refunds**: Debit Refunds (5010) / Credit Cash in Hand (1110)
   - **Discounts**: Debit Discounts (5020) / Credit Cash in Hand (1110)
   - Reference: CT number (`CT/YYYY/MM/NNNN`)
   - Logic lives in `AbacusClosingService::createEntriesForClosing()`

2. **Closing sync (migration)** — Same logic via `SyncOldHIMS --entity=abacus-closings`

3. **Expense voucher payment** — When a voucher is paid:
   - Debit appropriate expense account / Credit Cash in Hand (1110)
   - Reference: VC number (`VC/YYYY/MM/NNNN`)

4. **Any new financial event** — Salary payments, asset purchases, etc.

---

## AbacusClosingService

Shared service at `app/Services/AbacusClosingService.php` for creating Abacus entries from closings.

### Usage

```php
$service = new AbacusClosingService;
$incoming = $service->createEntriesForClosing($closing);
```

### Transaction Classification

Transactions in a closing are categorized as:
- **Cash Sales**: INCOME tx without `receaveable_id` and no associated Receaveable
- **Credit Sales**: INCOME tx that created a Receaveable (patient underpaid)
- **Receivable Payments**: INCOME tx with `receaveable_id` set (collecting existing debt)
- **Regular Expenses**: EXPENSE tx where category type is NOT RFND or DISC
- **Refunds**: EXPENSE tx where `expenseCategory.type = 'RFND'`
- **Discounts**: EXPENSE tx where `expenseCategory.type = 'DISC'`

### Hospital Setting

Toggle `abacus_auto_map_accounts` in Hospital Settings enables auto-creation on Report & Receive.
Check with: `AbacusClosingService::isAutoMapEnabled()`

---

## Double-Entry Patterns

### Counter Closing (Granular via AbacusClosingService)

```php
// Cash sales: Dr Cash in Hand, Cr Service Revenue
// Credit sales (cash): Dr Cash in Hand, Cr Service Revenue
// Credit sales (receivable): Dr Accounts Receivable, Cr Service Revenue
// Receivable payments: Dr Cash in Hand, Cr Accounts Receivable
// Regular expenses: Dr Other Expenses, Cr Cash in Hand
// Refunds: Dr Refunds, Cr Cash in Hand
// Discounts: Dr Discounts, Cr Cash in Hand
```

### Pure Expense (Voucher Payment)

```php
// Debit the expense account, Credit Cash in Hand
AbacusTransaction::create([...debit expense account, credit Cash in Hand]);
```

---

## Financial Year Resolution (Pakistan)

```php
// Pakistan FY: July 1 to June 30
// Jan-June → FY started previous July
// Jul-Dec → FY started this July
$fyStartYear = $date->month >= 7 ? $date->year : $date->year - 1;
$fyStart = Carbon::create($fyStartYear, 7, 1);
$fyEnd = Carbon::create($fyStartYear + 1, 6, 30);
```

---

## Key Reminders

- Always seed COA via `ChartOfAccountsSeeder` before using Abacus
- Use `AbacusIncoming::isBalanced()` to verify entry integrity
- Link incomings to closings via `closing_id` FK for traceability
- Abacus models live in `Processton\Abacus\Models\` namespace
- The conversion UI is at the EditAbacusIncoming page (Accounts panel)
