# Hospital All In One Operations Software

Software meant to organize a hospital in every operation, compliant with international standards and Punjab Healthcare Commission guidelines. A dockerized distribution `ahmadfaryabkokab/hospital-care`, which hospitals can easily run on their servers. Each release publishes a cli tag `ahmadfaryabkokab/hospital-care:{version}-cli` and app tag `ahmadfaryabkokab/hospital-care:{version}`. The cli container is responsible for running schedules and granting SSH access for CLI operations.

---

## 1. Project Overview

Application manages Patients Information, Transactions, Service Orders, Receivables, and panel payments. It collects and shares data with respect to regulatory compliances.

### Application Panels

#### Frontend — React TypeScript (Inertia.js)

- **Auth Pages** — Login, Register, Password Reset, Two-Factor Authentication, Email Verification
- **Dashboard** — Landing page after login
- **Settings** — Profile, Password, Two-Factor Authentication, Appearance
- **Counter (Receptionist)**
    - **Open Counter** — Form to open a new counter with opening amount and reception selection
    - **Counter View** — Displays open counter details, transactions, and actions (close, add expense, add income)
    - **Income (Transaction Create)** — Select patient → department → services → providers; creates income transaction; if unpaid, creates a receivable
    - **Expense** — Register expense transactions by selecting expense category, voucher, service order, or transaction
    - **Close Counter** — Form showing counter statement number and closing amount
    - **Counter List** — Lists all closings with year/month filtering and pagination
    - **Receivables List** — Lists patient receivables (outstanding payments) with payment dialog
    - **Vouchers List** — Lists expense vouchers with year/month filtering
    - **New Voucher** — Form to create new expense voucher
- **Patient Register** — Patient registration with year/month/number filters and PS number lookup
- **Hospital Queues**
    - **OPD Queue** — Grouped by service type, shows active OPD orders
    - **General Queue** — Department-based queue display (OPD, Indoor, Emergency, Dental, Lab, Ultrasound, Radiology)
- **Patient Manager Portal** — *(Planned: user registers, enters mobile number, OTP validation, links patient records)*

#### Admin Panel — Filament (`/admin`, default panel)

**Dashboard**
- 6-column responsive grid with date range filter (Today, Last 3/7 Days, This/Last Week/Month/Year, Last Financial Year, Custom Range)
- **AdminStatsOverview Widget** — 6 stat cards with charts and 10s polling:
    1. New Users (with user limit tracking)
    2. Services (departments and services count)
    3. Patient Stats
    4. Counter Stats
    5. Expense Voucher Stats
    6. Transaction Stats

**Reports** (4 Filament Pages)
- **Income Report** — TransactionElements filtered by `income_or_expense = 'INCOME'`; filters: date range, reception, transaction type, service, provider; grouped by status/panel; PDF export
- **Expense Report** — TransactionElements filtered by `income_or_expense = 'EXPENSE'`; filters: date range, reception, type, expense category; PDF export
- **Receivables Report** — Receivable records with columns: date, TR#, patient, panel, original/remaining amount, due date, status; grouped by status/panel; PDF export
- **Services Report** — TransactionElements with services or expense vouchers; filters: date range, reception, flow, service, provider; PDF export

**Closings** (Filament Resource — Full CRUD)
- Record title: `ct_number`
- Pages: List, Create, View, Edit
- **View page with 6 tabs:**
    1. Summary — Mini print view of closing statement
    2. Detailed Summary — Full print view
    3. Services Report — Service delivery breakdown
    4. Income Report — Income transactions
    5. Expense Report — Expense transactions
    6. Receivables Report — Outstanding receivables
- All tabs render shared custom Blade view with print URLs (mini/normal/report-specific)

**Service Orders** (Filament Resource)
- Pages: List, View

**Expense Vouchers** (Filament Resource — Full CRUD)
- Record title: `vc_number`
- Pages: List, Create, View, Edit

