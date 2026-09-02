# Website Build Prompt — Hospital OS (Product Marketing / Resell Site)

> **What this document is:** a self-contained briefing for an AI coding agent working in a **separate repository** — the one that will design and build a marketing/resell website for this software product. This document is not about the hospital-care application's own codebase; it is the *content source* (features, compliance credentials, positioning) for a commercial website that sells/licenses this software to hospitals, clinics, and resellers.
>
> **How to use it:** paste this whole document into the other repo's AI session as the creative brief. Everything under "Feature Content" and "Compliance & Trust Content" is verified against the actual product (as of this writing) and can be used directly as website copy source material. Everything under "Needs Your Input" is a placeholder only the business owner can fill in — do not invent values for these.

---

## 1. Product Identity

| Field | Value |
|---|---|
| **Product name** | Hospital All-In-One Operations Software ("Hospital OS") |
| **One-line positioning** | A self-hosted, all-in-one hospital operations platform — patient records, billing, clinical workflows, pharmacy, inventory, HR, and compliance reporting in one Dockerized system a hospital fully owns and controls. |
| **Deployment model** | Self-hosted via Docker (`docker-compose up`) or as prebuilt Docker Hub images. Two containers per release: an app image (web server) and a CLI image (jobs/schedules/SSH). Hospital's own infrastructure — no forced multi-tenant cloud, no vendor data lock-in. |
| **Primary market** | Pakistani hospitals and clinics (built around Punjab Healthcare Commission requirements), with architecture and safeguards designed to generalize to other markets (HIPAA-inspired practices, FHIR-ready roadmap). |
| **Category** | Hospital Management System (HMS) / Electronic Medical Record-adjacent operations platform — broader than a pure EMR: it also covers point-of-sale billing, accounting, HR/payroll, and asset/inventory management in the same system. |

### Buyer personas the site should speak to
1. **Hospital owner/administrator** — cares about cost control, staff accountability, revenue visibility, and regulatory compliance risk.
2. **IT/operations evaluator** — cares about self-hosting, data ownership, integration/API readiness, uptime, and whether the stack is modern and maintainable.
3. **Compliance/quality officer** — cares about audit trails, PHC inspection readiness, record immutability, and patient data protection.
4. **Reseller / system integrator** — cares about deployment simplicity (single Docker command), white-label potential, and licensing terms.

---

## 2. Feature Content (verified — safe to use as website copy source)

Organize the site's feature section around these modules. Each line below is written in benefit language suitable for adaptation into headlines/body copy — the underlying capability has been confirmed to exist in the actual product, not aspirational.

### Patient Management
- Every patient gets a permanent, auto-numbered medical record ID the moment they register — no duplicate numbering, no manual tracking.
- Full patient history in one view: every visit, every payment, every treatment, every outstanding balance.
- Built-in duplicate-patient detection to keep one clean record per person.
- Fast patient lookup by name, ID card number, contact number, or record number.

### Front-Desk & Billing Operations
- A complete daily cash-counter workflow: open a shift, take payments across the day, close and reconcile at end of shift with an automatic statement.
- Every transaction is auto-numbered and traceable back to the shift, staff member, and patient.
- Multiple payment methods supported per transaction — cash, card, cheque, bank transfer, and insurance/corporate "panel" billing — including split payments across methods.
- Automatic handling of partial payments and outstanding balances (receivables), with a dedicated collections view.
- Bank statement import and automatic matching against recorded transactions, so reconciliation isn't a manual spreadsheet exercise.
- Insurance/corporate panel cheque tracking from issue to receipt.

### Built-In Accounting
- Real double-entry bookkeeping runs underneath every cash transaction and expense — not just a log of numbers, an actual chart-of-accounts ledger.
- Automatic financial-year handling and account mapping, so daily operations produce audit-ready books without extra data entry.

### Clinical Workflows Across Every Department
- Dedicated digital workflows for Outpatient (OPD), Inpatient/Indoor Admission, Emergency, Dental, Laboratory, Ultrasound, and Radiology — each with its own live queue and department-specific treatment form.
- Doctors get a real-time queue dashboard: who's waiting, who's being seen, one-click "call next patient."
- Structured treatment records per visit: chief complaint, history, examination findings, diagnosis, treatment plan, and prescriptions — not free-text notes lost in a folder.
- Standardized diagnosis coding (ICD-10) built into the treatment form, so records are structured and reportable, not just written prose.
- Emergency triage system with color-coded severity levels (including a "Code Black" mass-casualty designation), live triage dashboard, and full audit history of triage changes.
- Dental treatment includes a tooth-by-tooth procedure chart.
- Vital signs (temperature, blood pressure, pulse, respiration, oxygen saturation, height/weight) captured as structured data per visit.
- Consent capture tied to specific services/treatments, with method and timestamp recorded.
- Attachments (lab results, imaging, documents) can be attached directly to a treatment record.
- Patient-facing queue/token displays for waiting rooms, department by department.

