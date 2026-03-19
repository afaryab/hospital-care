# Hospital All In One Operations Software

Software meant to organize a hospital in every operation, compliant with international standards and Punjab Healthcare Commission guidelines. A dockerized distribution `ahmadfaryabkokab/hospital-care`, which hospitals can easily run on their servers. Each release publishes a cli tag `ahmadfaryabkokab/hospital-care:{version}-cli` and app tag `ahmadfaryabkokab/hospital-care:{version}`. The cli container is responsible for running schedules and granting SSH access for CLI operations.

---

## 1. Project Overview

Application manages Patients Information, Transactions, Service Orders, Receivables, and panel payments. It collects and shares data with respect to regulatory compliances.

### URL Resolution Architecture

Every record in the system is addressable via a hierarchical URL that progressively resolves from panel → record type → time context → individual record. Each segment of the URL is independently resolvable and renders meaningful content at that level.

**Pattern:** `/{Panel}/{RecordType}/{Year}/{Month}/{Sequence}`

| URL Depth | Resolves To | Example |
|-----------|------------|---------|
| `/{Panel}` | Panel landing / dashboard | `/COUNTER` → Counter panel home |
| `/{Panel}/{RecordType}` | Full listing of that record type | `/COUNTER/CT` → All closings |
| `/{Panel}/{RecordType}/{Year}` | Filtered listing for that year | `/COUNTER/CT/2026` → 2026 closings |
| `/{Panel}/{RecordType}/{Year}/{Month}` | Filtered listing for that year+month | `/COUNTER/CT/2026/03` → March 2026 closings |
| `/{Panel}/{RecordType}/{Year}/{Month}/{Seq}` | Individual record view | `/COUNTER/CT/2026/03/0001` → Closing statement |

#### Record Type URL Maps

**Patient Register Panel:**
| URL | Content |
|-----|---------|
| `/PS` | All patients listing |
| `/PS/{year}` | Patients registered in year |
| `/PS/{year}/{month}` | Patients registered in year/month |
| `/PS/{year}/{month}/{number}` | Individual patient view |
| `/PS/{year}/{month}/{number}/{departmentKey}` | Patient → department services |
| `/PS/{year}/{month}/{number}/{departmentKey}/{serviceNumber}` | Patient → specific service order |

**Counter Panel:**
| URL | Content |
|-----|---------|
| `/COUNTER` | Counter panel landing (open counter or resume) |
| `/COUNTER/CT` | All closings listing |
| `/COUNTER/CT/{year}` | Closings in year |
| `/COUNTER/CT/{year}/{month}` | Closings in year/month |
| `/COUNTER/CT/{year}/{month}/{sequence}` | Individual closing statement view |
| `/COUNTER/TR` | Transaction search |
| `/COUNTER/TR/{year}/{month}/{day}/{number}` | Individual transaction view |
| `/COUNTER/VC` | Expense vouchers listing |
| `/COUNTER/VC/{year}` | Vouchers in year |
| `/COUNTER/VC/{year}/{month}` | Vouchers in year/month |
| `/COUNTER/VC/NEW` | Create new voucher |
| `/COUNTER/EXP` | Record expense transaction |
| `/COUNTER/RECV` | Receivables listing |

**Hospital Queues Panel:**
| URL | Content |
|-----|---------|
| `/QUE` | Queue dashboard (all departments) |
| `/QUE/{department}` | Department-specific queue (OPD, Indoor, Emergency, Dental, Lab, Ultrasound, Radiology) |

**Admin Panel (Filament):**
| URL | Content |
|-----|---------|
| `/admin` | Admin dashboard |
| `/admin/closings` | Closings resource (list, CRUD) |
| `/admin/service-orders` | Service orders resource |
| `/admin/expense-vouchers` | Expense vouchers resource |
| `/admin/users` | Users resource |
| `/admin/reports/{type}` | Report pages (income, expense, receivables, services) |

**Accounts Panel (Filament):**
| URL | Content |
|-----|---------|
| `/accounts` | Accounts dashboard |

**Print/Download (Auth Required):**
| URL | Content |
|-----|---------|
| `/PRINT/CT/{year}/{month}/{number}` | Closing statement PDF |
| `/PRINT/TR/{year}/{month}/{day}/{number}` | Transaction receipt PDF |
| `/PRINT/SO/{id}` | Service order PDF |
| `/DOWNLOAD/TR/{year}/{month}/{day}/{number}` | Transaction PDF download |

#### Design Principles