**Administration — Services Group**
- **Services** — Manage services with charges, tax rates, departments, composite services, multiple provider types (OPD, Emergency, Inpatient Doctors, Dentists, X-Ray Technicians, Ultrasound); single ManageServices page
- **Departments** — Manage service departments with `have_composit_services` flag; single manage page
- **Receptions** — Manage receptions with allowed departments, payment method flags (cash/cheques/card), voucher payment permission; merge receptions bulk action; single manage page

**Users** (Filament Resource — Full CRUD)
- Pages: List, Create, View, Edit
- **9 profile types** as collapsible repeaters:
    1. **Administrator** — Authorities: Assistant, Administrator, Super Admin
    2. **Accountant** — Authorities: Assistant, Manager
    3. **Receptionist** — Authorities: Assistant, Manager
    4. **Patient Manager** — Associates with Patient via relationship
    5. **OPD Doctor** — Authorities: Assistant, Senior Doctor, Consultant
    6. **Inpatient Doctor** — Authorities: Assistant, Senior Doctor, Consultant
    7. **Emergency Doctor** — Authorities: Assistant, Senior Doctor, Consultant
    8. **Dentist** — Authorities: Assistant, Senior Dentist, Consultant Dentist
    9. **Ultrasound Doctor** — Authorities: Assistant, Specialist, Consultant

#### Accounts Panel — Filament (`/accounts`)
- **Dashboard** — Same date range filter structure as Admin dashboard
- Integrates **Processton\Abacus\AbacusPlugin** (custom accounting package from `packages/processton/abacus/`)
- Resources and specialized widgets not yet populated

### Not Yet Implemented (Planned)
- **Patients** Filament Resource — Patient view with tabs for all relations (service orders, payments)
- **Transaction** Filament Resource
- **Panels** Filament Resource — With pending panel payments
- **Activity Dashboard** — Activity tracking based on records being created, with line charts
- **Operations Dashboard** — Service order stats with line/donut charts by department/service
- **Sales Dashboard** — Incoming transaction stats with group bar chart
- **Expenditure Dashboard** — Expense paid/pending vouchers with payment graph
- **Settings Page** — General settings (Hospital Name, Logo, Address, Contact Info)
- **X-Ray Technician** profile type in User Resource
- **Nursing Staff** profile type in User Resource
- **Patient Manager Portal** — OTP-based mobile verification and patient record linking

---

## 2. Data Models

### Core Models (14)

| Model | Key Fields | Relationships |
|-------|-----------|---------------|
| **User** | name, username, email, mobile, password, is_active | Has many role profiles (admin, accountant, receptionist, opd_doctor, ind_doctor, emergency_doctor, dentist, ultrasound_doctor, xray_technician, nursing_staff, patient_manager) |
| **Patient** | ps_number, name, gender, age_*, address, guardian, contact, cnic | hasMany transactions, service orders (treatments), receivables |
| **ServiceOrder** | type, token, so_number, patient_id, service_id, doctor_id, notes_json, is_composit | belongsTo creator, patient, service, doctor; morphTo payee |
| **Transaction** | tr_number, closing_id, patient_id, panel_id, income_or_expense, amount, is_refunded, exp_voucher_id | belongsTo closing, patient |
| **TransactionElement** | transaction_id, service_order_id, type, income_or_expense, amount, service_id, doctor_id | belongsTo transaction, service, doctor, patient, expenseCategory |
| **Closing** | ct_number, reception_id, receptionist_id, status, opening_amount, closing_amount_*, expense_payed | belongsTo reception; hasMany transactions |
| **ExpenseVoucher** | vc_number, exp_category_id, service_order_id, amount, payed_to | belongsTo expenseCategory, serviceOrder, transaction |
| **Service** | name, slug, charges, tax_rate, have_service_provider, service_provider_types, is_composit_service | belongsTo department |
| **ServiceDepartment** | name, slug, image, have_composit_services | hasMany services |
| **ServiceRecestation** | name, slug, charges, tax_rate, service_provider_types | — |
| **Reception** | name, allowed_departments, is_allowed_to_pay_voucher, is_cash_allowed, is_cheques_allowed, is_card_allowed | hasMany closings |
| **Receaveable** | patient_id, panel_id, amount, due_date, status | belongsTo patient, transaction, panel; hasOneThrough serviceOrder |
| **Panel** | name, code, is_active | — |
| **ExpenseCategory** | name, type, pay_doc, pay_others, pay_users | — |

