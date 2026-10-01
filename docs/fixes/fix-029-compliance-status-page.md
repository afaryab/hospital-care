# Fix #029 — Compliance Status Page (PHC / HIPAA)

**GitHub Issue:** [afaryab/hospital-care#96](https://github.com/afaryab/hospital-care/issues/96)
**Severity:** Feature (PHC §12 inspection readiness)
**Status:** ✅ Done
**Branch:** `fix/number-generators-skip-soft-deleted-rows`
**Date:** 2026-09-27

---

## For Developers

### What was added

The admin panel has a new page, **Compliance › Compliance Status** (`App\Filament\Admin\Pages\Compliance`, admins only). It is backed by `App\Services\Compliance\ComplianceService::checks()`. Each check reports a status (`ComplianceStatus`: pass / warning / fail / attested / pending), a summary line, and the specific issues found.

**Automated checks:**

| Check | What it verifies |
|---|---|
| RBAC | Every account has a role profile |
| MFA | 2FA is enforced; lists named admins or accountants without 2FA |
| Audit trail | Activity log enabled, recent entries, core models logged |
| PHI encryption | Counts plain-text CNIC, contact or address values |
| Transport | HTTPS `APP_URL`, secure and encrypted sessions |
| Session timeout | ≤ 30 minutes |
| Production hardening | `APP_DEBUG` off in production |
| Soft deletes | On clinical and financial models |
| Record finalization | Open treatment records or unlocked certificates older than 7 days |
| Consent gate | Consent required before treatment |
| Incidents | Open, critical and overdue incidents |
| Breach alert contacts | Not a placeholder address |
| Backups | Encrypted, off-site, and less than 26 hours old |

**Manual attestations:** risk assessment, workforce training, business associate agreements, breach response plan, physical safeguards, and downtime procedures.

- An admin attests with an optional evidence note.
- The attestation is stored in the `compliance_attestations` hospital setting and written to the activity log (`compliance_attested`).
- Attestations expire after 1 year.

### Files changed

`app/Enum/ComplianceStatus.php`, `app/Services/Compliance/ComplianceService.php`, `app/Filament/Admin/Pages/Compliance.php`, `resources/views/filament/admin/pages/compliance.blade.php`, `tests/Feature/Compliance/CompliancePageTest.php`.

## For IT / DevOps

No schema change. Many findings are environment settings: `SESSION_SECURE_COOKIE`, `SESSION_ENCRYPT`, `SESSION_LIFETIME`, `BACKUP_ARCHIVE_PASSWORD`, `BACKUP_OFFSITE_DISK`, `SECURITY_CONTACT_EMAILS`, `APP_URL` (https) and `APP_DEBUG`. The page tells you which ones to set.

## For Reception Staff

Nothing changes.

## For Hospital Administration

Open **Compliance › Compliance Status** before a PHC inspection or monthly. It shows:

- an overall score;
- each requirement with its guideline reference;
- the exact problems, such as which administrators have no two-factor login, whether backups are encrypted, and how many incidents are overdue.

Items the system can't verify, such as staff training, need you to confirm them each year with a short evidence note. That confirmation is recorded against your name.
