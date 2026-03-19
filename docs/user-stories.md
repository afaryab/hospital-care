# User Stories — Hospital All In One Operations Software

> Stories cover both implemented and planned features.
> Format: **As a [role], I want [action], so that [benefit].**
> Priority: **High** / **Medium** / **Low**
> Status: ✅ Implemented | 🔲 Planned

---

## Epic 1 — Authentication & Account Security

### US-1.1 · Login ✅ **High**
**As a** staff member, **I want** to log in with my username/email and password, **so that** I can access the system securely.
- **Acceptance Criteria:**
  - Valid credentials redirect to the dashboard.
  - Invalid credentials show an error message; account is not revealed.
  - Session persists until explicit logout or timeout.

### US-1.2 · Registration ✅ **High**
**As an** administrator, **I want** to register new staff accounts, **so that** authorized personnel can access the system.
- **Acceptance Criteria:**
  - Registration form requires name, username, email, mobile, and password.
  - Duplicate username/email is rejected.
  - New accounts default to inactive until an admin activates them.

### US-1.3 · Password Reset ✅ **High**
**As a** staff member, **I want** to reset my password via email, **so that** I can regain access if I forget my credentials.
- **Acceptance Criteria:**
  - Reset link is sent to the registered email.
  - Link expires after a configured duration.
  - Password must meet complexity requirements.

### US-1.4 · Two-Factor Authentication ✅ **Medium**
**As a** staff member, **I want** to enable two-factor authentication on my account, **so that** my account is protected against unauthorized access.
- **Acceptance Criteria:**
  - User can enable/disable 2FA from settings.
  - QR code is displayed for authenticator app setup.
  - Recovery codes are generated and downloadable.
  - Login requires a valid OTP when 2FA is enabled.

### US-1.5 · Email Verification ✅ **Medium**
**As a** staff member, **I want** to verify my email address, **so that** the system can send me notifications and reset links.
- **Acceptance Criteria:**
  - Verification email is sent on registration.
  - User can request a new verification email.
  - Unverified users see a reminder banner.

### US-1.6 · Profile Management ✅ **Medium**
**As a** staff member, **I want** to update my profile information, name, and password, **so that** my account details stay current.
- **Acceptance Criteria:**
  - User can update name, email, and mobile from settings.
  - Password change requires current password confirmation.

### US-1.7 · Appearance Settings ✅ **Low**
**As a** staff member, **I want** to switch between light and dark mode, **so that** I can use the application comfortably.
- **Acceptance Criteria:**
  - Preference persists across sessions.

---

## Epic 2 — User & Role Management (Admin)

### US-2.1 · Create User with Profiles ✅ **High**
**As an** administrator, **I want** to create user accounts and assign one or more role profiles (Administrator, Accountant, Receptionist, Doctor types, Patient Manager), **so that** each staff member has the correct access level.
- **Acceptance Criteria:**
  - Admin can add multiple profiles per user via collapsible repeaters.
  - Each profile has its own authority level (e.g., Assistant, Senior, Consultant).
  - User is active/inactive toggleable.

### US-2.2 · Edit User Profiles ✅ **High**
**As an** administrator, **I want** to edit existing user profiles and authority levels, **so that** I can adjust responsibilities as staff roles change.
- **Acceptance Criteria:**
  - Profiles can be added or removed from an existing user.
  - Authority levels can be changed.

### US-2.3 · List & Filter Users ✅ **Medium**
**As an** administrator, **I want** to list, search, and filter users by name, role, or status, **so that** I can quickly find and manage staff.
- **Acceptance Criteria:**
  - Searchable and paginated user list.
  - Filter by active/inactive status.

### US-2.4 · User Limit Tracking ✅ **Medium**
**As an** administrator, **I want** to see how many user accounts exist versus the allowed limit, **so that** I know when the hospital needs to upgrade.
- **Acceptance Criteria:**
  - Dashboard stat card shows current user count and limit.

### US-2.5 · X-Ray Technician Profile 🔲 **Medium**
**As an** administrator, **I want** to assign an X-Ray Technician profile to a user, **so that** radiology services can be tracked to specific technicians.

### US-2.6 · Nursing Staff Profile 🔲 **Low**
**As an** administrator, **I want** to assign a Nursing Staff profile to a user, **so that** nursing duties can be attributed and tracked.

---

## Epic 3 — Patient Registration

### US-3.1 · Register New Patient ✅ **High**
**As a** receptionist, **I want** to register a new patient with their personal details, **so that** they receive a unique PS number and can be processed.
- **Acceptance Criteria:**
  - Form captures name, gender, age, address, guardian, contact, CNIC.
  - PS number is auto-assigned in format `PS/{year}/{month}/{sequence}`.
  - Patient is searchable immediately after creation.

### US-3.2 · Search & Filter Patients ✅ **High**
**As a** receptionist, **I want** to search patients by PS number, name, or CNIC and filter by year/month, **so that** I can quickly locate returning patients.
- **Acceptance Criteria:**
  - Real-time search with API-backed results.
  - Year/month/number filter controls on the patient register.

### US-3.3 · Patient View with All Relations 🔲 **Medium**
**As an** administrator, **I want** to view a patient's full profile with tabs for service orders, transactions, receivables, and history, **so that** I have a complete picture of the patient's interactions.
- **Acceptance Criteria:**
  - Filament resource with tabbed view page.
  - Each tab shows the relevant related records.

---

## Epic 4 — Counter (Closing) Operations

### US-4.1 · Open Counter ✅ **High**
**As a** receptionist, **I want** to open a counter by selecting a reception and entering an opening amount, **so that** I can begin processing transactions for the day.
- **Acceptance Criteria:**
  - Form requires reception selection and opening cash amount.
  - Counter is assigned a CT number in format `CT/{year}/{month}/{sequence}`.
  - Counter status is set to `OPEN`.
  - Only one counter can be open per receptionist at a time.

### US-4.2 · View Open Counter ✅ **High**
**As a** receptionist, **I want** to view my open counter with its transactions, totals, and available actions, **so that** I can monitor daily activity.
- **Acceptance Criteria:**
  - Displays counter details: CT number, reception, opening amount.
  - Lists associated transactions.
  - Provides action buttons: close counter, add income, add expense.

### US-4.3 · Close Counter ✅ **High**
**As a** receptionist, **I want** to close my counter by entering the closing amount, **so that** the day's financial activity is finalized and auditable.
- **Acceptance Criteria:**
  - Closing form shows counter statement number.
  - Cash, card, and cheque amounts are recorded.
  - Counter status changes to `CLOSED`.
  - Discrepancy between expected and actual closing amounts is calculated.

### US-4.4 · Counter List with Filtering ✅ **Medium**
**As a** receptionist, **I want** to view a list of all my past counter closings filtered by year and month, **so that** I can review historical activity.
- **Acceptance Criteria:**
  - Paginated list with year/month filter.
  - Each entry links to the closing detail view.