### Role/Profile Models (11)
Administrator, Accountant, Receptionist, OpdDoctor, IndDoctor, EmergencyDoctor, Dentist, UltrasoundDoctor, XrayTechnician, NursingStaff, PatientManager

### Utility Models
- **Image** — Polymorphic file attachment (owner_id, path, file_type, file_size)
- **InstanceVariable** — Generic key-value storage

### Enums (5)

| Enum | Values |
|------|--------|
| **CounterStatus** | OPEN, CLOSED, REPORTED |
| **ExpenseVoucherStatus** | PENDING, PAYED |
| **PaymentMethods** | CASH, CARD, CHEQUE |
| **ServiceOrderStatus** | OPEN, CLOSED |
| **TransactionElementType** | OPD, IND, EMG, LAB, RAD, DNT, ULT, PETTY_CASH, VOUCHER_PAY, IND_EXP |

### Observers (5)
- **PatientObserver** — Assigns PS number (`PS/{year}/{month}/{sequence}`)
- **ClosingObserver** — Assigns CT number (`CT/{year}/{month}/{sequence}`)
- **TransactionObserver** — Assigns TR number (`TR/{year}/{month}/{sequence}`)
- **TransactionElementObserver** — Post-creation processing
- **ExpenseVoucherObserver** — Assigns VC number

---

## 3. Frontend Architecture

### Reusable Elements (`resources/js/elements/`)

Elements are the building blocks for entity interaction in the frontend. Each core model should have a family of elements for consistent UX across all pages. Elements are organized by entity in subdirectories.

#### Element Patterns

Every entity that appears in forms, lists, or detail views should have the following element types as needed:

| Pattern | Naming Convention | Purpose |
|---------|------------------|--------|
| **FilterAndSelect** | `filter-and-select-{entity}.tsx` | Search/filter dialog with API-backed results, returns selected record |
| **Select** | `select-{entity}.tsx` | Simple dropdown for picking from a small set of options |
| **MiniCard** | `mini-card.tsx` | Compact read-only display card showing key fields of a record |
| **View** | `view.tsx` | Detailed read-only display of a single record |
| **HistoryTree** | `{entity}-history-mini-tree.tsx` | Timeline/tree view of related records |
| **ActionButton** | `{Entity}Button.tsx` | Modal/dialog trigger for a specific action (e.g. payment collection) |

#### Implemented Elements

| Entity | Elements | Status |
|--------|----------|--------|
| **Patient** | `FilterAndSelectPatient`, `FindOrSelectPatient`, `PatientMiniCard`, `TransactionsHistoryCard`, `PatientHistoryMiniTree` | Core set done |
| **User** | `FilterAndSelectUser`, `SelectUser` | Core set done |
| **Counter** | `SelectCounter` | Select only |
| **Department** | `DepartmentMiniCard` | MiniCard only |
| **ExpenseCategory** | `SelectExpenseCategory` | Select only |
| **ExpenseVoucher** | `FilterAndSelectExpenseVoucher` | FilterAndSelect only |
| **Transaction** | `FilterAndSelectTransaction` | FilterAndSelect only |
| **ServiceOrder** | `FilterAndSelectServiceOrder`, `ServiceOrderView` | FilterAndSelect + View |
| **Receivable** | `ReceaveAblesButton` | ActionButton only |
| **Layout** | `BulletsWrapper` | Vertical nav sidebar with active/inactive states |

#### Elements Still Needed

As new pages and features get built, the following elements will need to be created. Follow the patterns above and check sibling elements for conventions.

