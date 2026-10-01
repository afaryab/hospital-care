# Fix #027 — Command Palette: Outside-Click Close, Ctrl/⌘K Toggle, Secure Global Search

**GitHub Issue:** [afaryab/hospital-care#94](https://github.com/afaryab/hospital-care/issues/94)
**Severity:** Medium (includes an authorization gap)
**Status:** ✅ Fixed
**Branch:** `fix/number-generators-skip-soft-deleted-rows`
**Date:** 2026-09-27

---

## For Developers

### What was wrong

- `components/kbar-wrapper.tsx` was a hand-built `div` overlay:
  - it had no outside-click or backdrop handler;
  - Ctrl/⌘K only opened it, never closed it;
  - Enter after typing did nothing, because `activeIndex` started at -1.
- `/api/lookup` only understood `PS…`/`TR…` prefixes and had several broken results:
  - some rows had no URL, and some were "static" and not clickable;
  - 31-character XRAY service order numbers never matched.
- **`/api/lookup` performed no authorization.** Any logged-in account, including LCD display operators, could list patient names by typing PS prefixes.

### What was changed

- **New `components/command-palette.tsx`** (replaces `kbar-wrapper.tsx`), built on the existing Radix `Dialog`:
  - closes on outside click and Esc, traps focus, and uses one global key listener so Ctrl/⌘K toggles it;
  - arrow keys and Enter work, and the first result is pre-selected;
  - it closes on navigation.
  - It still wraps the app at the root. Page props come from `initialPage` plus Inertia's `navigate` event, so it knows the user's role profiles.
- **Pages and actions:** a role-filtered command list mirroring the sidebar (`lib/abilities.ts`), including counter actions, department dashboards, queues, settings, dark mode toggle and log out. It has fuzzy matching and is shown immediately when the query is empty.
- **Record search:** `app/Services/Search/GlobalSearchService.php` behind the same `GET /api/lookup?q=`. It reads the shape of the query:

  | Query | Searches |
  |---|---|
  | `PS/…` | patient number; longer strings match service order numbers |
  | `TR/…` | transactions (plus an Edit link if allowed) |
  | `CT/…` | closings |
  | `VC/…` | expense vouchers |
  | `APT/…` | appointments |
  | `DEPT/123…` | service order short numbers |
  | CNIC, or a phone number (10+ digits) | patients, matched by hash |
  | anything else | patient name or number, and service order numbers |

  Every result passes the model's policy `view` check, so each role only sees what it can already open. Responses are `{data: [{group, title, subtitle, url}]}`.

### Files changed

`resources/js/components/command-palette.tsx` (new), `resources/js/lib/abilities.ts` (new), `resources/js/app.tsx`, `resources/js/components/kbar-wrapper.tsx` (deleted), `app/Services/Search/GlobalSearchService.php` (new), `app/Http/Controllers/Api/LookUpController.php`, `tests/Feature/Api/LookupApiTest.php` (PS/name/CNIC/contact/CT/TR/VC matching, hidden from unauthorized users, minimum length).

### Notes

- The `kbar` npm package is now unused; removing it is a dependency change awaiting approval.
- The unused header search button in `app-header.tsx` was left alone (that layout isn't active).

## For IT / DevOps

No schema change. Frontend rebuild required. The endpoint stays under the existing 120/min API throttle.

## For Reception Staff

Press **Ctrl + K** (⌘K on Mac) anywhere to open search; press it again, or click outside, to close it.

- Type a name, phone number, CNIC or any PS/TR/CT/VC/APT number and press **Enter** to open it.
- Type a page name, such as "open counter" or "receivables", to go there quickly.

## For Hospital Administration

- **Security fix:** before this change, any logged-in account (including queue display screens) could look up patient names through search. Search now shows only records each role is already allowed to see (PHC §4.1 / HIPAA minimum necessary).
- Faster navigation for all staff.
