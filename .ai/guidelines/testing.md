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