### Inpatient / Ward Management
- Full ward → room → bed hierarchy with live occupancy status.
- Bed assignment and discharge workflows tied directly to the patient's admission record.
- Instant "what beds are free right now" snapshot across the hospital.

### Appointments & Scheduling
- Patients can be booked in advance against a specific doctor/service with a scheduled time.
- Configurable priority-tier booking model so the hospital controls how appointment slots are allocated.
- Automatic no-show handling and check-in tracking.

### Pharmacy & Drug Formulary
- Centralized drug catalog with generic name, strength, manufacturer, default dosing, route, and clinical notes (usage, contraindications, side effects).
- Prescriptions pull directly from the formulary during treatment, keeping drug names standardized across the hospital.

### Inventory & Stock Control
- Track consumables and medicine stock by category, with reorder levels and current stock level computed automatically from movement history.
- Full stock movement ledger (in/out, batch number, expiry date, department) for traceability.
- Purchase order workflow from request through vendor receipt.

### Asset Management
- Register and track fixed assets (equipment, furniture, machinery) with purchase cost, warranty, and location.
- Assignment history when equipment moves between departments or staff.
- Maintenance logging and depreciation tracking by category.

### HR & Payroll
- Configurable salary structures per employee (basic pay plus allowances).
- Monthly payroll period processing with an approval workflow.
- Salary advance tracking with automatic deduction against future pay.

### Internal Task Management
- Assign and track internal operational tasks by priority, department, and due date — separate from the clinical queue, for facilities/admin work.

### Reporting & Analytics
- An executive analytics suite with dedicated dashboards for financial performance, HR, historical trends, diagnosis/ICD-10 patterns, day-to-day operations, patient demographics, and emergency triage — dozens of live charts and KPI tiles, not static spreadsheets.
- One-click PDF generation for income, expense, receivables, and service reports.
- Patient receipts and closing statements print in multiple formats — full A4, thermal receipt printer, and dot-matrix — so the system fits whatever hardware the front desk already has.

### Access Control Built for a Real Hospital Org Chart
- Every staff member gets individual login credentials tied to a specific role — receptionist, doctor (per department), nurse, lab/imaging technician, accountant, administrator, and more.
- Dedicated read-only "display terminal" accounts for waiting-room queue screens, separate from working staff accounts.
- A single staff member can hold multiple roles (e.g., a doctor who is also an administrator) without juggling separate logins.

### Deployment & Ownership
- Runs entirely on infrastructure the hospital controls — one Docker command to stand up the full system.
- No mandatory recurring cloud dependency; the hospital's patient data stays on the hospital's own servers.
- Published, versioned Docker images available for straightforward updates.

---

## 3. Compliance & Trust Content (verified — safe to use, with the accuracy notes below respected)

This is a healthcare product — compliance and data protection should be a first-class section on the website, not a footnote. The following are actually implemented in the product today, not roadmap promises:

- **Full audit trail** — every create, update, and delete across core records is logged with who did it and when, viewable in a dedicated audit log.
- **Role-based access control** — enforced per staff role across the entire system; no shared logins.
- **Encryption of sensitive patient fields** — identifying information (national ID number, contact, address) is encrypted at rest, not stored in plain text.
- **Immutable medical & financial records** — once a patient record, treatment record, or service order is finalized, edits don't silently overwrite history; every change is versioned and the original is preserved.
- **No hard deletes on patient or financial data** — records are archived (soft-deleted), never destroyed, preserving a complete legal/medical history.
- **Automated backups** — scheduled, systemized backup of the database and file storage.
- **Breach/anomaly detection** — the system flags suspicious activity such as logins from new devices or unusual patient-record access patterns and logs them as trackable incidents.
- **Consent tracking** — treatment and data-use consent is captured and timestamped against the specific patient encounter.
- **Built for regulatory inspection** — designed around Punjab Healthcare Commission (PHC) expectations for record-keeping, standardized documentation, and audit-ready evidence during inspections.