### US-4.5 · Admin Closing Management ✅ **High**
**As an** administrator, **I want** to list, view, and edit closings with full CRUD access, **so that** I can audit and correct financial records.
- **Acceptance Criteria:**
  - Filament resource with List, Create, View, Edit pages.
  - View page has 6 tabs: Summary, Detailed Summary, Services Report, Income Report, Expense Report, Receivables Report.
  - Each tab includes print/PDF URLs.

### US-4.6 · Merge Receptions 🔲 **Medium**
**As an** administrator, **I want** to merge two receptions into one, **so that** duplicate or reorganized reception points are consolidated without losing data.
- **Acceptance Criteria:**
  - Bulk action on Receptions manage page.
  - All closings, transactions, and related records are migrated to the target reception.

### US-4.7 · Receptionist Dashboard Landing ✅ **High**
**As a** receptionist, **I want** the dashboard to show whether I have an open counter and prompt me to open one or resume the active counter, **so that** I can quickly start or continue my shift.
- **Acceptance Criteria:**
  - If a counter is open, dashboard links directly to the counter view.
  - If no counter is open, a prominent "Open Counter" call-to-action is shown.

### US-4.8 · Patient Lookup from Active Counter ✅ **Medium**
**As a** receptionist, **I want** to look up a patient's history (prior transactions, receivables, service orders) from within my active counter, **so that** I have context before creating a new transaction.
- **Acceptance Criteria:**
  - `/CT-PS` route accessible from the counter view.
  - Shows patient mini card, transaction history tree, and outstanding receivables.

---

## Epic 5 — Income Transactions

### US-5.1 · Create Income Transaction ✅ **High**
**As a** receptionist, **I want** to create an income transaction by selecting a patient, department, services, and service providers, **so that** the hospital records revenue for services rendered.
- **Acceptance Criteria:**
  - Patient is selected via FilterAndSelect element.
  - Department narrows available services.
  - Multiple services and providers can be added.
  - Transaction is assigned a TR number in format `TR/{year}/{month}/{sequence}`.
  - Transaction elements are created per service/provider combination.

### US-5.2 · Record Unpaid Transactions as Receivables ✅ **High**
**As a** receptionist, **I want** unpaid income transactions to automatically create a receivable record, **so that** outstanding payments are tracked.
- **Acceptance Criteria:**
  - If the transaction is not fully paid, a Receivable is created with the remaining amount.
  - Receivable is linked to the patient, transaction, and panel (if applicable).

### US-5.3 · Panel-Based Transactions ✅ **High**
**As a** receptionist, **I want** to associate a transaction with an insurance/corporate panel, **so that** the billing is routed to the correct payer.
- **Acceptance Criteria:**
  - Panel can be selected during transaction creation.
  - Transaction and receivable are linked to the panel.

### US-5.4 · Refund Transaction 🔲 **Medium**
**As an** administrator, **I want** to mark a transaction as refunded, **so that** the financial records reflect the reversal.
- **Acceptance Criteria:**
  - Refund flag is set on the transaction.
  - Related receivable (if any) is updated or cancelled.

### US-5.5 · Transaction Filament Resource 🔲 **Medium**
**As an** administrator, **I want** to list, view, and manage all transactions from the admin panel, **so that** I have centralized access to financial records.

### US-5.6 · Select Payment Method ✅ **High**
**As a** receptionist, **I want** to select a payment method (cash, card, or cheque) when recording a transaction, constrained by the reception's allowed payment flags, **so that** the payment is recorded correctly and policy is enforced.
- **Acceptance Criteria:**
  - Only payment methods enabled on the reception (`is_cash_allowed`, `is_cheques_allowed`, `is_card_allowed`) are selectable.
  - If only one method is allowed, it is preselected.

### US-5.7 · Split Payment Across Methods ✅ **Medium**
**As a** receptionist, **I want** to split a transaction's payment across multiple methods (e.g., part cash, part card), **so that** the actual payment breakdown is accurately recorded.
- **Acceptance Criteria:**
  - Multiple payment method fields are available on the transaction form.
  - Sum of amounts across methods must equal the total transaction amount.
  - Closing tracks separate totals per payment method (`closing_amount_*` fields).

### US-5.8 · View Patient History During Transaction Creation ✅ **Medium**
**As a** receptionist, **I want** to see a patient's prior transactions and outstanding receivables inline when selecting them for a new income transaction, **so that** I can make informed decisions (e.g., collect a pending amount).
- **Acceptance Criteria:**
  - Patient selection shows the PatientHistoryMiniTree and TransactionsHistoryCard elements.
  - Outstanding receivables are highlighted.

---

## Epic 6 — Expense Management

### US-6.1 · Create Expense Voucher ✅ **High**
**As a** receptionist, **I want** to create a new expense voucher by selecting an expense category, amount, and payee, **so that** hospital expenditures are documented.
- **Acceptance Criteria:**
  - Voucher is assigned a VC number automatically.
  - Status defaults to `PENDING`.
  - Voucher can optionally link to a service order.

### US-6.2 · Record Expense Transaction ✅ **High**
**As a** receptionist, **I want** to register an expense transaction against a voucher, service order, or expense category, **so that** cash outflows are recorded in the counter.
- **Acceptance Criteria:**
  - Expense is linked to the open counter's closing.
  - Transaction is of type `EXPENSE` with a TR number.
  - Linked voucher status changes to `PAYED` when paid.

### US-6.3 · List Expense Vouchers ✅ **Medium**
**As a** receptionist, **I want** to list expense vouchers filtered by year/month, **so that** I can track pending and paid expenses.
- **Acceptance Criteria:**
  - Paginated list with year/month filter.
  - Status (PENDING / PAYED) is visible.

### US-6.4 · Admin Expense Voucher CRUD ✅ **Medium**
**As an** administrator, **I want** full CRUD access to expense vouchers from the admin panel, **so that** I can audit and correct expense records.
- **Acceptance Criteria:**
  - Filament resource with List, Create, View, Edit pages.
  - Record title shows VC number.

### US-6.5 · Expense Against a Specific Transaction ✅ **Medium**
**As a** receptionist, **I want** to link an expense to a specific prior transaction (in addition to voucher, service order, or expense category), **so that** the expense is traceable to its originating revenue event.
- **Acceptance Criteria:**
  - Expense form allows selecting a transaction via FilterAndSelectTransaction.
  - The expense transaction element references the source transaction.

### US-6.6 · Voucher Payment Restriction ✅ **High**
**As a** receptionist, **I want** to be blocked from paying expense vouchers when my reception does not permit voucher payments, **so that** payment policy is enforced at each reception point.
- **Acceptance Criteria:**
  - If `is_allowed_to_pay_voucher` is false on the reception, voucher payment actions are hidden or disabled.
  - Attempting to pay a voucher from a restricted reception returns a validation error.

---

## Epic 7 — Receivables

### US-7.1 · List Patient Receivables ✅ **High**
**As a** receptionist, **I want** to view a list of outstanding patient receivables, **so that** I can follow up on unpaid amounts.
- **Acceptance Criteria:**
  - List shows patient name, TR number, original amount, remaining amount, due date, and status.
  - Filterable and paginated.

