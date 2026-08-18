# Fix #007 — 11 Vulnerable Dependencies Patched Within Existing Constraints

**GitHub Issue:** [afaryab/hospital-care#50](https://github.com/afaryab/hospital-care/issues/50)
**Severity:** Critical (phpspreadsheet) down to Medium
**Status:** ✅ Fixed
**Branch:** `chore/dependency-security-updates`
**Date:** 2026-08-19

---

## For Developers

### What was wrong

`composer audit` reported 63 vulnerability advisories across 21 PHP packages; `pnpm audit --prod` reported 54 JS advisories. The worst was `phpoffice/phpspreadsheet` 1.30.2 — a critical RCE/SSRF (one CVE was a patch-bypass of an earlier fix), reachable via 13 Filament importer classes in this app. `filament/filament` 4.9.2 had 5 CVEs including reusable two-factor recovery codes, which directly undermines this app's own MFA feature.

### What was added

`composer update` on 10 packages plus the transitive `symfony/yaml`, all within the existing `composer.json` constraints — no breaking-change work needed:

| Package | Was | Now |
|---|---|---|
| phpoffice/phpspreadsheet | 1.30.2 | latest 1.30.x |
| filament/{filament,actions,infolists,tables} | 4.9.2 | ≥4.11.5 |
| guzzlehttp/guzzle | 7.10.0 | ≥7.15.2 |
| guzzlehttp/psr7 | 2.9.0 | ≥2.12.3 |
| laravel/framework | 12.56.0 | ≥12.60.0 |
| symfony/html-sanitizer | 8.0.7 | ≥8.0.13 |
| dompdf/dompdf | 3.1.5 | ≥3.1.6 |
| league/commonmark | 2.8.2 | ≥2.9.0 |
| spatie/laravel-medialibrary | 11.21.0 | ≥11.23.0 |
| setasign/fpdi | 2.6.6 | ≥2.6.7 |

`composer audit` now reports zero advisories.

On the JS side, `axios`/`lodash-es`/`form-data` are transitive dependencies of `@inertiajs/react` — the only vulnerable packages in the dependency tree that actually ship to the browser bundle (everything else flagged by `pnpm audit` is build-time-only tooling like vite/esbuild/babel, never bundled into shipped output regardless of which `package.json` section lists it). Pinned via `package.json`'s `pnpm.overrides`:

```json
"pnpm": {
    "onlyBuiltDependencies": ["esbuild"],
    "overrides": {
        "axios": ">=1.16.0",
        "lodash-es": ">=4.17.24",
        "form-data": ">=4.0.6"
    }
}
```

`pnpm audit --prod` dropped from 54 to 22 advisories, all remaining ones in dev-only tooling.

### A wrong turn worth documenting

pnpm 10+ moved several `pnpm.*` settings (including, as of the pnpm version used for local dev here, `overrides`) out of `package.json` into a new `pnpm-workspace.yaml` file. I initially put the overrides there — but that file is **deliberately `.gitignore`d in this repo** (`fix: remove stray pnpm-workspace.yaml breaking the Docker build`, 2026-08-10): Docker's `COPY . .` picks it up before `pnpm run build` runs, and this repo's Docker images pin `pnpm@9`, which predates that migration and reads `pnpm.*` config from `package.json` directly. Committing a `pnpm-workspace.yaml` there breaks the Docker build with a `packages field missing or empty` error. Moved the overrides back to `package.json` and confirmed the resolved versions are what's actually baked into the committed `pnpm-lock.yaml` — Docker's `pnpm install` step respects the checked-in lockfile regardless of which pnpm version generated it.

### Files changed

- `composer.lock`, `package.json`, `pnpm-lock.yaml`
- `public/css/filament/`, `public/js/filament/` — republished by `artisan filament:upgrade` to match the new Filament version

### Tests

```bash
php artisan test --compact
pnpm run build
```

800 tests, 0 failures. Frontend build verified.

### What is NOT yet covered

- The remaining 22 JS advisories are all in dev-only build tooling (vite, esbuild, babel, concurrently→shell-quote) — never shipped to the browser, low priority.
- No `composer.json`/`package.json` constraint bumps were made — a couple of packages (e.g. `firebase/php-jwt`, if a future feature adds it) may need a closer look on their own merits later.

---

## For IT / DevOps

### What changed on the server

No database changes. `vendor/` and `node_modules/` need reinstalling from the updated lockfiles — standard for any dependency bump.

### Deployment steps

1. Pull the latest code.
2. Rebuild the Docker image (`docker compose up --build`) so `composer install`/`pnpm install` pick up the new lockfiles and the frontend rebuilds against the patched packages.
3. No artisan commands required beyond the normal deploy flow.

### How to verify after deploy

1. `composer audit` inside the container should report no advisories.
2. Admin panel (Filament) should load and function normally — this is the package with the most version movement (4.9.2 → 4.11.5+).
3. Spot-check an Excel import (uses phpoffice/phpspreadsheet) and a PDF print (uses dompdf) still work.

### Rollback

`git revert` this commit and rebuild — no data or schema changes to undo.

### Risk of this change

**Low.** All updates stayed within already-declared version constraints (patch/minor bumps only), and the full test suite plus a frontend build both passed. The main risk class for any dependency bump is a behavioral change in a minor version that isn't caught by tests — worth a normal smoke-test pass on the admin panel and a few PDF/Excel workflows after deploy, same as any routine dependency update.

---

## For Reception Staff

### Does anything look different?

No. This is a behind-the-scenes update to keep the software's building blocks secure — nothing about how you use the system changes.

---

## For Hospital Administration

### Business risk mitigated

A critical remote-code-execution/SSRF vulnerability in the Excel import library, a vulnerability that let two-factor authentication recovery codes be reused, and 9 other lower-severity issues across the software's underlying components — all patched with zero functional changes to the application.

### Compliance relevance

Both `.ai/hippa-compliance` and PHC guidelines call for keeping software patched against known vulnerabilities as part of ordinary security hygiene. This closes that gap for the current dependency set as of the audit date; it's not a one-time fix — dependencies should be re-audited periodically going forward.

### Financial impact

No cost to deploy. No downtime beyond a normal deploy/rebuild cycle.
