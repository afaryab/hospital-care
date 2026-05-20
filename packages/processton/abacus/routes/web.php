<?php

use Illuminate\Support\Facades\Route;
use Processton\Abacus\Filament\Pages\BalanceSheet;
use Processton\Abacus\Filament\Pages\CashFlowStatement;
use Processton\Abacus\Filament\Pages\GeneralLedger;
use Processton\Abacus\Filament\Pages\ProfitLossStatement;
use Processton\Abacus\Filament\Pages\TrialBalance;

Route::middleware([
    'web',
])->group(function () {

    Route::middleware([
        'auth',
        'verified',
    ])->prefix('abacus')->group(function () {

        Route::get('/general-ledger', [GeneralLedger::class, 'streamPdf'])->name('general-ledger.stream-pdf');

        Route::get('/trial-balance', [TrialBalance::class, 'streamPdf'])->name('trial-balance.stream-pdf');

        Route::get('/profit-loss-statement', [ProfitLossStatement::class, 'streamPdf'])->name('profit-loss-statement.stream-pdf');

        Route::get('/balance-sheet', [BalanceSheet::class, 'streamPdf'])->name('balance-sheet.stream-pdf');

        Route::get('/cash-flow-statement', [CashFlowStatement::class, 'streamPdf'])->name('cash-flow-statement.stream-pdf');

    });
});