### US-7.2 · Collect Receivable Payment ✅ **High**
**As a** receptionist, **I want** to collect a payment against a receivable via a dialog, **so that** outstanding balances are reduced or cleared.
- **Acceptance Criteria:**
  - Payment dialog allows entering payment amount.
  - Partial payments reduce the remaining amount.
  - Full payment marks the receivable as closed.
  - Payment is recorded as a transaction on the open counter.

### US-7.3 · Admin Collect Payment from Closing View ✅ **Medium**
**As an** administrator, **I want** to view receivables within a closing's Receivables Report tab and collect payments, **so that** I can manage outstanding debts at closing level.

---

## Epic 8 — Service Orders & Queues

### US-8.1 · Create Service Order ✅ **High**
**As a** receptionist, **I want** a service order to be created when an income transaction includes a service, **so that** the service delivery can be tracked.
- **Acceptance Criteria:**
  - Service order is auto-created per service in a transaction.
  - SO number: `{PS_NUMBER}/{department}/{sequence}`.
  - Token number assigned for queue management.
  - Status defaults to `OPEN`.

### US-8.2 · View Service Orders ✅ **Medium**
**As an** administrator, **I want** to list and view service orders from the admin panel, **so that** I can monitor service delivery.
- **Acceptance Criteria:**
  - Filament resource with List and View pages.
  - Shows patient, service, doctor, status, and token.

### US-8.3 · OPD Queue ✅ **High**
**As a** doctor, **I want** to view the OPD queue grouped by service type, **so that** I know which patients are waiting.
- **Acceptance Criteria:**
  - Displays active OPD service orders.
  - Grouped by service type.
  - Shows patient name, token number, and waiting time.

### US-8.4 · Department-Based Queues ✅ **High**
**As a** doctor, **I want** to view queues filtered by department (OPD, Indoor, Emergency, Dental, Lab, Ultrasound, Radiology), **so that** I see only relevant patients.
- **Acceptance Criteria:**
  - Route per department: `/que/{department}`.
  - Only `OPEN` service orders are shown.

### US-8.5 · Auto-Close Stale Service Orders ✅ **Medium**
**As the** system, **I want** to automatically close service orders that have been open beyond a threshold, **so that** the queues stay clean.
- **Acceptance Criteria:**
  - Console command `CloseOldServiceOrders` runs on schedule.
  - Status changes from `OPEN` to `CLOSED`.

### US-8.6 · Composite Service Orders ✅ **Medium**
**As a** receptionist, **I want** to create a composite service order when a service contains sub-services, **so that** bundled services are tracked as a single unit.
- **Acceptance Criteria:**
  - `is_composit` flag is set on the service order.
  - Related sub-service orders are linked if applicable.

### US-8.7 · Token Number Display & Print ✅ **High**
**As a** receptionist, **I want** the patient's token number to be displayed on screen and printable after a service order is created, **so that** the patient knows their queue position and can present the token at the department.
- **Acceptance Criteria:**
  - Token number is shown in the transaction confirmation view.
  - Token number is included on the printed service order document.
  - Token format: `{year}{month}{sequence with leading zero}`.

---

## Epic 9 — Services & Configuration (Admin)

### US-9.1 · Manage Services ✅ **High**
**As an** administrator, **I want** to create and edit services with charges, tax rates, departments, and provider types, **so that** the hospital's service catalog is up to date.
- **Acceptance Criteria:**
  - Single ManageServices page.
  - Supports composite services.
  - Provider types: OPD, Emergency, Inpatient Doctors, Dentists, X-Ray Technicians, Ultrasound.

### US-9.2 · Manage Departments ✅ **High**
**As an** administrator, **I want** to create and edit service departments, **so that** services are properly categorized.
- **Acceptance Criteria:**
  - Department has name, slug, image, and `have_composit_services` flag.
  - Single manage page with inline editing.

### US-9.3 · Manage Receptions ✅ **High**
**As an** administrator, **I want** to create and edit receptions with allowed departments and payment method flags, **so that** each reception point is configured correctly.
- **Acceptance Criteria:**
  - Configurable: allowed departments, cash/cheques/card toggles, voucher payment permission.
  - Merge receptions bulk action.

### US-9.4 · Manage Expense Categories ✅ **Medium**
**As an** administrator, **I want** to create and edit expense categories with payment rules (pay to doctors, others, users), **so that** expenses are properly classified.

### US-9.5 · Manage Panels 🔲 **Medium**
**As an** administrator, **I want** to create, edit, and deactivate insurance/corporate panels, **so that** panel-based billing is configurable.
- **Acceptance Criteria:**
  - Filament resource with active/inactive toggle.
  - Pending panel payments visible.

### US-9.6 · Hospital Settings 🔲 **Medium**
**As an** administrator, **I want** to configure hospital-wide settings (name, logo, address, contact info), **so that** printed documents and headers reflect the correct organization details.

---

## Epic 10 — Reports & Analytics (Admin)

### US-10.1 · Income Report ✅ **High**
**As an** administrator, **I want** to generate an income report filtered by date range, reception, transaction type, service, and provider, **so that** I can audit revenue.
- **Acceptance Criteria:**
  - Filament page with filters.
  - Grouped by status and panel.
  - PDF export with green accent.

### US-10.2 · Expense Report ✅ **High**
**As an** administrator, **I want** to generate an expense report filtered by date range, reception, type, and expense category, **so that** I can audit expenditures.
- **Acceptance Criteria:**
  - Filament page with filters.
  - PDF export with red accent.

### US-10.3 · Receivables Report ✅ **High**
**As an** administrator, **I want** to generate a receivables report showing all outstanding amounts grouped by status and panel, **so that** I can track debts.
- **Acceptance Criteria:**
  - Columns: date, TR#, patient, panel, original/remaining amount, due date, status.
  - PDF export with purple accent.

### US-10.4 · Services Report ✅ **Medium**
**As an** administrator, **I want** to generate a services report showing transaction elements by service and provider, **so that** I can analyze service utilization.
- **Acceptance Criteria:**
  - Filters: date range, reception, flow, service, provider.
  - PDF export with indigo accent.

### US-10.5 · Dashboard Stat Widgets ✅ **High**
**As an** administrator, **I want** to see a dashboard with 6 stat cards (users, services, patients, counters, vouchers, transactions) with charts and date range filtering, **so that** I have an at-a-glance operational overview.
- **Acceptance Criteria:**
  - 6-column responsive grid.
  - Date range presets: Today, Last 3/7 Days, This/Last Week/Month/Year, Last Financial Year, Custom.
  - Auto-refresh (10s polling).

### US-10.6 · Closing Statement Prints ✅ **Medium**
**As an** administrator, **I want** to print a closing statement in mini (thermal) or full (A4) format, **so that** paper records are available.
- **Acceptance Criteria:**
  - Mini print: thermal receipt format.
  - Normal print: full A4 closing statement.
  - Per-tab report prints available.

### US-10.7 · Activity Dashboard 🔲 **Low**
**As an** administrator, **I want** an activity dashboard with line charts showing record creation over time, **so that** I can monitor system usage patterns.

### US-10.8 · Operations Dashboard 🔲 **Low**
**As an** administrator, **I want** an operations dashboard showing service order stats with line/donut charts by department and service, **so that** I can measure operational throughput.

