<?php

use App\Filament\Admin\Resources\AdministrativeTransactions\Pages\CreateAdministrativeTransaction;
use App\Filament\Admin\Resources\AdministrativeTransactions\Pages\EditAdministrativeTransaction;
use App\Filament\Admin\Resources\AdministrativeTransactions\Pages\ListAdministrativeTransactions;
use App\Filament\Admin\Resources\AdministrativeTransactions\Pages\ViewAdministrativeTransaction;
use App\Models\Administrator;
use App\Models\ExpenseCategory;
use App\Models\Panel;
use App\Models\Patient;
use App\Models\PaymentMethod;
use App\Models\Receaveable;
use App\Models\Transaction;
use App\Models\User;
use App\Services\ReceivableSettlementService;
use Filament\Forms\Components\Repeater;
use Illuminate\Validation\ValidationException;

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

function panelReceivableSetup(): array
{
    $panel = Panel::factory()->create(['is_active' => true]);
    $method = PaymentMethod::factory()->create(['name' => 'Bank Transfer', 'slug' => 'BANK_TRANSFER']);
    $first = Receaveable::factory()->create(['panel_id' => $panel->id, 'amount' => 3000, 'orignal_amount' => 3000, 'status' => 'unpaid']);
    $second = Receaveable::factory()->create(['panel_id' => $panel->id, 'amount' => 2000, 'orignal_amount' => 2000, 'status' => 'unpaid']);

    return [$panel, $method, $first, $second];
}

test('admin can record a panel payment that settles several receivables', function () {
    $undoRepeaterFake = Repeater::fake();
    [$panel, $method, $first, $second] = panelReceivableSetup();

    Livewire\Livewire::test(CreateAdministrativeTransaction::class)
        ->fillForm([
            'income_or_expense' => 'INCOME',
            'income_type' => 'panel_receivable',
            'panel_id' => $panel->id,
            'allocations' => [
                ['receaveable_id' => $first->id, 'amount' => 3000],
                ['receaveable_id' => $second->id, 'amount' => 500],
            ],
            'payment_method_id' => $method->id,
            'notes' => 'Panel transfer for August',
        ])
        ->call('create')
        ->assertHasNoFormErrors()
        ->assertNotified()
        ->assertRedirect();

    $undoRepeaterFake();

    expect(Transaction::query()->where('receaveable_id', $first->id)->first())
        ->closing_id->toBeNull()
        ->type->toBe('ADMIN')
        ->income_or_expense->toBe('INCOME')
        ->panel_id->toBe($panel->id)
        ->payment_method_id->toBe($method->id)
        ->patient_id->toBe($first->patient_id);

    expect((float) Transaction::query()->where('receaveable_id', $second->id)->value('amount'))->toBe(500.0)
        ->and($first->fresh())->status->toBe('paid')
        ->and((float) $first->fresh()->amount)->toBe(0.0)
        ->and($second->fresh())->status->toBe('unpaid')
        ->and((float) $second->fresh()->amount)->toBe(1500.0);
});

test('a panel payment cannot allocate more than a receivable owes', function () {
    $undoRepeaterFake = Repeater::fake();
    [$panel, $method, $first] = panelReceivableSetup();

    Livewire\Livewire::test(CreateAdministrativeTransaction::class)
        ->fillForm([
            'income_or_expense' => 'INCOME',
            'income_type' => 'panel_receivable',
            'panel_id' => $panel->id,
            'allocations' => [
                ['receaveable_id' => $first->id, 'amount' => 3500],
            ],
            'payment_method_id' => $method->id,
        ])
        ->call('create')
        ->assertHasFormErrors();

    $undoRepeaterFake();

    expect(Transaction::query()->where('receaveable_id', $first->id)->exists())->toBeFalse()
        ->and((float) $first->fresh()->amount)->toBe(3000.0);
});

test('settlement service refuses receivables of another panel', function () {
    [$panel, , $first] = panelReceivableSetup();
    $otherPanel = Panel::factory()->create();

    expect(fn () => app(ReceivableSettlementService::class)
        ->settleForPanel($otherPanel->id, [['receaveable_id' => $first->id, 'amount' => 100]]))
        ->toThrow(ValidationException::class);

    expect((float) $first->fresh()->amount)->toBe(3000.0);
});

test('editing a panel payment amount moves the receivable balance', function () {
    [$panel, $method, $first] = panelReceivableSetup();
    $payment = app(ReceivableSettlementService::class)->settle($first, 1000, [
        'closing_id' => null,
        'type' => 'ADMIN',
        'created_by' => auth()->id(),
        'payment_method_id' => $method->id,
    ]);

    Livewire\Livewire::test(EditAdministrativeTransaction::class, ['record' => $payment->getRouteKey()])
        ->fillForm(['amount' => 1500])
        ->call('save')
        ->assertHasNoFormErrors();

    expect((float) $first->fresh()->amount)->toBe(1500.0);
});

test('general income still creates a single administrative transaction', function () {
    $method = PaymentMethod::factory()->create();

    Livewire\Livewire::test(CreateAdministrativeTransaction::class)
        ->fillForm([
            'income_or_expense' => 'INCOME',
            'income_type' => 'general',
            'amount' => 750,
            'payment_method_id' => $method->id,
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $this->assertDatabaseHas(Transaction::class, ['income_or_expense' => 'INCOME', 'amount' => 750, 'receaveable_id' => null, 'type' => 'ADMIN']);
});
