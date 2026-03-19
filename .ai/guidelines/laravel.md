# Laravel Conventions

Project-specific Laravel patterns extracted from the codebase. Follow these when creating or editing PHP files.

---

## Controllers

- **Location:** `app/Http/Controllers/` with subdirectories by domain (`Api/`, `Prints/`, `Settings/`)
- Type hints on all parameters and return types (`Response`, `RedirectResponse`, etc.)
- Use `Inertia::render()` for frontend rendering with kebab-case page names
- API controllers return `response()->json(['data' => [...]])` structure
- Build queries progressively with conditionals for filters
- Use `with()` for eager loading

```php
// Web controller pattern
public function patient($year, $month, $number, $departmentKey = false, $serviceNumber = false)
{
    $psNumber = 'PS/' . $year . '/' . $month . '/' . $number;
    return Inertia::render('patient', [
        'departmentKey' => $departmentKey,
        'patientData' => $patientData,
        'serviceDepartments' => $serviceDepartments,
    ]);
}

// API controller pattern
$query = Transaction::query()->with(['patient'])->latest('id');
if (!empty($filters['tr_number'])) {
    $query->where('tr_number', 'like', "%{$filters['tr_number']}%");
}
return response()->json(['data' => $query->limit($filters['limit'] ?? 10)->get()]);
```

---

## Form Requests

- **Location:** `app/Http/Requests/Settings/` (by feature/domain)
- Use **array-based** validation rules (not pipe-delimited strings)
- Include PHPDoc `@return array<string, ValidationRule|array<mixed>|string>`
- Use `Rule::unique()` for model-specific constraints

```php
public function rules(): array
{
    return [
        'name' => ['required', 'string', 'max:255'],
        'email' => [
            'required', 'string', 'lowercase', 'email', 'max:255',
            Rule::unique(User::class)->ignore($this->user()->id),
        ],
    ];
}
```

---

## Models

- **Location:** `app/Models/`
- Use `protected $fillable` (not `$guarded`)
- Use `protected $appends` for computed attributes
- Use `protected function casts(): array` method (not `$casts` property) — follow whichever convention existing sibling models use
- Explicit return types on all relationship methods
- Static helper methods for number generation with `lockForUpdate()` for concurrency

```php
protected $fillable = ['id', 'name', 'email'];
protected $hidden = ['password', 'two_factor_secret', 'remember_token'];
protected $appends = ['profiles', 'age', 'ps_number_parts'];

protected function casts(): array
{
    return [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];
}

// Relationships — always use return type hints
public function patient(): BelongsTo
{
    return $this->belongsTo(Patient::class);
}

public function elements(): HasMany
{
    return $this->hasMany(TransactionElement::class);
}

// Computed accessors (appended attributes)
public function getAgeAttribute()
{
    if ($this->age_dob !== null) {
        return (int) Carbon::parse($this->age_dob)->diffInYears(Carbon::now());
    }
}
```

---

## Observers

- **Location:** `app/Observers/`
- **Naming:** `{Model}Observer`
- Registered in `AppServiceProvider::boot()` via `Model::observe(Observer::class)`
- All lifecycle hooks return `void`
- Use `saveQuietly()` to prevent observer recursion
- Use `isDirty()` to detect field changes, `getOriginal()` for previous values

```php
public function creating(Transaction $transaction): void
{
    if (empty($transaction->tr_number)) {
        $transaction->tr_number = $transaction->generateTransactionNumber();
    }
}

public function updated(Transaction $transaction): void
{
    if ($transaction->isDirty('amount')) {
        $transaction->edited_amount = $transaction->getOriginal('amount');
        $transaction->saveQuietly();
    }
}
```

---

## Services

- **Location:** `app/Services/`
- Single responsibility per service class
- Constructor injection with optional dependencies
- Use `config()` helper for configuration, never `env()`

---

## Helpers

- **Location:** `app/Helpers/`
- Static utility methods grouped by domain (`NumberHelper`, etc.)
- Example: `NumberHelper::moneyfy($number): string` for K/M/B/T formatting

---

## Routes

### Web Routes (`routes/web.php`)

- Middleware: `['auth', 'verified']` for protected routes
- Naming: kebab-case (e.g., `patients-register`, `counter-view`, `counter-open`)
- Composite URL keys: `{year}/{month}/{number}` instead of `{model}`
- Use descriptive route names

```php
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/', [WebController::class, 'index'])->name('home');
    Route::get('PS/{year}/{month}/{number}', [WebController::class, 'patient'])->name('patients-register-ps-number');
    Route::get('CT/{ctYear}/{ctMonth}/{ctNumber}', [WebController::class, 'counterView'])->name('counter-view');
});
```

### API Routes (`routes/api.php`)

- POST endpoints for search operations
- Consistent response structure: `['data' => ['exact' => ..., 'possible' => ...]]`

---

## Middleware & Shared Data

### HandleInertiaRequests

- Share default data for all pages (app name, auth user, sidebar state)
- Access config via `config()`, cookies via `$request->cookie()`

```php
public function share(Request $request): array
{
    return [
        ...parent::share($request),
        'name' => config('app.name'),
        'auth' => ['user' => $request->user()],
        'sidebarOpen' => ! $request->hasCookie('sidebar_state') || $request->cookie('sidebar_state') === 'true',
    ];
}
```
