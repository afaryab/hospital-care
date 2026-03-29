<?php

use App\Filament\Admin\Resources\ExpenseVouchers\Pages\CreateExpenseVoucher;
use App\Filament\Admin\Resources\ExpenseVouchers\Pages\EditExpenseVoucher;
use App\Filament\Admin\Resources\ExpenseVouchers\Pages\ListExpenseVouchers;
use App\Filament\Admin\Resources\ExpenseVouchers\Pages\ViewExpenseVoucher;
use App\Models\Administrator;
use App\Models\ExpenseVoucher;
use App\Models\User;

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
