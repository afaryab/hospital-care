# Administrator Training Guide

## Who This Guide Is For
System Administrators have **full access** to all areas of the hospital system. This guide covers the admin panel and all management functions.

## Your Portal
**URL:** `/admin`  
**Access:** Admin panel only (not the clinical portals).

---

## Admin Panel Navigation

The left sidebar is grouped into sections:

| Group | What's Inside |
|-------|--------------|
| **Dashboards** | Executive Overview, Financial Analytics, Patient Analytics, Operations, History, ICD-10 Analytics, Human Resources |
| **Services** | Service Departments, Services, Service Recestations, Receptions |
| **Finance** | Bank Accounts, Bank Transactions, Panel Cheques, Payment Methods, Administrative Transactions, Receivables |
| **Expenses** | Expense Categories, Expense Vouchers |
| **Indoor** | Wards, Rooms, Beds, Admissions |
| **Compliance** | Audit Log, Incidents, Consents |
| **Clinical** | ICD-10 Codes |
| **Operations** | Tasks |
| **Inventory** | Stock Categories, Stock Items, Stock Movements, Purchase Orders |
| **Assets** | Asset Categories, Assets |
| **HR & Payroll** | Salary Structures, Payroll Periods |
| **Reports** | Income Report, Expense Report, Receivables, Services, Service Orders, Service Performance, Service Provider |

---

## Daily Tasks

### 1. Check the Executive Dashboard
Go to **Dashboards → Executive Overview**.  
- View today's patient registrations, income, and open counters.
- Use the **date filter** (top-right Filter button) to change the period.

### 2. Review Open Counters
- Dashboard → **Operations**  
- Shows how many counters are currently open and their collections.

### 3. Manage Users
**Users** (in sidebar) → **Create User**  
Required fields: Name, Email, Password.  
After creating a user, assign their role:
- Go to Users → edit the user → assign profiles (OPD Doctor, Receptionist, etc.)

### 4. Register a New Patient
Go to `/PS` → click **Register Patient** → fill in:
- Full name, gender, date of birth, contact, CNIC, address
- Click **Save**. A PS number is auto-assigned.

---

## Key Management Tasks

### Adding a New Service
Services → **Create** → fill in:
- Name, Department (e.g. OPD), Charges, Tax Rate
- Toggle "Generate Service Order" if this service creates a treatment record
- Save.

### Setting Up a Reception Counter
Receptions → **Create** → fill in:
- Counter name, allowed departments (OPD, Emergency, etc.)
- Toggle "Can Pay Vouchers" and "Petty Cash" as needed.

### Recording an Incident (PHC Compliance)
Compliance → Incidents → **Create** →
- Select type (Clinical Error, System Failure, etc.)
- Assign severity, department, and responsible person.
- Track through the lifecycle: Reported → Investigated → Resolved → Closed.

### Generating a Report PDF
Reports → (select report type) →
- Set the date range using the **From / Until** filters.
- Click the **PDF** button on the top-right.
- The PDF opens in a new tab — use browser print to save.

### Adding ICD-10 Codes
Clinical → ICD-10 Codes → **Create** →
- Enter code (e.g. `J06.9`), description, category.
- Ensure **Active** is toggled on so doctors can search it.

---

## User Role Assignment Reference

| Profile | How to Assign | Portal Given |
|---------|--------------|--------------|
| Administrator | Admin panel → Users → Edit → Administrator profile | `/admin` |
| Accountant | Admin panel → Users → Edit → Accountant profile | `/accounts` |
| Receptionist | Admin panel → Users → Edit → Receptionist profile | Counter (`/CT`) |
| OPD Doctor | Admin panel → Users → Edit → OPD Doctor profile | `/OPD` |
| Indoor Doctor | Admin panel → Users → Edit → Indoor Doctor profile | `/IND` |
| Emergency Doctor | Admin panel → Users → Edit → Emergency Doctor profile | `/EMG` |
| Dentist | Admin panel → Users → Edit → Dentist profile | `/DNT` |
| Ultrasound Doctor | Admin panel → Users → Edit → Ultrasound Doctor profile | `/ULT` |
| X-Ray Technician | Admin panel → Users → Edit → X-Ray Technician profile | `/XRAY` |
| Nursing Staff | Admin panel → Users → Edit → Nursing Staff profile | `/PS` register |
| Patient Manager | Admin panel → Users → Edit → Patient Manager profile | `/PS` restricted |

> A user can hold **multiple profiles** (e.g. a doctor who is also an administrator).

---

## Tips
- The **Audit Log** records every create/update/delete action with user and timestamp. Use it to investigate disputes.
- **Expense Vouchers** that have no linked transaction are "pending payment". Use Finance → Expense Vouchers to track them.
- Set up **Salary Structures** for each employee before processing payroll periods.
