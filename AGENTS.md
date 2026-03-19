<laravel-boost-guidelines>
=== .ai/database rules ===

# Database Conventions

Project-specific database patterns extracted from the codebase. Follow these for migrations, factories, seeders, enums, and query patterns.

---

## Migrations

- **Location:** `database/migrations/`
- **Naming:** Timestamp prefix + descriptive name
- Both `up()` and `down()` return `void`
- Use `constrained()` for foreign key constraints
- When modifying a column, include **all** previously defined attributes (they'll be dropped otherwise)

```php
public function up(): void
{
    Schema::create('users', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->string('email')->unique();
        $table->timestamp('email_verified_at')->nullable();
        $table->string('password');
        $table->foreignId('profile_img_id')->nullable()->constrained('images');
        $table->timestamps();
    });
}

public function down(): void
{
    Schema::dropIfExists('users');
}
```

---

## Factories

- **Location:** `database/factories/`
- **Naming:** `{Model}Factory`
- Return array from `definition()` method
- Use `fake()` for data generation (not `$this->faker`)
- Include helper states: `->unverified()`, `->withoutTwoFactor()`, etc.
- Cache expensive defaults: `static::$password ??= 'password'`

```php
class UserFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= 'password',
        ];
    }

    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
```

**Usage:**
- Single: `User::factory()->create()`
- Multiple: `User::factory(5)->create()`
- With state: `User::factory()->unverified()->create()`

---

## Enums

- **Location:** `app/Enum/`
- **Naming:** TitleCase (e.g., `CounterStatus`, `ExpenseVoucherStatus`, `PaymentMethods`)
- Use pure enums when no backing value is needed, string-backed for stored values

```php
// Pure enum
enum CounterStatus
{
    case OPEN;
    case CLOSED;
    case REPORTED;
}

// Backed enum
enum ExpenseVoucherStatus: string
{
    case PENDING = 'pending';
    case PAYED = 'payed';
}
```

**Usage in Filament/selects:**
```php
collect(CounterStatus::cases())
    ->mapWithKeys(fn (CounterStatus $s) => [$s->name => ucfirst(strtolower($s->name))])
    ->toArray()
```

---

## Model Casts

- Prefer `casts()` method over `$casts` property (follow existing model conventions)
- Common cast types: `'json'`, `'boolean'`, `'datetime'`, `'hashed'`, enum classes

```php
protected function casts(): array
{
    return [
        'allowed_departments' => 'json',
        'is_allowed_to_pay_voucher' => 'boolean',
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'status' => CounterStatus::class,
    ];
}
```

---

## Query Patterns

### Progressive Query Building

```php
$query = Transaction::query()->with(['patient'])->latest('id');

if (!empty($filters['tr_number'])) {
    $query->where('tr_number', 'like', "%{$filters['tr_number']}%");
}

if (!empty($filters['patient_id'])) {
    $query->where('patient_id', $filters['patient_id']);
}

return $query->limit($filters['limit'] ?? 10)->get();
```

### Concurrency-Safe Number Generation

```php
public static function generateCounterNumber(): string
{
    return DB::transaction(function () {
        $count = self::where('ps_number', 'like', "PS/{$year}/{$month}/%")
            ->lockForUpdate()
            ->count();

        return "PS/{$year}/{$month}/" . str_pad($count + 1, 4, '0', STR_PAD_LEFT);
    });
}
```

### Computed Attributes with Appends

```php
protected $appends = ['ps_number_parts', 'age', 'year', 'month'];

public function getPsNumberPartsAttribute()
{
    if (empty($this->ps_number)) {
        return null;
    }

    $parts = explode('/', $this->ps_number);

    return [
        'year' => $parts[1] ?? null,
        'month' => $parts[2] ?? null,
        'number' => $parts[3] ?? null,
    ];
}
```

---

## Key Reminders

- Prefer `Model::query()` over `DB::` facade
- Always eager load relationships to prevent N+1 (`->with([...])`)
- Use `lockForUpdate()` inside `DB::transaction()` for sequential number generation
- Use `saveQuietly()` in observers to prevent recursion
- Numbering format: `PS/YYYY/MM/NNNN`, `CT/YYYY/MM/NNNN`, `TR/YYYY/MM/NNNN`

=== .ai/filament rules ===

# Filament Conventions

Project-specific Filament v4 patterns extracted from the codebase. Follow these when creating or editing Filament resources.

---

## Resource Structure

- **Location:** `app/Filament/{PanelName}/Resources/{ResourceName}/`
- Two panels exist: **Admin** (`/admin`) and **Accounts** (`/accounts`)
- Separate schema classes in `Schemas/` subdirectory: `{Resource}Form.php`, `{Resource}Infolist.php`
- Separate table config in `Tables/` subdirectory: `{Resource}sTable.php` (pluralized)
- Page classes in `Pages/` subdirectory: `Create{Resource}`, `Edit{Resource}`, `List{Resources}`, `View{Resource}`

```
app/Filament/Admin/Resources/ClosingResource/
├── ClosingResource.php
├── Pages/
│   ├── CreateClosing.php
│   ├── EditClosing.php
│   ├── ListClosings.php
│   └── ViewClosing.php
├── Schemas/
│   ├── ClosingForm.php
│   └── ClosingInfolist.php
└── Tables/
    └── ClosingsTable.php
```

---

## Resource Class

- Extends `Resource`
- Use `Heroicon` enum for navigation icons (not string icon names)
- Delegate form/table/infolist to separate schema classes via static `configure()` method

```php
class ClosingResource extends Resource
{
    protected static ?string $model = Closing::class;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;
    protected static ?string $recordTitleAttribute = 'ct_number';

    public static function form(Schema $schema): Schema
    {
        return ClosingForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ClosingInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ClosingsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListClosings::route('/'),
            'create' => CreateClosing::route('/create'),
            'view' => ViewClosing::route('/{record}'),
            'edit' => EditClosing::route('/{record}/edit'),
        ];
    }
}
```

---

## Form Schemas

- **Pattern:** Static method `configure(Schema $schema): Schema`
- Components: `TextInput`, `Select`, `DateTimePicker`, `Toggle`, etc.
- Use `->live()` for reactive fields, `fn ($get)` for conditional rendering
- Use `->relationship()` for model relationships

```php
class ClosingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('ct_number')->required(),
            TextInput::make('opening_amount')->required()->numeric()->default(0.0),
            DateTimePicker::make('closed_at'),
        ]);
    }
}
```

---

## Table Schemas

- **Pattern:** Static method `configure(Table $table): Table`
- Use `->formatStateUsing()` with `->html()` for custom rendering
- Use `->sortable()`, `->searchable()`, `->toggleable()` for interactions
- Use `->description()` for sub-text
- Filters: `SelectFilter::make()`, `Filter::make()` with closures
- Groups: `Group::make()` for grouping records, `->defaultGroup()`
- Enum options via `collect(Enum::cases())->mapWithKeys(fn ($s) => [$s->name => ucfirst(strtolower($s->name))])->toArray()`

```php
class ClosingsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('ct_number')
                    ->label('CT Information')
                    ->formatStateUsing(fn ($record) => "CT: {$record->ct_number}<br>Reception: {$record->reception?->name}")
                    ->html()
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(collect(CounterStatus::cases())
                        ->mapWithKeys(fn (CounterStatus $s) => [$s->name => ucfirst(strtolower($s->name))])
                        ->toArray()),
            ])
            ->groups([
                Group::make('status')->label('Status'),
            ])
            ->defaultGroup('status');
    }
}
```

---

## Infolists (Read-only Views)

- **Pattern:** Static method `configure(Schema $schema): Schema`
- Use `Tabs::make()` with `Tab::make()` for tabbed layouts
- Use `ViewEntry::make()` with `->view()` and `->viewData()` for custom Blade views

```php
class ClosingInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Tabs::make('Tabs')->tabs([
                Tab::make('Summary')->schema([
                    ViewEntry::make('closing_overview')
                        ->label(false)
                        ->view('filament.closings.summary')
                        ->viewData(fn (Closing $record) => ['closing' => $record])
                        ->columnSpanFull(),
                ]),
            ])->columnSpanFull(),
        ]);
    }
}
```

---

## Page Classes

- Extend `CreateRecord`, `EditRecord`, `ListRecords`, `ViewRecord`
- Minimal code — inherit from parent, override only when needed

```php
class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;
}
```

---

## Namespace Reference

| Component | Correct Namespace |
|---|---|
| Form fields (TextInput, Select, etc.) | `Filament\Forms\Components\` |
| Infolist entries (TextEntry, IconEntry, etc.) | `Filament\Infolists\Components\` |
| Layout (Grid, Section, Tabs, Wizard, etc.) | `Filament\Schemas\Components\` |
| Schema utilities (Get, Set) | `Filament\Schemas\Components\Utilities\` |
| Actions | `Filament\Actions\` |
| Icons | `Filament\Support\Icons\Heroicon` enum |

---

## Icons

- Use `Heroicon` enum: `Heroicon::OutlinedRectangleStack`, `Heroicon::PencilSquare`
- Set on resource via `protected static string|BackedEnum|null $navigationIcon`

---

## Key Reminders

- File visibility is `private` by default — use `->visibility('public')` for public access
- `Grid`, `Section`, and `Fieldset` no longer span all columns by default in v4
- Always authenticate before testing panel functionality
- Use `livewire()` or `Livewire::test()` for testing (Filament is built on Livewire)

=== .ai/frontend rules ===

# Frontend Conventions (React + Inertia + TypeScript)

Project-specific frontend patterns extracted from the codebase. Follow these when creating or editing React/TypeScript files.

---

## Page Components

- **Location:** `resources/js/pages/`
- **Naming:** kebab-case filenames (e.g., `counter/income.tsx`, `patient.tsx`)
- Default export React component
- Props via `usePage<PropsType>().props`
- Wrap in `AppLayout` with breadcrumbs
- Use `<Head>` for page title

```tsx
import { usePage } from '@inertiajs/react';
import AppLayout from '@/layouts/app-layout';
import { Head } from '@inertiajs/react';

type CounterIncomeProps = {
    selectedPatient?: Patient;
    departments: Department[];
    openCounter?: Closing;
};

export default function CounterIncome() {
    const { selectedPatient, departments, openCounter } = usePage<CounterIncomeProps>().props;

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Counter Income" />
            {/* content */}
        </AppLayout>
    );
}
```

---

## Components

- **Location:** `resources/js/components/`
- **Naming:** kebab-case (e.g., `currency.tsx`, `input-error.tsx`)
- Typed props interface
- Named export or default export
- Use 30+ shadcn/ui Radix components from `@/components/ui/`

```tsx
type CurrencyProps = {
    value: number;
    currency?: string;
    className?: string;
};