| Entity | Missing Elements |
|--------|-----------------|
| **Patient** | `SelectPatient` (simple dropdown for small lists) |
| **Service** | `FilterAndSelectService`, `SelectService`, `ServiceMiniCard` |
| **ServiceDepartment** | `FilterAndSelectDepartment`, `SelectDepartment` |
| **Panel** | `FilterAndSelectPanel`, `SelectPanel`, `PanelMiniCard` |
| **Closing** | `FilterAndSelectClosing`, `ClosingMiniCard`, `ClosingView` |
| **Reception** | `SelectReception` |
| **Transaction** | `TransactionMiniCard`, `TransactionView` |
| **ExpenseVoucher** | `ExpenseVoucherMiniCard`, `ExpenseVoucherView` |
| **ExpenseCategory** | `FilterAndSelectExpenseCategory`, `ExpenseCategoryMiniCard` |
| **Receivable** | `FilterAndSelectReceivable`, `ReceivableMiniCard` |
| **Doctor (any role)** | `FilterAndSelectDoctor`, `DoctorMiniCard` (unified across OPD/IND/EMG/DNT/ULT) |

### UI Components (30+ shadcn/ui components)
Accordion, Alert Dialog, Badge, Button, Card, Checkbox, Collapsible, Command, Data Table, Dialog, Dropdown Menu, Input, Label, Pagination, Popover, Radio Group, Scroll Area, Select, Separator, Sheet, Sidebar, Skeleton, Switch, Table, Tabs, Textarea, Toggle Group, Tooltip

### Layouts
- **AppLayout** — Main application shell with sidebar navigation
- **AuthLayout** — Authentication pages layout
- **Panel layouts** — For hospital panel views
- **SettingsLayout** — Settings pages layout

### Hooks (6)
use-appearance, use-clipboard, use-initials, use-mobile-navigation, use-mobile, use-two-factor-auth

---

## 4. API Layer

### Web Routes (routes/web.php)
- **Patient Management** — `/PS` with year/month/number filters
- **Counter Operations** — `/CT-NEW`, `/CT-CLOSE`, `/CT`, `/MY-CT-LIST`, `/CT-PS`
- **Transactions** — `/TR`, `/TR-CREATE`
- **Expenses** — `/CT-EXP`, `/CT-EXP-VOUCHER`, `/CT-EXP-VOUCHER/NEW`
- **Hospital Queues** — `/que/{opd|indoor|emergency|dental|lab|ultrasound|radiology}`
- **Receivables** — `/RECEAVEABLES`, `/RECEAVEABLES-PAYMENT`

### API Routes (routes/api.php)
- `/lookup`, `/patients`, `/expense-vouchers`, `/expense-categories`, `/users`, `/transactions`, `/service-orders`, `/closings`

### Settings Routes (routes/settings.php)
- `/settings/profile`, `/settings/password`, `/settings/appearance`, `/settings/two-factor`

### Controllers
- **WebController** — 30+ methods for patient management, counter operations, transactions, queues
- **10 API Controllers** — Closing, ExpenseVoucher, Patient, Service, ServiceDepartment, ServiceOrder, Transaction, User, ExpenseCategory, LookUp
- **Print/Report Controllers** — ClosingStatementPdfPrint, ServiceOrderPdfPrint, TransactionPdfPrint, GenericReportPdf, IncomeCashFlowReport
- **Settings Controllers** — Password, Profile, TwoFactorAuthentication

---

## 5. PDF Templates & Reports

### Closing Statements
- `closing-statement-mini.blade.php` — Thermal/mini receipt format
- `closing-statement-normal.blade.php` — Full A4 closing statement

### A4 Reports (with shared header/footer partials)
- `report-income.blade.php` — Income report (green accent)
- `report-expense.blade.php` — Expense report (red accent)
- `report-receivables.blade.php` — Receivables report (purple accent)
- `report-services.blade.php` — Services report (indigo accent)

