# Fix #021 — Route Table Hygiene

**GitHub Issue:** [afaryab/hospital-care#78](https://github.com/afaryab/hospital-care/issues/78)
**Severity:** Low/Medium
**Status:** ✅ Fixed
**Branch:** `chore/route-table-hygiene`
**Date:** 2026-08-19

---

## For Developers

### What was wrong

Five separate hygiene findings from the compliance/route audit of `release-0.10.2`:

1. **`WebController::counterClose()`'s GET branch wrote to the database.** The route is registered as both `GET CT-CLOSE` (`counter-close`, renders the close-confirmation screen) and `POST CT-CLOSE` (`counter-close-post`, actually closes the counter), both handled by the same method. The `if ($request->isMethod('post'))` branch correctly does the real close. But the `else` path — what runs for the GET — recomputed `closing_amount`/`expense_payed` from the transaction totals and called `$openCounter->save()` unconditionally. A page refresh, browser prefetch, or link scanner hitting this GET route would silently persist a write.
2. **`routes/api.php` registered `POST /closings/search` twice** — once in the general shared block (line 35), once misplaced inside the OPD Doctor section (line 50), both pointing at `ClosingController@index` with the same route name. Laravel's route collection is keyed by method+URI, so the second registration silently overwrote the first in the live route table — the first was dead code, not a functional duplicate, but still worth removing.
3. **Dead code**: a fully commented-out `Route::get('/', function () {...})` block (referencing `Features::registration()` and rendering a `welcome` Inertia page) sat above the real `/` route, along with its then-unused `use Laravel\Fortify\Features;` and `use Inertia\Inertia;` imports. `resources/js/pages/welcome.tsx` was the orphaned target — nothing in the app renders it. **Note**: the original audit framed this as a "dead public-registration env toggle" — investigation found no such toggle exists anywhere in the codebase (Fortify's `Features::registration()` is hardcoded-enabled, not env-gated). The actual finding was this dead route block, not an env var.
4. **Inconsistent API response envelope**: most of the ~13 API controllers use `{data: ...}` as the primary payload key (with real but bounded variation — some add `message`, some nest `{exact, possible}` under `data`). `LookUpController` was the one clear outlier: `{results, keyWord, strlen}`, no `data` key at all, and `keyWord`/`strlen` were dead debug fields never read by any frontend consumer.
5. **`route:cache` failures were silently swallowed on boot.** Both `docker/app/start.sh` and `docker/cli/start.sh` ran `php artisan route:cache 2>/dev/null || true` — stderr redirected away *and* the exit code forced to zero, with no `set -e` in either script. A broken route cache (e.g. a route file syntax error) would leave zero trace in container logs and the container would boot anyway with a stale or missing cache. `docker/cli/start.sh` additionally ran `optimize:clear` *after* building the caches, immediately wiping out what it had just built two lines earlier — an internal ordering bug independent of the swallowing issue.

### What was added / changed

1. `WebController::counterClose()`'s GET branch now sets the computed totals on the in-memory model for display only — no `save()` call. The POST branch (the real close action) is untouched.
2. Removed the duplicate `/closings/search` registration.
3. Removed the dead commented route block, the two now-unused imports, and the orphaned `resources/js/pages/welcome.tsx`.
4. `LookUpController::index()` now returns `{data: $results}`. Updated its one frontend consumer (`resources/js/components/kbar-wrapper.tsx`, the command palette) from `data.results` to `data.data`, and the existing structure test.
5. Both `docker/*/start.sh` scripts: removed `2>/dev/null`, replaced `|| true` with `|| echo "WARNING: ..."` per command so failures are non-fatal but now visible in container logs. Fixed `docker/cli/start.sh`'s ordering so `optimize:clear` runs before the cache builds, matching `docker/app/start.sh`.

### A note on what was deliberately NOT done

**Full API response envelope standardization was scoped out.** The broader catalog (from the original audit) found ~8 distinct response shapes across 13 controllers — `destroy()` endpoints omitting `data` entirely, ad hoc extra keys (`warning`, `stats`, `can_proceed`, `used_existing`), `PateintController` conditionally skipping JSON entirely based on content negotiation. Fixing all of it would mean touching on the order of 40 return statements plus whatever frontend TypeScript code destructures each of those shapes — a large, genuinely separate refactor with real risk of introducing frontend regressions across the whole app, not a "hygiene" change. This fix closes the one unambiguous outlier (`LookUpController`, the only endpoint with *no* `data` key and dead debug fields) and leaves the rest catalogued as known follow-up work, consistent with how other broad-blast-radius items were scoped down earlier in this audit series (e.g. API versioning, the consent hard-gate).

**The pre-existing `WebController.ts` Wayfinder-generated TypeScript duplicate-key issue was left as-is.** Running `php artisan wayfinder:generate` confirmed this is not a stale-generation problem — it's a genuine upstream Wayfinder limitation: two routes (`counter-close` GET, `counter-close-post` POST) sharing an identical URI but different names generate a duplicate object key in the emitted `.ts` file, which fails `npm run types` (a dedicated strict type-check) but does **not** fail `npm run build` (confirmed — a real production build succeeds). This predates this entire fix series (verified via `git status`/`git diff` on the generated file — completely untouched by any change here) and is unrelated to the actual GET-write bug this fix addresses (item 1). Fixing it properly would mean consolidating the two routes into one `Route::match(['get','post'], ...)`, which the frontend's `counter/close.tsx` page currently depends on being separately named (`counterClosePost.form()` for the Inertia `<Form>` submission) — that's a real change to the revenue-critical close-counter flow, disproportionate risk for what is a type-check-only issue in an auto-generated file. Flagged here for visibility, not silently dropped.

### Files changed

- `app/Http/Controllers/WebController.php` — `counterClose()` GET branch no longer writes
- `routes/api.php` — duplicate registration removed
- `routes/web.php` — dead route block + unused imports removed
- `resources/js/pages/welcome.tsx` — deleted (orphaned)
- `app/Http/Controllers/Api/LookUpController.php` — response envelope standardized to `{data: ...}`
- `resources/js/components/kbar-wrapper.tsx` — updated to match
- `docker/app/start.sh`, `docker/cli/start.sh` — visible cache-failure warnings, ordering fix
- `tests/Feature/RouteTableHygieneTest.php` — new; `tests/Feature/Api/LookupApiTest.php` — updated for the new envelope

### Tests

```bash
php -d memory_limit=1024M vendor/bin/pest --compact
```

883 tests, 0 failures (4 new). Also verified directly against a real environment: `php artisan route:list --path=closings` shows exactly one `api-closings-search` entry, and `php artisan route:cache` completes cleanly. `npm run build` succeeds (confirms the frontend change didn't break the production build).

### What is NOT yet covered

- Full API response envelope standardization (see above) — `LookUpController` fixed, the rest catalogued as follow-up.
- The pre-existing `WebController.ts` Wayfinder duplicate-key `npm run types` failure (see above) — real but low-severity (doesn't block `npm run build`), and fixing it safely requires a separate, deliberate change to the close-counter frontend flow.

---

## For IT / DevOps

### What changed on the server

- No schema changes, no migrations.
- Both Docker entrypoint scripts now print `WARNING: ...` lines to container logs if a cache-build step fails, instead of silently continuing. **This is a logging-visibility change only** — a failed cache build still doesn't stop the container from starting (deliberately — a broken cache shouldn't take down patient care), but you'll now actually see it happen.

### Deployment steps

1. Pull the latest code.
2. Standard deploy (`docker compose up --build` or equivalent) — the Docker image needs rebuilding since the entrypoint scripts changed.
3. No migrations, no other steps.

### How to verify after deploy

1. Check container boot logs (`docker compose logs app` / `docker compose logs cli`) for the `Running Laravel optimizations...` line and confirm no unexpected `WARNING:` lines appear.
2. Open the counter-close confirmation screen (without submitting) a few times in a row — confirm the closing's `updated_at` doesn't change from page views alone.
3. Use the command palette (Cmd/Ctrl+K) search — confirm lookup results still work correctly.

### Rollback

Revert the application files. No data/schema changes to undo.

### Risk of this change

**Low.** All five changes are narrow and targeted. The one with the most real-world surface area is #1 (`counterClose`) — verified with tests that the confirmation screen still displays correct computed totals and that actually closing the counter (POST) still works and persists correctly.

---

## For Reception Staff

### Does anything look different?

**No.** The close-counter screen, its totals, and the actual close action all work exactly as before.

---

## For Hospital Administration

### Business risk mitigated

| Risk | Before fix | After fix |
|---|---|---|
| A closing record's cached totals could drift from an unintended write on page view/refresh | Possible (GET wrote to DB) | No — GET is now read-only |
| A route-cache build failure during deployment going completely unnoticed | Yes — silently swallowed | No — now logged |
| Dead/orphaned code accumulating in the route table and page tree | Yes | Cleaned up |

### Compliance relevance

Minor but real: HTTP semantics (GET must not have side effects) is a basic web-security/correctness expectation, and PHC/HIPAA's emphasis on data integrity extends to "the system does what it's supposed to and nothing else." This is the smallest-severity item in this audit series but closes out the full 16-item roadmap from the original `release-0.10.2` compliance/security/performance/routes audit.

### Financial impact

No cost to deploy. No downtime required.
