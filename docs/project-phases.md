# Project Phases — Hospital All In One Operations Software

> Comprehensive task breakdown derived from `docs/user-stories.md` and `docs/project-description.md`.
> Each task includes automated feature tests as acceptance criteria.
> Status: ✅ Completed | 🔲 Planned

---

## Phase 1 — Database Structure

> Models, migrations, factories, and seeders for the entire application.
> DB guidelines: no DB-level ENUM columns; use `string` columns with comments for rarely-changing values and PHP Enums; use lookup tables for frequently-changing values. Use `spatie/laravel-medialibrary` for file/image uploads.

---

### Phase 1.1 — Core Model Factories & Seeders (Missing Infrastructure) ✅

The codebase has 30 models but only 1 factory (`UserFactory`). All core models need factories for proper testing.

| Task | Model | Status |
|------|-------|--------|
| 1.1.1 | Create `PatientFactory` | 🔲 |
| 1.1.2 | Create `ClosingFactory` (depends on `ReceptionFactory`) | 🔲 |
| 1.1.3 | Create `TransactionFactory` (depends on `ClosingFactory`, `PatientFactory`) | 🔲 |
| 1.1.4 | Create `TransactionElementFactory` (depends on `TransactionFactory`) | 🔲 |
| 1.1.5 | Create `ServiceDepartmentFactory` | 🔲 |
| 1.1.6 | Create `ServiceFactory` (depends on `ServiceDepartmentFactory`) | 🔲 |
| 1.1.7 | Create `ServiceOrderFactory` (depends on `PatientFactory`, `ServiceFactory`) | 🔲 |
| 1.1.8 | Create `ReceptionFactory` | 🔲 |
| 1.1.9 | Create `PanelFactory` | ✅ |
| 1.1.10 | Create `ExpenseCategoryFactory` | 🔲 |
| 1.1.11 | Create `ExpenseVoucherFactory` (depends on `ExpenseCategoryFactory`) | 🔲 |
| 1.1.12 | Create `ReceaveableFactory` (depends on `PatientFactory`, `TransactionFactory`) | 🔲 |
| 1.1.13 | Create profile factories: `AdministratorFactory`, `AccountantFactory`, `ReceptionistFactory`, `OpdDoctorFactory`, `IndDoctorFactory`, `EmergencyDoctorFactory`, `DentistFactory`, `UltrasoundDoctorFactory`, `XrayTechnicianFactory`, `NursingStaffFactory`, `PatientManagerFactory` | ✅ |
| 1.1.14 | Update `DatabaseSeeder` to use all factories for dev environment | ✅ |

**Feature Tests:**
```
tests/Feature/Factories/FactoryCreationTest.php
- test each factory creates a valid model instance
- test each factory can create multiple instances without collisions
- test factory states (e.g., PatientFactory with/without CNIC, ClosingFactory with status OPEN/CLOSED)
- test factory relationships resolve correctly (e.g., TransactionFactory creates linked Closing)
```

---

### Phase 1.2 — Existing Core Tables (Already Implemented) ✅

These migrations and models already exist and are functional.

| Table | Model | Migration | Factory | Status |
|-------|-------|-----------|---------|--------|
| `users` | `User` | ✅ | ✅ `UserFactory` | ✅ |
| `images` | `Image` | ✅ | — | ✅ |
| `patients` | `Patient` | ✅ | 🔲 | ✅ |
| `service_departments` | `ServiceDepartment` | ✅ | 🔲 | ✅ |
| `services` | `Service` | ✅ | 🔲 | ✅ |
| `service_recestations` | `ServiceRecestation` | ✅ | — | ✅ |
| `service_orders` | `ServiceOrder` | ✅ | 🔲 | ✅ |
| `receptions` | `Reception` | ✅ | 🔲 | ✅ |
| `closings` | `Closing` | ✅ | 🔲 | ✅ |
| `transactions` | `Transaction` | ✅ | 🔲 | ✅ |
| `transaction_elements` | `TransactionElement` | ✅ | 🔲 | ✅ |
| `expense_categories` | `ExpenseCategory` | ✅ | 🔲 | ✅ |
| `expense_vouchers` | `ExpenseVoucher` | ✅ | 🔲 | ✅ |
| `expense_voucher_service_order` | — (pivot) | ✅ | — | ✅ |
| `receaveables` | `Receaveable` | ✅ | 🔲 | ✅ |
| `panels` | `Panel` | ✅ | ✅ `PanelFactory` | ✅ |
| `instance_variables` | `InstanceVariable` | ✅ | — | ✅ |
| `administrators` | `Administrator` | ✅ | ✅ `AdministratorFactory` | ✅ |
| `accountants` | `Accountant` | ✅ | ✅ `AccountantFactory` | ✅ |
| `receptionists` | `Receptionist` | ✅ | ✅ `ReceptionistFactory` | ✅ |
| `opd_doctors` | `OpdDoctor` | ✅ | ✅ `OpdDoctorFactory` | ✅ |
| `ind_doctors` | `IndDoctor` | ✅ | ✅ `IndDoctorFactory` | ✅ |
| `emergency_doctors` | `EmergencyDoctor` | ✅ | ✅ `EmergencyDoctorFactory` | ✅ |
| `dentists` | `Dentist` | ✅ | ✅ `DentistFactory` | ✅ |
| `ultrasound_doctors` | `UltrasoundDoctor` | ✅ | ✅ `UltrasoundDoctorFactory` | ✅ |
| `xray_technicians` | `XrayTechnician` | ✅ | ✅ `XrayTechnicianFactory` | ✅ |
| `nursing_staff` | `NursingStaff` | ✅ | ✅ `NursingStaffFactory` | ✅ |
| `patient_managers` | `PatientManager` | ✅ | ✅ `PatientManagerFactory` | ✅ |
| `password_reset_tokens` | — | ✅ | — | ✅ |
| `sessions` | — | ✅ | — | ✅ |
| `cache` / `cache_locks` | — | ✅ | — | ✅ |
| `jobs` / `job_batches` / `failed_jobs` | — | ✅ | — | ✅ |
| `telescope_entries` | — | ✅ | — | ✅ |
| `pulse_*` | — | ✅ | — | ✅ |
| `migration_logs` | `MigrationLog` | ✅ | — | ✅ |
| `upgrade_processes` | `UpgradeProcess` | ✅ | — | ✅ |

**Existing Observers:** `PatientObserver`, `ClosingObserver`, `TransactionObserver`, `TransactionElementObserver`, `ExpenseVoucherObserver`

**Existing Enums (PHP):** `CounterStatus`, `ExpenseVoucherStatus`, `PaymentMethods`, `ServiceOrderStatus`, `TransactionElementType`

**Existing Seeders:** `DatabaseSeeder`, `ExpenseCategorySeeder`, `ServicesAndDepartmentsSeeder`

---

### Phase 1.3 — Hospital Settings Table ✅

> Ref: US-9.6 — Hospital-wide settings (name, logo, address, contact).

**Option A (recommended):** Install `spatie/laravel-settings` for typed settings stored in DB.
**Option B:** Simple `hospital_settings` key-value table.

**Migration — `hospital_settings` table:**

```
hospital_settings
├── id
├── key (string, unique) — e.g. 'hospital_name', 'logo_path', 'address', 'phone', 'email', 'ntn', 'strn'
├── value (text, nullable)
├── created_at / updated_at
```

**Model:** `HospitalSetting`
**Factory:** `HospitalSettingFactory`
**Seeder:** `HospitalSettingSeeder` — seed default keys with empty values

**Feature Tests:**
```
tests/Feature/Models/HospitalSettingTest.php
- test hospital setting can be created with key and value
- test duplicate key is rejected (unique constraint)
- test setting value can be updated
- test settings can be retrieved by key
```

---

### Phase 1.4 — Consent Management Tables ✅

> Ref: US-15.4, §13.1 Consent Management, §14.1 PHC Compliance.

**Migration — `consents` table:**

```
consents
├── id
├── patient_id (FK → patients)
├── service_order_id (FK → service_orders, nullable) — specific to a service/treatment
├── consent_type (string) — comment: 'treatment', 'procedure', 'data_sharing'
├── consent_method (string) — comment: 'digital_checkbox', 'paper_signed', 'verbal_recorded'
├── consented_at (datetime)
├── recorded_by (FK → users) — staff who captured consent
├── notes (text, nullable)
├── created_at / updated_at
```

**Model:** `Consent`
**Factory:** `ConsentFactory`

**Feature Tests:**
```
tests/Feature/Models/ConsentTest.php
- test consent can be created with required fields
- test consent belongs to patient
- test consent optionally belongs to service order
- test consent belongs to recording user
- test consent_type accepts expected values
```

