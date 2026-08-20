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