1. **Progressive Resolution** — Every URL prefix is valid and renders content. `/COUNTER/CT` shows all closings; `/COUNTER/CT/2026` shows 2026 closings; deeper segments narrow the view.
2. **Record Identity in URL** — The URL fully identifies the record without requiring database IDs. The combination `{RecordType}/{Year}/{Month}/{Sequence}` maps directly to the record's identity number (e.g., `CT/2026/03/0001`).
3. **Panel Scoping** — The first URL segment determines which panel/context the user is in, governing navigation, sidebar, and available actions.
4. **Consistency Across Panels** — Both the React frontend and Filament admin panel follow the same hierarchical resolution pattern for their respective record types.

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
- **URL Migration** — Migrate legacy routes (CT-NEW, CT-CLOSE, MY-CT-LIST, etc.) to hierarchical URL pattern (see §1)
- **Service Order Treatments** — Full clinical treatment records per PHC guidelines (see §14.1)
- **Stock Tracking** — Hospital consumables and medicine inventory management (see §14.2)
- **Asset Tracking** — Fixed asset lifecycle management with depreciation (see §14.3)
- **User Tasking** — Internal task assignment and tracking system (see §14.4)
- **User Payroll** — Salary management, payslips, and advance tracking (see §14.5)

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

Routes follow the hierarchical URL resolution pattern (see §1). Each route prefix resolves progressively.

**Current Routes (to be migrated to hierarchical pattern):**

| Current URL | Target URL | Resolves |
|-------------|-----------|----------|
| `/PS` | `/PS` ✅ | Patient listing |
| `/PS/{year}` | `/PS/{year}` ✅ | Patients by year |
| `/PS/{year}/{month}` | `/PS/{year}/{month}` ✅ | Patients by year/month |
| `/PS/{year}/{month}/{number}` | `/PS/{year}/{month}/{number}` ✅ | Patient view |
| `/CT` | `/COUNTER` | Counter panel landing |
| `/CT-NEW` | `/COUNTER/CT/NEW` | Open new counter |
| `/CT-CLOSE` | `/COUNTER/CT/CLOSE` | Close active counter |
| `/CT/{y}/{m}/{n}` | `/COUNTER/CT/{y}/{m}/{n}` | Closing statement view |
| `/MY-CT-LIST` | `/COUNTER/CT` | My closings listing |
| `/MY-CT-LIST/{y}` | `/COUNTER/CT/{y}` | My closings by year |
| `/MY-CT-LIST/{y}/{m}` | `/COUNTER/CT/{y}/{m}` | My closings by year/month |
| `/TR` | `/COUNTER/TR` | Transaction search |
| `/TR/{y}/{m}/{d}/{n}` | `/COUNTER/TR/{y}/{m}/{d}/{n}` | Transaction view |
| `/CT-PS` | `/COUNTER/PS` | Select patient for income |
| `/CT-PS/{y}/{m}/{n}` | `/COUNTER/PS/{y}/{m}/{n}` | Select department for patient |
| `/CT-EXP` | `/COUNTER/EXP` | Record expense |
| `/CT-EXP-VOUCHER` | `/COUNTER/VC` | Vouchers listing |
| `/CT-EXP-VOUCHER/NEW` | `/COUNTER/VC/NEW` | Create new voucher |
| `/RECEAVEABLES` | `/COUNTER/RECV` | Receivables listing |
| `/que/{dept}` | `/QUE/{dept}` | Department queue |
| `/ACC-CT-ALL` | `/ACCOUNTS/CT` | All closings (accountant) |

**Print Routes:** `/PRINT/{RecordType}/{year}/{month}/{...}/{number}`
**Report Routes:** `/reports/{type}` (income-cash-flow, generic/income, generic/expense, etc.)

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
| PDF Generation | DomPDF, mPDF |
| Accounting | Processton/Abacus (custom package) |

### 7.1 PHP Packages (Composer)

#### Production Dependencies

| Package | Version | Purpose |
|---------|---------|---------|
| `laravel/framework` | ^12.0 | Core Laravel framework |
| `filament/filament` | ^4.0 | Admin panel (Livewire 3 + Alpine.js based SDUI) |
| `inertiajs/inertia-laravel` | ^2.0 | Server-side Inertia.js adapter — bridges Laravel to React SPA |
| `livewire/livewire` | ^3 | Reactive server-rendered components (used by Filament) |
| `laravel/fortify` | ^1.30 | Headless authentication backend (login, register, 2FA, password reset) |
| `laravel/wayfinder` | ^0.1.9 | TypeScript route generation from Laravel routes |
| `laravel/pulse` | ^1.5 | Application performance monitoring dashboard |
| `laravel/telescope` | ^5.15 | Debug assistant — requests, queries, jobs, exceptions |
| `laravel/tinker` | ^2.10.1 | REPL for Laravel — interactive console for debugging |
| `sentry/sentry-laravel` | ^4.20 | Error tracking and performance monitoring (production) |
| `barryvdh/laravel-dompdf` | ^3.1 | PDF generation via DomPDF — A4 reports, receipts |
| `mpdf/mpdf` | ^8.2 | PDF generation via mPDF — alternative renderer for complex layouts |
| `blade-ui-kit/blade-heroicons` | ^2.6 | Heroicons as Blade components |
| `owenvoke/blade-fontawesome` | ^3.0 | Font Awesome icons as Blade components |
| `troccoli/blade-health-icons` | ^5.0 | Health-specific icons (medical UI) as Blade components |
| `processton/abacus` | * (local) | Custom double-entry accounting package (`packages/processton/abacus/`) |