---

### Phase 1.5 — Treatment Record & Vital Signs Tables ✅

> Ref: Epic 18 — Service Order Treatments (PHC Compliance).

**Migration — `treatment_records` table:**

```
treatment_records
├── id
├── service_order_id (FK → service_orders, unique) — one-to-one
├── department_id (FK → service_departments)
├── treating_doctor_id (FK → users)
├── chief_complaint (text, nullable)
├── history_of_present_illness (text, nullable)
├── examination_findings (json, nullable) — structured physical examination
├── diagnosis_code (string, nullable) — ICD-10 code
├── diagnosis_text (text, nullable) — descriptive diagnosis
├── treatment_plan (text, nullable)
├── prescriptions (json, nullable) — [{drug_name, dosage, frequency, duration, route}]
├── follow_up_date (date, nullable)
├── outcome (string, nullable) — comment: 'improved', 'unchanged', 'deteriorated', 'referred', 'expired'
├── referral_to (string, nullable) — department or hospital name
├── department_specific_data (json, nullable) — department-specific fields (triage, tooth number, bed, etc.)
├── treated_at (datetime, nullable)
├── recorded_by (FK → users) — staff who entered the record
├── is_finalized (boolean, default false) — immutable once finalized
├── finalized_at (datetime, nullable)
├── created_at / updated_at
```

**Migration — `vital_signs` table:**

```
vital_signs
├── id
├── treatment_record_id (FK → treatment_records)
├── temperature (decimal 5,2, nullable) — °F
├── blood_pressure_systolic (integer, nullable) — mmHg
├── blood_pressure_diastolic (integer, nullable) — mmHg
├── pulse_rate (integer, nullable) — BPM
├── respiratory_rate (integer, nullable) — breaths/min
├── oxygen_saturation (decimal 5,2, nullable) — SpO2 %
├── weight (decimal 6,2, nullable) — kg
├── height (decimal 5,2, nullable) — cm
├── recorded_at (datetime)
├── recorded_by (FK → users) — staff who recorded
├── created_at / updated_at
```

**Migration — `icd10_codes` lookup table:**

```
icd10_codes
├── id
├── code (string, unique, indexed) — e.g. 'A00.0'
├── description (string) — e.g. 'Cholera due to Vibrio cholerae 01, biovar cholerae'
├── category (string, nullable) — e.g. 'Certain infectious and parasitic diseases'
├── is_active (boolean, default true)
├── created_at / updated_at
```

**Models:** `TreatmentRecord`, `VitalSign`, `Icd10Code`
**Factories:** `TreatmentRecordFactory`, `VitalSignFactory`, `Icd10CodeFactory`
**Seeder:** `Icd10CodeSeeder` — import common ICD-10 codes

**New PHP Enum:** `TreatmentOutcome` (string-backed: improved, unchanged, deteriorated, referred, expired)

**Update existing:** Extend `ServiceOrderStatus` enum with: `IN_PROGRESS`, `TREATED`, `REVIEWED`, `REFERRED`, `CANCELLED` (in addition to existing `OPEN`, `CLOSED`)

**Feature Tests:**
```
tests/Feature/Models/TreatmentRecordTest.php
- test treatment record can be created with required fields
- test treatment record has one-to-one relationship with service order
- test treatment record belongs to department and treating doctor
- test prescriptions JSON stores structured medication data
- test department_specific_data JSON stores department-specific fields
- test finalized treatment record cannot be updated (immutability)
- test treatment record can have multiple vital signs

tests/Feature/Models/VitalSignTest.php
- test vital signs can be created with numeric values
- test vital signs belong to treatment record
- test vital signs recorded_by tracks the recording staff

tests/Feature/Models/Icd10CodeTest.php
- test ICD-10 code can be created with code and description
- test ICD-10 code is searchable by code and description
- test duplicate code is rejected (unique constraint)
```

---

### Phase 1.6 — Stock Tracking Tables ✅

> Ref: Epic 19 — Stock Tracking.

**Migration — `stock_categories` table:**

```
stock_categories
├── id
├── name (string)
├── parent_id (FK → stock_categories, nullable) — hierarchical
├── is_medicine (boolean, default false) — requires pharmacy-level tracking
├── created_at / updated_at
```

**Migration — `stock_items` table:**

```
stock_items
├── id
├── name (string)
├── sku (string, unique) — stock keeping unit code
├── category_id (FK → stock_categories)
├── unit (string) — comment: 'pcs', 'ml', 'mg', 'box', 'strip', 'bottle', 'vial'
├── reorder_level (integer, default 0) — minimum quantity before alert
├── default_vendor (string, nullable) — primary supplier name
├── is_active (boolean, default true)
├── created_at / updated_at
```

**Migration — `stock_movements` table:**

```
stock_movements
├── id
├── stock_item_id (FK → stock_items)
├── type (string) — comment: 'IN', 'OUT'
├── quantity (decimal 10,2)
├── unit_cost (decimal 10,2, nullable) — cost per unit at time of movement
├── reference_type (string, nullable) — morph type (PurchaseOrder, ServiceOrder, ExpenseVoucher)
├── reference_id (unsignedBigInteger, nullable) — morph ID
├── department_id (FK → service_departments, nullable) — consuming/receiving department
├── batch_number (string, nullable) — for medicines
├── expiry_date (date, nullable) — for medicines
├── moved_by (FK → users)
├── notes (text, nullable)
├── created_at / updated_at
```

**Migration — `purchase_orders` table:**

```
purchase_orders
├── id
├── po_number (string, unique) — PO/{year}/{month}/{sequence}
├── vendor_name (string)
├── status (string, default 'draft') — comment: 'draft', 'approved', 'received', 'cancelled'
├── total_amount (decimal 12,2, default 0)
├── approved_by (FK → users, nullable)
├── approved_at (datetime, nullable)
├── received_at (datetime, nullable)
├── notes (text, nullable)
├── created_at / updated_at
```

**Migration — `purchase_order_items` table:**

```
purchase_order_items
├── id
├── purchase_order_id (FK → purchase_orders)
├── stock_item_id (FK → stock_items)
├── quantity (decimal 10,2)
├── unit_cost (decimal 10,2)
├── total_cost (decimal 12,2) — quantity × unit_cost
├── batch_number (string, nullable)
├── expiry_date (date, nullable)
├── created_at / updated_at
```

**Migration — `service_stock_item` pivot table:**

```
service_stock_item
├── id
├── service_id (FK → services)
├── stock_item_id (FK → stock_items)
├── quantity_consumed (decimal 10,2) — how much is consumed per service order
├── created_at / updated_at
```

**Models:** `StockCategory`, `StockItem`, `StockMovement`, `PurchaseOrder`, `PurchaseOrderItem`
**Factories:** `StockCategoryFactory`, `StockItemFactory`, `StockMovementFactory`, `PurchaseOrderFactory`, `PurchaseOrderItemFactory`
**Seeders:** `StockCategorySeeder` (default categories: Medicines, Surgical Supplies, Stationery, Cleaning, General)
**Observer:** `PurchaseOrderObserver` — assigns PO number in `creating` hook

**New PHP Enums:**
- `StockMovementType` (string-backed: `IN`, `OUT`)
- `PurchaseOrderStatus` (string-backed: `draft`, `approved`, `received`, `cancelled`)

**Feature Tests:**
```
tests/Feature/Models/StockCategoryTest.php
- test stock category can be created
- test stock category supports hierarchical parent/child
- test stock category has many stock items

tests/Feature/Models/StockItemTest.php
- test stock item can be created with required fields
- test stock item belongs to category
- test stock item SKU is unique
- test current_stock_level computed attribute returns SUM(IN) - SUM(OUT)

tests/Feature/Models/StockMovementTest.php
- test stock IN movement increases stock level
- test stock OUT movement decreases stock level
- test stock movement with morphable reference (PurchaseOrder, ServiceOrder)
- test stock movement tracks batch_number and expiry_date for medicines

tests/Feature/Models/PurchaseOrderTest.php
- test purchase order can be created
- test PO number is auto-assigned in PO/{year}/{month}/{sequence} format
- test purchase order has many items
- test purchase order status transitions (draft → approved → received)
- test receiving a PO creates stock IN movements for all items
```

---

### Phase 1.7 — Asset Tracking Tables ✅

> Ref: Epic 20 — Asset Tracking.

**Migration — `asset_categories` table:**

```
asset_categories
├── id
├── name (string) — e.g. Medical Equipment, Furniture, IT, Vehicles
├── depreciation_method (string, default 'straight_line') — comment: 'straight_line', 'declining_balance', 'none'
├── useful_life_years (integer, nullable) — default useful life for depreciation
├── created_at / updated_at
```