const Currency: React.FC<CurrencyProps> = ({ value, currency = 'PKR', className }) => {
    const formatted = React.useMemo(() => formatCurrency(value, currency), [value, currency]);
    return <span className={className}>{formatted}</span>;
};

export default Currency;
```

---

## Elements (Domain Components)

- **Location:** `resources/js/elements/`
- **Organization:** By domain (`patient/`, `counter/`, `department/`, `expense-voucher/`, `serviceorder/`)
- Reusable UI pieces for specific business domains
- Can be compound components

---

## Layouts

- **Location:** `resources/js/layouts/`
- `app-layout.tsx` — Main layout with sidebar/header
- Accept `children` and `breadcrumbs` props

```tsx
interface AppLayoutProps {
    children: ReactNode;
    breadcrumbs?: BreadcrumbItem[];
}

export default ({ children, breadcrumbs, ...props }: AppLayoutProps) => (
    <AppLayoutTemplate breadcrumbs={breadcrumbs} {...props}>
        {children}
    </AppLayoutTemplate>
);
```

---

## Hooks

- **Location:** `resources/js/hooks/`
- **Naming:** `use-{name}.ts` or `.tsx`
- Existing hooks: `use-appearance`, `use-clipboard`, `use-mobile`, `use-mobile-navigation`, `use-two-factor-auth`, `use-initials`

---

## Types

- **Location:** `resources/js/types/index.d.ts`
- Interface-based type system
- Include `[key: string]: unknown` for extensibility on shared interfaces
- Domain-specific types: `Patient`, `Transaction`, `ServiceOrder`, `Department`, etc.

```typescript
export interface SharedData {
    name: string;
    quote: { message: string; author: string };
    auth: Auth;
    sidebarOpen: boolean;
    [key: string]: unknown;
}

export interface Patient {
    id: string;
    ps_number: string;
    year: number;
    month: number;
    name: string;
    gender: 'm' | 'f' | 't' | 'o';
    contact: string;
    cnic: string;
    age: number;
    treatments: ServiceOrder[];
}
```

---

## Routing (Wayfinder)

- Import from `@/routes` for named routes, `@/actions` for controller invocables
- Usage: `routeFunction({...}).url` to get URL string

```tsx
import { home, counter, counterSelectDepartment } from '@/routes';

const url = counterSelectDepartment({
    pYear: selectedPatient.year,
    pMonth: selectedPatient.month,
    number: selectedPatient.number,
}).url;
```

---

## Styling

- Tailwind CSS v4 utility classes
- Use `clsx` for conditional classes (not `classnames`)
- Use `cn()` utility from `@/lib/utils` (shadcn pattern)

```tsx
import { clsx } from 'clsx';