### Transaction Prints
- `transaction-full.blade.php` — Full A4 transaction receipt
- `transaction-thermal.blade.php` — Thermal printer format
- `transaction-dot-printer.blade.php` — Dot matrix printer format

### Service Order Print
- `serviceorder.blade.php` — Service order document

### Shared Partials (`resources/views/pdfs/closing-statement/partials/`)
- `report-header.blade.php` — Unified header with company name, report title, color accent, info grid
- `report-footer.blade.php` — Footer with generation timestamp
- `generic-header.blade.php`, `generic-footer.blade.php`

---

## 6. Key Structural Points

- **PS Number** (Patient): `PS/{year}/{month}/{sequence with leading zero}` — assigned via PatientObserver
- **CT Number** (Counter/Closing): `CT/{year}/{month}/{sequence with leading zero}` — assigned via ClosingObserver
- **TR Number** (Transaction): `TR/{year}/{month}/{sequence with leading zero}` — assigned via TransactionObserver
- **VC Number** (Expense Voucher): Auto-assigned via ExpenseVoucherObserver
- **SO Number** (Service Order): `{PS_NUMBER}/{department}/{sequence with leading zero}`
- **SO Short Number**: `{department}/{sequence with leading zero}`
- **Token Number**: `{year}{month}{sequence with leading zero}`

---

## 7. Technical Stack

| Layer | Technology |
|-------|-----------|
| Backend | Laravel 12 (PHP 8.4) |
| Frontend | React 19 + TypeScript + Inertia.js v2 |
| Styling | Tailwind CSS v4 |
| Admin Panel | Filament v4 (Livewire 3 + Alpine.js) |
| Database | MySQL 8 |
| Containerization | Docker (docker-compose with app + cli containers) |
| Testing | Pest v4 / PHPUnit v12 |
| Code Style | Laravel Pint |
| Route Generation | Laravel Wayfinder |
| Auth Backend | Laravel Fortify |
| Monitoring | Laravel Pulse, Telescope, Sentry |
| PDF Generation | DomPDF |
| Accounting | Processton/Abacus (custom package) |

---

## 8. Database

### Migrations
Covering: users, patients, services, departments, receptions, closings, transactions, transaction_elements, service_orders, expense_vouchers, expense_categories, receivables, panels, profiles (11 role tables), images, instance_variables, jobs, telescope, pulse, and performance indexes.

### Seeders
- **DatabaseSeeder** — Main seeder
- **ExpenseCategorySeeder** — Default expense categories
- **ServicesAndDepartmentsSeeder** — Default services and departments

### Factories
- **UserFactory** — User model factory

---

## 9. Services & Helpers

### Services
- **CaptivePortalService** — WiFi captive portal integration (isEnabled, getEndpoint, getDuration, authorizeClient)
- **FilamentThemeService** — Brand colors and custom styles for Filament

### Helpers
- **NumberHelper** — `moneyfy()`, `formatCurrency()`, `formatPercentage()`

---

## 10. Console Commands
- **CloseOldServiceOrders** — Auto-close stale service orders

---

## 11. Testing & Quality

### Current Test Coverage

| Area | Tests | Status |
|------|-------|--------|
| **Auth** | Authentication, Email Verification, Password Confirmation, Password Reset, Registration, Two-Factor Challenge, Verification Notification | 7 feature tests |
| **Settings** | Password Update, Profile Update, Two-Factor Authentication | 3 feature tests |
| **General** | Dashboard access | 1 feature test |

### Test Coverage Gaps (Priority Order)

The following areas need test coverage. When implementing any feature, write tests alongside it.

**High Priority — Core Business Logic:**
- Patient creation and PS number assignment (Observer)
- Counter open/close lifecycle and CT number assignment
- Transaction creation (income + expense) and TR number assignment
- Service order creation, token assignment, and status transitions (OPEN → CLOSED)
- Receivable creation when transaction is unpaid, and payment collection
- Expense voucher creation, VC number assignment, and payment flow
- Transaction element observer processing

