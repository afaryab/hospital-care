<?php

use App\Models\Closing;
use App\Models\ExpenseCategory;
use App\Models\HospitalSetting;
use App\Models\Receaveable;
use App\Models\Transaction;
use App\Services\AbacusClosingService;
use Processton\Abacus\Models\AbacusChartOfAccount;
use Processton\Abacus\Models\AbacusIncoming;
use Processton\AbacusDatabase\Seeders\ChartOfAccountsSeeder;

beforeEach(function () {
    (new ChartOfAccountsSeeder)->run();
});

test('abacus sync command requires ENABLE_OLD_SYNC env variable', function () {
    $closing = Closing::factory()->create();
    Transaction::factory()->count(3)->create([
        'closing_id' => $closing->id,
        'income_or_expense' => 'INCOME',
        'amount' => 1000,
    ]);

    $this->artisan('app:sync-old-hims', ['--entity' => 'abacus-closings'])
        ->assertExitCode(1);
});

test('abacus incoming links to closing via morph relation', function () {
    $closing = Closing::factory()->create();

    $incoming = AbacusIncoming::create([
        'source_type' => $closing->getMorphClass(),
        'source_id' => $closing->id,
        'reference' => $closing->ct_number,
        'date' => now()->toDateString(),
        'description' => "Closing {$closing->ct_number}",
        'amount' => 5000,
    ]);

    expect($incoming->source_id)->toBe($closing->id);
    expect($incoming->source_type)->toBe($closing->getMorphClass());
    expect($incoming->source)->toBeInstanceOf(Closing::class);
    expect($closing->fresh()->abacusIncoming)->toBeInstanceOf(AbacusIncoming::class);
});

test('closing has abacusIncoming relationship', function () {
    $closing = Closing::factory()->create();

    expect($closing->abacusIncoming)->toBeNull();

    AbacusIncoming::create([
        'source_type' => $closing->getMorphClass(),
        'source_id' => $closing->id,
        'reference' => $closing->ct_number,
        'date' => now()->toDateString(),
        'description' => 'Test',
        'amount' => 1000,
    ]);

    expect($closing->fresh()->abacusIncoming)->not->toBeNull();
});

test('pakistan financial year is correctly calculated for july-december dates', function () {
    $service = new AbacusClosingService;
    $year = $service->resolveOrCreateFinancialYear(now()->setDate(2025, 10, 15));

    expect($year)->not->toBeNull();
    expect($year->start_date->format('Y-m-d'))->toBe('2025-07-01');
    expect($year->end_date->format('Y-m-d'))->toBe('2026-06-30');
});

test('pakistan financial year is correctly calculated for january-june dates', function () {
    $service = new AbacusClosingService;
    $year = $service->resolveOrCreateFinancialYear(now()->setDate(2026, 3, 15));

    expect($year)->not->toBeNull();
    expect($year->start_date->format('Y-m-d'))->toBe('2025-07-01');
    expect($year->end_date->format('Y-m-d'))->toBe('2026-06-30');
});

test('chart of accounts seeder creates essential accounts including refunds and discounts', function () {
    expect(AbacusChartOfAccount::where('code', '1110')->exists())->toBeTrue();
    expect(AbacusChartOfAccount::where('code', '1130')->exists())->toBeTrue();
    expect(AbacusChartOfAccount::where('code', '4200')->exists())->toBeTrue();
    expect(AbacusChartOfAccount::where('code', '5010')->exists())->toBeTrue();
    expect(AbacusChartOfAccount::where('code', '5020')->exists())->toBeTrue();
    expect(AbacusChartOfAccount::where('code', '5900')->exists())->toBeTrue();
});

test('service creates balanced entries for cash sales', function () {
    $closing = Closing::factory()->create();
    Transaction::factory()->count(3)->create([
        'closing_id' => $closing->id,
        'income_or_expense' => 'INCOME',
        'amount' => 1000,
    ]);

    $service = new AbacusClosingService;
    $incoming = $service->createEntriesForClosing($closing);

    expect($incoming)->not->toBeNull();
    expect($incoming->isBalanced())->toBeTrue();
    expect($incoming->total_debits)->toBe(3000.0);
    expect($incoming->total_credits)->toBe(3000.0);

    // Cash sales: Dr Cash 1110, Cr Service Revenue 4200
    $cashAccount = AbacusChartOfAccount::where('code', '1110')->first();
    $revenueAccount = AbacusChartOfAccount::where('code', '4200')->first();

    $debit = $incoming->transactions()->where('abacus_chart_of_account_id', $cashAccount->id)->where('entry_type', 'debit')->first();
    $credit = $incoming->transactions()->where('abacus_chart_of_account_id', $revenueAccount->id)->where('entry_type', 'credit')->first();

    expect($debit->amount)->toBe('3000.00');
    expect($credit->amount)->toBe('3000.00');
});