<div className={clsx('flex gap-4', isActive && 'bg-blue-500')} />
```

---

## Common Patterns

- **Props destructuring:** Always destructure with types via `usePage<T>().props`
- **Conditional rendering:** Ternary or logical operators, not if/else blocks
- **Breadcrumbs:** Import `BreadcrumbItem` from `@/types`, pass array to `AppLayout`
- **Currency formatting:** Use `Currency` component or `formatCurrency()` utility

=== .ai/github-operating-guideline rules ===

# Git Workflow and Change Control Guideline for AI Agent

## Branching Rule

- Before starting any task, always check out the latest `main` branch.
- Pull the newest changes from `main`.
- Create a new branch for the task.
- Never work directly on `main`.

### Branch Naming

Use a clear and descriptive branch name, such as:
- `feature/add-user-notifications`
- `fix/login-validation-error`
- `refactor/order-service-cleanup`

Suggested format:
- `feature/<short-description>`
- `fix/<short-description>`
- `chore/<short-description>`
- `refactor/<short-description>`

---

## Required Flow Before Making Changes

1. Switch to `main`.
2. Pull latest `main`.
3. Create a fresh task branch from `main`.
4. Confirm current branch is not `main`.
5. Start implementation only after branch creation.

Example flow:

```bash
git checkout main
git pull origin main
git checkout -b feature/<task-name>
```

---

## Development Rule

- Make changes only inside the newly created branch.
- Keep changes scoped to the requested task.
- Avoid unrelated edits.
- Preserve existing functionality unless the task explicitly requires a change.

---

## Testing Rule

After completing code changes:
- Run all relevant tests.
- Run linting/formatting checks if available.
- Fix failing tests before requesting review.
- Do not mark the task complete if tests are failing, unless explicitly reported to the user.

Examples:

```bash
php artisan test
composer test
npm test
npm run build
```

Use the commands relevant to the project.

---

## Commit Rule

- Do not commit automatically without permission.
- Prepare changes in small, logical, procedural steps.
- Share a summary of completed work with the admin/user.
- Ask for admin permission before creating commits.

### Commit Procedure

When permission is granted:
1. Review changed files.
2. Group related changes logically.
3. Create clear and focused commits.
4. Use meaningful commit messages.

Example commit message styles:
- `fix: correct login validation flow`
- `feat: add patient search filters`
- `refactor: simplify invoice calculation service`

---

## Completion Rule

When implementation is finished:
1. Provide a summary of what was changed.
2. Report test results clearly.
3. Ask the user/admin to test the changes.
4. Wait for user confirmation before committing and pushing.

---

## Push Rule

- Do not push code automatically after implementation.
- Do not push code automatically after commit.
- Push only after explicit user consent.

### Final Sequence

1. Implementation completed
2. Tests passed
3. User/admin reviews and tests
4. User gives consent
5. Commit changes
6. Push branch to remote

---

## Safety Rules

- Never commit directly to `main`.
- Never push directly to `main`.
- Never skip branch creation.
- Never skip tests unless the user explicitly approves.
- Never push without user consent.
- Never assume permission; wait for explicit approval.

---

## Expected AI Agent Behavior

For every task, the AI agent must:
- start from updated `main`
- create a new branch
- perform only task-related changes
- run relevant tests
- summarize results
- wait for admin permission before commit
- wait for user consent before push

---

## Example Operational Checklist

- [ ] Checkout `main`
- [ ] Pull latest changes
- [ ] Create new task branch
- [ ] Implement requested changes
- [ ] Run relevant tests
- [ ] Summarize changes for user/admin
- [ ] Request permission to commit
- [ ] Wait for user testing
- [ ] Request consent to push
- [ ] Push only after approval

=== .ai/hippa-compliance rules ===

# HIPAA Compliance Guidelines (Production Grade)

## For Healthcare Software Systems (HMS / EMR / SaaS)

---

## 1. Overview

This document defines compliance requirements based on the **Health Insurance Portability and Accountability Act (HIPAA)**.

Applicable to:

- Electronic Medical Records (EMR)
- Hospital Management Systems (HMS)
- Telemedicine Platforms
- Cloud-hosted healthcare SaaS

---

## 2. Core HIPAA Rules

HIPAA consists of three primary rules:

### 2.1 Privacy Rule

- Protects **Protected Health Information (PHI)**
- Defines how PHI can be used and disclosed

### 2.2 Security Rule

- Defines safeguards for **electronic PHI (ePHI)**

### 2.3 Breach Notification Rule

- Requires notification in case of data breaches

---

## 3. Protected Health Information (PHI)

### 3.1 What is PHI?

Any information that identifies a patient and relates to:

- Health condition
- Treatment
- Payment

Examples:

- Name
- CNIC / SSN
- Phone number
- Medical records
- Lab results

---

## 4. Administrative Safeguards

### 4.1 Risk Analysis

- Perform periodic risk assessments
- Identify vulnerabilities

### 4.2 Workforce Training

- Staff must be trained on:
  - Data privacy
  - System usage
  - Incident reporting

### 4.3 Access Management

- Role-Based Access Control (RBAC)
- Least privilege enforcement

### 4.4 Business Associate Agreements (BAA)

- Required with:
  - Cloud providers
  - SaaS vendors
  - Third-party services

---

## 5. Physical Safeguards

### 5.1 Facility Access Control

- Restricted server room access
- Visitor logs

### 5.2 Workstation Security

- Auto-lock systems
- Screen privacy

### 5.3 Device Management

- Secure disposal of devices
- Encryption on laptops

---

## 6. Technical Safeguards

### 6.1 Access Control

- Unique user IDs
- Multi-factor authentication (MFA)
- Automatic session timeout

### 6.2 Audit Controls

- Log all system activity:
  - Logins
  - Data access
  - Data modification

### 6.3 Integrity Controls

- Ensure data is not altered improperly
- Use:
  - Hashing
  - Version control

### 6.4 Transmission Security

- TLS encryption (HTTPS)
- Secure APIs

---

## 7. Data Encryption

### 7.1 At Rest

- AES-256 recommended

### 7.2 In Transit

- TLS 1.2+

---

## 8. Audit Logging

### 8.1 Required Logs

- User authentication
- PHI access
- Record changes
- System errors

### 8.2 Log Requirements

- Immutable (append-only)
- Timestamped
- User-linked

---

## 9. Breach Notification

### 9.1 Definition

A breach = unauthorized access/disclosure of PHI

### 9.2 Notification Timeline

- Within **60 days** of discovery

### 9.3 Required Notifications

- Affected individuals
- Regulatory authority
- Media (if large breach)

---

## 10. Data Minimization

- Collect only necessary data
- Avoid storing unnecessary PHI

---

## 11. Patient Rights

Patients have the right to:

- Access their data
- Request corrections
- Get activity logs
- Request data restrictions

---

## 12. Data Retention

- Retain records for **6 years minimum**
- Logs must also be retained

---

## 13. System Design Requirements (For Your Architecture)

### 13.1 Multi-Tenant Isolation

- Separate data per hospital/organization
- Prevent cross-tenant access

### 13.2 Central Logging System

- Collect logs from all nodes
- Ensure:
  - Secure transmission
  - PHI masking where required

### 13.3 Incident Monitoring

- Real-time alerts
- SIEM integration (optional)

---

## 14. API Security

- Token-based authentication
- Rate limiting
- Input validation
- Audit all API access

---

## 15. Backup & Disaster Recovery

- Regular backups
- Encrypted backups
- Tested recovery procedures

---

## 16. Compliance Checklist

- [ ] Risk assessment completed
- [ ] RBAC implemented
- [ ] MFA enabled
- [ ] Audit logs active
- [ ] Encryption enabled
- [ ] Backup system in place
- [ ] Breach response plan defined
- [ ] BAA agreements signed

---

## 17. Common Violations (Avoid These)

- Shared user accounts
- Unencrypted databases
- No audit logs
- No breach response plan
- Storing excessive PHI

---

## 18. Penalties

Non-compliance may result in:

- Heavy fines (up to millions USD)
- Legal action
- Business shutdown

---

## 19. Legal Disclaimer

This document is a **technical guideline**, not legal advice.

Consult:

- HIPAA compliance experts
- Legal advisors

---

## 20. Version Info

**Version:** v1.0
**Prepared for:** Healthcare SaaS / Host-Swarm Systems
**Last Updated:** {{DATE}}

=== .ai/laravel rules ===

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

=== .ai/product rules ===

# Product Guidelines — Hospital All In One Operations Software

> Principles and standards that govern product decisions, feature design, and implementation priorities.

---

## 1. Product Vision

A self-hosted, dockerized hospital management system that handles every operational aspect — patient registration, financial transactions, clinical treatments, inventory, asset management, and payroll — compliant with Punjab Healthcare Commission (PHC) guidelines and HIPAA-inspired practices. Deployable by any hospital with `docker-compose up`.

---

## 2. Design Principles

### 2.1 Progressive URL Resolution

Every URL in the system is hierarchical and independently resolvable. Truncating the URL always yields a valid page (listing at broad level, detail at deep level). Pattern: `/{Panel}/{RecordType}/{Year}/{Month}/{Sequence}`.

### 2.2 Record Identity in URLs

Records are identified in URLs by their human-readable numbers (`CT/2026/03/0001`), not database IDs. This makes URLs bookmarkable, shareable, and meaningful without database access.

### 2.3 Panel-Scoped Navigation

The first URL segment determines the panel context (COUNTER, PS, QUE, ACCOUNTS, etc.), governing sidebar, available actions, and role-based access. Filament panels (/admin, /accounts) follow the same scoping principle.

### 2.4 Offline-First Mindset

Design for unreliable network conditions common in Pakistani hospitals. Minimize round-trips, use optimistic UI updates, and ensure critical operations (transaction creation, patient registration) complete atomically.

### 2.5 Compliance by Default

Every feature that touches patient data, financial records, or clinical information must inherently satisfy PHC and audit requirements — not as an afterthought. Records are immutable (append-only), actions are logged, and access is role-gated.

---

## 3. Feature Prioritization Framework

### Priority Tiers

| Tier | Criteria | Examples |
|------|----------|---------|
| **P0 — Critical** | Revenue-blocking, compliance-mandatory, or data-integrity features | Transaction recording, patient registration, audit trail, data encryption |
| **P1 — High** | Core workflow features that staff use daily | Counter operations, service order treatments, stock tracking, payroll |
| **P2 — Medium** | Quality-of-life improvements and secondary workflows | Dashboards, reports, task management, asset tracking |
| **P3 — Low** | Nice-to-have features and future-proofing | Patient portal, FHIR API, QR code labels, command palette |

### Implementation Order

When multiple features are planned, implement in this order:
1. **Data model & migrations** — Schema first, always
2. **Observers & business rules** — Auto-numbering, status transitions, validation
3. **Filament admin CRUD** — Admin can manage records immediately
4. **Frontend (Inertia) pages** — Receptionist/doctor facing workflows
5. **Reports & exports** — PDF, Excel output
6. **Tests** — Feature tests alongside every change

---

## 4. Record Numbering Standards

All records follow a consistent auto-numbering pattern with these rules:

| Record | Format | Observer |
|--------|--------|----------|
| Patient | `PS/{YYYY}/{MM}/{NNNN}` | PatientObserver |
| Closing | `CT/{YYYY}/{MM}/{NNNN}` | ClosingObserver |
| Transaction | `TR/{YYYY}/{MM}/{DD}/{NNNN}` | TransactionObserver |
| Expense Voucher | `VC/{YYYY}/{MM}/{NNNN}` | ExpenseVoucherObserver (boot) |
| Service Order | `{PS_NUMBER}/{dept}/{NN}` | — |
| Purchase Order | `PO/{YYYY}/{MM}/{NNNN}` | PurchaseOrderObserver (planned) |
| Asset | `AST/{YYYY}/{NNNN}` | AssetObserver (planned) |
| Task | `TSK/{YYYY}/{MM}/{NNNN}` | TaskObserver (planned) |
| Payroll Period | `PAY/{YYYY}/{MM}` | — |

**Rules:**
- Sequence resets per year/month context (except Transaction which includes day)
- Leading zeros in sequence: `0001`, `0002`, etc.
- Generated via `lockForUpdate()` inside `DB::transaction()` to prevent race conditions
- Number is assigned in the `creating` observer hook and is immutable once set

---

## 5. Data Integrity Rules

### 5.1 Immutable Records

Once created, the following records can never have their identity or core data silently changed:
- Patient (ps_number, name, CNIC)
- Transaction (tr_number, amount after finalization)
- Service Order (so_number)
- Treatment Record (diagnosis, treatment plan after finalization)

Changes create amendment records or new versions. Original data is preserved.

### 5.2 Soft Deletes Only

No model dealing with patient data, financial records, or clinical records may use hard deletes. Use `SoftDeletes` trait with audit trail logging.

### 5.3 Cascade Protection

Deleting a parent record (e.g., Patient) must not cascade-delete children (Transactions, ServiceOrders). The system must prevent deletion if related records exist.

### 5.4 Financial Consistency

- Transaction amounts must match the sum of their TransactionElements
- Closing amounts must match the sum of associated Transactions
- Receivable amounts must track partial payments accurately
- Stock movements must balance (current level = SUM(IN) - SUM(OUT))

---

## 6. Department & Service Architecture

### Department Types (TransactionElementType mapping)

| Department | Code | Service Provider Types | Treatment Scope |
|-----------|------|----------------------|-----------------|
| OPD | OPD | OPD Doctors | Consultation, prescription, referral |
| Indoor/Inpatient | IND | Inpatient Doctors | Admission, daily rounds, discharge |
| Emergency | EMG | Emergency Doctors | Triage, stabilization, intervention |
| Dental | DNT | Dentists | Procedures, extractions, fillings |
| Laboratory | LAB | — (no provider) | Sample collection, test results |
| Ultrasound | ULT | Ultrasound Doctors | Imaging, findings, impression |
| Radiology | RAD | X-Ray Technicians | Imaging, findings, impression |

### Service Configuration

- Each Service belongs to a ServiceDepartment
- Services have: charges, tax_rate, service_provider_types (JSON array), generate_service_order flag
- Composite services bundle multiple services into one
- Services can link to stock items for auto-consumption (planned)

---

## 7. User Role Matrix

| Profile | Panel Access | Key Permissions |
|---------|-------------|-----------------|
| Administrator | Admin, Accounts | Full CRUD on all resources; user management; settings |
| Accountant | Accounts | Financial reports; payroll processing; ledger access |
| Receptionist | Counter (Frontend) | Patient registration; transaction creation; counter operations |
| OPD Doctor | Queue (Frontend) | OPD queue; treatment records; prescriptions |
| Inpatient Doctor | Queue (Frontend) | Indoor queue; admission/discharge; daily notes |
| Emergency Doctor | Queue (Frontend) | Emergency queue; triage; interventions |
| Dentist | Queue (Frontend) | Dental queue; dental procedures |
| Ultrasound Doctor | Queue (Frontend) | Ultrasound queue; imaging reports |
| X-Ray Technician | Queue (Frontend) | Radiology queue; imaging |
| Nursing Staff | Queue (Frontend) | Vital signs; treatment assistance |
| Patient Manager | Patient Portal | Patient registration; record linking |

---

## 8. PHC Compliance Checklist for New Features

Before shipping any feature that touches patient or clinical data, verify:

- [ ] **Audit trail** — All create/update/delete actions are logged with user, timestamp, old/new values
- [ ] **Immutability** — Records cannot be silently edited; changes create versions/amendments
- [ ] **Access control** — Feature is gated by user profile and permissions
- [ ] **Data encryption** — Sensitive fields (CNIC, contact, medical notes) are encrypted at rest
- [ ] **Consent** — If treatment-related, consent record is captured before proceeding
- [ ] **Standardized codes** — ICD-10 for diagnoses, generic drug names for prescriptions
- [ ] **Timestamps** — All clinical events have accurate timestamps (arrival, treatment, discharge)
- [ ] **Doctor attribution** — Every clinical action identifies the responsible doctor
- [ ] **Soft deletes** — No hard deletes on any patient-facing record
- [ ] **Test coverage** — Feature has Pest tests covering happy path and error cases

---

## 9. Integration Standards

### API Design

- REST endpoints following FHIR resource naming conventions
- Versioned: `/api/v1/patients`, `/api/v1/encounters`
- Token-based auth via Sanctum
- Rate limiting on all public endpoints
- Consistent response structure: `{ "data": {...}, "meta": {...} }`

### External Systems (Future)

- Lab information systems (LIS) — HL7/FHIR messages for results
- Pharmacy systems — Prescription routing
- Insurance/Panel APIs — Claim submission
- FBR e-invoicing — Tax reporting
- Government health portals — PHC reporting

---

## 10. Known Issues & Technical Debt

| Issue | Location | Impact |
|-------|----------|--------|
| `laboratoryQueue()` uses `type='DNT'` instead of `'LAB'` | WebController ~L1115 | Lab queue shows dental orders instead of lab orders |
| Legacy route naming inconsistency | routes/web.php | Mix of `CT-NEW`, `CT-CLOSE`, `MY-CT-LIST` patterns; need migration to hierarchical URLs |
| Only `UserFactory` exists | database/factories/ | 12+ models need factories for proper testing |
| `Receaveable` spelling | Throughout codebase | Model name has typo; migration needed to rename (low priority) |
| Transaction day in number format | Transaction model | TR number includes day (`TR/{Y}/{M}/{D}/{N}`) unlike other records — intentional but inconsistent |
| Mixed `$casts` property vs `casts()` method | Various models | Some models use property, others use method; should standardize per Laravel 12 convention |

=== .ai/punjab-health-care-commission-guideline-compliance rules ===

# Punjab Health Care Commission (PHC) Compliance Guidelines — v2 (Production Grade)

## For Hospital / Clinic Software Systems (HMS / EMR / Telemedicine)

---

## 1. Purpose

This document defines a **comprehensive compliance framework** aligned with:

- Punjab Health Care Commission (PHC) expectations
- Minimum Service Delivery Standards (MSDS)
- Medico-legal record requirements in Pakistan

This is designed for:
- Hospital Management Systems (HMS)
- Electronic Medical Records (EMR)
- SaaS healthcare platforms (e.g., Host-Swarm deployments)

---

## 2. Compliance Philosophy

PHC compliance is not just technical — it is:

- **Clinical**
- **Operational**
- **Legal**
- **Audit-driven**

👉 The system must **produce defensible evidence** during inspections.

---

## 3. System Architecture Requirements

### 3.1 Deployment Models

- On-Premise (preferred for sensitive hospitals)
- Private Cloud (Pakistan region recommended)
- Hybrid (central reporting + local data)

### 3.2 Mandatory Components

- Application Server (HMS/EMR)
- Database Server (secured)
- Central Logging System
- Incident Reporting Service
- Backup System

---

## 4. Patient Data Protection

### 4.1 Confidentiality

- Strict RBAC (Role-Based Access Control)
- No shared accounts
- Session tracking required

### 4.2 Encryption

- At rest: AES-256 (recommended)
- In transit: TLS 1.2+

### 4.3 Data Access Logging

Every access must log:
- user_id
- patient_id
- action
- timestamp
- IP/device (if available)

---

## 5. Medico-Legal Record Integrity (CRITICAL)

### 5.1 Record Finalization

- Once finalized:
  - Record becomes **immutable**
  - Edits require:
    - New version entry
    - Reason for change
    - User identity

### 5.2 Digital Signatures

- Doctor must “sign”:
  - Prescriptions
  - Diagnoses
  - Discharge summaries

### 5.3 Version Control

- Maintain full history:
  - Original entry
  - Modified entry
  - Who changed it
  - Why

---

## 6. Clinical Workflow Compliance

### 6.1 Mandatory Structured Data

System must enforce structured entries:

- Patient demographics
- Vitals
- Diagnosis (ICD-ready if possible)
- Prescriptions (drug + dosage + duration)
- Notes (timestamped)

### 6.2 Workflow Enforcement

System must NOT allow:

- Prescription without patient record
- Discharge without doctor approval
- Billing without recorded service

---

## 7. Consent Management

### 7.1 Consent Types

- Treatment consent
- Procedure/surgery consent
- Data sharing consent

### 7.2 System Requirements

- Store:
  - Consent type
  - Timestamp
  - Captured method (digital/manual)
- Link consent to patient record

---

## 8. Audit Trail System

### 8.1 Mandatory Events

- Login / logout
- Patient record access
- Record edits
- Prescription issuance
- Billing changes
- Incident reports

### 8.2 Requirements

- Append-only logs
- Tamper-proof storage
- Central aggregation (recommended)

---

## 9. Incident Management System

### 9.1 Incident Types

- Clinical error
- System failure
- Data breach
- Delay in treatment

### 9.2 Incident Lifecycle

1. Reported
2. Classified (severity)
3. Assigned
4. Investigated
5. Resolved
6. Closed (with audit log)

### 9.3 Required Fields

- incident_type
- department
- timestamp
- patient_reference (optional/anonymized)
- severity_level
- status

---

## 10. Central Incident & Log Reporting (Host-Swarm Ready)

### 10.1 Architecture

Each hospital instance → sends to → Central Compliance Server

### 10.2 Data Sent

- Incident metadata
- Aggregated logs
- Metrics (no raw PII unless required)

### 10.3 API Requirements

- Token-based authentication
- Rate limiting
- Validation layer

---

## 11. Role-Based Access Control (RBAC)

### 11.1 Roles

- Doctor
- Nurse
- Receptionist
- Admin
- Auditor

### 11.2 Enforcement

- Least privilege principle
- Sensitive actions logged
- Optional approval flows

---

## 12. Inspection Readiness Module (HIGH VALUE FEATURE)

### 12.1 PHC Audit Dashboard

Provide one-click access to:

- Patient records
- Audit logs
- Incident reports
- Staff activity
- Compliance checklist

### 12.2 Export Options

- PDF reports
- CSV logs
- Patient summaries

---

## 13. Data Retention & Backup

### 13.1 Retention

- Patient records: long-term (as per policy)
- Logs: minimum 1–3 years

### 13.2 Backup Strategy

- Daily backups
- Offsite storage
- Disaster recovery plan

---

## 14. System Reliability

- High availability recommended
- Failover support
- Monitoring (uptime + errors)

---

## 15. Downtime & Fallback Procedures

### 15.1 Mandatory Capability

- Printable forms
- Manual entry fallback

### 15.2 Post-Recovery

- Sync manual entries into system
- Maintain audit of delayed entries

---

## 16. Interoperability

- Lab systems
- Pharmacy systems
- Future PHC/Gov APIs

Standards:
- HL7 / FHIR (recommended)

---

## 17. Security Best Practices

- MFA for admins
- Strong password policy
- Regular patching
- API security (rate limiting + tokens)

---

## 18. User Accountability

- Every action tied to a unique user
- Session tracking
- Optional device tracking

---

## 19. Compliance Checklist

- [ ] RBAC enforced
- [ ] Audit logs enabled
- [ ] Incident lifecycle implemented
- [ ] Record locking enabled
- [ ] Consent tracking implemented
- [ ] Backup system active
- [ ] Central reporting connected
- [ ] Audit dashboard available

---

## 20. Future Enhancements

- AI-based anomaly detection
- Predictive incident alerts
- Multi-hospital analytics
- Government integration APIs

---

## 21. Legal Disclaimer

This document provides a **technical compliance framework**.

Final compliance must be validated with:
- Legal advisors
- PHC inspection teams

---

## 22. Version Info

**Version:** v2.0  
**Prepared for:** Host-Swarm Healthcare Platform  
**Last Updated:** {{DATE}}

=== .ai/testing rules ===

# Testing Conventions

Project-specific Pest testing patterns extracted from the codebase. Follow these when writing or editing tests.

---

## Setup

- **Config:** `tests/Pest.php`
- Uses `RefreshDatabase` trait automatically for Feature tests
- Extends `Tests\TestCase`

```php
pest()->extend(Tests\TestCase::class)
    ->use(Illuminate\Foundation\Testing\RefreshDatabase::class)
    ->in('Feature');
