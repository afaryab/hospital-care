<?php

use App\Filament\Admin\Resources\AdministrativeTransactions\Pages\CreateAdministrativeTransaction;
use App\Filament\Admin\Resources\AdministrativeTransactions\Pages\EditAdministrativeTransaction;
use App\Filament\Admin\Resources\AdministrativeTransactions\Pages\ListAdministrativeTransactions;
use App\Filament\Admin\Resources\AdministrativeTransactions\Pages\ViewAdministrativeTransaction;
use App\Models\Administrator;
use App\Models\ExpenseCategory;
use App\Models\Patient;
use App\Models\PaymentMethod;
use App\Models\Transaction;
use App\Models\User;

use function Pest\Laravel\actingAs;

beforeEach(function () {
    $user = User::factory()->create();
    Administrator::create(['user_id' => $user->id, 'authority' => 'administrator']);
    actingAs($user);
});

test('admin can list administrative transactions', function () {
    $adminTr = Transaction::factory()->administrative()->expense()->create();
    $counterTr = Transaction::factory()->create(); // has closing_id, should not appear

    Livewire\Livewire::test(ListAdministrativeTransactions::class)
        ->assertSuccessful()
        ->assertCanSeeTableRecords([$adminTr])
        ->assertCanNotSeeTableRecords([$counterTr]);
});

test('admin can create an administrative expense transaction', function () {
    $category = ExpenseCategory::factory()->create(['name' => 'Test Category']);
    $method = PaymentMethod::factory()->create(['name' => 'Cash', 'slug' => 'CASH']);

    Livewire\Livewire::test(CreateAdministrativeTransaction::class)
        ->fillForm([
            'income_or_expense' => 'EXPENSE',
            'expense_category_id' => $category->id,
            'amount' => 1500,
            'payment_method_id' => $method->id,
            'notes' => 'Office supplies',
        ])
        ->call('create')
        ->assertNotified()
        ->assertRedirect();

    $this->assertDatabaseHas(Transaction::class, [
        'income_or_expense' => 'EXPENSE',
        'expense_category_id' => $category->id,
        'amount' => 1500,
        'payment_method_id' => $method->id,
        'closing_id' => null,
        'type' => 'ADMIN',
    ]);
});

test('admin can view an administrative transaction', function () {
    $tr = Transaction::factory()->administrative()->expense()->create([
        'tr_number' => 'TR/2026/05/01/9901',
        'amount' => 2500,
    ]);

    Livewire\Livewire::test(ViewAdministrativeTransaction::class, ['record' => $tr->getRouteKey()])
        ->assertSuccessful()
        ->assertSee('TR/2026/05/01/9901');
});

test('admin can edit an administrative transaction notes', function () {
    $method = PaymentMethod::factory()->create();
    $tr = Transaction::factory()->administrative()->expense()->create([
        'notes' => 'old note',
        'payment_method_id' => $method->id,
    ]);

    Livewire\Livewire::test(EditAdministrativeTransaction::class, ['record' => $tr->getRouteKey()])
        ->fillForm(['notes' => 'updated note', 'payment_method_id' => $method->id])
        ->call('save')
        ->assertHasNoFormErrors();

    $this->assertDatabaseHas(Transaction::class, [
        'id' => $tr->id,
        'notes' => 'updated note',
        'closing_id' => null,
    ]);
});

test('the patient field does not eagerly load every patient into the create form', function () {
    // The old ->options(fn () => Patient::query()->...->pluck(...)) pattern
    // embedded every patient's name directly in the initial Livewire
    // payload. A lazy-search field fetches nothing until the user types,
    // so none of these names should appear on first render.
    $patients = Patient::factory()->count(20)->create();

    $response = Livewire\Livewire::test(CreateAdministrativeTransaction::class);

    foreach ($patients as $patient) {
        $response->assertDontSee($patient->name);
    }
});

test('creating an administrative transaction still works when a patient is attached', function () {
    $patient = Patient::factory()->create(['name' => 'Ayesha Khan']);
    $method = PaymentMethod::factory()->create(['name' => 'Cash', 'slug' => 'CASH']);

    Livewire\Livewire::test(CreateAdministrativeTransaction::class)
        ->fillForm([
            'income_or_expense' => 'INCOME',
            'patient_id' => $patient->id,
            'amount' => 500,
            'payment_method_id' => $method->id,
        ])
        ->call('create')
        ->assertNotified()
        ->assertRedirect();

    expect(Transaction::query()->where('patient_id', $patient->id)->exists())->toBeTrue();
});

test('creating administrative transaction requires amount and payment method', function () {
    Livewire\Livewire::test(CreateAdministrativeTransaction::class)
        ->fillForm([
            'amount' => null,
            'payment_method_id' => null,
        ])
        ->call('create')
        ->assertHasFormErrors(['amount' => 'required', 'payment_method_id' => 'required'])
        ->assertNotNotified();
});

test('administrative transactions list filters by direction', function () {
    $expense = Transaction::factory()->administrative()->expense()->create();
    $income = Transaction::factory()->administrative()->create(['income_or_expense' => 'INCOME']);

    Livewire\Livewire::test(ListAdministrativeTransactions::class)
        ->filterTable('income_or_expense', 'EXPENSE')
        ->assertCanSeeTableRecords([$expense])
        ->assertCanNotSeeTableRecords([$income]);
});