### ⚠️ Accuracy guardrails for the website copy — do not violate these
- The product is **"HIPAA-inspired"** — it follows HIPAA-style safeguards (access control, audit logging, encryption, breach handling) as an architectural discipline. It is **not HIPAA-certified**, and HIPAA certification is not a real, awardable status in the way some vendors imply. **Never write "HIPAA certified" or "HIPAA compliant" as a certification claim.** Use language like *"built on HIPAA-inspired data protection practices."*
- Frame PHC alignment as **"built to align with Punjab Healthcare Commission (PHC) guidelines,"** not as an official PHC endorsement or certification, unless the business owner supplies actual certification documentation.
- Do not claim FHIR interoperability, GDPR compliance, PCI DSS certification, or FBR e-invoicing integration as current features — these are architectural goals, not shipped capabilities today. If the site references a roadmap/"coming soon" section, they may be listed there, clearly labeled as upcoming.
- Do not state or imply the product has passed any third-party security audit or penetration test unless the business owner provides one to cite.

---

## 4. Technical Credibility Content (for IT-buyer-facing sections)

Use sparingly — this belongs in an "under the hood" or IT/technical-buyer section, not the primary hero/marketing copy.

- Built on a modern, actively maintained stack: Laravel 12 (PHP), React 19, and a Dockerized MySQL deployment.
- Automated test suite and continuous integration pipeline validate every change before release.
- Ships as versioned, reproducible Docker images — updates are a version bump, not a manual reinstall.
- Built-in error tracking and performance monitoring hooks for production operations teams.

---

## 5. Website Content & Structure Guidance (for the building agent)

Recommended section structure for a B2B healthcare SaaS/resell site:

1. **Hero** — one-line positioning + primary CTA ("Request a Demo" / "Talk to Sales"), not a signup form (this is a sold/licensed product, not self-serve SaaS).
2. **Problem → Solution** — hospitals juggle disconnected paper/Excel/legacy systems for billing, records, and compliance; this product unifies them in one self-hosted platform.
3. **Feature showcase** — grouped by the module sections in §2 above (Patient Management, Billing & Accounting, Clinical Workflows, Ward Management, Pharmacy, Inventory, Assets, Payroll, Analytics, Access Control). Use icons + short benefit statements, with a "see full feature list" expansion rather than walls of text up front.
4. **Compliance & Trust** — a dedicated, visually distinct section using §3 content. This is a key differentiator for healthcare buyers — do not bury it.
5. **How It Works / Deployment** — explain the self-hosted Docker deployment model in plain language (their data, their servers, one-command setup) to reassure IT buyers about data ownership.
6. **Screenshots / Product Tour** — **use generic, clearly-placeholder or synthetically generated UI mockups only.** Do not fabricate screenshots implying a specific real hospital is using the software, and never include real or realistic-looking patient data (names, IDs, medical details) in any screenshot or mockup — use obviously fake sample data.
7. **Pricing / Licensing** — leave as a clearly marked placeholder (see §6) until the business owner supplies real terms.
8. **FAQ** — cover self-hosting requirements, data ownership, PHC/HIPAA-inspired posture (using the accuracy guardrails above), support/update model.
9. **Request a Demo / Contact Sales CTA** — primary conversion action; treat this as a lead-gen form (name, hospital/org, email, phone, message), not a self-signup flow.
10. **Footer** — legal disclaimers, licensing terms placeholder, contact placeholder.

### Hard constraints for the building agent
- **No fabricated social proof.** Do not invent customer testimonials, hospital logos, "trusted by N hospitals" numbers, or review scores. If the business owner has none to supply yet, omit the section rather than fabricate it.
- **No fabricated screenshots of real deployments.** Product visuals must be clearly original/mockup or explicitly synthetic — never presented as a real hospital's live data.
- **No real PHI anywhere on the site**, including in demo/screenshot content — all sample data must be obviously fictional.
- **Do not overstate compliance** — follow the accuracy guardrails in §3 exactly; this is a healthcare product and overclaiming regulatory status is a legal and trust risk.
- **Tone**: professional, trust-first, healthcare B2B SaaS — confident but not hypey. Compliance and reliability should read as reassurance, not fear-based sales tactics.

---

## 6. Needs Your Input — do not invent these

The building agent should treat the following as placeholders (e.g. `[Pricing TBD]`, `[Contact TBD]`) until the business owner supplies real values:

- Pricing/licensing model (one-time license fee vs. subscription vs. per-hospital/per-bed pricing; reseller/white-label terms)
- Company/brand name to sell under (if different from "Hospital OS")
- Contact details (sales email, phone, physical address)
- Real customer references or case studies, if any exist
- Actual screenshots or a recorded product demo, if the business owner wants to supply real (sanitized) ones instead of mockups
- Domain name and any existing brand assets (logo files, color palette) beyond what's in this repo's `public/logo.png`
- Support/SLA terms to advertise
- Any actual third-party compliance certifications or audits completed, if applicable
