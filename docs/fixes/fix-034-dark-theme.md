# Fix #034: Dark Theme Across Frontend Pages and Filament Panels

**GitHub Issue:** [afaryab/hospital-care#102](https://github.com/afaryab/hospital-care/issues/102)
**Severity:** Medium (readability / UX)
**Status:** ✅ Fixed
**Branch:** `fix/number-generators-skip-soft-deleted-rows`
**Date:** 2026-09-30

---

## For Developers

### Root cause

Switching between light and dark worked: the `.dark` class is applied without a flash on load, and `app.css` defines tokens for both themes. But most Inertia/React pages hardcoded Tailwind's light colours (`bg-white`, `border-slate-200`, `text-slate-900`, `bg-emerald-50 text-emerald-700`, …) and had no `dark:` counterparts. An audit found **1,148 light-only colour classes in 53 files**. The worst files, including `DeptPatientForm`/`DeptQueueDashboard` (shared by every department portal) and the IND and OPD pages, had no `dark:` classes at all. The result in dark mode was white cards, washed-out badges and, in places, invisible text.

The theme tokens could **not** simply replace these classes. The light theme is custom-branded: `--foreground` is white and `--muted-foreground` is green, so switching `text-slate-900` to `text-foreground` would put white text on white cards in light mode.

A second, separate bug: `resources/css/filament/theme.css`, which **both** Filament panels use, only listed Accounts folders in `@source`. So **57 of the 79** Tailwind colour classes used in Admin panel Blade views were never compiled. Status badges in the cheques and receivables tables, and the `divide-*` lines, were unstyled in **both** themes.

### What was changed

- **React:** every light-only colour class now has a `dark:` pair right next to it. Light mode is unchanged.
  - Surfaces: `bg-white` → `dark:bg-neutral-900`, `slate/gray-50/100/200` → `dark:bg-neutral-800(/50)`/`700`.
  - Borders and dividers: `slate-100/200/300` → `dark:border-neutral-800/700`.
  - Text: `slate-900/800` → `dark:text-neutral-100`, `700` → `200`, `600` → `300`, `500` → `400`.
  - Colour tints: `C-50` → `dark:bg-C-950/40`, `C-100` → `dark:bg-C-900/40`.
  - Colour text: `C-600` → `dark:text-C-400`, `C-700+` → `dark:text-C-300`.
  - Page-level backgrounds use `neutral-950`, so cards (`neutral-900`) stand out from the page.
  - Prefixes such as `hover:` and `focus:` are preserved (`dark:hover:bg-…`).
  - Classes that already had a `dark:` pair were left alone.
- **Shared colour maps:** `resources/js/lib/constants.ts` (triage and status badge colours) now has dark pairs, which fixes the badges everywhere.
- **Counter accents:** the saturated row bars on the counter view, the selected-service tile on counter income and the teal bar on patient cards use `-700` in dark mode. The triage selection ring's offset no longer renders white (`dark:ring-offset-neutral-900`).
- **Custom components in `components/ui/`:** dental chart, drug picker, ICD-10 picker, treatment attachments, table pagination and radio input got the same treatment. The stock shadcn components were already themed.
- **Filament:**
  - `theme.css` now covers `app/Filament/**`, `resources/views/filament/**` and `resources/views/vendor/filament-panels/**`; all 97 colour classes used there now compile.
  - The remaining light-only classes in the income cash-flow, cheques, receivables and service-order table views got `dark:` pairs using Filament's `gray` scale.
- **Intentionally left as they are:**
  - White text on coloured buttons.
  - The small white radio dots.
  - `bg-black` modal overlays.
  - The white backdrop behind the hospital logo. It keeps uploaded logos readable; see the comment in `filament/partials/brand-logo.blade.php`.

### Files changed

- 60 files in `resources/js`: pages under `counter/`, `ind/`, `opd/`, `service-orders/`, `doctor/`, `hospital/`, `appointments/`, `transaction/`, the auth and settings pages, and `dashboard.tsx`; `elements/dept-portal/*`, `elements/serviceorder/*` and `elements/patient/*`; `policy/create-patient-policy.tsx`; `lib/constants.ts`; the custom `components/ui/*` components; and shell components such as `nav-main`, `app-header`, `command-palette` and `text-link`.
- `resources/css/filament/theme.css`.
- 10 Blade views in `resources/views/filament/`.

### Verification

- The audit script went from 1,148 unhandled light-only classes to 0; the only remaining hits are the intentional exceptions above.
- All Filament colour classes are present in the compiled `theme-*.css`.
- `npm run build` passes, and ESLint reports 0 errors; its 173 warnings were already there.
- Pest doesn't cover styling, so the check is visual: dark mode on the OPD/IND patient pages, the department portals, the counter pages and the Filament panel/receivables pages.
- `tsc` reports one error in the generated Wayfinder file `resources/js/actions/.../WebController.ts` (a duplicate key). It was already there and isn't touched by this change.

## For IT / DevOps

- **Deploy:** run `npm run build` (front end and Filament theme). No migration, no config change.
- **Rollback:** revert the commit and rebuild the assets.
- **Risk:** low. Class-only changes; light mode renders exactly as before.

## For Reception Staff

If you use dark mode (Settings → Appearance), screens such as the counter, patient pages and department queues now show properly: no more bright white boxes or hard-to-read faded labels. Nothing changes in light mode, and nothing about how you work changes.

## For Hospital Administration

- Staff working night shifts or in dim wards can use dark mode comfortably on every screen. Before, most clinical and counter screens were half-themed and hard to read, which risks misread values.
- The Admin panel's cheque and receivable status badges (Received / Bounced / Pending) had lost their colours in every theme because of a build configuration gap. They are colour-coded again.
- No data, workflow or compliance behaviour changed.