### US-10.9 · Sales Dashboard 🔲 **Low**
**As an** administrator, **I want** a sales dashboard with grouped bar charts for incoming transactions, **so that** I can analyze revenue trends.

### US-10.10 · Expenditure Dashboard 🔲 **Low**
**As an** administrator, **I want** an expenditure dashboard showing paid/pending vouchers with a payment graph, **so that** I can monitor cash outflows.

### US-10.11 · Excel/CSV Export for Reports 🔲 **Medium**
**As an** administrator, **I want** to export any report (income, expense, receivables, services) to Excel or CSV in addition to PDF, **so that** I can analyze data in spreadsheets and share with external auditors.
- **Acceptance Criteria:**
  - Export button on each report page alongside the PDF button.
  - Exported file includes all filtered data with column headers.
  - File name includes report type and date range.

---

## Epic 11 — Transaction & Service Order Prints

### US-11.1 · Print Transaction Receipt ✅ **Medium**
**As a** receptionist, **I want** to print a transaction receipt in full (A4), thermal, or dot-matrix format, **so that** the patient receives proof of payment.
- **Acceptance Criteria:**
  - Three print templates available.
  - Receipt includes TR number, patient, services, amounts, and payment method.

### US-11.2 · Print Service Order ✅ **Medium**
**As a** receptionist, **I want** to print a service order document, **so that** the patient can present it at the relevant department.
- **Acceptance Criteria:**
  - Document includes SO number, token, patient, service, and assigned doctor.

### US-11.3 · Income Cash Flow Report ✅ **Medium**
**As an** accountant, **I want** to generate an income cash flow report, **so that** I can reconcile cash movements.

### US-11.4 · Print Closing Statement from Counter List ✅ **Medium**
**As a** receptionist, **I want** to print my own closing statement in mini (thermal) or full (A4) format from my counter list, **so that** I have a paper record of my shift's financials without needing admin access.
- **Acceptance Criteria:**
  - Print action available on each entry in the receptionist's counter list (`/MY-CT-LIST`).
  - Mini (thermal) and normal (A4) templates are both available.
  - Only the receptionist's own closings are printable from this view.

---

## Epic 12 — Accounts Panel

### US-12.1 · Accounts Dashboard ✅ **Medium**
**As an** accountant, **I want** a dedicated accounts panel with date-range-filtered dashboards, **so that** I can focus on financial data.
- **Acceptance Criteria:**
  - Accessible at `/accounts`.
  - Same date range filter structure as admin dashboard.

### US-12.2 · Abacus Accounting Integration ✅ **Medium**
**As an** accountant, **I want** the accounts panel to integrate with the Abacus accounting package, **so that** double-entry bookkeeping is supported.
- **Acceptance Criteria:**
  - Processton\Abacus\AbacusPlugin is registered.
  - Resources and widgets populate as the package matures.

---

## Epic 13 — Patient Manager Portal (Planned)

### US-13.1 · Patient Self-Registration 🔲 **Medium**
**As a** patient, **I want** to register on the portal with my mobile number and verify via OTP, **so that** I can access my medical records online.
- **Acceptance Criteria:**
  - Registration form with mobile number.
  - OTP sent via SMS.
  - Account created upon successful verification.

### US-13.2 · Link Patient Records 🔲 **Medium**
**As a** patient, **I want** to link my portal account to my hospital patient record (PS number), **so that** I can view my history.
- **Acceptance Criteria:**
  - Patient enters PS number or CNIC to find their record.
  - Verification step ensures the correct patient is linked.

### US-13.3 · View My Medical History 🔲 **Medium**
**As a** patient, **I want** to view my service orders, transactions, and receivables from the portal, **so that** I can track my hospital interactions.
- **Acceptance Criteria:**
  - Patient sees a read-only history of all linked records.

---

## Epic 14 — System & Infrastructure

### US-14.1 · Dockerized Deployment ✅ **High**
**As a** system administrator, **I want** the application distributed as Docker images (app + cli), **so that** hospitals can deploy it easily on any server.
- **Acceptance Criteria:**
  - `docker-compose.yml` orchestrates app and CLI containers.
  - CLI container runs scheduled tasks and provides SSH access.
  - Versioned tags published per release.

### US-14.2 · Auto-Numbering Sequences ✅ **High**
**As the** system, **I want** PS, CT, TR, VC, and SO numbers to be auto-assigned via observers with `{year}/{month}/{sequence}` format, **so that** records are uniquely and chronologically identifiable.
- **Acceptance Criteria:**
  - Sequence resets by year/month context.
  - Leading zeros in sequence numbers.
  - Observer-based assignment on model creation.

### US-14.3 · Monitoring & Debugging ✅ **Medium**
**As a** system administrator, **I want** Laravel Pulse, Telescope, and Sentry integrated, **so that** I can monitor performance, debug issues, and track errors.

### US-14.4 · Captive Portal Integration ✅ **Low**
**As a** system administrator, **I want** WiFi captive portal integration, **so that** hospital guests can be authorized for internet access.
- **Acceptance Criteria:**
  - CaptivePortalService provides enable check, endpoint, duration, and client authorization.

### US-14.5 · Regulatory Compliance 🔲 **High**
**As a** hospital administrator, **I want** the system to be compliant with Punjab Healthcare Commission guidelines and HIPAA-inspired practices, **so that** the hospital meets regulatory standards.
- **Acceptance Criteria:**
  - Data handling follows PHC guidelines.
  - FHIR-ready API structure for eventual interoperability.
  - Audit trails for financial and patient records.
  - See Epic 15 for detailed compliance stories.

---

## Epic 15 — Compliance & Security

### US-15.1 · Audit Trail 🔲 **High**
**As a** hospital administrator, **I want** every create, update, and delete action on core models to be logged with user, timestamp, old/new values, and IP address, **so that** the hospital has a tamper-proof audit trail for regulatory inspections.
- **Acceptance Criteria:**
  - Activity log records: `user_id`, `action`, `auditable_type`, `auditable_id`, `old_values`, `new_values`, `timestamp`, `ip_address`, `user_agent`.
  - All core models covered: Patient, Transaction, TransactionElement, Closing, ServiceOrder, ExpenseVoucher, Receaveable.
  - Logs are immutable — append-only, no edits or deletes.
  - Viewable from admin panel with filters by user, model, and date.

### US-15.2 · Data Encryption at Rest 🔲 **High**
**As a** hospital administrator, **I want** sensitive patient fields (CNIC, contact, medical notes) to be encrypted in the database, **so that** a database breach does not expose personal health information.
- **Acceptance Criteria:**
  - Laravel encrypted casts on: `Patient.cnic`, `Patient.contact`, `Patient.address`, `ServiceOrder.notes_json`.
  - Encrypted fields are searchable via blind index or hash lookup for CNIC duplicate check.
  - Encryption key managed via `APP_KEY`.

### US-15.3 · Immutable Medical Records 🔲 **High**
**As a** hospital administrator, **I want** patient records, service orders, and prescriptions to be version-controlled so that changes create new versions rather than overwriting, **so that** medical record integrity is maintained.
- **Acceptance Criteria:**
  - Edits to Patient, ServiceOrder create a new version; prior version is preserved.
  - Version history is viewable from the patient profile.
  - Soft deletes only — no hard deletes on any patient-facing model.