**Medium Priority — Filament Resources:**
- Closing resource: list, view tabs, collect payment action
- User resource: create with profiles, edit profiles, list/filter
- Expense voucher resource: CRUD lifecycle
- Service order resource: list and view
- Service / Department / Reception manage pages: create, edit, delete
- Report pages: filter application, data correctness, PDF generation
- Dashboard widgets: stat calculations with date range filters

**Lower Priority — API & Frontend:**
- API controllers: search, filter, pagination for each entity
- Print/PDF controllers: correct rendering with sample data
- WebController methods: patient register, counter views, queue pages
- Receivable payment dialog flow

### Testing Conventions

- **Tool**: Pest v4 (`php artisan make:test --pest {name}`)
- **Run**: `php artisan test --compact` or `--filter=testName`
- **Feature tests** for all business logic (most tests should be feature tests)
- **Unit tests** only for isolated helpers/services with no DB dependency
- Use `RefreshDatabase` trait in feature tests
- Use model factories; create factories for all models that lack one (currently only `UserFactory` exists — **Patient, Closing, Transaction, ServiceOrder, ExpenseVoucher, Service, ServiceDepartment, Reception, Panel, ExpenseCategory, Receaveable, TransactionElement** all need factories)
- Use `fake()` helper for faker data (follow existing convention)
- Authenticate users in Filament tests before testing panel functionality
- Use `livewire()` / `Livewire::test()` for Filament resource tests
- Run `vendor/bin/pint --dirty` after modifying PHP files

---

## 12. Reusability Conventions
- Create reusable **Elements** (`resources/js/elements/`) for every entity — see Section 3 for the full element pattern guide and gap list
- Use **shadcn/ui** components as the base component library
- For entities, create Elements and reuse them for consistency across pages
- Factories and seeders for test data generation
- Shared PDF partials for consistent report formatting

---

## 13. Compliance & Regulatory Standards

Position: **"HIPAA-inspired, PHC-compliant Hospital Management System with FHIR-ready APIs"** — enables trust with hospitals, government project eligibility, and export potential (Middle East, UK).

### 13.1 Core Healthcare Compliance (Must-Have)

#### Patient Data Privacy & Security
- **Role-based access control** — Doctor, Nurse, Receptionist, Lab Staff, Admin each see only what they need (Laravel Policies + Gates)
- **Fine-grained permissions** — View vs Edit vs Delete per resource per role
- **No shared login accounts** — Each user must have individual credentials
- **Audit logs for every action** — Who viewed patient, who edited prescription, who created transaction (use activity log package)
- **Data encryption at rest** — Encrypted DB fields for sensitive data (CNIC, contact, medical notes)
- **Data encryption in transit** — HTTPS everywhere, no exceptions
- **No plain text passwords or sensitive data** — bcrypt/argon2 for passwords, encrypted storage for PHI

#### Medical Record Integrity
- Records must be **immutable** — No silent edits; all changes create new versions
- **Version-controlled** — Track who changed what and when on patient records, prescriptions, diagnoses
- Maintain complete history: diagnosis history, prescriptions, lab results, treatment records
- **Never delete patient history** — Soft delete only, with audit trail

#### Patient Identification
- **Unique Patient ID (MRN)** — PS Number system (`PS/{year}/{month}/{sequence}`) already implemented
- **Duplicate prevention** — CNIC/contact-based matching to avoid duplicate patient records
- **CNIC / Passport linkage** — Pakistan context identity verification

#### Consent Management
- Patient agrees to treatment and data usage
- Store digital signature or checkbox consent logs with timestamps
- Track consent per service order and treatment

### 13.2 International Standards

#### HIPAA (US — Health Insurance Portability and Accountability Act)
Protects patient health information (PHI). Following HIPAA makes the system globally acceptable.
- Access control (role-based, enforced)
- Audit logs (every access and modification)
- Encryption (at rest + in transit)
- Breach reporting (notification system for data breaches)