**Migration — `assets` table:**

```
assets
├── id
├── asset_number (string, unique) — AST/{year}/{sequence}
├── name (string) — asset description
├── category_id (FK → asset_categories)
├── serial_number (string, nullable) — manufacturer serial
├── purchase_date (date, nullable)
├── purchase_cost (decimal 12,2, default 0)
├── vendor_name (string, nullable)
├── warranty_expiry (date, nullable)
├── assigned_to_department_id (FK → service_departments, nullable)
├── assigned_to_user_id (FK → users, nullable)
├── location (string, nullable) — physical location description
├── status (string, default 'active') — comment: 'active', 'under_maintenance', 'retired', 'disposed'
├── disposed_at (date, nullable)
├── disposal_reason (text, nullable)
├── disposal_value (decimal 12,2, nullable) — salvage/sale value
├── created_at / updated_at
├── deleted_at (softDeletes)
```

**Migration — `asset_assignment_logs` table:**

```
asset_assignment_logs
├── id
├── asset_id (FK → assets)
├── from_department_id (FK → service_departments, nullable)
├── to_department_id (FK → service_departments, nullable)
├── from_user_id (FK → users, nullable)
├── to_user_id (FK → users, nullable)
├── transferred_by (FK → users) — who performed the transfer
├── notes (text, nullable)
├── transferred_at (datetime)
├── created_at / updated_at
```

**Migration — `asset_maintenance_logs` table:**

```
asset_maintenance_logs
├── id
├── asset_id (FK → assets)
├── type (string) — comment: 'preventive', 'corrective', 'calibration'
├── description (text, nullable)
├── cost (decimal 10,2, default 0)
├── performed_by (string, nullable) — technician/vendor name
├── scheduled_date (date, nullable)
├── completed_date (date, nullable)
├── next_maintenance_date (date, nullable)
├── created_at / updated_at
```

**Migration — `asset_depreciation_entries` table:**

```
asset_depreciation_entries
├── id
├── asset_id (FK → assets)
├── period_year (integer) — e.g. 2026
├── period_month (integer) — e.g. 3
├── depreciation_amount (decimal 12,2)
├── accumulated_depreciation (decimal 12,2) — running total
├── book_value (decimal 12,2) — purchase_cost minus accumulated
├── created_at / updated_at
```

**Models:** `AssetCategory`, `Asset`, `AssetAssignmentLog`, `AssetMaintenanceLog`, `AssetDepreciationEntry`
**Factories:** `AssetCategoryFactory`, `AssetFactory`, `AssetAssignmentLogFactory`, `AssetMaintenanceLogFactory`
**Seeder:** `AssetCategorySeeder` (defaults: Medical Equipment, Furniture, IT Equipment, Vehicles, Other)
**Observer:** `AssetObserver` — assigns AST number in `creating` hook

**New PHP Enums:**
- `AssetStatus` (string-backed: `active`, `under_maintenance`, `retired`, `disposed`)
- `DepreciationMethod` (string-backed: `straight_line`, `declining_balance`, `none`)
- `MaintenanceType` (string-backed: `preventive`, `corrective`, `calibration`)

**Feature Tests:**
```
tests/Feature/Models/AssetTest.php
- test asset can be created
- test AST number is auto-assigned in AST/{year}/{sequence} format
- test asset belongs to category, department, and user
- test asset supports soft deletes
- test asset status transitions

tests/Feature/Models/AssetAssignmentLogTest.php
- test assignment log is created when asset is transferred
- test assignment log tracks from/to department and user

tests/Feature/Models/AssetMaintenanceLogTest.php
- test maintenance log can be created for an asset
- test next_maintenance_date is calculated from schedule

tests/Feature/Models/AssetDepreciationEntryTest.php
- test straight-line depreciation calculates correctly
- test accumulated depreciation increases monthly
- test book value decreases monthly
```

---

### Phase 1.8 — User Tasking Tables ✅

> Ref: Epic 21 — User Tasking.
> File attachments via `spatie/laravel-medialibrary` (media table).

**Migration — `tasks` table:**

```
tasks
├── id
├── task_number (string, unique) — TSK/{year}/{month}/{sequence}
├── title (string)
├── description (text, nullable)
├── priority (string, default 'medium') — comment: 'low', 'medium', 'high', 'urgent'
├── status (string, default 'todo') — comment: 'todo', 'in_progress', 'blocked', 'completed', 'cancelled'
├── assigned_to (FK → users)
├── assigned_by (FK → users)
├── department_id (FK → service_departments, nullable)
├── due_date (datetime, nullable)
├── completed_at (datetime, nullable)
├── completion_notes (text, nullable)
├── created_at / updated_at
├── deleted_at (softDeletes)
```

**Migration — `task_comments` table:**

```
task_comments
├── id
├── task_id (FK → tasks)
├── user_id (FK → users)
├── body (text)
├── created_at / updated_at
```

**Note:** Task file attachments use `spatie/laravel-medialibrary`. The `Task` model implements `HasMedia` interface and registers a `task-attachments` media collection. No separate `task_attachments` table needed — files are stored in the `media` table.

**Models:** `Task`, `TaskComment`
**Factories:** `TaskFactory`, `TaskCommentFactory`
**Observer:** `TaskObserver` — assigns TSK number in `creating` hook

**New PHP Enums:**
- `TaskPriority` (string-backed: `low`, `medium`, `high`, `urgent`)
- `TaskStatus` (string-backed: `todo`, `in_progress`, `blocked`, `completed`, `cancelled`)

**Feature Tests:**
```
tests/Feature/Models/TaskTest.php
- test task can be created with required fields
- test TSK number is auto-assigned in TSK/{year}/{month}/{sequence} format
- test task belongs to assigned user and assigner
- test task has many comments
- test task status transitions (todo → in_progress → completed)
- test task supports soft deletes
- test task supports file attachments via media library

tests/Feature/Models/TaskCommentTest.php
- test comment can be created on a task
- test comment belongs to task and user
```

---

### Phase 1.9 — User Payroll Tables ✅

> Ref: Epic 22 — User Payroll.

**Migration — `payroll_periods` table:**

```
payroll_periods
├── id
├── period_number (string, unique) — PAY/{year}/{month}
├── year (integer)
├── month (integer)
├── status (string, default 'draft') — comment: 'draft', 'calculated', 'approved', 'paid', 'closed'
├── processed_by (FK → users, nullable)
├── approved_by (FK → users, nullable)
├── approved_at (datetime, nullable)
├── paid_at (datetime, nullable)
├── notes (text, nullable)
├── created_at / updated_at
```

**Migration — `salary_structures` table:**

```
salary_structures
├── id
├── user_id (FK → users)
├── basic_salary (decimal 12,2, default 0)
├── housing_allowance (decimal 10,2, default 0)
├── medical_allowance (decimal 10,2, default 0)
├── transport_allowance (decimal 10,2, default 0)
├── other_allowances (json, nullable) — [{name, amount}]
├── effective_from (date)
├── effective_to (date, nullable) — null = currently active
├── created_at / updated_at
```

**Migration — `payslip_entries` table:**

```
payslip_entries
├── id
├── payroll_period_id (FK → payroll_periods)
├── user_id (FK → users)
├── salary_structure_id (FK → salary_structures)
├── gross_salary (decimal 12,2, default 0) — sum of all allowances
├── deductions (json, nullable) — [{type, description, amount}] (tax, advances, absences, penalties)
├── total_deductions (decimal 12,2, default 0)
├── net_salary (decimal 12,2, default 0) — gross - deductions
├── payment_method (string, nullable) — comment: 'cash', 'bank_transfer', 'cheque'
├── paid_at (datetime, nullable)
├── paid_via_voucher_id (FK → expense_vouchers, nullable) — links to expense voucher
├── created_at / updated_at
```

**Migration — `salary_advances` table:**

```
salary_advances
├── id
├── user_id (FK → users)
├── amount (decimal 12,2)
├── granted_by (FK → users) — approver
├── granted_at (date)
├── deduction_per_month (decimal 10,2) — monthly recovery amount
├── remaining_balance (decimal 12,2) — outstanding
├── status (string, default 'active') — comment: 'active', 'fully_recovered', 'written_off'
├── notes (text, nullable)
├── created_at / updated_at
```

**Models:** `PayrollPeriod`, `SalaryStructure`, `PayslipEntry`, `SalaryAdvance`
**Factories:** `PayrollPeriodFactory`, `SalaryStructureFactory`, `PayslipEntryFactory`, `SalaryAdvanceFactory`

