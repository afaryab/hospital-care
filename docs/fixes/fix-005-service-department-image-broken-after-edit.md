# Fix #005 — Service Department Image Breaks After Editing via Filament

**GitHub Issue:** [afaryab/hospital-care#38](https://github.com/afaryab/hospital-care/issues/38)
**Severity:** Medium
**Status:** ✅ Fixed
**Branch:** `fix/service-department-image-upload-path`
**Date:** 2026-08-11

---

## For Developers

### What was wrong

Two compounding bugs, both confirmed by tracing Filament's `FileUpload` source and live-testing in Docker:

**1. No centralized image URL resolution.** Filament's `FileUpload::make('image')` (no `->directory()` set) stores newly-uploaded files as a bare ULID filename with **no path prefix at all** — e.g. `01KA9TMXFD8H1BW3XBRTRG3ZNJ.png` — traced through `vendor/filament/forms/src/Components/BaseFileUpload.php`'s default `saveUploadedFileUsing`. Seeded rows use a completely different, incompatible format: `/img/emergency.png` (a `public/` folder path).

`ServiceDepartmentResource`'s Filament table had ad-hoc logic to handle both formats, but it existed **only** there. Every frontend render site used the raw `image` field directly with zero resolution:
- `resources/js/elements/department/mini-card.tsx`
- `resources/js/pages/patient.tsx`
- `resources/js/pages/counter/income.tsx` (×2)

Seeded `/img/*.png` values happened to render correctly by accident (root-relative path). Post-edit bare-filename values were a guaranteed 404 — the browser resolves a path with no leading slash relative to the *current page URL*, not the site root.

**2. `APP_URL` didn't match the actual served port**, breaking even the Filament table's own fallback. `docker-compose.yml` set `APP_URL=http://localhost:8000` for the `app`/`cli` services, but `app`'s port mapping is `"80:80"`. Verified live: `curl http://localhost:8000/storage/...` failed to connect; `curl http://localhost:80/storage/...` returned 200. `.env.example` already had the correct value — `docker-compose.yml`'s override was the actual bug.

### Fix

- Added `ServiceDepartment::getImageUrlAttribute()` (appended as `image_url`), centralizing the http(s)/`/img/`/storage-disk resolution in one place instead of duplicating it.
- Filament's `ImageColumn` now points at `image_url` directly — the inline `Str::startsWith` closure is gone.
- All 4 frontend render sites switched from `image`/`dept.image` to `image_url`; `ServiceDepartment` TS type updated.
- `FileUpload::make('image')` now sets `->directory('service-departments')` and `->visibility('public')` for clarity/organization (not the root cause, but matches project convention).
- Fixed `docker-compose.yml`'s `APP_URL` to match the actual exposed port.

No data migration needed — this is a read-time resolution fix, not a stored-data format change. Existing bare-filename rows from before the fix resolve correctly the moment `image_url` is read.

### Files changed

- `app/Models/ServiceDepartment.php` — `image_url` accessor + `$appends`
- `app/Filament/Admin/Resources/ServiceDepartments/ServiceDepartmentResource.php` — simplified `ImageColumn`, `FileUpload` directory/visibility
- `resources/js/elements/department/mini-card.tsx`, `resources/js/pages/patient.tsx`, `resources/js/pages/counter/income.tsx` — use `image_url`
- `resources/js/types/index.d.ts` — `ServiceDepartment.image_url`
- `docker-compose.yml` — `APP_URL` fix
- `tests/Feature/Models/ServiceDepartmentModelTest.php`, `tests/Feature/Filament/Admin/ServiceDepartmentResourceTest.php` — new coverage

### Tests

```bash
php artisan test --compact tests/Feature/Models/ServiceDepartmentModelTest.php tests/Feature/Filament/Admin/ServiceDepartmentResourceTest.php
```

16 tests / 37 assertions, all passing. New coverage: `image_url` for all three input formats (URL passthrough, `/img/` passthrough, storage-disk path) plus the empty-image case; a Filament end-to-end test uploading a fake image and asserting the resolved `image_url` starts with `/storage/service-departments/` rather than the raw bare filename; a table-column state assertion. Full suite: 716 passed, 1 pre-existing unrelated failure, 1 skipped.

### Manual verification

Live-tested in the actual Docker environment after recreating containers with the fixed `APP_URL`: created departments with both a seeded-style path and an uploaded-style path, confirmed `image_url` resolved to `http://localhost/img/emergency.png` and `http://localhost/storage/service-departments/....png` respectively, and `curl`'d both — both returned `200`.

---

## For IT / DevOps

### What changed on the server

No schema changes, no migrations. One `.env`-equivalent value changed in `docker-compose.yml` (`APP_URL`).

### Deployment steps

1. Pull latest code.
2. If your deployment's real `APP_URL` differs from `http://localhost` (e.g. a real domain in staging/production), no action needed — `docker-compose.yml`'s value only affects local dev; production environments should already have their own correct `APP_URL` in their actual `.env`/secrets, unaffected by this change.
3. `docker compose up -d --build` (or standard deploy) to pick up the code changes. No artisan commands required.

### How to verify after deploy

1. In `/admin` → Services → Service Departments, edit a department's image and save.
2. Confirm the image now displays correctly in the admin table.
3. Visit the frontend Counter income page and confirm the department icon renders there too.

### Rollback

Revert the listed files via git. No data was changed, so rollback is a pure code revert.

### Risk

**Low.** The accessor is purely additive (new computed field); existing `image` column and its consumers elsewhere are untouched. `APP_URL` in `docker-compose.yml` only affects local Docker Compose dev environments — production deployments manage their own environment configuration separately.

---

## For Reception Staff

### What changed?

Department icons (in the Counter income screen and elsewhere) will now display correctly after an administrator updates a department's picture in the admin panel. Previously, editing a department's image would cause its icon to disappear until someone manually fixed the underlying file path.

---

## For Hospital Administration

### Business impact

| Scenario | Before fix | After fix |
|---|---|---|
| Admin updates a department's icon via Filament | Icon disappears everywhere (admin + reception-facing Counter screen) | Icon updates and displays correctly everywhere |
| Diagnosing the broken icon | Required a developer to inspect the database and file storage | No longer occurs |

### Compliance relevance

None — this is a cosmetic/display bug affecting department branding icons, not patient data, financial records, or clinical information.

### Data impact

No patient, financial, or clinical data was affected. Existing department image references remain valid; only how they're *displayed* changed.