```

---

## Test Style

- Use `test()` function (not `it()`) with descriptive strings
- Use factories for all model creation
- Check existing factory states before manually setting attributes
- Feature tests in `tests/Feature/`, unit tests in `tests/Unit/`
- Create with: `php artisan make:test --pest {name}` (add `--unit` for unit tests)

```php
test('guests are redirected to the login page', function () {
    $this->get(route('dashboard'))->assertRedirect(route('login'));
});

test('authenticated users can visit the dashboard', function () {
    $this->actingAs($user = User::factory()->create());
    $this->get(route('dashboard'))->assertOk();
});
```

---

## Running Tests

```bash

# All tests (compact output)

php artisan test --compact

# Filter by name

php artisan test --compact --filter=testName

# Specific file

php artisan test --compact tests/Feature/Auth/AuthenticationTest.php
```

---

## Filament Testing

- Authenticate before testing panel functionality
- Use `livewire()` helper (or `Livewire::test()`)

```php
// Table test
livewire(ListUsers::class)
    ->assertCanSeeTableRecords($users)
    ->searchTable($users->first()->name)
    ->assertCanSeeTableRecords($users->take(1))
    ->assertCanNotSeeTableRecords($users->skip(1));

// Create resource test
livewire(CreateUser::class)
    ->fillForm(['name' => 'Test', 'email' => 'test@example.com'])
    ->call('create')
    ->assertNotified()
    ->assertRedirect();