#### Development Dependencies

| Package | Version | Purpose |
|---------|---------|---------|
| `pestphp/pest` | ^4.1 | Testing framework (BDD-style PHP testing) |
| `pestphp/pest-plugin-laravel` | ^4.0 | Laravel-specific Pest helpers and assertions |
| `fakerphp/faker` | ^1.23 | Fake data generation for tests and seeders |
| `mockery/mockery` | ^1.6 | Mock object framework for tests |
| `nunomaduro/collision` | ^8.6 | Beautiful error reporting for CLI/tests |
| `laravel/pint` | ^1.24 | Code style fixer (PSR-12 / Laravel conventions) |
| `laravel/pail` | ^1.2.2 | Real-time log viewer in the terminal |
| `laravel/sail` | ^1.41 | Docker development environment |
| `laravel/boost` | ^2.3 | MCP server for AI-assisted development (database, docs, logs) |
| `fruitcake/laravel-debugbar` | ^3.16 | In-browser debug toolbar (queries, routes, views) |
| `doctrine/dbal` | ^4.3 | Database abstraction — required for column modification in migrations |

### 7.2 JavaScript/TypeScript Packages (npm)

#### Production Dependencies

| Package | Version | Purpose |
|---------|---------|---------|
| `react` | ^19.2.3 | UI library |
| `react-dom` | ^19.2.3 | React DOM renderer |
| `@inertiajs/react` | ^2.3.10 | Inertia.js React adapter — client-side page rendering |
| `tailwindcss` | ^4.1.18 | Utility-first CSS framework |
| `@tailwindcss/vite` | ^4.1.18 | Tailwind CSS v4 Vite integration |
| `tailwind-merge` | ^3.4.0 | Merges Tailwind classes without conflicts (used by shadcn/ui) |
| `tw-animate-css` | ^1.4.0 | Tailwind CSS animation utilities |
| `class-variance-authority` | ^0.7.1 | Component variant management (used by shadcn/ui) |
| `clsx` | ^2.1.1 | Conditional className utility |
| `lucide-react` | ^0.475.0 | Icon library (used by shadcn/ui components) |
| `@headlessui/react` | ^2.2.9 | Unstyled accessible UI components (menus, dialogs, transitions) |
| `@radix-ui/react-avatar` | ^1.1.11 | Avatar component primitive |
| `@radix-ui/react-checkbox` | ^1.3.3 | Checkbox component primitive |
| `@radix-ui/react-collapsible` | ^1.1.12 | Collapsible component primitive |
| `@radix-ui/react-dialog` | ^1.1.15 | Dialog/modal component primitive |
| `@radix-ui/react-dropdown-menu` | ^2.1.16 | Dropdown menu component primitive |
| `@radix-ui/react-label` | ^2.1.8 | Label component primitive |
| `@radix-ui/react-navigation-menu` | ^1.2.14 | Navigation menu component primitive |
| `@radix-ui/react-select` | ^2.2.6 | Select component primitive |
| `@radix-ui/react-separator` | ^1.1.8 | Separator component primitive |
| `@radix-ui/react-slot` | ^1.2.4 | Slot component for composition patterns |
| `@radix-ui/react-toggle` | ^1.1.10 | Toggle component primitive |
| `@radix-ui/react-toggle-group` | ^1.1.11 | Toggle group component primitive |
| `@radix-ui/react-tooltip` | ^1.2.8 | Tooltip component primitive |
| `@sentry/browser` | ^10.39.0 | Sentry browser SDK — frontend error tracking |
| `@sentry/react` | ^10.39.0 | Sentry React integration — component error boundaries |
| `@sentry/replay` | ^7.116.0 | Sentry session replay — reproduce user errors |
| `framer-motion` | ^12.26.2 | Animation library for React |
| `input-otp` | ^1.4.2 | OTP input component (used for 2FA) |
| `kbar` | ^0.1.0-beta.48 | Command palette (⌘K) — quick navigation |
| `reactjs-human-body` | ^0.0.8 | Human body diagram component (medical UI) |
| `laravel-vite-plugin` | ^2.0.1 | Vite integration for Laravel |
| `vite` | ^7.3.1 | Frontend build tool |
| `typescript` | ^5.9.3 | TypeScript compiler |
| `concurrently` | ^9.2.1 | Run multiple dev processes in parallel (`composer run dev`) |
| `globals` | ^15.15.0 | Global JS variable definitions for ESLint |
| `@vitejs/plugin-react` | ^5.1.2 | Vite plugin for React (Fast Refresh, JSX transform) |
| `@types/react` | ^19.2.8 | TypeScript type definitions for React |
| `@types/react-dom` | ^19.2.3 | TypeScript type definitions for React DOM |
| `@types/node` | ^22.19.7 | TypeScript type definitions for Node.js |
| `babel-plugin-react-compiler` | ^1.0.0 | React Compiler — automatic memoization |

