<?php

use App\Filament\Admin\Resources\ExpenseVouchers\Pages\CreateExpenseVoucher;
use App\Filament\Admin\Resources\ExpenseVouchers\Pages\EditExpenseVoucher;
use App\Filament\Admin\Resources\ExpenseVouchers\Pages\ListExpenseVouchers;
use App\Filament\Admin\Resources\ExpenseVouchers\Pages\ViewExpenseVoucher;
use App\Models\Administrator;
use App\Models\ExpenseVoucher;
use App\Models\ServiceOrder;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Facades\DB;

beforeEach(function () {
    $this->user = User::factory()->create();
    Administrator::create(['user_id' => $this->user->id, 'authority' => 'administrator']);
    $this->actingAs($this->user);
});

test('expense voucher list page renders', function () {
    Livewire\Livewire::test(ListExpenseVouchers::class)->assertSuccessful();
});

test('expense voucher create page renders', function () {
    Livewire\Livewire::test(CreateExpenseVoucher::class)->assertSuccessful();
});

test('expense voucher view page renders', function () {
    $voucher = ExpenseVoucher::factory()->create();
    Livewire\Livewire::test(ViewExpenseVoucher::class, ['record' => $voucher->getRouteKey()])->assertSuccessful();
});

test('expense voucher edit page renders', function () {
    $voucher = ExpenseVoucher::factory()->create();
    Livewire\Livewire::test(EditExpenseVoucher::class, ['record' => $voucher->getRouteKey()])->assertSuccessful();
});

test('the list table query count does not grow with the number of rows (no N+1)', function () {
    $makeVoucherWithRelations = function () {
        return ExpenseVoucher::factory()->create([
            'service_order_id' => ServiceOrder::factory(),
            'transaction_id' => Transaction::factory(),
        ]);
    };

    $makeVoucherWithRelations();

    DB::enableQueryLog();
    Livewire\Livewire::test(ListExpenseVouchers::class)->assertSuccessful();
    $queryCountForOneRow = count(DB::getQueryLog());
    DB::disableQueryLog();
    DB::flushQueryLog();

    $makeVoucherWithRelations();
    $makeVoucherWithRelations();
    $makeVoucherWithRelations();
    $makeVoucherWithRelations();

    DB::enableQueryLog();
    Livewire\Livewire::test(ListExpenseVouchers::class)->assertSuccessful();
    $queryCountForFiveRows = count(DB::getQueryLog());
    DB::disableQueryLog();

    // expCategory/serviceOrder.service/payedTo/transaction are eager loaded
    // via modifyQueryUsing() — without it, each relationship access below
    // would add ~4 queries per extra row (16+ for 4 extra rows), swamping
    // the small incidental variance (pagination/filter-option queries)
    // allowed here.
    expect($queryCountForFiveRows)->toBeLessThanOrEqual($queryCountForOneRow + 3);
});
