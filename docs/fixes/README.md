# Fixes & Issue Log

Each fix is documented from four perspectives so every stakeholder has the information they need without reading irrelevant detail.

| Perspective | Audience | What it covers |
|---|---|---|
| **Developer** | Engineers maintaining the codebase | Root cause, files changed, tests, technical notes |
| **IT / DevOps** | Server admins, deployment team | Deployment steps, risk, rollback |
| **Reception** | Front-desk staff | What changed in daily workflow (usually: nothing visible) |
| **Admin** | Hospital management | Business risk that was mitigated, compliance impact |

---

## Index

| # | GitHub Issue | Title | Severity | Status |
|---|---|---|---|---|
| 1 | [#3](https://github.com/afaryab/hospital-care/issues/3) | Race condition in CT and SO number generation | High | ✅ Fixed |
| 2 | [#4](https://github.com/afaryab/hospital-care/issues/4) | Missing authorization — resource-level access control | Critical | ✅ Fixed |
| 3 | [#5](https://github.com/afaryab/hospital-care/issues/5) | CNIC patient search uses wrong variable | High | ✅ Fixed |
| 4 | [#34](https://github.com/afaryab/hospital-care/issues/34) | Configurable 1-page compact emergency triage print template | Enhancement | ✅ Implemented (PR #35) |
| 5 | [#38](https://github.com/afaryab/hospital-care/issues/38) | Service department image breaks after editing via Filament | Medium | ✅ Fixed |
| 6 | [#48](https://github.com/afaryab/hospital-care/issues/48) | Broken access control — any staff account could read/print/write any patient's record | Critical | ✅ Fixed |
| 7 | [#50](https://github.com/afaryab/hospital-care/issues/50) | 11 vulnerable dependencies with in-constraint upgrade paths | Critical | ✅ Fixed |
| 8 | [#52](https://github.com/afaryab/hospital-care/issues/52) | Financial records (Transaction/Closing/ExpenseVoucher/Receaveable) were hard-deletable | Critical | ✅ Fixed |
| 9 | [#54](https://github.com/afaryab/hospital-care/issues/54) | Slow admin dashboard & patient dropdown (unscoped query + uncached aggregates) | Critical | ✅ Fixed |
| 10 | [#56](https://github.com/afaryab/hospital-care/issues/56) | Unauthenticated /import-old route + verified middleware was a silent no-op app-wide | Critical | ✅ Fixed |
| 11 | [#58](https://github.com/afaryab/hospital-care/issues/58) | No rate limiting on any API route — throttler was never registered | High | ✅ Fixed |
| 12 | [#60](https://github.com/afaryab/hospital-care/issues/60) | No genuine per-view audit log of PHI access | High | ✅ Fixed |
| 13 | [#62](https://github.com/afaryab/hospital-care/issues/62) | Two-factor authentication available but never enforced for Admin/Accounts panels | High | ✅ Fixed |
| 14 | [#64](https://github.com/afaryab/hospital-care/issues/64) | Treatment attachments (X-ray/ultrasound images) had no auth and could be deleted by anyone | High | ✅ Fixed |
| 15 | [#66](https://github.com/afaryab/hospital-care/issues/66) | Clinical narrative fields and certificate CNIC/name columns were plaintext; CNIC hash was unsalted SHA-256; version-snapshot audit trail leaked plaintext PII | High | ✅ Fixed |
| 16 | [#68](https://github.com/afaryab/hospital-care/issues/68) | N+1 queries on admin tables, missing indexes, reference-data cache bypass, unmemoized isAdmin() | Medium | ✅ Fixed |
| 17 | [#70](https://github.com/afaryab/hospital-care/issues/70) | Consent resource had no create/view UI; no way to require recorded consent before treatment | Medium | ✅ Fixed |
| 18 | [#72](https://github.com/afaryab/hospital-care/issues/72) | Incident model had no manual reporting, no lifecycle, and no authorization | Medium | ✅ Fixed |
| 19 | [#74](https://github.com/afaryab/hospital-care/issues/74) | Backups unencrypted by default in production; retention short of the 6-year minimum | Medium | ✅ Fixed |
| 20 | [#76](https://github.com/afaryab/hospital-care/issues/76) | Transaction had no version history and hard-deleted; DeathCertificate/ReferralCertificate had no finalization lock | Medium | ✅ Fixed |
| 21 | [#78](https://github.com/afaryab/hospital-care/issues/78) | GET route writes to DB, duplicate route, dead code, inconsistent API envelope, swallowed route:cache failures | Low/Medium | ✅ Fixed |
| 22 | [#88](https://github.com/afaryab/hospital-care/issues/88) | Number generators ignored soft-deleted rows — duplicate TR/CT/PS/VC/SO/TSK/AST numbers after any delete | Critical | ✅ Fixed |
| 23 | [#90](https://github.com/afaryab/hospital-care/issues/90) | Service departments could be created/deleted in admin (seeder-managed); seeded departments could not be edited | Medium | ✅ Fixed |
| 24 | [#91](https://github.com/afaryab/hospital-care/issues/91) | Dashboard filter drawer slow; preset date ranges silently showed this month | Medium | ✅ Fixed |
| 25 | [#92](https://github.com/afaryab/hospital-care/issues/92) | Patient register listed every patient; now defaults to current month, stable newest-first | Low | ✅ Fixed |
| 26 | [#93](https://github.com/afaryab/hospital-care/issues/93) | Patient photo capture via webcam or upload (private, audit-logged) | Feature | ✅ Done |
| 27 | [#94](https://github.com/afaryab/hospital-care/issues/94) | Command palette: outside-click close, Ctrl/⌘K toggle, policy-filtered global search | Medium | ✅ Fixed |
| 28 | [#95](https://github.com/afaryab/hospital-care/issues/95) | Administrative transactions: panel receivable income (manual per-receivable allocation) | Feature | ✅ Done |
| 29 | [#96](https://github.com/afaryab/hospital-care/issues/96) | Compliance status page with live PHC/HIPAA checks and attestations | Feature | ✅ Done |
| 30 | [#97](https://github.com/afaryab/hospital-care/issues/97) | Public appointment requests (page + /api/v1/public) confirmed by reception | Feature | ✅ Done |
| 31 | [#98](https://github.com/afaryab/hospital-care/issues/98) | New Peds (pediatrics) department with doctor profile, dashboard, API and queue | Feature | ✅ Done |
| 32 | [#99](https://github.com/afaryab/hospital-care/issues/99) | Whole app went down when the OnlyOffice document server was unavailable | High | ✅ Fixed |
| 33 | [#100](https://github.com/afaryab/hospital-care/issues/100) | Emergency triage note print rework; BP never saved in any department; GCS/BSL/past history not captured | High | ✅ Fixed |
| 34 | [#102](https://github.com/afaryab/hospital-care/issues/102) | Dark theme: React pages hardcoded the light palette; Filament theme skipped Admin views | Medium | ✅ Fixed |
| 35 | [#103](https://github.com/afaryab/hospital-care/issues/103) | Record edits failed: encrypted version snapshots rejected by MySQL json columns | High | ✅ Fixed |
| 36 | [#104](https://github.com/afaryab/hospital-care/issues/104) | Webcam photo of patient/guardian captured on every counter slip (CT-PS) | Feature | ✅ Done |
| 37 | [#105](https://github.com/afaryab/hospital-care/issues/105) | Snapshot encryption migration took hours on large DBs and blocked later migrations | High | ✅ Fixed |
| 38 | [#107](https://github.com/afaryab/hospital-care/issues/107) | Receivable payment silently failed (302) for multi-service bills; errors and notes lost | High | ✅ Fixed |
| 39 | [#108](https://github.com/afaryab/hospital-care/issues/108) | Peds slips silently rejected at counters whose allowed departments predate PED | High | ✅ Fixed |
