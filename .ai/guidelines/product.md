# Product Guidelines — Hospital All In One Operations Software

> Principles and standards that govern product decisions, feature design, and implementation priorities.

---

## 1. Product Vision

A self-hosted, dockerized hospital management system that handles every operational aspect — patient registration, financial transactions, clinical treatments, inventory, asset management, and payroll — compliant with Punjab Healthcare Commission (PHC) guidelines and HIPAA-inspired practices. Deployable by any hospital with `docker-compose up`.

---

## 2. Design Principles

### 2.1 Progressive URL Resolution
Every URL in the system is hierarchical and independently resolvable. Truncating the URL always yields a valid page (listing at broad level, detail at deep level). Pattern: `/{Panel}/{RecordType}/{Year}/{Month}/{Sequence}`.

### 2.2 Record Identity in URLs
Records are identified in URLs by their human-readable numbers (`CT/2026/03/0001`), not database IDs. This makes URLs bookmarkable, shareable, and meaningful without database access.

### 2.3 Panel-Scoped Navigation
The first URL segment determines the panel context (COUNTER, PS, QUE, ACCOUNTS, etc.), governing sidebar, available actions, and role-based access. Filament panels (/admin, /accounts) follow the same scoping principle.

### 2.4 Offline-First Mindset
Design for unreliable network conditions common in Pakistani hospitals. Minimize round-trips, use optimistic UI updates, and ensure critical operations (transaction creation, patient registration) complete atomically.

### 2.5 Compliance by Default
Every feature that touches patient data, financial records, or clinical information must inherently satisfy PHC and audit requirements — not as an afterthought. Records are immutable (append-only), actions are logged, and access is role-gated.

---

## 3. Feature Prioritization Framework

### Priority Tiers

| Tier | Criteria | Examples |
|------|----------|---------|
| **P0 — Critical** | Revenue-blocking, compliance-mandatory, or data-integrity features | Transaction recording, patient registration, audit trail, data encryption |
| **P1 — High** | Core workflow features that staff use daily | Counter operations, service order treatments, stock tracking, payroll |
| **P2 — Medium** | Quality-of-life improvements and secondary workflows | Dashboards, reports, task management, asset tracking |
| **P3 — Low** | Nice-to-have features and future-proofing | Patient portal, FHIR API, QR code labels, command palette |

### Implementation Order

When multiple features are planned, implement in this order:
1. **Data model & migrations** — Schema first, always
2. **Observers & business rules** — Auto-numbering, status transitions, validation
3. **Filament admin CRUD** — Admin can manage records immediately
4. **Frontend (Inertia) pages** — Receptionist/doctor facing workflows
5. **Reports & exports** — PDF, Excel output
6. **Tests** — Feature tests alongside every change

---

## 4. Record Numbering Standards

All records follow a consistent auto-numbering pattern with these rules:

| Record | Format | Observer |
|--------|--------|----------|
| Patient | `PS/{YYYY}/{MM}/{NNNN}` | PatientObserver |
| Closing | `CT/{YYYY}/{MM}/{NNNN}` | ClosingObserver |
| Transaction | `TR/{YYYY}/{MM}/{DD}/{NNNN}` | TransactionObserver |
| Expense Voucher | `VC/{YYYY}/{MM}/{NNNN}` | ExpenseVoucherObserver (boot) |
| Service Order | `{PS_NUMBER}/{dept}/{NN}` | — |
| Purchase Order | `PO/{YYYY}/{MM}/{NNNN}` | PurchaseOrderObserver (planned) |
| Asset | `AST/{YYYY}/{NNNN}` | AssetObserver (planned) |
| Task | `TSK/{YYYY}/{MM}/{NNNN}` | TaskObserver (planned) |
| Payroll Period | `PAY/{YYYY}/{MM}` | — |

**Rules:**
- Sequence resets per year/month context (except Transaction which includes day)
- Leading zeros in sequence: `0001`, `0002`, etc.
- Generated via `lockForUpdate()` inside `DB::transaction()` to prevent race conditions
- Number is assigned in the `creating` observer hook and is immutable once set

---

## 5. Data Integrity Rules

### 5.1 Immutable Records
Once created, the following records can never have their identity or core data silently changed:
- Patient (ps_number, name, CNIC)
- Transaction (tr_number, amount after finalization)
- Service Order (so_number)
- Treatment Record (diagnosis, treatment plan after finalization)

Changes create amendment records or new versions. Original data is preserved.

### 5.2 Soft Deletes Only
No model dealing with patient data, financial records, or clinical records may use hard deletes. Use `SoftDeletes` trait with audit trail logging.

### 5.3 Cascade Protection
Deleting a parent record (e.g., Patient) must not cascade-delete children (Transactions, ServiceOrders). The system must prevent deletion if related records exist.