test('service creates entries for credit sales with receivables', function () {
    $closing = Closing::factory()->create();
    $transaction = Transaction::factory()->create([
        'closing_id' => $closing->id,
        'income_or_expense' => 'INCOME',
        'amount' => 5000,
        'customer_payed' => 3000,
    ]);

    // Receivable created from this transaction (patient underpaid by 2000)
    Receaveable::factory()->create([
        'transaction_id' => $transaction->id,
        'patient_id' => $transaction->patient_id,
        'amount' => 2000,
        'orignal_amount' => 2000,
        'status' => 'unpaid',
    ]);

    $service = new AbacusClosingService;
    $incoming = $service->createEntriesForClosing($closing);

    expect($incoming)->not->toBeNull();
    expect($incoming->isBalanced())->toBeTrue();

    // Dr Cash 3000 + Dr A/R 2000 = Cr Revenue 5000
    $cashAccount = AbacusChartOfAccount::where('code', '1110')->first();
    $arAccount = AbacusChartOfAccount::where('code', '1130')->first();
    $revenueAccount = AbacusChartOfAccount::where('code', '4200')->first();

    $cashDebit = $incoming->debits()->where('abacus_chart_of_account_id', $cashAccount->id)->sum('amount');
    $arDebit = $incoming->debits()->where('abacus_chart_of_account_id', $arAccount->id)->sum('amount');
    $revenueCredit = $incoming->credits()->where('abacus_chart_of_account_id', $revenueAccount->id)->sum('amount');

    expect((float) $cashDebit)->toBe(3000.0);
    expect((float) $arDebit)->toBe(2000.0);
    expect((float) $revenueCredit)->toBe(5000.0);
});

test('service creates entries for receivable payments', function () {
    $closing = Closing::factory()->create();

    // An income transaction that pays off an existing receivable
    $receaveable = Receaveable::factory()->create([
        'amount' => 2000,
        'orignal_amount' => 2000,
        'status' => 'unpaid',
    ]);

    Transaction::factory()->create([
        'closing_id' => $closing->id,
        'income_or_expense' => 'INCOME',
        'amount' => 2000,
        'receaveable_id' => $receaveable->id,
    ]);

    $service = new AbacusClosingService;
    $incoming = $service->createEntriesForClosing($closing);

    expect($incoming)->not->toBeNull();
    expect($incoming->isBalanced())->toBeTrue();

    // Dr Cash 2000, Cr A/R 2000
    $cashAccount = AbacusChartOfAccount::where('code', '1110')->first();
    $arAccount = AbacusChartOfAccount::where('code', '1130')->first();

    $cashDebit = $incoming->debits()->where('abacus_chart_of_account_id', $cashAccount->id)->sum('amount');
    $arCredit = $incoming->credits()->where('abacus_chart_of_account_id', $arAccount->id)->sum('amount');

    expect((float) $cashDebit)->toBe(2000.0);
    expect((float) $arCredit)->toBe(2000.0);
});

test('service creates entries for regular expenses', function () {
    $closing = Closing::factory()->create();
    Transaction::factory()->create([
        'closing_id' => $closing->id,
        'income_or_expense' => 'EXPENSE',
        'amount' => 1500,
    ]);

    $service = new AbacusClosingService;
    $incoming = $service->createEntriesForClosing($closing);

    expect($incoming)->not->toBeNull();
    expect($incoming->isBalanced())->toBeTrue();

    // Dr Other Expenses 5900, Cr Cash 1110
    $expenseAccount = AbacusChartOfAccount::where('code', '5900')->first();
    $cashAccount = AbacusChartOfAccount::where('code', '1110')->first();

    $expenseDebit = $incoming->debits()->where('abacus_chart_of_account_id', $expenseAccount->id)->sum('amount');
    $cashCredit = $incoming->credits()->where('abacus_chart_of_account_id', $cashAccount->id)->sum('amount');

    expect((float) $expenseDebit)->toBe(1500.0);
    expect((float) $cashCredit)->toBe(1500.0);
});

test('service creates entries for refund expenses', function () {
    $refundCategory = ExpenseCategory::factory()->create(['type' => 'RFND']);
    $closing = Closing::factory()->create();
    Transaction::factory()->create([
        'closing_id' => $closing->id,
        'income_or_expense' => 'EXPENSE',
        'amount' => 800,
        'expense_category_id' => $refundCategory->id,
    ]);

    $service = new AbacusClosingService;
    $incoming = $service->createEntriesForClosing($closing);

    expect($incoming)->not->toBeNull();
    expect($incoming->isBalanced())->toBeTrue();

    // Dr Refunds 5010, Cr Cash 1110
    $refundAccount = AbacusChartOfAccount::where('code', '5010')->first();
    $cashAccount = AbacusChartOfAccount::where('code', '1110')->first();

    $refundDebit = $incoming->debits()->where('abacus_chart_of_account_id', $refundAccount->id)->sum('amount');
    $cashCredit = $incoming->credits()->where('abacus_chart_of_account_id', $cashAccount->id)->sum('amount');

    expect((float) $refundDebit)->toBe(800.0);
    expect((float) $cashCredit)->toBe(800.0);
});