### US-15.4 · Consent Management 🔲 **Medium**
**As a** receptionist, **I want** to record patient consent (checkbox or digital signature) per service order and treatment, **so that** the hospital has proof of informed consent.
- **Acceptance Criteria:**
  - Consent record stores: `patient_id`, `service_order_id`, `consent_type`, `consented_at`, `recorded_by`.
  - Consent is required before a service order can be created (configurable per department).
  - Consent history viewable on the patient profile.

### US-15.5 · Duplicate Patient Prevention 🔲 **High**
**As a** receptionist, **I want** the system to warn me when a patient with the same CNIC or contact number already exists during registration, **so that** duplicate records are avoided.
- **Acceptance Criteria:**
  - On patient creation, CNIC and contact are checked against existing records.
  - If a match is found, a warning is shown with the existing patient's details.
  - Receptionist can choose to proceed (different person) or select the existing patient.

### US-15.6 · Automated Backups 🔲 **High**
**As a** system administrator, **I want** daily automated backups of the database and file storage sent to offsite storage, **so that** the hospital can recover from data loss.
- **Acceptance Criteria:**
  - Scheduled artisan command runs daily.
  - Backups stored locally and uploaded to S3/SFTP (configurable).
  - Retention policy: keep last 7 daily, 4 weekly, 12 monthly.
  - Health check alerts if a backup fails.

### US-15.7 · Breach Notification 🔲 **Medium**
**As a** hospital administrator, **I want** to be alerted when unusual access patterns or unauthorized access attempts are detected, **so that** potential data breaches are caught early.
- **Acceptance Criteria:**
  - Alert on: multiple failed login attempts, access from new IP/device, bulk record access.
  - Notifications via email to designated security contacts.
  - Incident log viewable from admin panel.

### US-15.8 · FHIR-Ready API Design 🔲 **Medium**
**As a** system administrator, **I want** the API layer to follow FHIR resource conventions, **so that** the hospital can integrate with labs, pharmacies, insurance, and government systems in the future.
- **Acceptance Criteria:**
  - API resources for Patient, Encounter (ServiceOrder), Claim (Transaction) follow FHIR naming and structure.
  - Versioned API endpoints (`/api/v1/`).
  - Token-based authentication via Sanctum.
  - Rate limiting on all public endpoints.

### US-15.9 · FBR Tax Compliance 🔲 **Medium**
**As an** accountant, **I want** invoices and transaction receipts to include the hospital's NTN and STRN numbers with proper tax breakdowns, **so that** the hospital meets FBR tax filing requirements.
- **Acceptance Criteria:**
  - Hospital Settings stores NTN and STRN.
  - All printed invoices and PDF reports include NTN/STRN in the header.
  - Tax amount is itemized separately per service on receipts.

### US-15.10 · Role-Based Access Control Enforcement 🔲 **High**
**As a** hospital administrator, **I want** every resource (patients, transactions, closings, reports) to be protected by Laravel Policies/Gates based on user profiles, **so that** staff can only access data relevant to their role.
- **Acceptance Criteria:**
  - Policy class for each core model.
  - Middleware gates on all web and API routes.
  - Filament resources restrict actions (view, create, edit, delete) per role.
  - Receptionist cannot access admin panel; Doctor cannot access accounts panel.

---

## Epic 16 — Development Workflow & Release Process

> These stories define how Copilot and developers must work on the codebase.
> They are process stories, not feature stories.

### US-16.1 · Branch from Latest Main 🔲 **High**
**As a** developer (or Copilot), **I want** to always create a new feature branch from the latest `main` branch before making changes, **so that** work starts from the latest stable codebase.
- **Acceptance Criteria:**
  - `git checkout main && git pull origin main` before branching.
  - Branch naming: `feature/{short-description}`, `fix/{short-description}`, or `docs/{short-description}`.
  - No direct commits to `main`.

### US-16.2 · Sequential Atomic Commits 🔲 **High**
**As a** developer (or Copilot), **I want** to make sequential, atomic commits — each covering a single logical change — **so that** the git history is clean, reviewable, and bisectable.
- **Acceptance Criteria:**
  - Each commit message follows: `type(scope): description` (e.g., `feat(patient): add duplicate CNIC prevention`).
  - Types: `feat`, `fix`, `docs`, `test`, `refactor`, `style`, `chore`.
  - One logical change per commit — no mixing features with test fixes.
  - Run `vendor/bin/pint --dirty` before each PHP commit.
  - Run `npm run format && npm run lint` before each JS/TS commit.

### US-16.3 · Tests Alongside Changes 🔲 **High**
**As a** developer (or Copilot), **I want** every feature or fix to include updated or new tests committed alongside the change, **so that** regressions are caught immediately.
- **Acceptance Criteria:**
  - Feature changes include corresponding Pest feature tests.
  - Run `php artisan test --compact` and confirm all tests pass before committing.
  - If a test broke due to the change, fix it in the same commit or the next.
  - Test commit can be separate: `test(patient): add duplicate prevention test`.

### US-16.4 · Update Documentation 🔲 **High**
**As a** developer (or Copilot), **I want** README, `docs/project-description.md`, and `docs/user-stories.md` to be updated when a feature changes behavior or adds new functionality, **so that** documentation stays in sync with the codebase.
- **Acceptance Criteria:**
  - New features: update relevant section in `docs/project-description.md`.
  - Completed stories: flip status from 🔲 to ✅ in `docs/user-stories.md`.
  - README updated if setup steps, commands, or architecture change.
  - Docs commit: `docs: update project description for {feature}`.

### US-16.5 · User Consent for Git Operations 🔲 **High**
**As a** developer, **I want** Copilot to ask for my explicit confirmation before running any `git commit`, `git push`, or branch operations, **so that** I maintain control over what enters the repository.
- **Acceptance Criteria:**
  - Copilot shows the commit message and changed files before committing.
  - Copilot shows the branch and remote before pushing.
  - No `--force` push or `--no-verify` without explicit user approval.
  - Destructive operations (branch delete, reset) always require confirmation.

### US-16.6 · Release Notes 🔲 **High**
**As a** developer, **I want** each release to include meaningful release notes summarizing changes, **so that** hospital administrators deploying updates know what changed.
- **Acceptance Criteria:**
  - Maintain `CHANGELOG.md` at the project root.
  - Format: grouped by `Added`, `Changed`, `Fixed`, `Removed` per version.
  - GitHub Actions release body references the CHANGELOG section for that version.
  - Release notes include migration steps if database changes are required.

### US-16.7 · CI/CD Pipeline 🔲 **High**
**As a** developer, **I want** the GitHub Actions pipeline to calculate version, create a release with notes, build Docker images (app + cli), and publish to Docker Hub, **so that** hospitals receive tested, versioned images.
- **Acceptance Criteria:**
  - Pipeline triggers on push to `main`.
  - Steps: calculate version → create git tag → create GitHub release → Sentry release → build app image → build cli image.
  - Images tagged: `{version}`, `latest`, `{version}-cli`, `latest-cli`.
  - Pipeline documented in project description.