**New PHP Enums:**
- `PayrollPeriodStatus` (string-backed: `draft`, `calculated`, `approved`, `paid`, `closed`)
- `SalaryAdvanceStatus` (string-backed: `active`, `fully_recovered`, `written_off`)

**Feature Tests:**
```
tests/Feature/Models/PayrollPeriodTest.php
- test payroll period can be created
- test period_number follows PAY/{year}/{month} format
- test payroll period has many payslip entries
- test status transitions (draft → calculated → approved → paid → closed)

tests/Feature/Models/SalaryStructureTest.php
- test salary structure can be created for a user
- test only one active structure per user (effective_to is null)
- test gross salary is computed from components

tests/Feature/Models/PayslipEntryTest.php
- test payslip entry can be created
- test net_salary = gross_salary - total_deductions
- test payslip entry belongs to payroll period, user, and salary structure
- test payslip entry optionally links to expense voucher

tests/Feature/Models/SalaryAdvanceTest.php
- test advance can be created
- test advance deduction reduces remaining_balance
- test advance status changes to fully_recovered when remaining_balance reaches 0
```

---

### Phase 1.10 — Package Installation for New Features 🔲

> Install required packages before the models that depend on them.

| Package | Purpose | Phase Dependency |
|---------|---------|------------------|
| `spatie/laravel-medialibrary` | File/image uploads (replaces custom `Image` model long-term; immediate use for Task attachments, Radiology/Ultrasound images) | Phase 1.8, Phase 5 |
| `spatie/laravel-activitylog` | Audit trail for all core models | Phase 2.1 |
| `spatie/laravel-permission` | Role & permission management (RBAC) | Phase 2.3 |
| `spatie/laravel-backup` | Automated backups | Phase 2.6 |
| `maatwebsite/laravel-excel` | Excel/CSV export for reports | Phase 4.7 |
| `laravel/sanctum` | API token authentication for Portal & FHIR APIs | Phase 7, Phase 8 |

**Feature Tests:**
```
tests/Feature/PackageInstallationTest.php
- test spatie/laravel-medialibrary migration runs successfully
- test spatie/laravel-activitylog migration runs successfully
- test spatie/laravel-permission migration runs successfully
```

---

## Phase 2 — Compliance & Security Foundation

> Ref: Epic 15, §13 Compliance, HIPAA & PHC guidelines.

---

### Phase 2.1 — Audit Trail (spatie/laravel-activitylog) 🔲

> Ref: US-15.1

- Install `spatie/laravel-activitylog`
- Add `LogsActivity` trait to all core models: `Patient`, `Transaction`, `TransactionElement`, `Closing`, `ServiceOrder`, `ExpenseVoucher`, `Receaveable`, `User`, `Service`, `Reception`
- Configure activity log to capture: `user_id`, `action`, `old_values`, `new_values`, `ip_address`, `user_agent`
- Create Filament admin page to browse audit logs with filters (user, model, date range)

**Feature Tests:**
```
tests/Feature/Compliance/AuditTrailTest.php
- test creating a patient logs an activity
- test updating a transaction logs old and new values
- test deleting (soft) an expense voucher logs the deletion
- test activity log captures the authenticated user
- test activity log captures IP address
- test activity logs are immutable (cannot be edited or deleted via app)
- test audit log admin page renders and filters correctly
```

---

### Phase 2.2 — Data Encryption at Rest 🔲

> Ref: US-15.2

- Add encrypted casts to `Patient` model: `cnic`, `contact`, `address`
- Add encrypted casts to `ServiceOrder` model: `notes_json`
- Implement blind index or hash for CNIC duplicate lookup
- Migrate existing plaintext data to encrypted format

**Feature Tests:**
```
tests/Feature/Compliance/DataEncryptionTest.php
- test patient CNIC is encrypted in database
- test patient contact is encrypted in database
- test patient address is encrypted in database
- test encrypted fields are decrypted when accessed via model
- test CNIC duplicate check works with encrypted data
- test service order notes_json is encrypted
```

---

### Phase 2.3 — Role-Based Access Control (spatie/laravel-permission) 🔲

> Ref: US-15.10

- Install `spatie/laravel-permission`
- Define permissions per resource: `view`, `create`, `edit`, `delete` for each core model
- Define roles mapping to existing profile types: `administrator`, `accountant`, `receptionist`, `opd_doctor`, `ind_doctor`, `emergency_doctor`, `dentist`, `ultrasound_doctor`, `xray_technician`, `nursing_staff`, `patient_manager`
- Create Laravel Policies for: `Patient`, `Transaction`, `Closing`, `ServiceOrder`, `ExpenseVoucher`, `Receaveable`, `User`
- Apply middleware gates on all web and API routes
- Restrict Filament resource actions per role

**Feature Tests:**
```
tests/Feature/Compliance/RBACTest.php
- test receptionist cannot access admin panel
- test doctor cannot access accounts panel
- test administrator can access all resources
- test receptionist can create transactions but not edit closings
- test doctor can view service orders but not create transactions
- test unauthenticated user cannot access any protected route
- test each policy correctly permits/denies actions per role
```

---

### Phase 2.4 — Immutable Medical Records 🔲

> Ref: US-15.3

- Implement versioning on `Patient` model (amendment records)
- Implement versioning on `ServiceOrder` / `TreatmentRecord`
- Enforce soft deletes on all patient-facing models
- Prevent hard deletes via model events

**Feature Tests:**
```
tests/Feature/Compliance/ImmutableRecordsTest.php
- test editing a patient creates a version record
- test original patient data is preserved
- test editing a treatment record after finalization is rejected
- test soft deleted patient is not actually removed from database
- test hard delete on patient throws exception or is prevented
- test version history is retrievable for a patient
```

---

### Phase 2.5 — Duplicate Patient Prevention 🔲

> Ref: US-15.5

- Add CNIC and contact matching check on patient creation
- Return warning with existing patient details if a match is found
- Allow receptionist to proceed or select existing patient

**Feature Tests:**
```
tests/Feature/Compliance/DuplicatePatientTest.php
- test creating patient with existing CNIC returns a warning
- test creating patient with existing contact returns a warning
- test receptionist can proceed to create despite warning
- test receptionist can select existing patient to avoid duplicate
- test CNIC check works with encrypted data
```

---

### Phase 2.6 — Automated Backups 🔲

> Ref: US-15.6

- Install `spatie/laravel-backup`
- Configure daily backup schedule (database + file storage)
- Configure offsite storage destination (S3/SFTP)
- Set retention policy: 7 daily, 4 weekly, 12 monthly
- Add backup health check notification

**Feature Tests:**
```
tests/Feature/Compliance/BackupTest.php
- test backup command runs successfully
- test backup creates a zip file with database dump
- test backup retention policy cleans old backups
- test backup health check notifies on failure
```

---

### Phase 2.7 — Breach Notification 🔲

> Ref: US-15.7

- Detect unusual access patterns: multiple failed logins, new IP/device, bulk record access
- Send email notifications to designated security contacts
- Create incident log viewable from admin panel

**Feature Tests:**
```
tests/Feature/Compliance/BreachNotificationTest.php
- test multiple failed login attempts triggers alert
- test login from new IP triggers notification
- test bulk patient record access triggers alert
- test incident log entry is created for each alert
- test security contacts receive email notification
```

---

## Phase 3 — Core Feature Enhancements

> Complete planned features for existing functionality.

---

### Phase 3.1 — X-Ray Technician Profile Enhancement 🔲

> Ref: US-2.5

- Add `authority` column to `xray_technicians` table (currently missing, unlike other profile tables)
- Update `UserResource` Filament form to include X-Ray Technician authority levels

**Feature Tests:**
```
tests/Feature/Admin/UserProfileTest.php
- test X-Ray Technician profile can be assigned to a user with authority levels
- test X-Ray Technician appears in user profiles list
```

---

### Phase 3.2 — Nursing Staff Profile Enhancement 🔲

> Ref: US-2.6

- Nursing Staff model and table already exist with `authority` column
- Ensure `UserResource` Filament form includes Nursing Staff profile repeater

**Feature Tests:**
```
tests/Feature/Admin/NursingStaffProfileTest.php
- test Nursing Staff profile can be assigned to a user
- test Nursing Staff authority levels are configurable
```

---

### Phase 3.3 — Refund Transactions 🔲

> Ref: US-5.4

- Implement refund action on transactions (admin)
- Set `is_refunded` flag on transaction
- Update or cancel related receivable
- Create refund transaction element with `refunded_transaction_id`

**Feature Tests:**
```
tests/Feature/Transactions/RefundTransactionTest.php
- test admin can mark a transaction as refunded
- test refunded transaction sets is_refunded flag
- test related receivable is cancelled when transaction is refunded
- test refund creates a corresponding refund transaction element
- test non-admin cannot refund a transaction
```

