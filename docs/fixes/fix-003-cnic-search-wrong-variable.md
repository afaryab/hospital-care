# Fix #003 — CNIC Patient Search Uses Wrong Variable

**GitHub Issue:** [afaryab/hospital-care#5](https://github.com/afaryab/hospital-care/issues/5)
**Severity:** High
**Status:** ✅ Fixed
**Branch:** `claude/wizardly-bassi`
**Date:** 2026-03-28

---

## For Developers

### What was wrong

In `PateintController::index()`, the CNIC search block used `$mrNumber` in all three places where it should have used `$cnicNumber`. The block was copy-pasted from the MR number block and the variable was never updated.

```php
// BEFORE — broken
$cnicNumber = $request->get('cnic_number', false);

if($cnicNumber){
    if(Str::length($mrNumber) === 17){                          // ← wrong variable
        $exactMatches[] = Patient::where(['cnic' => $mrNumber])->first(); // ← wrong variable
    }
    $query->where('cnic', 'LIKE', "{$mrNumber}%");              // ← wrong variable
}

// AFTER — fixed
$cnicNumber = $request->get('cnic_number', false);

if($cnicNumber){
    if(Str::length($cnicNumber) === 15){
        $exactMatches[] = Patient::where(['cnic' => $cnicNumber])->first();
    }
    $query->where('cnic', 'LIKE', "{$cnicNumber}%");
}
```

### Secondary fix: length check

The exact-match condition previously checked for length `17`. A Pakistani CNIC in `XXXXX-XXXXXXX-X` format is **15 characters**. Changed to `15`.

### Behaviour before fix

| Request | Expected | Actual |
|---|---|---|
| `cnic_number=35202-1234567-1` | Returns patient with that CNIC | Searched by MR number — returned nothing (or wrong patient) |
| `cnic_number=35202` | Returns patients with CNIC starting with 35202 | If `mr_number` was also provided, searched that instead; otherwise matched nothing |

### Files changed

- `app/Http/Controllers/Api/PateintController.php` — 3 variable replacements + length check fix
- `tests/Feature/Api/PatientApiTest.php` — 3 new CNIC search tests

### Tests

```bash
php artisan test --compact tests/Feature/Api/PatientApiTest.php
```

New tests:
- `patient search filters by cnic prefix` — partial CNIC returns correct patient
- `patient search returns exact match for full 15-char cnic` — 15-char CNIC lands in `exact` array
- `cnic search does not return results for unrelated mr_number` — regression guard

### Known issues not in scope of this fix

- **Class name typo:** `PateintController` (should be `PatientController`) — renaming requires updating routes, tests, and all references; tracked as separate work
- **`orWhere` in contact/gender filters** — may return unexpected results when multiple filters are combined; tracked as separate work

---

## For IT / DevOps

### What changed on the server

One PHP controller file changed: `app/Http/Controllers/Api/PateintController.php`.

No schema changes. No migrations. No environment variables.

### Deployment steps

1. Pull latest code
2. Run `docker compose up --build` (or standard deploy)
3. No artisan commands required

### How to verify after deploy

In the frontend patient search, type the first 5 digits of a patient's CNIC number. The patient should appear in the search results. If CNIC data exists in the database, this will now return correct results.

### Rollback

Revert `app/Http/Controllers/Api/PateintController.php` to the previous commit.

### Risk

**Very low.** The change only corrects a variable reference inside an `if` block. Normal operation is unaffected; CNIC search now works where it previously returned wrong results silently.

---

## For Reception Staff

### What changed?

CNIC search in the patient search box now works. Previously, if you typed a patient's CNIC number, the system would not find the correct patient. This is now fixed.

### When will you notice the difference?

When searching for a patient using their CNIC number — the correct patient will now appear in the results.

### What if I still can't find a patient by CNIC?

- Make sure the patient's CNIC was saved when they were registered
- CNIC format should be: `XXXXX-XXXXXXX-X` (13 digits with dashes, total 15 characters)
- If the field is blank in the patient record, update it via the patient edit screen

---

## For Hospital Administration

### Business impact

| Scenario | Before fix | After fix |
|---|---|---|
| Staff search by CNIC to verify patient identity | Returned wrong results or nothing | Returns the correct patient |
| Duplicate patient detection by CNIC | Unreliable | Reliable — CNIC unique constraint enforced at create, search now consistent |
| Patient identification in busy counter | Receptionist had to fall back to name/MR number | CNIC search now a reliable lookup method |

### Compliance relevance

PHC guidelines require reliable patient identification. CNIC is the national identity document used to verify patient identity. This fix restores that verification capability.

### Data impact

No patient data was changed. This was a read-only search bug. All existing patient records are intact.