assertDatabaseHas(User::class, ['name' => 'Test', 'email' => 'test@example.com']);

// Validation test
livewire(CreateUser::class)
    ->fillForm(['name' => null, 'email' => 'invalid-email'])
    ->call('create')
    ->assertHasFormErrors(['name' => 'required', 'email' => 'email'])
    ->assertNotNotified();

// Action test
livewire(EditUser::class, ['record' => $user->id])
    ->callAction(DeleteAction::class)
    ->assertNotified()
    ->assertRedirect();
```

---

## Key Reminders

- Every code change must include a new or updated test
- Use `fake()` for data generation (project convention over `$this->faker`)
- Most tests should be feature tests (not unit)
- Run minimum tests needed: filter by file or name for speed
- Do NOT delete existing tests without approval

=== foundation rules ===

# Laravel Boost Guidelines

The Laravel Boost guidelines are specifically curated by Laravel maintainers for this application. These guidelines should be followed closely to ensure the best experience when building Laravel applications.

## Foundational Context

This application is a Laravel application and its main Laravel ecosystems package & versions are below. You are an expert with them all. Ensure you abide by these specific packages & versions.

- php - 8.4
- filament/filament (FILAMENT) - v4
- inertiajs/inertia-laravel (INERTIA_LARAVEL) - v2
- laravel/fortify (FORTIFY) - v1
- laravel/framework (LARAVEL) - v12
- laravel/prompts (PROMPTS) - v0
- laravel/pulse (PULSE) - v1
- laravel/telescope (TELESCOPE) - v5
- laravel/wayfinder (WAYFINDER) - v0
- livewire/livewire (LIVEWIRE) - v3
- laravel/boost (BOOST) - v2
- laravel/mcp (MCP) - v0
- laravel/pail (PAIL) - v1
- laravel/pint (PINT) - v1
- laravel/sail (SAIL) - v1
- pestphp/pest (PEST) - v4
- phpunit/phpunit (PHPUNIT) - v12
- @inertiajs/react (INERTIA_REACT) - v2
- react (REACT) - v19
- tailwindcss (TAILWINDCSS) - v4
- @laravel/vite-plugin-wayfinder (WAYFINDER_VITE) - v0
- eslint (ESLINT) - v9
- prettier (PRETTIER) - v3

## Skills Activation

This project has domain-specific skills available. You MUST activate the relevant skill whenever you work in that domain—don't wait until you're stuck.

- `wayfinder-development` — Activates whenever referencing backend routes in frontend components. Use when importing from @/actions or @/routes, calling Laravel routes from TypeScript, or working with Wayfinder route functions.
- `livewire-development` — Use for any task or question involving Livewire. Actovate if user mentions Livewire, wire: directives, or Livewire-specific concepts like wire:model, wire:click, invoke this skill. Covers building new components, debugging reactivity issues, real-time form validation, loading states, migrating from Livewire 2 to 3, converting component formats (SFC/MFC/class-based), and performance optimization. Do not use for non-Livewire reactive UI (React, Vue, Alpine-only, Inertia.js) or standard Laravel forms without Livewire.
- `pest-testing` — Use this skill for Pest PHP testing in Laravel projects only. Trigger whenever any test is being written, edited, fixed, or refactored — including fixing tests that broke after a code change, adding assertions, converting PHPUnit to Pest, adding datasets, and TDD workflows. Always activate when the user asks how to write something in Pest, mentions test files or directories (tests/Feature, tests/Unit, tests/Browser), or needs browser testing, smoke testing multiple pages for JS errors, or architecture tests. Covers: it()/expect() syntax, datasets, mocking, browser testing (visit/click/fill), smoke testing, arch(), Livewire component tests, RefreshDatabase, and all Pest 4 features. Do not use for factories, seeders, migrations, controllers, models, or non-test PHP code.
- `inertia-react-development` — Develops Inertia.js v2 React client-side applications. Activates when creating React pages, forms, or navigation; using <Link>, <Form>, useForm, or router; working with deferred props, prefetching, or polling; or when user mentions React with Inertia, React pages, React forms, or React navigation.
- `tailwindcss-development` — Always invoke when the user's message includes 'tailwind' in any form. Also invoke for: building responsive grid layouts (multi-column card grids, product grids), flex/grid page structures (dashboards with sidebars, fixed topbars, mobile-toggle navs), styling UI components (cards, tables, navbars, pricing sections, forms, inputs, badges), adding dark mode variants, fixing spacing or typography, and Tailwind v3/v4 work. The core use case: writing or fixing Tailwind utility classes in HTML templates (Blade, JSX, Vue). Skip for backend PHP logic, database queries, API routes, JavaScript with no HTML/CSS component, CSS file audits, build tool configuration, and vanilla CSS.
- `eloquent-best-practices` — Best practices for Laravel Eloquent ORM including query optimization, relationship management, and avoiding common pitfalls like N+1 queries.
- `laravel-inertia-react` — Laravel + Inertia.js + React integration patterns. Use when building Inertia page components, handling forms with useForm, managing shared data, or implementing persistent layouts. Triggers on tasks involving Inertia.js, page props, form handling, or Laravel React integration.
- `laravel-specialist` — Build and configure Laravel 10+ applications, including creating Eloquent models and relationships, implementing Sanctum authentication, configuring Horizon queues, designing RESTful APIs with API resources, and building reactive interfaces with Livewire. Use when creating Laravel models, setting up queue workers, implementing Sanctum auth flows, building Livewire components, optimising Eloquent queries, or writing Pest/PHPUnit tests for Laravel features.
- `php-best-practices` — PHP 8.x modern patterns, PSR standards, and SOLID principles. Use when reviewing PHP code, checking type safety, auditing code quality, or ensuring PHP best practices. Triggers on "review PHP", "check PHP code", "audit PHP", or "PHP best practices".
- `php-pro` — Use when building PHP applications with modern PHP 8.3+ features, Laravel, or Symfony frameworks. Invokes strict typing, PHPStan level 9, async patterns with Swoole, and PSR standards. Creates controllers, configures middleware, generates migrations, writes PHPUnit/Pest tests, defines typed DTOs and value objects, sets up dependency injection, and scaffolds REST/GraphQL APIs. Use when working with Eloquent, Doctrine, Composer, Psalm, ReactPHP, or any PHP API development.

## Conventions

- You must follow all existing code conventions used in this application. When creating or editing a file, check sibling files for the correct structure, approach, and naming.
- Use descriptive names for variables and methods. For example, `isRegisteredForDiscounts`, not `discount()`.
- Check for existing components to reuse before writing a new one.

## Verification Scripts

- Do not create verification scripts or tinker when tests cover that functionality and prove they work. Unit and feature tests are more important.

## Application Structure & Architecture

- Stick to existing directory structure; don't create new base folders without approval.
- Do not change the application's dependencies without approval.

## Frontend Bundling

- If the user doesn't see a frontend change reflected in the UI, it could mean they need to run `npm run build`, `npm run dev`, or `composer run dev`. Ask them.

## Documentation Files

- You must only create documentation files if explicitly requested by the user.

## Replies

- Be concise in your explanations - focus on what's important rather than explaining obvious details.

=== boost rules ===

# Laravel Boost

- Laravel Boost is an MCP server that comes with powerful tools designed specifically for this application. Use them.

## Artisan Commands

- Run Artisan commands directly via the command line (e.g., `php artisan route:list`, `php artisan tinker --execute "..."`).
- Use `php artisan list` to discover available commands and `php artisan [command] --help` to check parameters.

## URLs

- Whenever you share a project URL with the user, you should use the `get-absolute-url` tool to ensure you're using the correct scheme, domain/IP, and port.

## Debugging

- Use the `database-query` tool when you only need to read from the database.
- Use the `database-schema` tool to inspect table structure before writing migrations or models.
- To execute PHP code for debugging, run `php artisan tinker --execute "your code here"` directly.
- To read configuration values, read the config files directly or run `php artisan config:show [key]`.
- To inspect routes, run `php artisan route:list` directly.
- To check environment variables, read the `.env` file directly.

## Reading Browser Logs With the `browser-logs` Tool

- You can read browser logs, errors, and exceptions using the `browser-logs` tool from Boost.
- Only recent browser logs will be useful - ignore old logs.

## Searching Documentation (Critically Important)

- Boost comes with a powerful `search-docs` tool you should use before trying other approaches when working with Laravel or Laravel ecosystem packages. This tool automatically passes a list of installed packages and their versions to the remote Boost API, so it returns only version-specific documentation for the user's circumstance. You should pass an array of packages to filter on if you know you need docs for particular packages.
- Search the documentation before making code changes to ensure we are taking the correct approach.
- Use multiple, broad, simple, topic-based queries at once. For example: `['rate limiting', 'routing rate limiting', 'routing']`. The most relevant results will be returned first.
- Do not add package names to queries; package information is already shared. For example, use `test resource table`, not `filament 4 test resource table`.

### Available Search Syntax

1. Simple Word Searches with auto-stemming - query=authentication - finds 'authenticate' and 'auth'.
2. Multiple Words (AND Logic) - query=rate limit - finds knowledge containing both "rate" AND "limit".
3. Quoted Phrases (Exact Position) - query="infinite scroll" - words must be adjacent and in that order.
4. Mixed Queries - query=middleware "rate limit" - "middleware" AND exact phrase "rate limit".
5. Multiple Queries - queries=["authentication", "middleware"] - ANY of these terms.

=== php rules ===

# PHP

- Always use curly braces for control structures, even for single-line bodies.

## Constructors

- Use PHP 8 constructor property promotion in `__construct()`.
    - `public function __construct(public GitHub $github) { }`
- Do not allow empty `__construct()` methods with zero parameters unless the constructor is private.

## Type Declarations

- Always use explicit return type declarations for methods and functions.
- Use appropriate PHP type hints for method parameters.

<!-- Explicit Return Types and Method Params -->
```php
protected function isAccessible(User $user, ?string $path = null): bool
{
    ...
}
```

## Enums

- Typically, keys in an Enum should be TitleCase. For example: `FavoritePerson`, `BestLake`, `Monthly`.

## Comments

- Prefer PHPDoc blocks over inline comments. Never use comments within the code itself unless the logic is exceptionally complex.

## PHPDoc Blocks

- Add useful array shape type definitions when appropriate.

=== tests rules ===

# Test Enforcement

- Every change must be programmatically tested. Write a new test or update an existing test, then run the affected tests to make sure they pass.
- Run the minimum number of tests needed to ensure code quality and speed. Use `php artisan test --compact` with a specific filename or filter.

=== inertia-laravel/core rules ===

# Inertia

- Inertia creates fully client-side rendered SPAs without modern SPA complexity, leveraging existing server-side patterns.
- Components live in `resources/js/pages` (unless specified in `vite.config.js`). Use `Inertia::render()` for server-side routing instead of Blade views.
- ALWAYS use `search-docs` tool for version-specific Inertia documentation and updated code examples.
- IMPORTANT: Activate `inertia-react-development` when working with Inertia client-side patterns.

# Inertia v2

- Use all Inertia features from v1 and v2. Check the documentation before making changes to ensure the correct approach.
- New features: deferred props, infinite scrolling (merging props + `WhenVisible`), lazy loading on scroll, polling, prefetching.
- When using deferred props, add an empty state with a pulsing or animated skeleton.

=== laravel/core rules ===

# Do Things the Laravel Way

- Use `php artisan make:` commands to create new files (i.e. migrations, controllers, models, etc.). You can list available Artisan commands using `php artisan list` and check their parameters with `php artisan [command] --help`.
- If you're creating a generic PHP class, use `php artisan make:class`.
- Pass `--no-interaction` to all Artisan commands to ensure they work without user input. You should also pass the correct `--options` to ensure correct behavior.

## Database

- Always use proper Eloquent relationship methods with return type hints. Prefer relationship methods over raw queries or manual joins.
- Use Eloquent models and relationships before suggesting raw database queries.
- Avoid `DB::`; prefer `Model::query()`. Generate code that leverages Laravel's ORM capabilities rather than bypassing them.
- Generate code that prevents N+1 query problems by using eager loading.
- Use Laravel's query builder for very complex database operations.

### Model Creation

- When creating new models, create useful factories and seeders for them too. Ask the user if they need any other things, using `php artisan make:model --help` to check the available options.

### APIs & Eloquent Resources

- For APIs, default to using Eloquent API Resources and API versioning unless existing API routes do not, then you should follow existing application convention.

## Controllers & Validation

- Always create Form Request classes for validation rather than inline validation in controllers. Include both validation rules and custom error messages.
- Check sibling Form Requests to see if the application uses array or string based validation rules.

## Authentication & Authorization

- Use Laravel's built-in authentication and authorization features (gates, policies, Sanctum, etc.).

## URL Generation

- When generating links to other pages, prefer named routes and the `route()` function.

## Queues

- Use queued jobs for time-consuming operations with the `ShouldQueue` interface.

## Configuration

- Use environment variables only in configuration files - never use the `env()` function directly outside of config files. Always use `config('app.name')`, not `env('APP_NAME')`.

## Testing

- When creating models for tests, use the factories for the models. Check if the factory has custom states that can be used before manually setting up the model.
- Faker: Use methods such as `$this->faker->word()` or `fake()->randomDigit()`. Follow existing conventions whether to use `$this->faker` or `fake()`.
- When creating tests, make use of `php artisan make:test [options] {name}` to create a feature test, and pass `--unit` to create a unit test. Most tests should be feature tests.

## Vite Error

- If you receive an "Illuminate\Foundation\ViteException: Unable to locate file in Vite manifest" error, you can run `npm run build` or ask the user to run `npm run dev` or `composer run dev`.

=== laravel/v12 rules ===

# Laravel 12

- CRITICAL: ALWAYS use `search-docs` tool for version-specific Laravel documentation and updated code examples.
- Since Laravel 11, Laravel has a new streamlined file structure which this project uses.

## Laravel 12 Structure

- In Laravel 12, middleware are no longer registered in `app/Http/Kernel.php`.
- Middleware are configured declaratively in `bootstrap/app.php` using `Application::configure()->withMiddleware()`.
- `bootstrap/app.php` is the file to register middleware, exceptions, and routing files.
- `bootstrap/providers.php` contains application specific service providers.
- The `app/Console/Kernel.php` file no longer exists; use `bootstrap/app.php` or `routes/console.php` for console configuration.
- Console commands in `app/Console/Commands/` are automatically available and do not require manual registration.

## Database

- When modifying a column, the migration must include all of the attributes that were previously defined on the column. Otherwise, they will be dropped and lost.
- Laravel 12 allows limiting eagerly loaded records natively, without external packages: `$query->latest()->limit(10);`.

### Models

- Casts can and likely should be set in a `casts()` method on a model rather than the `$casts` property. Follow existing conventions from other models.

=== wayfinder/core rules ===

# Laravel Wayfinder

Wayfinder generates TypeScript functions for Laravel routes. Import from `@/actions/` (controllers) or `@/routes/` (named routes).

- IMPORTANT: Activate `wayfinder-development` skill whenever referencing backend routes in frontend components.
- Invokable Controllers: `import StorePost from '@/actions/.../StorePostController'; StorePost()`.
- Parameter Binding: Detects route keys (`{post:slug}`) — `show({ slug: "my-post" })`.
- Query Merging: `show(1, { mergeQuery: { page: 2, sort: null } })` merges with current URL, `null` removes params.
- Inertia: Use `.form()` with `<Form>` component or `form.submit(store())` with useForm.

=== livewire/core rules ===

# Livewire

- Livewire allow to build dynamic, reactive interfaces in PHP without writing JavaScript.
- You can use Alpine.js for client-side interactions instead of JavaScript frameworks.
- Keep state server-side so the UI reflects it. Validate and authorize in actions as you would in HTTP requests.

=== pint/core rules ===

# Laravel Pint Code Formatter

- If you have modified any PHP files, you must run `vendor/bin/pint --dirty` before finalizing changes to ensure your code matches the project's expected style.
- Do not run `vendor/bin/pint --test`, simply run `vendor/bin/pint` to fix any formatting issues.

=== pest/core rules ===

## Pest

- This project uses Pest for testing. Create tests: `php artisan make:test --pest {name}`.
- Run tests: `php artisan test --compact` or filter: `php artisan test --compact --filter=testName`.
- Do NOT delete tests without approval.

=== inertia-react/core rules ===

# Inertia + React

- IMPORTANT: Activate `inertia-react-development` when working with Inertia React client-side patterns.

=== filament/filament rules ===

## Filament

- Filament is used by this application. Follow existing conventions for how and where it's implemented.
- Filament is a Server-Driven UI (SDUI) framework for Laravel that lets you define user interfaces in PHP using structured configuration objects. Built on Livewire, Alpine.js, and Tailwind CSS.
- Use the `search-docs` tool for official documentation on Artisan commands, code examples, testing, relationships, and idiomatic practices.

### Artisan

- Use Filament-specific Artisan commands to create files. Find them with `list-artisan-commands` or `php artisan --help`.
- Inspect required options and always pass `--no-interaction`.

### Patterns

Use static `make()` methods to initialize components. Most configuration methods accept a `Closure` for dynamic values.

Use `Get $get` to read other form field values for conditional logic:

<code-snippet name="Conditional form field" lang="php">
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;

Select::make('type')
    ->options(CompanyType::class)
    ->required()
    ->live(),

TextInput::make('company_name')
    ->required()
    ->visible(fn (Get $get): bool => $get('type') === 'business'),
</code-snippet>

Use `state()` with a `Closure` to compute derived column values:

<code-snippet name="Computed table column" lang="php">
use Filament\Tables\Columns\TextColumn;

TextColumn::make('full_name')
    ->state(fn (User $record): string => "{$record->first_name} {$record->last_name}"),
</code-snippet>

Actions encapsulate a button with optional modal form and logic:

<code-snippet name="Action with modal form" lang="php">
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;

Action::make('updateEmail')
    ->form([
        TextInput::make('email')->email()->required(),
    ])
    ->action(fn (array $data, User $record): void => $record->update($data)),
</code-snippet>

### Testing

Authenticate before testing panel functionality. Filament uses Livewire, so use `livewire()` or `Livewire::test()`:

<code-snippet name="Filament Table Test" lang="php">
    livewire(ListUsers::class)
        ->assertCanSeeTableRecords($users)
        ->searchTable($users->first()->name)
        ->assertCanSeeTableRecords($users->take(1))
        ->assertCanNotSeeTableRecords($users->skip(1));
</code-snippet>

<code-snippet name="Filament Create Resource Test" lang="php">
    livewire(CreateUser::class)
        ->fillForm([
            'name' => 'Test',
            'email' => 'test@example.com',
        ])
        ->call('create')
        ->assertNotified()
        ->assertRedirect();

    assertDatabaseHas(User::class, [
        'name' => 'Test',
        'email' => 'test@example.com',
    ]);
</code-snippet>

<code-snippet name="Testing Validation" lang="php">
    livewire(CreateUser::class)
        ->fillForm([
            'name' => null,
            'email' => 'invalid-email',
        ])
        ->call('create')
        ->assertHasFormErrors([
            'name' => 'required',
            'email' => 'email',
        ])
        ->assertNotNotified();
</code-snippet>

<code-snippet name="Calling Actions" lang="php">
    use Filament\Actions\DeleteAction;
    use Filament\Actions\Testing\TestAction;

    livewire(EditUser::class, ['record' => $user->id])
        ->callAction(DeleteAction::class)
        ->assertNotified()
        ->assertRedirect();

    livewire(ListUsers::class)
        ->callAction(TestAction::make('promote')->table($user), [
            'role' => 'admin',
        ])
        ->assertNotified();
</code-snippet>

### Common Mistakes

**Commonly Incorrect Namespaces:**
- Form fields (TextInput, Select, etc.): `Filament\Forms\Components\`
- Infolist entries (for read-only views) (TextEntry, IconEntry, etc.): `Filament\Infolists\Components\`
- Layout components (Grid, Section, Fieldset, Tabs, Wizard, etc.): `Filament\Schemas\Components\`
- Schema utilities (Get, Set, etc.): `Filament\Schemas\Components\Utilities\`
- Actions: `Filament\Actions\` (no `Filament\Tables\Actions\` etc.)
- Icons: `Filament\Support\Icons\Heroicon` enum (e.g., `Heroicon::PencilSquare`)

**Recent breaking changes to Filament:**
- File visibility is `private` by default. Use `->visibility('public')` for public access.
- `Grid`, `Section`, and `Fieldset` no longer span all columns by default.

=== laravel/fortify rules ===

## Laravel Fortify

Fortify is a headless authentication backend that provides authentication routes and controllers for Laravel applications.

**Before implementing any authentication features, use the `search-docs` tool to get the latest docs for that specific feature.**

### Configuration & Setup

- Check `config/fortify.php` to see what's enabled. Use `search-docs` for detailed information on specific features.
- Enable features by adding them to the `'features' => []` array: `Features::registration()`, `Features::resetPasswords()`, etc.
- To see the all Fortify registered routes, use the `list-routes` tool with the `only_vendor: true` and `action: "Fortify"` parameters.
- Fortify includes view routes by default (login, register). Set `'views' => false` in the configuration file to disable them if you're handling views yourself.

### Customization

- Views can be customized in `FortifyServiceProvider`'s `boot()` method using `Fortify::loginView()`, `Fortify::registerView()`, etc.
- Customize authentication logic with `Fortify::authenticateUsing()` for custom user retrieval / validation.
- Actions in `app/Actions/Fortify/` handle business logic (user creation, password reset, etc.). They're fully customizable, so you can modify them to change feature behavior.

## Available Features

- `Features::registration()` for user registration.
- `Features::emailVerification()` to verify new user emails.
- `Features::twoFactorAuthentication()` for 2FA with QR codes and recovery codes.
  - Add options: `['confirmPassword' => true, 'confirm' => true]` to require password confirmation and OTP confirmation before enabling 2FA.
- `Features::updateProfileInformation()` to let users update their profile.
- `Features::updatePasswords()` to let users change their passwords.
- `Features::resetPasswords()` for password reset via email.

</laravel-boost-guidelines>