---

### Phase 3.4 — Panels Filament Resource 🔲

> Ref: US-9.5

- Create Filament CRUD resource for `Panel` model
- Include active/inactive toggle
- Show pending panel payments (receivables linked to panel)

**Feature Tests:**
```
tests/Feature/Admin/PanelResourceTest.php
- test admin can list panels
- test admin can create a panel with name and code
- test admin can edit panel (toggle active/inactive)
- test panel list shows associated receivables count
- test duplicate panel code is rejected
```

---

### Phase 3.5 — Patient Filament Resource 🔲

> Ref: US-3.3

- Create Filament resource for `Patient` model with tabbed view page
- Tabs: Overview, Service Orders, Transactions, Receivables, Treatment History

**Feature Tests:**
```
tests/Feature/Admin/PatientResourceTest.php
- test admin can list patients with search
- test admin can view patient with all tabs
- test patient service orders tab shows related records
- test patient transactions tab shows related records
- test patient receivables tab shows outstanding amounts
```

---

### Phase 3.6 — Transaction Filament Resource 🔲

> Ref: US-5.5

- Create Filament resource for `Transaction` model
- List, View pages with filters (date, type, patient, closing)
- Refund action from edit page

**Feature Tests:**
```
tests/Feature/Admin/TransactionResourceTest.php
- test admin can list transactions with search and filters
- test admin can view transaction details with elements
- test admin can filter transactions by income/expense
- test admin can filter transactions by date range
```

---

### Phase 3.7 — Hospital Settings Page 🔲

> Ref: US-9.6

- Create Filament settings page for hospital configuration
- Fields: Hospital Name, Logo (media library), Address, Phone, Email, NTN, STRN

**Feature Tests:**
```
tests/Feature/Admin/HospitalSettingsTest.php
- test admin can view settings page
- test admin can update hospital name and address
- test settings appear on printed PDF headers
- test NTN/STRN appear on transaction receipts
```

---

### Phase 3.8 — Merge Receptions 🔲

> Ref: US-4.6

- Implement merge bulk action on Receptions manage page
- Migrate all closings, transactions, and related records to target reception
- Delete source reception after migration

**Feature Tests:**
```
tests/Feature/Admin/MergeReceptionsTest.php
- test admin can merge two receptions
- test all closings are moved to target reception
- test all transactions are moved to target reception
- test source reception is deleted after merge
- test merging into self is prevented
```

---

### Phase 3.9 — Excel/CSV Export for Reports 🔲

> Ref: US-10.11

- Install `maatwebsite/laravel-excel` (if not already in Phase 1.10)
- Add export button to Income, Expense, Receivables, and Services report pages
- Export includes all filtered data with column headers

**Feature Tests:**
```
tests/Feature/Reports/ExcelExportTest.php
- test income report can be exported to Excel
- test expense report can be exported to CSV
- test receivables report export includes all filtered records
- test exported file includes correct column headers
- test export filename includes report type and date range
```

---

## Phase 4 — URL Resolution Architecture

> Ref: Epic 17 — Migrate all routes to hierarchical `/{Panel}/{RecordType}/{Year}/{Month}/{Sequence}` pattern.

---

### Phase 4.1 — Counter Panel Route Migration 🔲

> Ref: US-17.1

- Migrate: `CT` → `/COUNTER`, `CT-NEW` → `/COUNTER/CT/NEW`, `CT-CLOSE` → `/COUNTER/CT/CLOSE`, `MY-CT-LIST` → `/COUNTER/CT`
- Set up legacy URL 301 redirects

**Feature Tests:**
```
tests/Feature/Routes/CounterRoutesTest.php
- test /COUNTER renders counter landing page
- test /COUNTER/CT renders closings listing
- test /COUNTER/CT/{year} filters closings by year
- test /COUNTER/CT/{year}/{month} filters closings by year/month
- test /COUNTER/CT/{year}/{month}/{sequence} renders individual closing
- test /COUNTER/CT/NEW renders open counter form
- test /COUNTER/CT/CLOSE renders close counter form
- test legacy /CT redirects to /COUNTER with 301
- test legacy /CT-NEW redirects to /COUNTER/CT/NEW with 301
- test legacy /MY-CT-LIST redirects to /COUNTER/CT with 301
```

---

### Phase 4.2 — Transaction Route Migration 🔲

> Ref: US-17.2

- Migrate: `/TR` → `/COUNTER/TR`
- Set up legacy URL 301 redirects

**Feature Tests:**
```
tests/Feature/Routes/TransactionRoutesTest.php
- test /COUNTER/TR renders transaction search
- test /COUNTER/TR/{year}/{month}/{day}/{number} renders transaction view
- test legacy /TR redirects to /COUNTER/TR with 301
```

---

### Phase 4.3 — Voucher & Expense Route Migration 🔲

> Ref: US-17.3

- Migrate: `CT-EXP-VOUCHER` → `/COUNTER/VC`, `CT-EXP` → `/COUNTER/EXP`, `RECEAVEABLES` → `/COUNTER/RECV`
- Set up legacy URL 301 redirects

**Feature Tests:**
```
tests/Feature/Routes/VoucherRoutesTest.php
- test /COUNTER/VC renders voucher listing
- test /COUNTER/VC/{year} filters vouchers by year
- test /COUNTER/VC/{year}/{month} filters vouchers by year/month
- test /COUNTER/VC/NEW renders create voucher form
- test /COUNTER/EXP renders record expense form
- test /COUNTER/RECV renders receivables listing
- test legacy routes redirect to new URLs with 301
```

---

### Phase 4.4 — Queue Route Migration 🔲

> Ref: US-17.4

- Migrate: `/que/{dept}` → `/QUE/{dept}`
- Add `/QUE` dashboard

**Feature Tests:**
```
tests/Feature/Routes/QueueRoutesTest.php
- test /QUE renders queue dashboard
- test /QUE/{department} renders department-specific queue
- test legacy /que/{department} redirects to /QUE/{department} with 301
```

---

### Phase 4.5 — Accounts Panel Route Structure 🔲

> Ref: US-17.5

- Migrate: `ACC-CT-ALL` → `/ACCOUNTS/CT`

**Feature Tests:**
```
tests/Feature/Routes/AccountsRoutesTest.php
- test /ACCOUNTS/CT renders all closings listing
- test /ACCOUNTS/CT/{year} filters closings by year
- test /ACCOUNTS/CT/{year}/{month} filters closings by year/month
- test legacy /ACC-CT-ALL redirects to /ACCOUNTS/CT with 301
```

---

### Phase 4.6 — Progressive Resolution Behavior 🔲

> Ref: US-17.6

- Ensure every URL prefix renders meaningful content
- Breadcrumbs reflect hierarchical navigation
- Year/month segments filter listings; sequence loads individual record

**Feature Tests:**
```
tests/Feature/Routes/ProgressiveResolutionTest.php
- test truncating /COUNTER/CT/2026/03/0001 to /COUNTER/CT/2026/03 returns listing
- test truncating /COUNTER/CT/2026/03 to /COUNTER/CT/2026 returns year listing
- test truncating /COUNTER/CT/2026 to /COUNTER/CT returns all closings
- test breadcrumbs show correct hierarchy at each URL depth
- test /PS/{year}/{month}/{number} renders patient view
- test /PS/{year}/{month} renders filtered patient listing
- test /PS/{year} renders year-filtered patient listing
```

---

## Phase 5 — Service Order Treatments (PHC Compliance)

> Ref: Epic 18 — Extend ServiceOrders into full clinical treatment records.

---

### Phase 5.1 — Core Treatment Record 🔲

> Ref: US-18.1

- Create treatment form accessible from service order view (Filament + frontend)
- Ensure immutability once finalized

**Feature Tests:**
```
tests/Feature/Treatments/TreatmentRecordCreationTest.php
- test doctor can create treatment record for a service order
- test treatment record requires chief_complaint and treating_doctor
- test treatment record stores structured examination_findings as JSON
- test treatment record stores prescriptions as structured JSON
- test finalized treatment record cannot be modified
- test treatment record updates service order status to TREATED
```

---

### Phase 5.2 — Vital Signs Recording 🔲

> Ref: US-18.2

- Create vital signs form within treatment record view
- Support multiple entries per treatment

**Feature Tests:**
```
tests/Feature/Treatments/VitalSignsTest.php
- test nurse can record vital signs for a treatment record
- test multiple vital sign entries per treatment record
- test vital signs include all required measurements
- test vital signs track recording staff and timestamp
- test vital signs are visible in patient history
```