### US-16.8 · Pre-Push Checklist 🔲 **Medium**
**As a** developer (or Copilot), **I want** a checklist to verify before pushing changes, **so that** nothing is missed.
- **Acceptance Criteria:**
  - All tests pass (`php artisan test --compact`).
  - PHP code formatted (`vendor/bin/pint --dirty`).
  - JS/TS linted and formatted (`npm run lint && npm run format:check`).
  - TypeScript compiles (`npm run types`).
  - Documentation updated if applicable.
  - CHANGELOG.md updated.
  - User has reviewed and approved the changes.

---

## Epic 17 — URL Resolution Architecture

> Migrate all routes to hierarchical `/{Panel}/{RecordType}/{Year}/{Month}/{Sequence}` pattern where every prefix resolves.

### US-17.1 · Counter Panel Route Migration 🔲 **High**
**As a** developer, **I want** to migrate counter routes from legacy flat URLs (`CT-NEW`, `CT-CLOSE`, `MY-CT-LIST`) to hierarchical URLs under `/COUNTER/`, **so that** the URL structure is consistent and every prefix renders meaningful content.
- **Acceptance Criteria:**
  - `/COUNTER` → Counter landing (open counter or resume active).
  - `/COUNTER/CT` → All closings listing (replaces `MY-CT-LIST`).
  - `/COUNTER/CT/{year}` → Closings filtered by year.
  - `/COUNTER/CT/{year}/{month}` → Closings filtered by year/month.
  - `/COUNTER/CT/{year}/{month}/{sequence}` → Individual closing statement view.
  - `/COUNTER/CT/NEW` → Open counter form (replaces `CT-NEW`).
  - `/COUNTER/CT/CLOSE` → Close counter form (replaces `CT-CLOSE`).
  - Legacy URLs redirect to new URLs (301) for backward compatibility.

### US-17.2 · Transaction Route Migration 🔲 **High**
**As a** developer, **I want** transaction routes to live under `/COUNTER/TR/`, **so that** they follow the panel-scoped resolution pattern.
- **Acceptance Criteria:**
  - `/COUNTER/TR` → Transaction search.
  - `/COUNTER/TR/{year}/{month}/{day}/{number}` → Transaction view.
  - `/COUNTER/TR/{year}/{month}/{day}/{number}/edit` → Transaction edit.
  - Legacy `/TR` routes redirect to `/COUNTER/TR`.

### US-17.3 · Voucher & Expense Route Migration 🔲 **High**
**As a** developer, **I want** voucher and expense routes to live under `/COUNTER/VC/` and `/COUNTER/EXP/`, **so that** expense management URLs are panel-scoped.
- **Acceptance Criteria:**
  - `/COUNTER/VC` → Vouchers listing (replaces `CT-EXP-VOUCHER`).
  - `/COUNTER/VC/{year}` → Vouchers by year.
  - `/COUNTER/VC/{year}/{month}` → Vouchers by year/month.
  - `/COUNTER/VC/NEW` → Create voucher (replaces `CT-EXP-VOUCHER/NEW`).
  - `/COUNTER/EXP` → Record expense (replaces `CT-EXP`).
  - `/COUNTER/RECV` → Receivables listing (replaces `RECEAVEABLES`).

### US-17.4 · Queue Route Migration 🔲 **Medium**
**As a** developer, **I want** queue routes to live under `/QUE/`, **so that** hospital department queues are panel-scoped.
- **Acceptance Criteria:**
  - `/QUE` → Queue dashboard showing all departments.
  - `/QUE/{department}` → Department-specific queue.
  - Legacy `/que/{department}` redirects to `/QUE/{department}`.

### US-17.5 · Accounts Panel Route Structure 🔲 **Medium**
**As a** developer, **I want** accountant-specific routes to live under `/ACCOUNTS/`, **so that** account operations have their own panel scope.
- **Acceptance Criteria:**
  - `/ACCOUNTS/CT` → All closings listing (replaces `ACC-CT-ALL`).
  - `/ACCOUNTS/CT/{year}` → Closings by year.
  - `/ACCOUNTS/CT/{year}/{month}` → Closings by year/month.

### US-17.6 · Progressive Resolution Behavior 🔲 **High**
**As a** user, **I want** every URL prefix to render meaningful content (listing at broad levels, detail at deep levels), **so that** I can navigate by editing the URL and every bookmark works.
- **Acceptance Criteria:**
  - Truncating URL segments (removing rightmost) always loads a valid page.
  - Breadcrumbs reflect the hierarchy: Panel > Record Type > Year > Month > Record.
  - Year/month segments filter the listing; sequence segment loads the individual record.

---

## Epic 18 — Service Order Treatments (PHC Compliance)

> Extend service orders from billing tokens into full clinical treatment records per Punjab Healthcare Commission guidelines.

### US-18.1 · Treatment Record Creation 🔲 **High**
**As a** doctor, **I want** to record treatment details (chief complaint, examination findings, diagnosis, treatment plan) against a service order, **so that** the patient's clinical encounter is documented per PHC requirements.
- **Acceptance Criteria:**
  - TreatmentRecord model linked to ServiceOrder (one-to-one).
  - Fields: chief_complaint, history_of_present_illness, examination_findings, diagnosis_code (ICD-10), diagnosis_text, treatment_plan, prescriptions, follow_up_date, outcome, referral_to.
  - Treatment form accessible from the service order view.
  - Record is immutable once saved (amendments only).

### US-18.2 · Vital Signs Recording 🔲 **High**
**As a** nurse or doctor, **I want** to record vital signs (temperature, blood pressure, pulse, respiratory rate, SpO2, weight, height) for a treatment record, **so that** clinical observations are documented alongside the treatment.
- **Acceptance Criteria:**
  - VitalSigns model with multiple entries per TreatmentRecord.
  - Timestamp and recording staff captured per entry.
  - Vital signs visible in the treatment record view and patient history.

### US-18.3 · Department-Specific Treatment Forms 🔲 **High**
**As a** doctor, **I want** the treatment form to show fields specific to my department (e.g., triage level for Emergency, tooth number for Dental, test results for Lab), **so that** I can document department-specific clinical data.
- **Acceptance Criteria:**
  - OPD: complaint, diagnosis, prescription, follow-up.
  - Emergency: triage level (Red/Yellow/Green), mechanism of injury, interventions.
  - Inpatient: bed number, ward, daily progress notes, discharge summary.
  - Dental: tooth number/quadrant, procedure type, materials used.
  - Lab: sample type, test ordered, results (structured), normal ranges, abnormal flags.
  - Ultrasound: body region, findings, measurements, impression, image attachments.
  - Radiology: body part, view type, findings, impression, image attachments.

