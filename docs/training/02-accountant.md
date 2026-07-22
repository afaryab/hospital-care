# Accountant Training Guide

## Who This Guide Is For
Accountants access the **Accounts panel** for financial reports, payroll, receivables, and ledger management.

## Your Portal
**URL:** `/accounts`  
You can also view the **Patient Register** at `/PS`.

---

## Accounts Panel Overview

The accounts panel gives you access to:
- Closing statements (counter daily reports)
- Financial reports (income, expense, receivables)
- Bank accounts and transactions
- Expense vouchers and categories
- Payroll and salary management

---

## Daily Tasks

### 1. Check All Counter Closings for Today
In the Accounts panel → **Closings** →
- Filter by today's date to see which counters have been closed.
- Click a closing to view the full statement with income breakdown, expense payments, and cash received.

### 2. Review Pending Expense Vouchers
Expense Vouchers →
- Vouchers without a `Paid` status need to be processed.
- Open each voucher, verify the details, and mark as paid when the payment is made.

### 3. Check Outstanding Receivables
Reports → **Receivables Report** →
- Set date range to "This Month".
- Look for overdue receivables (Due Date in the past, status: Pending).
- Contact the relevant panel or patient for follow-up.

---

## Generating Reports

### Income Report
Reports → Income Report →
1. Set **From** and **Until** dates.
2. Optionally filter by Reception, Type, Service, or Provider.
3. View the total in the table footer.
4. Click **PDF** to export or **Excel** to download spreadsheet.

### Expense Report
Reports → Expense Report →
- Same filter options. Filter by Category to see petty cash vs voucher payments.

### Services Report
Reports → Services Report →
- Shows income per service and provider payments.
- Useful for monthly commission calculations.

### Service Performance Report
Reports → Service Performance →
- Shows income collected vs provider expenses per service order.
- Group by Department or Provider using the Group selector.

### Service Provider Report
Reports → Service Provider Report →
- Filter by a specific doctor to see their income and payable amount for the period.

---

## Managing Bank Accounts & Transactions
Finance → Bank Accounts → add your hospital's bank accounts.  
Finance → Bank Transactions → record deposits and withdrawals manually.

---

## Payroll Workflow

### Step 1 — Verify Salary Structures
Go to HR & Payroll → Salary Structures.  
Each employee should have a current structure (no Effective To date, or future date).

### Step 2 — Create a Payroll Period
HR & Payroll → Payroll Periods → **Create** →
- Select Year and Month.
- Status starts as **Draft**.

### Step 3 — Process and Approve
- Change status to **Calculated** after reviewing.
- Get supervisor approval → change to **Approved**.
- Process payments → change to **Paid**.

---

## Tips
- Use the **date filter** on all reports — it defaults to the current month.
- The **Service Orders** report at `/admin/service-orders` gives you a full transaction-linked view of all orders.
- Closing statements can be printed as PDFs from the Closings list.