---

### Phase 5.3 — Department-Specific Treatment Forms 🔲

> Ref: US-18.3

- OPD: complaint, diagnosis, prescription, follow-up
- Emergency: triage level, mechanism of injury, interventions
- Inpatient: bed number, ward, daily progress notes, discharge summary
- Dental: tooth number/quadrant, procedure type, materials used
- Lab: sample type, test ordered, results, normal ranges, abnormal flags
- Ultrasound: body region, findings, measurements, impression, images
- Radiology: body part, view type, findings, impression, images

**Feature Tests:**
```
tests/Feature/Treatments/DepartmentFormsTest.php
- test OPD treatment form shows OPD-specific fields
- test Emergency form includes triage level field
- test Inpatient form includes bed number and ward fields
- test Dental form includes tooth number and procedure type
- test Lab form includes sample type and results fields
- test Ultrasound form supports image attachments (via media library)
- test Radiology form supports image attachments (via media library)
- test department_specific_data JSON stores correct fields per department
```

---

### Phase 5.4 — Treatment Lifecycle Status 🔲

> Ref: US-18.4

- Extend ServiceOrderStatus: OPEN → IN_PROGRESS → TREATED → REVIEWED → CLOSED (+ REFERRED, CANCELLED)
- Validate status transitions
- Update queue views to filter by relevant statuses

**Feature Tests:**
```
tests/Feature/Treatments/TreatmentLifecycleTest.php
- test service order can transition OPEN → IN_PROGRESS
- test service order can transition IN_PROGRESS → TREATED
- test service order cannot skip from OPEN to CLOSED without treatment
- test CANCELLED status requires a reason
- test REFERRED status creates a referral service order
- test queue views only show relevant statuses
```

---

### Phase 5.5 — ICD-10 Diagnosis Coding 🔲

> Ref: US-18.5

- Seed ICD-10 lookup table with common codes
- Add searchable ICD-10 code picker to treatment form
- Link selected code to treatment record

**Feature Tests:**
```
tests/Feature/Treatments/ICD10CodingTest.php
- test ICD-10 codes are searchable by code and description
- test ICD-10 code can be selected and stored on treatment record
- test treatment record can be filtered by diagnosis code
```

---

### Phase 5.6 — Prescription Recording 🔲

> Ref: US-18.6

- Structured prescription entries in treatment form
- Fields: drug_name, dosage, frequency, duration, route
- Printable prescription from service order view

**Feature Tests:**
```
tests/Feature/Treatments/PrescriptionTest.php
- test prescription can be added to treatment record
- test prescription stores structured drug info (name, dosage, frequency, duration, route)
- test multiple prescriptions per treatment record
- test prescription is included in service order PDF print
```

---

### Phase 5.7 — Referral Chain Tracking 🔲

> Ref: US-18.7

- Referral creates a new ServiceOrder in target department linked to source
- Target department sees referral in queue
- Referral chain visible on patient profile

**Feature Tests:**
```
tests/Feature/Treatments/ReferralTest.php
- test doctor can refer patient to another department
- test referral creates a new service order in target department
- test referral service order links back to source order
- test target department queue shows the referral
- test referral chain is visible on patient profile
```

---

### Phase 5.8 — Treatment History on Patient Profile 🔲

> Ref: US-18.8

- Patient profile tab showing chronological treatment records
- Each entry: date, department, doctor, diagnosis, treatment plan

**Feature Tests:**
```
tests/Feature/Treatments/TreatmentHistoryTest.php
- test patient profile shows treatment history tab
- test treatment history is sorted chronologically
- test each entry shows department, doctor, and diagnosis
- test clicking entry opens full treatment record view
```

---

### Phase 5.9 — Follow-Up Tracking 🔲

> Ref: US-18.9

- Dashboard widget/page showing follow-ups due this week
- Filter by department, doctor, date range
- Flag missed follow-ups

**Feature Tests:**
```
tests/Feature/Treatments/FollowUpTrackingTest.php
- test dashboard shows follow-ups due this week
- test follow-ups can be filtered by department and doctor
- test past-due follow-ups are flagged as missed
- test follow-up list excludes patients who already returned
```

---

## Phase 6 — Stock Tracking

> Ref: Epic 19.

---

### Phase 6.1 — Manage Stock Items (Filament CRUD) 🔲

> Ref: US-19.1

**Feature Tests:**
```
tests/Feature/Stock/StockItemManagementTest.php
- test admin can create stock item with all required fields
- test admin can edit stock item
- test stock item requires unique SKU
- test stock item belongs to a category
- test hierarchical categories display correctly
- test medicine flag enables pharmacy-level fields
```

---

### Phase 6.2 — Record Stock Movements 🔲

> Ref: US-19.2

**Feature Tests:**
```
tests/Feature/Stock/StockMovementTest.php
- test stock IN movement increases current stock level
- test stock OUT movement decreases current stock level
- test stock movement tracks batch number and expiry for medicines
- test stock movement links to reference (PurchaseOrder, ServiceOrder)
- test current stock = SUM(IN) - SUM(OUT)
- test stock cannot go negative (configurable)
```

---

### Phase 6.3 — Purchase Order Workflow 🔲

> Ref: US-19.3

**Feature Tests:**
```
tests/Feature/Stock/PurchaseOrderTest.php
- test admin can create purchase order (DRAFT)
- test PO number is auto-assigned in PO/{year}/{month}/{sequence} format
- test admin can approve purchase order (DRAFT → APPROVED)
- test receiving PO creates stock IN movements for all items
- test PO links to expense voucher for payment
- test PO total equals sum of item totals
```

---

### Phase 6.4 — Low Stock Alerts 🔲

> Ref: US-19.4

**Feature Tests:**
```
tests/Feature/Stock/LowStockAlertTest.php
- test dashboard widget shows items below reorder level
- test critical alert when stock is zero
- test warning alert when stock is below reorder level
- test items at or above reorder level are not shown
```

---

### Phase 6.5 — Medicine Expiry Tracking 🔲

> Ref: US-19.5

**Feature Tests:**
```
tests/Feature/Stock/MedicineExpiryTest.php
- test expiry report shows medicines expiring within 30 days
- test expiry report shows medicines expiring within 60/90 days
- test expired items are flagged in stock listing
- test expired items cannot be dispensed (configurable)
```

---

### Phase 6.6 — Service Order Stock Consumption 🔲

> Ref: US-19.6

**Feature Tests:**
```
tests/Feature/Stock/ServiceConsumptionTest.php
- test service linked to stock items auto-deducts on service order creation
- test stock OUT movement is created with service order reference
- test zero stock blocks service order creation (when configured)
- test non-linked services do not create stock movements
```

---

### Phase 6.7 — Stock Reports 🔲

> Ref: US-19.7

**Feature Tests:**
```
tests/Feature/Stock/StockReportsTest.php
- test current stock report shows quantities and values
- test movement history report can be filtered by date and item
- test department consumption report groups by department
- test stock report PDF exports correctly
```

---

## Phase 7 — Asset Tracking

> Ref: Epic 20.

---

### Phase 7.1 — Register Assets (Filament CRUD) 🔲

> Ref: US-20.1

**Feature Tests:**
```
tests/Feature/Assets/AssetRegistrationTest.php
- test admin can create asset with all required fields
- test AST number is auto-assigned in AST/{year}/{sequence} format
- test asset belongs to category and department
- test asset status defaults to ACTIVE
- test asset supports soft delete
```

---

### Phase 7.2 — Asset Assignment History 🔲

> Ref: US-20.2

**Feature Tests:**
```
tests/Feature/Assets/AssetAssignmentTest.php
- test admin can transfer asset to another department/user
- test transfer creates assignment log entry
- test assignment history log shows chronological transfers
- test asset's current assignment reflects latest transfer
```

---

### Phase 7.3 — Maintenance Scheduling 🔲

> Ref: US-20.3

**Feature Tests:**
```
tests/Feature/Assets/MaintenanceSchedulingTest.php
- test admin can schedule preventive maintenance for an asset
- test maintenance log records type, cost, and dates
- test dashboard widget shows overdue maintenance items
- test next maintenance date is auto-calculated from schedule
```

---

### Phase 7.4 — Depreciation Calculation 🔲

> Ref: US-20.4

**Feature Tests:**
```
tests/Feature/Assets/DepreciationTest.php
- test straight-line depreciation calculates monthly amount correctly
- test declining balance depreciation calculates correctly
- test monthly depreciation entries are generated
- test accumulated depreciation increases each month
- test book value = purchase_cost - accumulated_depreciation
```

---

### Phase 7.5 — QR Code Asset Labels 🔲

