# Fix #032 — App Stays Up When the OnlyOffice Document Server Is Down

**GitHub Issue:** [afaryab/hospital-care#99](https://github.com/afaryab/hospital-care/issues/99)
**Severity:** High (availability)
**Status:** ✅ Fixed
**Branch:** `fix/number-generators-skip-soft-deleted-rows`
**Date:** 2026-09-28

---

## For Developers

### Root cause

`docker/app/default.conf` proxied `/onlyoffice/` with a literal upstream, `proxy_pass http://onlyoffice-documentserver/;`. nginx resolves literal upstream hosts **at startup**. When the document server container was stopped, missing or still booting, nginx refused to start:

```
nginx: [emerg] host not found in upstream "onlyoffice-documentserver"
```

That took the entire web container, and so the whole hospital app, offline. `docker-compose.yml` also listed the document server under `app.depends_on`. Inside the app, nothing checked whether the editor was reachable: **Edit** was always offered, and the editor page loaded a script from a dead proxy, giving a blank page.

### What was changed

- **nginx:** the upstream is now a variable resolved per request through Docker's DNS (`resolver 127.0.0.11`). An upstream 502, 503 or 504, or a failed lookup, returns a plain 503 for `/onlyoffice/` only. Verified with `nginx -t` on a network with no document server: the old config fails with the error above, the new one passes.
- **docker-compose:** `onlyoffice-documentserver` is no longer a dependency of `app`.
- **`App\Services\OnlyOffice\OnlyOfficeHealth::available()`:** calls `GET {internal_url}/healthcheck` (2s connect, 3s total), caches the result for 30s (`ONLYOFFICE_HEALTH_CACHE_SECONDS`), and treats any error as "down". `ONLYOFFICE_ENABLED=false` switches the editor off without probing.
- **The whole Documents section is switched off while the server is down.**
  - **Blocking:** the new `EnsureDocumentsAvailable` middleware sits on the `dms.*` route group and on the session download and zip routes. It returns a 503: GET page requests get an Inertia `dms/unavailable` notice page, and other requests get a plain 503.
  - **Navigation:** a shared Inertia prop, `features.documents`, is `true` only for admins while the server is healthy. The sidebar **Documents** link and the command-palette entry are hidden while it's `false`. The health check only runs for admins, the only role that can see Documents.
  - **Still reachable:** OnlyOffice's own content and callback endpoints, and public share and signed zip downloads, are **not** blocked. That way an edit that was in progress can still be saved once the server recovers.
- **Editor page:** as a last line of defence, if the server goes down between page load and opening the editor, it returns a 503 notice page (`onlyoffice/unavailable.blade.php`) instead of a blank editor.
- **Tests:** `tests/TestCase.php` pre-fills the health cache as "up", because no document server runs under test. `OnlyOfficeAvailabilityTest` clears it to test the real check.

### Files changed

`docker/app/default.conf`, `docker-compose.yml`, `config/onlyoffice.php`, `.env.example`, `app/Services/OnlyOffice/OnlyOfficeHealth.php` (new), `app/Http/Middleware/EnsureDocumentsAvailable.php` (new), `app/Http/Middleware/HandleInertiaRequests.php`, `app/Http/Controllers/OnlyOffice/EditorPageController.php`, `routes/web.php`, `resources/views/onlyoffice/unavailable.blade.php` (new), `resources/js/pages/dms/unavailable.tsx` (new), `resources/js/components/app-sidebar.tsx`, `resources/js/components/command-palette.tsx`, `resources/js/types/index.d.ts`, `tests/TestCase.php`, `tests/Feature/Dms/OnlyOfficeAvailabilityTest.php` (11 tests).

## For IT / DevOps

- **Deploy:** rebuild or restart the `app` container so nginx loads the new `default.conf`. No migration.
- The document server can now be stopped, restarted or upgraded without taking the app down. The Documents section disappears while it's down and comes back within about 30 seconds of the server passing its health check.
- To run without OnlyOffice at all, set `ONLYOFFICE_ENABLED=false` (Documents stays hidden) and leave that service out of `docker compose up`.
- **Resolver:** `127.0.0.11` is Docker's embedded DNS. If the app ever runs outside Docker networking, change the `resolver` line to the host's DNS server.
- **Rollback:** revert `default.conf` and the compose file. The app code works either way.

## For Reception Staff

Nothing changes in normal use. If the document editor is down, the rest of the system keeps working.

## For Hospital Administration

- **Availability:** an outage of the document editor used to make the whole hospital system unreachable, including counter billing, patient registration and doctor queues. Now only the Documents section is switched off, with a clear notice, and everything else keeps working.
- No documents are lost. Stored files are untouched, and the section comes back on its own when the document server recovers.