### 5.4 Financial Consistency
- Transaction amounts must match the sum of their TransactionElements
- Closing amounts must match the sum of associated Transactions
- Receivable amounts must track partial payments accurately
- Stock movements must balance (current level = SUM(IN) - SUM(OUT))

---

## 6. Department & Service Architecture

### Department Types (TransactionElementType mapping)

| Department | Code | Service Provider Types | Treatment Scope |
|-----------|------|----------------------|-----------------|
| OPD | OPD | OPD Doctors | Consultation, prescription, referral |
| Indoor/Inpatient | IND | Inpatient Doctors | Admission, daily rounds, discharge |
| Emergency | EMG | Emergency Doctors | Triage, stabilization, intervention |
| Dental | DNT | Dentists | Procedures, extractions, fillings |
| Laboratory | LAB | — (no provider) | Sample collection, test results |
| Ultrasound | ULT | Ultrasound Doctors | Imaging, findings, impression |
| Radiology | RAD | X-Ray Technicians | Imaging, findings, impression |

### Service Configuration
- Each Service belongs to a ServiceDepartment
- Services have: charges, tax_rate, service_provider_types (JSON array), generate_service_order flag
- Composite services bundle multiple services into one
- Services can link to stock items for auto-consumption (planned)

---

## 7. User Role Matrix

| Profile | Panel Access | Key Permissions |
|---------|-------------|-----------------|
| Administrator | Admin, Accounts | Full CRUD on all resources; user management; settings |
| Accountant | Accounts | Financial reports; payroll processing; ledger access |
| Receptionist | Counter (Frontend) | Patient registration; transaction creation; counter operations |
| OPD Doctor | Queue (Frontend) | OPD queue; treatment records; prescriptions |
| Inpatient Doctor | Queue (Frontend) | Indoor queue; admission/discharge; daily notes |
| Emergency Doctor | Queue (Frontend) | Emergency queue; triage; interventions |
| Dentist | Queue (Frontend) | Dental queue; dental procedures |
| Ultrasound Doctor | Queue (Frontend) | Ultrasound queue; imaging reports |
| X-Ray Technician | Queue (Frontend) | Radiology queue; imaging |
| Nursing Staff | Queue (Frontend) | Vital signs; treatment assistance |
| Patient Manager | Patient Portal | Patient registration; record linking |

---

## 8. PHC Compliance Checklist for New Features

Before shipping any feature that touches patient or clinical data, verify:

- [ ] **Audit trail** — All create/update/delete actions are logged with user, timestamp, old/new values
- [ ] **Immutability** — Records cannot be silently edited; changes create versions/amendments
- [ ] **Access control** — Feature is gated by user profile and permissions
- [ ] **Data encryption** — Sensitive fields (CNIC, contact, medical notes) are encrypted at rest
- [ ] **Consent** — If treatment-related, consent record is captured before proceeding
- [ ] **Standardized codes** — ICD-10 for diagnoses, generic drug names for prescriptions
- [ ] **Timestamps** — All clinical events have accurate timestamps (arrival, treatment, discharge)
- [ ] **Doctor attribution** — Every clinical action identifies the responsible doctor
- [ ] **Soft deletes** — No hard deletes on any patient-facing record
- [ ] **Test coverage** — Feature has Pest tests covering happy path and error cases

---

## 9. Integration Standards

### API Design
- REST endpoints following FHIR resource naming conventions
- Versioned: `/api/v1/patients`, `/api/v1/encounters`
- Token-based auth via Sanctum
- Rate limiting on all public endpoints
- Consistent response structure: `{ "data": {...}, "meta": {...} }`

### External Systems (Future)
- Lab information systems (LIS) — HL7/FHIR messages for results
- Pharmacy systems — Prescription routing
- Insurance/Panel APIs — Claim submission
- FBR e-invoicing — Tax reporting
- Government health portals — PHC reporting

---

## 10. Known Issues & Technical Debt

| Issue | Location | Impact |
|-------|----------|--------|
| `laboratoryQueue()` uses `type='DNT'` instead of `'LAB'` | WebController ~L1115 | Lab queue shows dental orders instead of lab orders |
| Legacy route naming inconsistency | routes/web.php | Mix of `CT-NEW`, `CT-CLOSE`, `MY-CT-LIST` patterns; need migration to hierarchical URLs |
| Only `UserFactory` exists | database/factories/ | 12+ models need factories for proper testing |
| `Receaveable` spelling | Throughout codebase | Model name has typo; migration needed to rename (low priority) |
| Transaction day in number format | Transaction model | TR number includes day (`TR/{Y}/{M}/{D}/{N}`) unlike other records — intentional but inconsistent |
| Mixed `$casts` property vs `casts()` method | Various models | Some models use property, others use method; should standardize per Laravel 12 convention |
