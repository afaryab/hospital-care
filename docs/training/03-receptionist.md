# Receptionist Training Guide

## Who This Guide Is For
Receptionists operate the **Counter** — they register patients, collect payments, issue transactions, and manage the daily closing.

## Your Portal
**Main URL:** `/CT` (Counter)  
**Patient Register:** `/PS`

---

## Start of Shift — Opening a Counter

1. Go to `/CT-NEW` or click **Open Counter** from the home page.
2. Enter the **Opening Cash Amount** (petty cash float handed to you).
3. Click **Open Counter**. Your counter is now active.

> You can only have **one open counter** at a time.

---

## Registering a New Patient

1. Go to `/PS` → click **Register Patient**.
2. Fill in:
   - **Full Name** (required)
   - **Gender**
   - **Date of Birth** or **Age**
   - **Contact Number** (Pakistani format: 0300-1234567)
   - **CNIC** (13-digit national ID)
   - **Address** (optional)
   - **Guardian** / **Relation** (for minors)
3. Click **Save**.
4. A **PS Number** is automatically assigned (e.g. `PS/2024/03/0001`).
5. Note the PS number for the patient — they will need it for future visits.

---

## Recording Income (Billing a Patient)

1. Go to `/CT` → your open counter → **Income**.
2. Search the patient by **PS Number** or name.
3. Select the patient.
4. Choose the **Department** (OPD, Emergency, Dental, etc.).
5. Select the **Service(s)** the patient is receiving.
   - For services with a provider, select the **Doctor**.
   - Quantity is 1 by default; change if needed.
6. The **Total** calculates automatically.
7. Enter **Amount Paid** by the patient.
8. Select **Payment Method** (Cash / Card / Cheque / Panel).
9. Click **Generate Bill**.
10. A receipt / transaction is created. You can print it.

> If the patient is paying via a **Panel** (insurance company), select Panel as payment method and choose the panel name. A receivable is automatically created.

---

## Recording an Expense (Petty Cash)

1. Go to `/CT-EXP` or Counter → **Expense**.
2. Select expense type: **Petty Cash** or **Voucher Payment**.

### Petty Cash
- Select the **Category** (e.g. Office Supplies).
- Enter the **Amount** and **Description**.
- Click **Pay**.

### Voucher Payment
- Search for an existing expense voucher by number.
- Select it and confirm payment.

---

## Creating an Expense Voucher

Expense vouchers are pre-approved expense authorizations (e.g. doctor's fee).

1. Go to `/CT-EXP-VOUCHER/NEW`.
2. Select voucher type:
   - **Doctor Voucher** (`/CT-DOCTOR-EXP-VOUCHER/NEW`) — for paying a doctor.
   - **User Voucher** (`/CT-USER-EXP-VOUCHER/NEW`) — for paying any staff member.
   - **General Voucher** — for other expenses.
3. Fill in the payee, amount, and expense category.
4. Click **Create Voucher**.

---

## Searching a Transaction

Go to `/TR` → enter the transaction number (format: `TR/YYYY/MM/DD/NNNN`) → click **View Transaction**.

---

## Managing Receivables

Patients who paid by panel create a receivable record.

1. Go to `/RECEAVEABLES`.
2. View all outstanding receivables.
3. When a panel payment is received, record the payment against the receivable.

---

## End of Shift — Closing the Counter

1. Go to `/CT-CLOSE`.
2. Count your cash, card slips, and cheques.
3. Enter:
   - **Cash Amount**
   - **Card/Cheque Amount** (if any)
4. Review the system totals vs your physical count.
5. Click **Close Counter**.
6. The closing statement is generated with your CT number (e.g. `CT/2024/03/0001`).
7. Hand the cash to the accounts office. The accountant will mark the closing as Reported.

---

## Viewing Your Counter History

Go to `/MY-CT-LIST` to see all your previous closings with dates and totals.

---

## Tips
- Always search the patient by PS number first to avoid creating duplicates.
- If a patient is a panel member, make sure to select the correct panel — it creates the receivable automatically.
- Never close the counter without counting your cash first.
- If you made a mistake on a transaction, contact the administrator — transactions cannot be deleted, only edited with authorization.