#### Development Dependencies

| Package | Version | Purpose |
|---------|---------|---------|
| `eslint` | ^9.39.2 | JavaScript/TypeScript linter |
| `@eslint/js` | ^9.39.2 | ESLint core JS rules |
| `eslint-config-prettier` | ^10.1.8 | Disables ESLint rules that conflict with Prettier |
| `eslint-plugin-react` | ^7.37.5 | React-specific linting rules |
| `eslint-plugin-react-hooks` | ^7.0.1 | Rules of Hooks linting |
| `typescript-eslint` | ^8.53.0 | TypeScript ESLint parser and rules |
| `prettier` | ^3.8.0 | Code formatter |
| `prettier-plugin-organize-imports` | ^4.3.0 | Auto-sorts imports on format |
| `prettier-plugin-tailwindcss` | ^0.6.14 | Sorts Tailwind classes on format |
| `@laravel/vite-plugin-wayfinder` | ^0.1.7 | Vite plugin for Wayfinder TypeScript route generation |

### 7.3 Suggested Packages

Packages that would directly benefit this hospital management system, aligned with the compliance requirements in Section 13 and planned features.

#### PHP — High Value

| Package | Purpose | Addresses |
|---------|---------|-----------|
| `spatie/laravel-activitylog` | Audit trail — logs every model create/update/delete with old/new values, user, IP | Compliance §13.4 Audit Trail (critical gap) |
| `spatie/laravel-permission` | Role & permission management — assign permissions to roles, check via middleware/policies | Compliance §13.4 Access Control; replaces manual profile-based checks |
| `spatie/laravel-backup` | Automated database + file backups to local/S3/SFTP, scheduled via artisan | Compliance §13.4 Backup & Disaster Recovery |
| `spatie/laravel-medialibrary` | File/image management with conversions — replaces custom Image model, adds responsive images | Replaces custom polymorphic Image model with battle-tested solution |
| `laravel/sanctum` | API token authentication — needed for Patient Manager Portal, FHIR APIs, mobile clients | Compliance §13.5 secure token-based auth; Patient Portal (planned) |
| `maatwebsite/laravel-excel` | Excel/CSV import & export — patient lists, transaction reports, bulk data operations | Reports export; bulk patient/transaction import |
| `spatie/laravel-query-builder` | Filter, sort, include API resources via query parameters — standardized API layer | API layer consistency for all 10 API controllers |

#### PHP — Medium Value

| Package | Purpose | Addresses |
|---------|---------|-----------|
| `spatie/laravel-data` | Typed DTOs — clean data transfer between layers, API resource serialization | Cleaner data flow; FHIR resource mapping |
| `spatie/laravel-settings` | Typed settings stored in DB — hospital name, logo, address, contact info | Planned Hospital Settings page (§1 Not Yet Implemented) |
| `propaganistas/laravel-phone` | Phone number validation & formatting (supports Pakistan +92) | Patient contact validation; OTP portal |
| `stancl/tenancy` | Multi-tenancy — run multiple hospitals on one installation | Future: SaaS distribution to multiple hospitals |
| `spatie/laravel-tags` | Taggable models — flexible categorization for services, patients, transactions | Service categorization beyond departments |

#### JavaScript/TypeScript — High Value

| Package | Purpose | Addresses |
|---------|---------|-----------|
| `@tanstack/react-table` | Headless table with sorting, filtering, pagination, grouping | Replace/enhance current Data Table for reports, lists |
| `recharts` | Chart library for React — line, bar, donut, area charts | All 4 planned dashboards (Activity, Operations, Sales, Expenditure) |
| `date-fns` | Lightweight date utility — formatting, parsing, relative time | Date range filters, queue waiting time, report dates |
| `react-to-print` | Print React components directly — thermal receipts, service orders | Cleaner print workflow for receipts and reports |
| `sonner` | Toast notification library for React | User feedback on transactions, payments, errors |

#### JavaScript/TypeScript — Medium Value