#### GDPR (EU — General Data Protection Regulation)
Applies if the system ever serves EU users.
- Right to delete data (anonymization workflow)
- Consent tracking (per-purpose, timestamped)
- Data portability (export patient data in standard format)

#### HL7 (Health Level Seven)
Standard for lab integrations and hospital-to-hospital communication.
- Required when connecting labs, pharmacies, or government APIs
- Standardized messaging format for clinical data exchange

#### FHIR (Fast Healthcare Interoperability Resources)
Modern API-based evolution of HL7 — JSON-based, ideal for Laravel APIs.
- Design API layer aligned with FHIR resource structure
- Use for patient records, observations, encounters, medications
- Strongly recommended for the architecture going forward

### 13.3 Pakistan-Specific Compliance

#### Punjab Healthcare Commission (PHC)
- Patient record maintenance — complete, accurate, retrievable
- Standardized documentation — consistent forms and reports
- Audit capability — inspectors can verify records and access logs

#### PCI DSS (Payment Card Industry Data Security Standard)
Required when processing card payments or online billing/POS.
- Secure cardholder data
- Maintain a vulnerability management program
- Implement strong access control measures

#### FBR / Tax Compliance
- Invoice generation must include NTN / STRN
- Proper tax calculation on all billable services
- Design for future e-invoicing integration with FBR

### 13.4 System-Level Compliance (Architecture Requirements)

#### Access Control System
- Multi-role with 11 profile types already defined (Admin, Accountant, Receptionist, Doctors, etc.)
- **Implementation**: Laravel Policies + Gates for RBAC across all resources
- Per-resource permission matrix: who can View, Create, Edit, Delete

#### Audit Trail (Critical)
Every action must log:
- `user_id` — who performed the action
- `action` — what was done (created, updated, viewed, deleted)
- `auditable_type` + `auditable_id` — which record was affected (e.g. Patient #42)
- `old_values` / `new_values` — what changed
- `timestamp` — when it happened
- `ip_address` / `user_agent` — from where

**Implementation**: Activity log package with model observers on all core models.

#### Backup & Disaster Recovery
- Daily automated backups of database and file storage
- Offsite storage (Host-Swarm infrastructure)
- Periodic restore testing to verify backup integrity
- Documented recovery procedure with RTO/RPO targets

#### Data Retention Policy
- Keep patient records for **minimum 5–10 years** (recommended)
- No random deletion — soft deletes with retention schedules
- Archival strategy for aging records

### 13.5 Integration Compliance (Future-Proofing)

The system must support integration with:
- Lab systems (results import/export)
- Pharmacy systems (prescription routing)
- Insurance APIs (claim processing)
- Government APIs (future Pakistan digitization)

**Implementation approach:**
- REST APIs designed with FHIR resource conventions
- Secure token-based authentication (Sanctum / OAuth)
- Rate limiting on all API endpoints
- Versioned API (`/api/v1/`, `/api/v2/`)

### 13.6 Minimum Viable Compliance Checklist

| Requirement | Status | Notes |
|-------------|--------|-------|
| Role-based access control | Partial | 11 profile types exist; Policies/Gates need enforcement across all resources |
| Audit logs | Not started | Need activity log on all core models |
| HTTPS everywhere | Infrastructure | Enforced at Docker/reverse proxy level |
| Unique Patient ID (MRN) | Done | PS Number via PatientObserver |
| Data encryption at rest | Not started | Need encrypted fields for CNIC, contact, medical notes |
| Data encryption in transit | Infrastructure | TLS certificates on all endpoints |
| Immutable medical records | Not started | Need versioning system for patient records |
| Consent logging | Not started | Need consent records per treatment/service order |
| Backup system | Not started | Need automated daily backups with offsite storage |
| Breach notification | Not started | Need alerting system for unauthorized access |
| FHIR-ready APIs | Not started | Design API resources following FHIR conventions |
| FBR tax compliance | Partial | Tax rates on services exist; NTN/STRN fields needed on invoices |
| Duplicate patient prevention | Not started | Need CNIC/contact matching on patient creation |