> Ref: US-20.5

**Feature Tests:**
```
tests/Feature/Assets/QRCodeLabelTest.php
- test QR code encodes asset number and URL
- test printable label sheet generates multiple QR codes per A4
- test scanning QR code URL resolves to asset detail page
```

---

### Phase 7.6 — Warranty & Expiry Alerts 🔲

> Ref: US-20.6

**Feature Tests:**
```
tests/Feature/Assets/WarrantyAlertTest.php
- test dashboard widget shows assets with warranties expiring within 30 days
- test warranty report can be filtered by expiry range (30/60/90 days)
- test assets with no warranty are excluded from report
```

---

## Phase 8 — User Tasking

> Ref: Epic 21.

---

### Phase 8.1 — Create & Assign Tasks (Filament CRUD) 🔲

> Ref: US-21.1

**Feature Tests:**
```
tests/Feature/Tasking/TaskCreationTest.php
- test admin can create task with title, description, priority, due_date
- test TSK number is auto-assigned in TSK/{year}/{month}/{sequence} format
- test task can be assigned to any active user
- test task belongs to department
- test task priority defaults to MEDIUM
```

---

### Phase 8.2 — Task Lifecycle 🔲

> Ref: US-21.2

**Feature Tests:**
```
tests/Feature/Tasking/TaskLifecycleTest.php
- test task status transitions: TODO → IN_PROGRESS → COMPLETED
- test task can transition to BLOCKED or CANCELLED
- test completing a task requires completion_notes
- test status changes are logged with timestamp and user
```

---

### Phase 8.3 — My Tasks View 🔲

> Ref: US-21.3

**Feature Tests:**
```
tests/Feature/Tasking/MyTasksTest.php
- test user sees only their assigned tasks
- test tasks are sorted by due date and priority
- test overdue tasks are highlighted
- test tasks can be filtered by status and priority
```

---

### Phase 8.4 — Task Comments & Attachments 🔲

> Ref: US-21.4

**Feature Tests:**
```
tests/Feature/Tasking/TaskCommentsTest.php
- test user can add comment to a task
- test comments are displayed in chronological order
- test user can attach files to a task (via media library)
- test comment belongs to task and user
```

---

### Phase 8.5 — Overdue Task Alerts 🔲

> Ref: US-21.5

**Feature Tests:**
```
tests/Feature/Tasking/OverdueAlertTest.php
- test dashboard widget shows count of overdue tasks
- test overdue tasks are grouped by assignee
- test widget is filterable by department
- test clicking through navigates to task detail
```

---

### Phase 8.6 — Task Templates 🔲

> Ref: US-21.6

**Feature Tests:**
```
tests/Feature/Tasking/TaskTemplateTest.php
- test admin can create task template with default values
- test one-click task creation from template
- test template preserves title, description, priority, and default assignee
```

---

## Phase 9 — User Payroll

> Ref: Epic 22.

---

### Phase 9.1 — Configure Salary Structures 🔲

> Ref: US-22.1

**Feature Tests:**
```
tests/Feature/Payroll/SalaryStructureTest.php
- test admin can create salary structure for a user
- test salary structure has all component fields (basic, housing, medical, transport)
- test only one active structure per user (effective_to is null)
- test changing structure sets effective_to on previous and creates new
- test gross salary is sum of all components
```

---

### Phase 9.2 — Monthly Payroll Processing 🔲

> Ref: US-22.2

**Feature Tests:**
```
tests/Feature/Payroll/PayrollProcessingTest.php
- test accountant can create payroll period (DRAFT)
- test payroll period number follows PAY/{year}/{month} format
- test bulk calculation generates payslip entries for all active users
- test payslip net_salary = gross - deductions
- test advance deductions are auto-applied from active salary advances
- test payroll status transitions: DRAFT → CALCULATED → APPROVED → PAID
```

---

### Phase 9.3 — Payslip Generation 🔲

> Ref: US-22.3

**Feature Tests:**
```
tests/Feature/Payroll/PayslipTest.php
- test payslip is generated per user per period
- test payslip PDF includes all salary components and deductions
- test staff can access their own payslips only
- test admin can access all payslips
```

---

### Phase 9.4 — Salary Advance Management 🔲

> Ref: US-22.4

**Feature Tests:**
```
tests/Feature/Payroll/SalaryAdvanceTest.php
- test admin can record salary advance for a user
- test advance is auto-deducted in monthly payroll
- test advance status changes to FULLY_RECOVERED when balance is zero
- test admin can write off an advance
```

---

### Phase 9.5 — Expense Voucher Integration 🔲

> Ref: US-22.5

**Feature Tests:**
```
tests/Feature/Payroll/VoucherIntegrationTest.php
- test paying payroll creates expense vouchers per user
- test expense voucher links to payroll period
- test voucher flows into Abacus double-entry ledger
```

---

### Phase 9.6 — Payroll Reports 🔲

> Ref: US-22.6

**Feature Tests:**
```
tests/Feature/Payroll/PayrollReportsTest.php
- test monthly payroll summary shows department totals and grand total
- test individual payroll detail report shows all components
- test year-end annual salary certificate per employee
- test payroll reports can be exported to PDF
```

---

## Phase 10 — Patient Manager Portal

> Ref: Epic 13. Requires `laravel/sanctum` for token-based auth.

---

### Phase 10.1 — Patient Self-Registration with OTP 🔲

> Ref: US-13.1

**Feature Tests:**
```
tests/Feature/Portal/PatientRegistrationTest.php
- test patient can register with mobile number
- test OTP is sent to the provided mobile number
- test valid OTP creates a verified account
- test invalid OTP is rejected
- test expired OTP is rejected
```

---

### Phase 10.2 — Link Patient Records 🔲

> Ref: US-13.2

**Feature Tests:**
```
tests/Feature/Portal/LinkRecordsTest.php
- test patient can search by PS number or CNIC
- test matching patient record is found and displayed
- test patient can confirm linkage
- test incorrect CNIC/PS combination is rejected
```

---

### Phase 10.3 — View Medical History 🔲

> Ref: US-13.3

**Feature Tests:**
```
tests/Feature/Portal/MedicalHistoryTest.php
- test patient can view their service orders
- test patient can view their transactions
- test patient can view their receivables
- test patient cannot view other patients' records
```

---

## Phase 11 — Dashboards & Analytics

> Ref: US-10.7 to US-10.10.

---

### Phase 11.1 — Activity Dashboard 🔲

> Ref: US-10.7

**Feature Tests:**
```
tests/Feature/Dashboards/ActivityDashboardTest.php
- test activity dashboard page renders
- test line charts show record creation over time
- test dashboard respects date range filter
```

---

### Phase 11.2 — Operations Dashboard 🔲

> Ref: US-10.8

**Feature Tests:**
```
tests/Feature/Dashboards/OperationsDashboardTest.php
- test operations dashboard page renders
- test service order stats show by department
- test donut chart shows distribution by service
```

---

### Phase 11.3 — Sales Dashboard 🔲

> Ref: US-10.9

**Feature Tests:**
```
tests/Feature/Dashboards/SalesDashboardTest.php
- test sales dashboard page renders
- test grouped bar chart shows incoming transactions
- test revenue trends are calculated correctly
```

---

### Phase 11.4 — Expenditure Dashboard 🔲

> Ref: US-10.10

**Feature Tests:**
```
tests/Feature/Dashboards/ExpenditureDashboardTest.php
- test expenditure dashboard page renders
- test paid/pending voucher counts are correct
- test payment graph shows expenditure trends
```

---

## Phase 12 — API & Integration

> Ref: US-15.8, US-15.9, §13.5.

---

### Phase 12.1 — FHIR-Ready API Design 🔲

> Ref: US-15.8

- Design API resources following FHIR naming: Patient, Encounter (ServiceOrder), Claim (Transaction)
- Versioned endpoints: `/api/v1/`
- Token-based auth via Sanctum
- Rate limiting on all endpoints

**Feature Tests:**
```
tests/Feature/Api/FhirApiTest.php
- test /api/v1/patients returns FHIR-structured patient resources
- test /api/v1/encounters returns service order resources
- test API requires bearer token authentication
- test rate limiting blocks excessive requests
- test API versioning works correctly
```

---

### Phase 12.2 — FBR Tax Compliance 🔲

> Ref: US-15.9

- Hospital Settings stores NTN and STRN
- All printed invoices include NTN/STRN
- Tax amount itemized per service on receipts

**Feature Tests:**
```
tests/Feature/Compliance/FBRComplianceTest.php
- test transaction receipt PDF includes NTN/STRN
- test tax amount is itemized per service on receipt
- test hospital settings NTN/STRN fields are configurable
```