### US-18.4 · Treatment Lifecycle Status 🔲 **High**
**As a** doctor, **I want** service orders to progress through a detailed lifecycle (OPEN → IN_PROGRESS → TREATED → REVIEWED → CLOSED), **so that** I can track treatment progress and know what still needs attention.
- **Acceptance Criteria:**
  - ServiceOrderStatus enum extended: OPEN, IN_PROGRESS, TREATED, REVIEWED, CLOSED, REFERRED, CANCELLED.
  - Status transitions are validated (e.g., can't go CLOSED without treatment record).
  - Queue views filter by relevant statuses per department.

### US-18.5 · ICD-10 Diagnosis Coding 🔲 **Medium**
**As a** doctor, **I want** to search and select ICD-10 diagnosis codes when recording a treatment, **so that** diagnoses are standardized per PHC reporting requirements.
- **Acceptance Criteria:**
  - ICD-10 code lookup with search (code or description).
  - Selected code stored on TreatmentRecord.
  - Monthly PHC report can aggregate by diagnosis code.

### US-18.6 · Prescription Recording 🔲 **High**
**As a** doctor, **I want** to record structured prescriptions (drug name, dosage, frequency, duration, route) per treatment, **so that** medication orders are clear and traceable.
- **Acceptance Criteria:**
  - Prescriptions stored as structured JSON with fields: drug_name, dosage, frequency, duration, route.
  - Generic drug names preferred (PHC requirement).
  - Prescription printable from the service order view.

### US-18.7 · Referral Chain Tracking 🔲 **Medium**
**As a** doctor, **I want** to refer a patient to another department or hospital and have the receiving department acknowledge the referral, **so that** continuity of care is maintained.
- **Acceptance Criteria:**
  - Referral creates a new ServiceOrder in the target department linked to the source.
  - Referral reason and notes are captured.
  - Target department sees the referral in their queue.
  - Referral chain is visible on the patient profile.

### US-18.8 · Treatment History on Patient Profile 🔲 **Medium**
**As a** doctor, **I want** to view a patient's complete treatment history (all TreatmentRecords across all visits) from the patient profile, **so that** I have clinical context for the current encounter.
- **Acceptance Criteria:**
  - Patient profile tab shows chronological treatment records.
  - Each entry shows: date, department, doctor, diagnosis, treatment plan.
  - Clickable to full treatment record view.

### US-18.9 · Follow-Up Tracking 🔲 **Medium**
**As a** receptionist, **I want** to see a list of patients with upcoming or missed follow-up dates, **so that** I can contact them and schedule appointments.
- **Acceptance Criteria:**
  - Dashboard widget/page showing follow-ups due this week.
  - Filter by department, doctor, date range.
  - Flag missed follow-ups (past due date, no new visit).

---

## Epic 19 — Stock Tracking

> Track hospital consumables and medicines from procurement to consumption.

### US-19.1 · Manage Stock Items 🔲 **High**
**As an** administrator, **I want** to create and manage stock items with categories, units, reorder levels, and vendor information, **so that** the hospital's inventory catalog is maintained.
- **Acceptance Criteria:**
  - StockItem and StockCategory models with Filament CRUD.
  - Hierarchical categories (parent/child).
  - Medicine flag for pharmacy-level tracking.
  - Reorder level per item.

### US-19.2 · Record Stock Movements 🔲 **High**
**As a** storekeeper, **I want** to record stock in (purchases) and stock out (consumption/disposal), **so that** real-time inventory levels are accurate.
- **Acceptance Criteria:**
  - StockMovement model: type (IN/OUT), quantity, unit_cost, reference (morph), department.
  - Current stock = SUM(IN) - SUM(OUT).
  - Batch number and expiry date for medicines.

### US-19.3 · Purchase Order Workflow 🔲 **Medium**
**As an** administrator, **I want** to create purchase orders with approval workflow, **so that** procurement is documented and controlled.
- **Acceptance Criteria:**
  - PurchaseOrder model: PO/{year}/{month}/{sequence}.
  - Status: DRAFT → APPROVED → RECEIVED → stock IN movement created.
  - Links to expense voucher for payment.

### US-19.4 · Low Stock Alerts 🔲 **High**
**As an** administrator, **I want** to be alerted when stock items fall below their reorder level, **so that** I can reorder before running out.
- **Acceptance Criteria:**
  - Dashboard widget showing items below reorder level.
  - Count and severity (critical = 0, warning = below reorder).

### US-19.5 · Medicine Expiry Tracking 🔲 **Medium**
**As a** pharmacist/storekeeper, **I want** to see medicines expiring within 30/60/90 days, **so that** expired medicines are not dispensed.
- **Acceptance Criteria:**
  - Expiry report filtered by date window.
  - Expired items flagged in stock listing.

### US-19.6 · Service Order Stock Consumption 🔲 **Medium**
**As the** system, **I want** stock to be automatically deducted when a service order is created or treatment is recorded, **so that** consumption is tracked without manual entry.
- **Acceptance Criteria:**
  - Services can be linked to stock items (what they consume).
  - On service order creation, an OUT movement is auto-created.
  - Stock level check prevents service if stock is zero (configurable).

### US-19.7 · Stock Reports 🔲 **Medium**
**As an** administrator, **I want** reports on stock levels, movement history, and department consumption, **so that** I can make informed procurement decisions.
- **Acceptance Criteria:**
  - Current stock report with quantities and values.
  - Movement history report filtered by date, item, department.
  - Department consumption report.
  - PDF export with stock-specific accent color.

---

## Epic 20 — Asset Tracking

> Track hospital fixed assets through procurement, assignment, maintenance, depreciation, and disposal.

### US-20.1 · Register Assets 🔲 **High**
**As an** administrator, **I want** to register hospital assets with serial numbers, purchase details, warranty info, and assigned department, **so that** I know what the hospital owns and where it is.
- **Acceptance Criteria:**
  - Asset model with AST/{year}/{sequence} number.
  - Categories with depreciation method and useful life.
  - Status: ACTIVE, UNDER_MAINTENANCE, RETIRED, DISPOSED.
  - Filament CRUD resource.

### US-20.2 · Asset Assignment History 🔲 **Medium**
**As an** administrator, **I want** to track which department and user each asset is assigned to over time, **so that** custody and accountability are clear.
- **Acceptance Criteria:**
  - Assignment history log: who had it, when, where.
  - Transfer action: moves asset from one department/user to another.

### US-20.3 · Maintenance Scheduling 🔲 **Medium**
**As an** administrator, **I want** to schedule and track preventive maintenance for assets, **so that** equipment downtime is minimized and compliance is maintained.
- **Acceptance Criteria:**
  - AssetMaintenanceLog model: type, description, cost, dates.
  - Dashboard widget for overdue maintenance.
  - Next maintenance date auto-calculated from schedule.

### US-20.4 · Depreciation Calculation 🔲 **Medium**
**As an** accountant, **I want** the system to calculate monthly asset depreciation, **so that** financial statements accurately reflect asset values.
- **Acceptance Criteria:**
  - Straight-line and declining balance methods.
  - Monthly depreciation entries generated.
  - Integrates with Accounts panel (Abacus ledger).
  - Current book value = purchase_cost - accumulated_depreciation.

### US-20.5 · QR Code Asset Labels 🔲 **Low**
**As an** administrator, **I want** to generate and print QR code labels for physical assets, **so that** assets can be identified by scanning.
- **Acceptance Criteria:**
  - QR code encodes asset number and URL.
  - Printable label sheet (multiple QR codes per A4 page).
  - Scanning the QR code opens the asset detail page.

### US-20.6 · Warranty & Expiry Alerts 🔲 **Low**
**As an** administrator, **I want** to be alerted when asset warranties are about to expire, **so that** I can arrange extended coverage or replacement.
- **Acceptance Criteria:**
  - Dashboard widget for warranties expiring within 30/60/90 days.
  - Filterable warranty report.

---

## Epic 21 — User Tasking

> Internal task management for assignment, tracking, and accountability across staff.

### US-21.1 · Create & Assign Tasks 🔲 **High**
**As an** administrator or department head, **I want** to create tasks and assign them to staff members with priority and due date, **so that** work is delegated and tracked.
- **Acceptance Criteria:**
  - Task model: TSK/{year}/{month}/{sequence}.
  - Fields: title, description, priority (LOW/MEDIUM/HIGH/URGENT), due_date.
  - Assignable to any user.
  - Filament resource in admin panel.

### US-21.2 · Task Lifecycle 🔲 **High**
**As a** staff member, **I want** to update my task status (TODO → In Progress → Completed), **so that** my manager can see progress.
- **Acceptance Criteria:**
  - Status flow: TODO → IN_PROGRESS → COMPLETED (or BLOCKED/CANCELLED).
  - Status changes logged with timestamp and user.
  - Completion requires completion_notes.

### US-21.3 · My Tasks View 🔲 **High**
**As a** staff member, **I want** to see a list of tasks assigned to me sorted by due date and priority, **so that** I know what to work on next.
- **Acceptance Criteria:**
  - Filterable by status, priority, due date.
  - Overdue tasks highlighted.
  - Accessible from the main dashboard.

### US-21.4 · Task Comments & Attachments 🔲 **Medium**
**As a** staff member, **I want** to add comments and attach files to a task, **so that** I can communicate progress and share relevant documents.
- **Acceptance Criteria:**
  - TaskComment model with threaded discussion.
  - TaskAttachment model for file uploads.
  - Comments visible in task detail view.

### US-21.5 · Overdue Task Alerts 🔲 **Medium**
**As an** administrator, **I want** a dashboard widget showing overdue tasks grouped by assignee, **so that** I can follow up with staff who have pending work.
- **Acceptance Criteria:**
  - Widget shows count and list of overdue tasks.
  - Filterable by department.
  - Click-through to task detail.

### US-21.6 · Task Templates 🔲 **Low**
**As an** administrator, **I want** to create task templates for recurring tasks (e.g., monthly equipment check, daily cleaning audit), **so that** repetitive tasks can be created quickly.
- **Acceptance Criteria:**
  - Template stores: title, description, priority, default assignee, recurrence pattern.
  - One-click creation from template.

---

## Epic 22 — User Payroll

> Salary management, payslip generation, advance tracking, and accounts integration.

### US-22.1 · Configure Salary Structures 🔲 **High**
**As an** administrator, **I want** to set up salary structures (basic, housing, medical, transport allowances) per user, **so that** monthly pay is calculated correctly.
- **Acceptance Criteria:**
  - SalaryStructure model per user with effective dates.
  - Components: basic_salary, housing_allowance, medical_allowance, transport_allowance, other_allowances (JSON).
  - History maintained when structure changes.

### US-22.2 · Monthly Payroll Processing 🔲 **High**
**As an** accountant, **I want** to generate monthly payroll (calculate gross, apply deductions, compute net salary) for all active staff, **so that** salaries are processed accurately and on time.
- **Acceptance Criteria:**
  - PayrollPeriod model: PAY/{year}/{month}.
  - Workflow: DRAFT → CALCULATED → APPROVED → PAID → CLOSED.
  - Deductions: tax, advances, absences, penalties (configurable).
  - Bulk calculation for all active users.

### US-22.3 · Payslip Generation 🔲 **High**
**As a** staff member, **I want** to view and print my payslip showing all salary components and deductions, **so that** I have a record of my monthly pay.
- **Acceptance Criteria:**
  - PayslipEntry model per user per period.
  - PDF payslip with: gross salary, itemized deductions, net salary, payment method.
  - Staff can access own payslips; admin can access all.

### US-22.4 · Salary Advance Management 🔲 **Medium**
**As an** accountant, **I want** to record salary advances and automatically deduct a configured amount each month from the staff member's payslip, **so that** advances are recovered systematically.
- **Acceptance Criteria:**
  - SalaryAdvance model: amount, monthly deduction, remaining balance.
  - Auto-deducted during payroll calculation.
  - Status: ACTIVE → FULLY_RECOVERED (or WRITTEN_OFF).

### US-22.5 · Expense Voucher Integration 🔲 **Medium**
**As an** accountant, **I want** each salary payment to automatically create an expense voucher, **so that** payroll expenses are recorded in the accounting system.
- **Acceptance Criteria:**
  - On payroll PAID status, expense voucher created per user or as bulk.
  - Voucher links to payroll period.
  - Flows into Abacus double-entry ledger.

### US-22.6 · Payroll Reports 🔲 **Medium**
**As an** administrator, **I want** payroll summary and detail reports with PDF export, **so that** I can audit salary payments and share with management.
- **Acceptance Criteria:**
  - Monthly payroll summary: department totals, grand total.
  - Individual payroll detail report.
  - Year-end annual salary certificate per employee.
  - PDF export.

---

## Summary

| Epic | Stories | High | Medium | Low |
|------|---------|------|--------|-----|
| 1 — Authentication | 7 | 3 | 3 | 1 |
| 2 — User & Role Mgmt | 6 | 2 | 3 | 1 |
| 3 — Patient Registration | 3 | 2 | 1 | 0 |
| 4 — Counter Operations | 8 | 4 | 3 | 1 |
| 5 — Income Transactions | 8 | 4 | 4 | 0 |
| 6 — Expense Management | 6 | 3 | 3 | 0 |
| 7 — Receivables | 3 | 2 | 1 | 0 |
| 8 — Service Orders & Queues | 7 | 4 | 3 | 0 |
| 9 — Services & Config | 6 | 3 | 3 | 0 |
| 10 — Reports & Analytics | 11 | 4 | 3 | 4 |
| 11 — Prints | 4 | 0 | 4 | 0 |
| 12 — Accounts Panel | 2 | 0 | 2 | 0 |
| 13 — Patient Portal | 3 | 0 | 3 | 0 |
| 14 — System & Infra | 5 | 3 | 1 | 1 |
| 15 — Compliance & Security | 10 | 5 | 4 | 0 |
| 16 — Dev Workflow & Release | 8 | 6 | 1 | 0 |
| 17 — URL Resolution | 6 | 4 | 2 | 0 |
| 18 — Service Order Treatments | 9 | 5 | 4 | 0 |
| 19 — Stock Tracking | 7 | 3 | 4 | 0 |
| 20 — Asset Tracking | 6 | 1 | 3 | 2 |
| 21 — User Tasking | 6 | 3 | 2 | 1 |
| 22 — User Payroll | 6 | 3 | 3 | 0 |
| **Total** | **137** | **64** | **57** | **11** |
