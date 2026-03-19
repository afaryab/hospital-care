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