| Package | Purpose | Addresses |
|---------|---------|-----------|
| `@tanstack/react-query` | Server state management — caching, background refresh, optimistic updates | API-backed elements (FilterAndSelect), 10s polling widgets |
| `react-hook-form` + `zod` | Form validation with schema — type-safe forms with Zod schemas | Complex forms: transaction create, patient register, counter open |
| `qrcode.react` | QR code generation — patient ID cards, token slips, 2FA setup | Patient token QR, service order QR for queue tracking |
| `react-barcode` | Barcode generation — patient wristbands, lab sample labels | Lab sample tracking, patient identification |

#### Filament Ecosystem — High Value

| Package | Dev? | Purpose | Addresses |
|---------|------|---------|----------|
| `laraveldaily/filacheck` | Yes | Filament project auditor — checks resources, pages, and widgets for best practices | Code quality gate; CI integration |
| `pxlrbt/filament-activity-log` | No | Activity log viewer inside Filament — browse/filter audit trail per model | Compliance §13.4 Audit Trail UI (pairs with `spatie/laravel-activitylog`) |
| `alizharb/filament-activity-log` | No | Alternative activity log viewer with model-level log display | Compliance §13.4 Audit Trail UI |
| `joserojasrodriguez/filament-delete-guard` | No | Prevent accidental deletion of records with confirmation guard | Compliance §13.1 Immutable records; data safety |
| `pxlrbt/filament-spotlight` | No | Spotlight/command palette (⌘K) for fast admin navigation | Admin UX; quick access to resources |
| `directorytree/metrics` | No | Metrics/analytics dashboard builder — time series, breakdowns | Planned dashboards (Activity, Operations, Sales, Expenditure) |

#### Filament Ecosystem — Medium Value