test('service creates entries for discount expenses', function () {
    $discountCategory = ExpenseCategory::factory()->create(['type' => 'DISC']);
    $closing = Closing::factory()->create();
    Transaction::factory()->create([
        'closing_id' => $closing->id,
        'income_or_expense' => 'EXPENSE',
        'amount' => 500,
        'expense_category_id' => $discountCategory->id,
    ]);

    $service = new AbacusClosingService;
    $incoming = $service->createEntriesForClosing($closing);

    expect($incoming)->not->toBeNull();
    expect($incoming->isBalanced())->toBeTrue();

    // Dr Discounts 5020, Cr Cash 1110
    $discountAccount = AbacusChartOfAccount::where('code', '5020')->first();
    $cashAccount = AbacusChartOfAccount::where('code', '1110')->first();

    $discountDebit = $incoming->debits()->where('abacus_chart_of_account_id', $discountAccount->id)->sum('amount');
    $cashCredit = $incoming->credits()->where('abacus_chart_of_account_id', $cashAccount->id)->sum('amount');

    expect((float) $discountDebit)->toBe(500.0);
    expect((float) $cashCredit)->toBe(500.0);
});

test('service creates balanced entries for mixed transaction types', function () {
    $refundCategory = ExpenseCategory::factory()->create(['type' => 'RFND']);
    $discountCategory = ExpenseCategory::factory()->create(['type' => 'DISC']);
    $closing = Closing::factory()->create();

    // Cash sale
    Transaction::factory()->create([
        'closing_id' => $closing->id,
        'income_or_expense' => 'INCOME',
        'amount' => 5000,
    ]);

    // Receivable payment
    $receaveable = Receaveable::factory()->create(['amount' => 2000, 'orignal_amount' => 2000, 'status' => 'unpaid']);
    Transaction::factory()->create([
        'closing_id' => $closing->id,
        'income_or_expense' => 'INCOME',
        'amount' => 2000,
        'receaveable_id' => $receaveable->id,
    ]);

    // Regular expense
    Transaction::factory()->create([
        'closing_id' => $closing->id,
        'income_or_expense' => 'EXPENSE',
        'amount' => 1000,
    ]);

    // Refund
    Transaction::factory()->create([
        'closing_id' => $closing->id,
        'income_or_expense' => 'EXPENSE',
        'amount' => 300,
        'expense_category_id' => $refundCategory->id,
    ]);

    // Discount
    Transaction::factory()->create([
        'closing_id' => $closing->id,
        'income_or_expense' => 'EXPENSE',
        'amount' => 200,
        'expense_category_id' => $discountCategory->id,
    ]);

    $service = new AbacusClosingService;
    $incoming = $service->createEntriesForClosing($closing);

    expect($incoming)->not->toBeNull();
    expect($incoming->isBalanced())->toBeTrue();

    // Total debits = total credits
    $totalDebits = (float) $incoming->debits()->sum('amount');
    $totalCredits = (float) $incoming->credits()->sum('amount');
    expect($totalDebits)->toBe($totalCredits);
});

test('service skips closing that already has abacus incoming', function () {
    $closing = Closing::factory()->create();
    Transaction::factory()->create([
        'closing_id' => $closing->id,
        'income_or_expense' => 'INCOME',
        'amount' => 1000,
    ]);

    $service = new AbacusClosingService;
    $first = $service->createEntriesForClosing($closing);
    expect($first)->not->toBeNull();

    $second = $service->createEntriesForClosing($closing->fresh());
    expect($second)->toBeNull();

    expect(AbacusIncoming::where('source_type', $closing->getMorphClass())->where('source_id', $closing->id)->count())->toBe(1);
});

test('service returns null for closing with no transactions', function () {
    $closing = Closing::factory()->create();

    $service = new AbacusClosingService;
    $incoming = $service->createEntriesForClosing($closing);

    expect($incoming)->toBeNull();
});

test('auto map setting can be toggled', function () {
    expect(AbacusClosingService::isAutoMapEnabled())->toBeFalse();

    HospitalSetting::set('abacus_auto_map_accounts', true);
    expect(AbacusClosingService::isAutoMapEnabled())->toBeTrue();

    HospitalSetting::set('abacus_auto_map_accounts', false);
    expect(AbacusClosingService::isAutoMapEnabled())->toBeFalse();
});