---

## Phase 13 — CI/CD & Release Process

> Ref: Epic 16.

---

### Phase 13.1 — CI/CD Pipeline 🔲

> Ref: US-16.7

- Create GitHub Actions workflow: calculate version → create git tag → create release → Sentry release → build Docker images → publish
- Images tagged: `{version}`, `latest`, `{version}-cli`, `latest-cli`

**Feature Tests:**
```
(CI/CD tests are GitHub Actions workflow tests, not PHP feature tests)
- Workflow file validates with act (local GitHub Actions runner)
- Docker images build successfully
- Tests pass in CI environment
```

---

### Phase 13.2 — Pre-Push Checklist & CHANGELOG 🔲

> Ref: US-16.8, US-16.6

- Create `CHANGELOG.md` at project root
- Establish pre-push validation script

**Validation:** Manual — checklist verification documented in CHANGELOG.md structure.

---

## Phase 14 — Frontend Element Enhancements

> Ref: §3 Frontend Architecture — Elements Still Needed.

---

### Phase 14.1 — Missing Entity Elements 🔲

Create missing frontend elements per the element pattern guide:

| Entity | Missing Elements |
|--------|-----------------|
| Service | `FilterAndSelectService`, `SelectService`, `ServiceMiniCard` |
| ServiceDepartment | `FilterAndSelectDepartment`, `SelectDepartment` |
| Panel | `FilterAndSelectPanel`, `SelectPanel`, `PanelMiniCard` |
| Closing | `FilterAndSelectClosing`, `ClosingMiniCard`, `ClosingView` |
| Reception | `SelectReception` |
| Transaction | `TransactionMiniCard`, `TransactionView` |
| ExpenseVoucher | `ExpenseVoucherMiniCard`, `ExpenseVoucherView` |
| ExpenseCategory | `FilterAndSelectExpenseCategory`, `ExpenseCategoryMiniCard` |
| Receivable | `FilterAndSelectReceivable`, `ReceivableMiniCard` |
| Doctor (unified) | `FilterAndSelectDoctor`, `DoctorMiniCard` |
| Patient | `SelectPatient` |

**Feature Tests:** Frontend component tests via browser testing or React Testing Library.

---

## Phase 15 — Existing Feature Test Coverage

> Fill test gaps for already-implemented features (Ref: §11 Testing & Quality — Test Coverage Gaps).

---

### Phase 15.1 — Core Business Logic Tests 🔲

**Feature Tests:**
```
tests/Feature/Core/PatientCreationTest.php
- test patient creation assigns PS number via PatientObserver
- test PS number follows PS/{year}/{month}/{sequence} format
- test PS number is unique and sequential
- test patient is searchable by PS number, name, CNIC

tests/Feature/Core/ClosingLifecycleTest.php
- test opening a counter assigns CT number via ClosingObserver
- test CT number follows CT/{year}/{month}/{sequence} format
- test only one open counter per receptionist
- test closing a counter records cash/card/cheque amounts
- test closing status changes from OPEN to CLOSED

tests/Feature/Core/TransactionCreationTest.php
- test creating income transaction assigns TR number via TransactionObserver
- test TR number follows TR/{year}/{month}/{day}/{sequence} format
- test transaction elements are created per service/provider
- test unpaid transaction creates a receivable
- test expense transaction links to closing

tests/Feature/Core/ServiceOrderTest.php
- test service order creation assigns SO number and token
- test SO number follows {PS_NUMBER}/{department}/{sequence} format
- test service order status defaults to OPEN
- test composite service orders track is_composit flag

tests/Feature/Core/ReceivablePaymentTest.php
- test creating receivable with correct amount
- test partial payment reduces remaining amount
- test full payment marks receivable as closed
- test payment creates a transaction on the open counter

tests/Feature/Core/ExpenseVoucherTest.php
- test expense voucher creation assigns VC number
- test voucher status defaults to PENDING
- test paying voucher changes status to PAYED
- test voucher payment blocked when reception disallows it
```

---

### Phase 15.2 — Filament Resource Tests 🔲

**Feature Tests:**
```
tests/Feature/Filament/ClosingResourceTest.php
- test admin can list closings
- test admin can view closing with 6 tabs
- test closing view tabs render correct data (summary, income, expense, receivables, services)
- test admin can create and edit closings

tests/Feature/Filament/UserResourceTest.php
- test admin can list users with search
- test admin can create user with multiple profiles
- test admin can edit user profiles and authority levels
- test user list filters by active/inactive status

tests/Feature/Filament/ExpenseVoucherResourceTest.php
- test admin can CRUD expense vouchers
- test voucher VC number is displayed as record title

tests/Feature/Filament/ServiceOrderResourceTest.php
- test admin can list and view service orders

tests/Feature/Filament/ServiceManagementTest.php
- test admin can create and edit services with charges and tax rates
- test admin can create and edit departments
- test admin can create and edit receptions with payment flags

tests/Feature/Filament/ReportPagesTest.php
- test income report page renders with filters
- test expense report page renders with filters
- test receivables report renders with grouping
- test services report renders with filters
- test all reports generate PDF exports

tests/Feature/Filament/DashboardWidgetsTest.php
- test admin dashboard shows 6 stat cards
- test stat cards respond to date range filter changes
- test stat cards refresh on 10s polling interval
```

---

### Phase 15.3 — API Controller Tests 🔲

**Feature Tests:**
```
tests/Feature/Api/PatientApiTest.php
- test patient search returns exact and possible matches
- test patient API requires authentication

tests/Feature/Api/TransactionApiTest.php
- test transaction search with filters
- test transaction API pagination

tests/Feature/Api/ClosingApiTest.php
- test closing search and filter

tests/Feature/Api/ServiceOrderApiTest.php
- test service order search

tests/Feature/Api/ExpenseVoucherApiTest.php
- test expense voucher search with year/month filter

tests/Feature/Api/ExpenseCategoryApiTest.php
- test expense category listing

tests/Feature/Api/UserApiTest.php
- test user search
```

---

### Phase 15.4 — PDF & Print Tests 🔲

**Feature Tests:**
```
tests/Feature/Prints/ClosingStatementPrintTest.php
- test closing statement mini (thermal) renders correctly
- test closing statement normal (A4) renders correctly
- test closing report tabs generate correct PDFs

tests/Feature/Prints/TransactionPrintTest.php
- test transaction full A4 receipt renders
- test transaction thermal receipt renders
- test receipt includes TR number, patient, services, amounts

tests/Feature/Prints/ServiceOrderPrintTest.php
- test service order document renders with SO number and token
```

---

## Summary

| Phase | Description | Tasks | Status |
|-------|-------------|-------|--------|
| **1** | Database Structure | 10 sub-phases | Partially ✅ (1.2 done) |
| **2** | Compliance & Security | 7 sub-phases | 🔲 |
| **3** | Core Feature Enhancements | 9 sub-phases | 🔲 |
| **4** | URL Resolution Architecture | 6 sub-phases | 🔲 |
| **5** | Service Order Treatments | 9 sub-phases | 🔲 |
| **6** | Stock Tracking | 7 sub-phases | 🔲 |
| **7** | Asset Tracking | 6 sub-phases | 🔲 |
| **8** | User Tasking | 6 sub-phases | 🔲 |
| **9** | User Payroll | 6 sub-phases | 🔲 |
| **10** | Patient Manager Portal | 3 sub-phases | 🔲 |
| **11** | Dashboards & Analytics | 4 sub-phases | 🔲 |
| **12** | API & Integration | 2 sub-phases | 🔲 |
| **13** | CI/CD & Release Process | 2 sub-phases | 🔲 |
| **14** | Frontend Element Enhancements | 1 sub-phase | 🔲 |
| **15** | Existing Feature Test Coverage | 4 sub-phases | 🔲 |

**Recommended execution order:** Phase 1 → Phase 15 → Phase 2 → Phase 3 → Phase 4 → Phase 5 → Phase 6 → Phase 7 → Phase 8 → Phase 9 → Phase 10 → Phase 11 → Phase 12 → Phase 13 → Phase 14

**Rationale:**
- **Phase 1 first** — All subsequent phases depend on models, factories, and migrations.
- **Phase 15 second** — Test coverage for existing features catches regressions before new work.
- **Phase 2 third** — Compliance is a cross-cutting concern that should be in place early.
- **Phase 3 next** — Completes existing feature gaps before adding new modules.
- **Phase 4** — URL migration is foundational for consistent navigation.
- **Phases 5–9** — New feature modules in priority order (treatments → stock → assets → tasking → payroll).
- **Phases 10–14** — Lower priority features and infrastructure.