| Package | Dev? | Purpose | Addresses |
|---------|------|---------|----------|
| `eslam-reda-div/filament-copilot` | No | AI assistant overlay inside Filament panels | Admin productivity; AI-assisted record lookup |
| `cocosmos/filament-quick-add-select` | No | Quick-add button in Select fields — create related records inline | Service, department, expense category inline creation |
| `solution-forest/tab-layout-plugin` | No | Tab layout plugin for Filament pages | Closing view 6-tab layout; patient profile tabs |
| `codewithdennis/filament-advanced-choice` | No | Advanced choice/radio field with descriptions and icons | Service provider type selection; profile authority level |
| `backstage/laravel-mails` | No | Mail management — log, preview, and retry sent emails | Monitoring sent password resets, verifications |
| `eslam-reda-div/filament-timezone-detector` | No | Auto-detect user timezone for correct date/time display | Multi-timezone hospital branches |
| `cms-multi/filament-clear-cache` | No | Clear application cache from Filament admin panel | Admin ops; no SSH needed for cache clear |
| `kahusoftware/filament-ckeditor-field` | No | CKEditor rich text field for Filament forms | Service order notes; medical notes with formatting |
| `filafly/filament-identity-column` | No | Auto-format identity columns (PS#, CT#, TR#) with copy button | Identity number display across all resources |
| `octopyid/filament-palette` | No | Command palette for Filament (alternative to Spotlight) | Fast admin navigation |

#### Filament Ecosystem — Low Value

| Package | Dev? | Purpose | Addresses |
|---------|------|---------|----------|
| `sanzgrapher/swippable-notification` | No | Swipeable toast notifications in Filament | Improved notification UX |
| `hammadzafar05/mobile-bottom-nav` | No | Mobile bottom navigation bar for Filament panels | Mobile-friendly admin on tablets at reception |
| `zvizvi/user-fields` | No | Dynamic custom fields on user model | Extensible user profiles beyond hardcoded types |
| `aqjw/shortcuts` | No | Keyboard shortcuts for Filament | Power-user navigation |
| `mckenziearts/laravel-notify` | No | Flash notification system for Laravel | Alternative notification display |

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

---

## 14. Planned Feature Modules

### 14.1 Service Order Treatments (High Priority — PHC Compliance)

**Purpose:** Extend the existing `ServiceOrder` model from a simple queue/billing token into a full clinical treatment record per Punjab Healthcare Commission (PHC) requirements. Each service order tracks what was done to the patient, by whom, and with what outcome.

**Current State:**
- ServiceOrder tracks: type, token, patient, service, doctor, notes_json, payment (payee morph)
- Status: OPEN → CLOSED (binary lifecycle)
- Notes is a free-form JSON field
- No structured treatment recording, vital signs, prescriptions, or outcomes

**Target State — Treatment Record per PHC Guidelines:**

#### Treatment Lifecycle
```
OPEN → IN_PROGRESS → TREATED → REVIEWED → CLOSED
                  ↘ REFERRED (to another department/hospital)
                  ↘ CANCELLED (with reason)
```

#### Data Model Additions

**TreatmentRecord** (new model — one per ServiceOrder)

| Field | Type | Purpose |
|-------|------|---------|
| `service_order_id` | FK | Links to parent ServiceOrder |
| `department_id` | FK | Department where treatment occurred |
| `treating_doctor_id` | FK → users | Doctor who performed treatment |
| `chief_complaint` | text | Patient's presenting complaint |
| `history_of_present_illness` | text | HPI narrative |
| `examination_findings` | json | Structured physical examination |
| `diagnosis_code` | string | ICD-10 code (PHC requires standardized diagnosis) |
| `diagnosis_text` | text | Descriptive diagnosis |
| `treatment_plan` | text | Treatment administered / prescribed |
| `prescriptions` | json | Medications prescribed (name, dosage, frequency, duration) |
| `follow_up_date` | date | Recommended follow-up |
| `outcome` | enum | Improved, Unchanged, Deteriorated, Referred, Expired |
| `referral_to` | string | Department or hospital referred to (if outcome=Referred) |
| `treated_at` | datetime | When treatment was administered |
| `recorded_by` | FK → users | Staff who entered the record |

**VitalSigns** (new model — multiple per TreatmentRecord)

| Field | Type | Purpose |
|-------|------|---------|
| `treatment_record_id` | FK | Parent treatment record |
| `temperature` | decimal | °F or °C |
| `blood_pressure_systolic` | integer | mmHg |
| `blood_pressure_diastolic` | integer | mmHg |
| `pulse_rate` | integer | BPM |
| `respiratory_rate` | integer | Breaths/min |
| `oxygen_saturation` | decimal | SpO2 % |
| `weight` | decimal | kg |
| `height` | decimal | cm |
| `recorded_at` | datetime | When vitals were taken |
| `recorded_by` | FK → users | Staff who recorded |

#### Department-Specific Treatment Fields

Each department has unique treatment requirements per PHC:

| Department | Additional Fields |
|-----------|-------------------|
| **OPD** | Chief complaint, diagnosis, prescription, follow-up date |
| **Emergency** | Triage level (Red/Yellow/Green), time of arrival, mechanism of injury, interventions performed |
| **Indoor/Inpatient** | Admission date, bed number, ward, daily progress notes, discharge summary, discharge date |
| **Dental** | Tooth number/quadrant, procedure type (extraction, filling, RCT, scaling), materials used |
| **Lab** | Sample type, test ordered, test results (structured), normal ranges, abnormal flags |
| **Ultrasound** | Body region, findings text, measurements, impression, images (file attachments) |
| **Radiology** | Body part, view type, findings, impression, images (file attachments) |

#### PHC Compliance Requirements for Treatments

1. **Standardized Documentation** — Every patient encounter must have: chief complaint, examination, diagnosis, treatment plan
2. **ICD-10 Coding** — Diagnosis codes are mandatory for PHC reporting
3. **Prescription Standards** — Generic drug names, dosage, frequency, duration, route of administration
4. **Informed Consent** — Recorded before any invasive procedure (links to §13.1 Consent Management)
5. **Treatment Timestamps** — Arrival time, treatment start, treatment end, discharge time
6. **Doctor Attribution** — Every treatment record must identify the treating doctor
7. **Follow-up Tracking** — System must track patients who need follow-up and flag missed appointments
8. **Referral Chain** — When a patient is referred, the receiving department must acknowledge and continue the record
9. **Immutable Records** — Treatment records are append-only; corrections create amendment records (links to §13.1)
10. **Audit Trail** — All access and modifications to treatment records are logged (links to §15.1)

#### Implementation Phases

**Phase 1 — Core Treatment Record:**
- Add `TreatmentRecord` model and migration
- Extend ServiceOrder status enum with full lifecycle
- Treatment form per department type in Filament
- Vital signs recording

**Phase 2 — Prescriptions & Lab Integration:**
- Structured prescription entries with drug database
- Lab result entry with normal range comparison
- Imaging attachment support for Radiology/Ultrasound

**Phase 3 — PHC Reporting:**
- ICD-10 code lookup and validation
- Monthly/quarterly PHC compliance reports
- Treatment outcome statistics per department

---

### 14.2 Stock Tracking

**Purpose:** Track hospital consumables, medicines, and supplies from procurement to consumption. Ensures the hospital never runs out of critical supplies and maintains accurate cost accounting.

#### Data Model

**StockCategory** — Classification of stock items

| Field | Type | Purpose |
|-------|------|---------|
| `name` | string | Category name (Medicines, Surgical Supplies, Stationery, Cleaning) |
| `parent_id` | FK (self) | Hierarchical categories |
| `is_medicine` | boolean | Requires pharmacy-level tracking |

**StockItem** — Individual trackable item

| Field | Type | Purpose |
|-------|------|---------|
| `name` | string | Item name |
| `sku` | string (unique) | Stock keeping unit code |
| `category_id` | FK → stock_categories | Classification |
| `unit` | string | Unit of measure (pcs, ml, mg, box, strip) |
| `reorder_level` | integer | Minimum quantity before alert |
| `default_vendor` | string | Primary supplier |
| `is_active` | boolean | Currently stocked |

**StockMovement** — Every in/out movement

| Field | Type | Purpose |
|-------|------|---------|
| `stock_item_id` | FK | Which item |
| `type` | enum | IN (purchase/return), OUT (consumption/disposal/transfer) |
| `quantity` | decimal | How many units |
| `unit_cost` | decimal | Cost per unit at time of movement |
| `reference_type` | morph | What triggered it (PurchaseOrder, ServiceOrder, ExpenseVoucher, etc.) |
| `reference_id` | morph | ID of triggering record |
| `department_id` | FK | Department consuming/receiving |
| `batch_number` | string | For medicines: batch tracking |
| `expiry_date` | date | For medicines: expiry tracking |
| `moved_by` | FK → users | Who performed the movement |
| `notes` | text | Additional context |

**PurchaseOrder** — Procurement records

| Field | Type | Purpose |
|-------|------|---------|
| `po_number` | string (unique) | `PO/{year}/{month}/{sequence}` |
| `vendor_name` | string | Supplier |
| `status` | enum | DRAFT, APPROVED, RECEIVED, CANCELLED |
| `total_amount` | decimal | Total cost |
| `approved_by` | FK → users | Approval authority |
| `received_at` | datetime | When goods were received |

#### Key Features
- **Real-time stock levels** — Current quantity = SUM(IN movements) - SUM(OUT movements)
- **Low stock alerts** — Dashboard widget when items fall below `reorder_level`
- **Medicine expiry tracking** — Alert for items expiring within 30/60/90 days
- **Department consumption reports** — Which department uses what and how much
- **Auto-deduction on service orders** — When a service is rendered, linked stock items are automatically consumed
- **Purchase order workflow** — Draft → Approve → Receive → Stock IN

---

### 14.3 Asset Tracking

**Purpose:** Track hospital fixed assets (equipment, furniture, vehicles) through their lifecycle — procurement, assignment, maintenance, depreciation, and disposal.

#### Data Model

**AssetCategory** — Classification

| Field | Type | Purpose |
|-------|------|---------|
| `name` | string | Category (Medical Equipment, Furniture, IT, Vehicles) |
| `depreciation_method` | enum | STRAIGHT_LINE, DECLINING_BALANCE, NONE |
| `useful_life_years` | integer | Default useful life for depreciation |

**Asset** — Individual tracked asset

| Field | Type | Purpose |
|-------|------|---------|
| `asset_number` | string (unique) | `AST/{year}/{sequence}` |
| `name` | string | Asset description |
| `category_id` | FK | Classification |
| `serial_number` | string | Manufacturer serial |
| `purchase_date` | date | When acquired |
| `purchase_cost` | decimal | Original cost |
| `vendor_name` | string | Supplier |
| `warranty_expiry` | date | Warranty end date |
| `assigned_to_department` | FK | Current department |
| `assigned_to_user` | FK → users | Current custodian |
| `location` | string | Physical location description |
| `status` | enum | ACTIVE, UNDER_MAINTENANCE, RETIRED, DISPOSED |
| `disposed_at` | date | Disposal date |
| `disposal_reason` | text | Why disposed |
| `disposal_value` | decimal | Salvage/sale value |

**AssetMaintenanceLog** — Maintenance records

| Field | Type | Purpose |
|-------|------|---------|
| `asset_id` | FK | Which asset |
| `type` | enum | PREVENTIVE, CORRECTIVE, CALIBRATION |
| `description` | text | What was done |
| `cost` | decimal | Maintenance cost |
| `performed_by` | string | Technician/vendor |
| `scheduled_date` | date | When planned |
| `completed_date` | date | When completed |
| `next_maintenance_date` | date | Next scheduled date |

#### Key Features
- **QR code labels** — Generate and print QR codes for physical asset tagging
- **Depreciation calculation** — Automated monthly depreciation per accounting standards
- **Maintenance scheduling** — Calendar-based preventive maintenance with overdue alerts
- **Assignment chain** — Full history of which department/user held each asset
- **Warranty expiry alerts** — Dashboard notification for expiring warranties
- **Integration with Accounts panel** — Depreciation entries flow into Abacus double-entry ledger

---

### 14.4 User Tasking

**Purpose:** Internal task management system allowing administrators and department heads to assign, track, and follow up on tasks across staff. Provides accountability and structured workflow beyond informal communication.

#### Data Model

**Task** — Individual task assignment

| Field | Type | Purpose |
|-------|------|---------|
| `task_number` | string (unique) | `TSK/{year}/{month}/{sequence}` |
| `title` | string | Brief task description |
| `description` | text | Detailed instructions |
| `priority` | enum | LOW, MEDIUM, HIGH, URGENT |
| `status` | enum | TODO, IN_PROGRESS, BLOCKED, COMPLETED, CANCELLED |
| `assigned_to` | FK → users | Responsible staff member |
| `assigned_by` | FK → users | Manager who created the task |
| `department_id` | FK | Related department |
| `due_date` | datetime | Deadline |
| `completed_at` | datetime | When marked complete |
| `completion_notes` | text | Notes on completion |

**TaskComment** — Discussion thread per task

| Field | Type | Purpose |
|-------|------|---------|
| `task_id` | FK | Parent task |
| `user_id` | FK → users | Commenter |
| `body` | text | Comment content |

**TaskAttachment** — File attachments per task

| Field | Type | Purpose |
|-------|------|---------|
| `task_id` | FK | Parent task |
| `file_path` | string | Storage path |
| `file_name` | string | Original file name |
| `uploaded_by` | FK → users | Uploader |

#### Key Features
- **Kanban board** — Visual task board in admin panel (TODO → In Progress → Done)
- **My Tasks view** — Each user sees their assigned tasks with due dates and priorities
- **Overdue alerts** — Dashboard widget showing overdue tasks
- **Department task reports** — Task completion rates per department
- **Task templates** — Recurring tasks (e.g., monthly equipment check, daily cleaning audits)
- **Notification system** — Email/in-app notifications on assignment, due date, completion

---

### 14.5 User Payroll

**Purpose:** Manage staff salary calculation, deductions, advances, and payment processing. Tracks attendance-based computation, generates payslips, and integrates with the Accounts panel for double-entry ledger entries.

#### Data Model

**PayrollPeriod** — Monthly pay cycle

| Field | Type | Purpose |
|-------|------|---------|
| `period_number` | string (unique) | `PAY/{year}/{month}` |
| `year` | integer | Fiscal year |
| `month` | integer | Pay month |
| `status` | enum | DRAFT, CALCULATED, APPROVED, PAID, CLOSED |
| `processed_by` | FK → users | Accountant who processed |
| `approved_by` | FK → users | Admin who approved |

**SalaryStructure** — Pay configuration per user

| Field | Type | Purpose |
|-------|------|---------|
| `user_id` | FK → users | Staff member |
| `basic_salary` | decimal | Base monthly salary |
| `housing_allowance` | decimal | Housing component |
| `medical_allowance` | decimal | Medical component |
| `transport_allowance` | decimal | Transport component |
| `other_allowances` | json | Additional components |
| `effective_from` | date | When this structure takes effect |
| `effective_to` | date | When superseded (null = current) |

**PayslipEntry** — Individual payslip line

| Field | Type | Purpose |
|-------|------|---------|
| `payroll_period_id` | FK | Which pay cycle |
| `user_id` | FK → users | Staff member |
| `salary_structure_id` | FK | Applied salary structure |
| `gross_salary` | decimal | Total earnings |
| `deductions` | json | Breakdown: tax, advances, absences, penalties |
| `net_salary` | decimal | Take-home pay |
| `payment_method` | enum | CASH, BANK_TRANSFER, CHEQUE |
| `paid_at` | datetime | When paid |
| `paid_via_voucher_id` | FK → expense_vouchers | Links to expense voucher for payment |

**SalaryAdvance** — Advance/loan tracking

| Field | Type | Purpose |
|-------|------|---------|
| `user_id` | FK → users | Staff member |
| `amount` | decimal | Advance amount |
| `granted_by` | FK → users | Approver |
| `deduction_per_month` | decimal | Monthly recovery amount |
| `remaining_balance` | decimal | Outstanding |
| `status` | enum | ACTIVE, FULLY_RECOVERED, WRITTEN_OFF |

#### Key Features
- **Salary structure management** — Per-user configurable salary components
- **Monthly payroll processing** — Bulk calculate → review → approve → pay workflow
- **Advance management** — Advance request, approval, and automatic monthly deduction
- **Payslip generation** — PDF payslips per user with all components
- **Expense voucher integration** — Each salary payment creates an expense voucher for accounting
- **Accounts integration** — Payroll entries flow into Abacus ledger (salary expense, cash/bank credit)
- **Year-end summary** — Annual salary certificates per employee for tax purposes