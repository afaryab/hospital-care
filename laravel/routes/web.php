<?php

use App\Http\Controllers\Migration\ImportController;
use App\Http\Controllers\Prints\ClosingStatementPdfPrintController;
use App\Http\Controllers\Prints\TransactionPdfPrintController;
use App\Http\Controllers\Reports\IncomeCashFlowReportController;
use App\Http\Controllers\WebController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;

// Route::get('/', function () {
//     return Inertia::render('welcome', [
//         'canRegister' => Features::enabled(Features::registration()),
//     ]);
// })->name('home');

Route::get('/import-old', [ImportController::class, 'index'])->name('import-old');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/', [WebController::class, 'index'])->name('home');

    /**
     * Global level
     */
    
    Route::get('PS', [WebController::class, 'register'])->name('patients-register');
    Route::get('PS/{year}', [WebController::class, 'register'])->name('patients-register-year');
    Route::get('PS/{year}/{month}', [WebController::class, 'register'])->name('patients-register-year-month');
    Route::get('PS/{year}/{month}/{number}', [WebController::class, 'patient'])->name('patients-register-ps-number');
    Route::get('PS/{year}/{month}/{number}/{departmentKey}', [WebController::class, 'patient'])->name('patients-register-ps-number-department');
    Route::get('PS/{year}/{month}/{number}/{departmentKey}/{serviceNumber}', [WebController::class, 'patient'])->name('patients-register-ps-number-department-service');

    
    /**
     * Counter routes
     */
    
    Route::get('CT-NEW', [WebController::class, 'counterOpen'])->name('counter-open');
    Route::post('CT-NEW', [WebController::class, 'counterStore'])->name('counter-store');
    Route::get('CT-CLOSE', [WebController::class, 'counterClose'])->name('counter-close');
    Route::post('CT-CLOSE', [WebController::class, 'counterClose'])->name('counter-close-post');
    Route::get('CT', [WebController::class, 'counter'])->name('counter');
    
    Route::get('CT/{ctYear}', [WebController::class, 'countersList'])->name('counters-year');
    Route::get('CT/{ctYear}/{ctMonth}', [WebController::class, 'countersList'])->name('counters-year-month');
    Route::get('CT/{ctYear}/{ctMonth}/{ctNumber}', [WebController::class, 'counterView'])->name('counter-view');

    Route::get('MY-CT-LIST', [WebController::class, 'userCountersList'])->name('my-counter-list');
    Route::get('MY-CT-LIST/{year}', [WebController::class, 'userCountersList'])->name('my-counter-list-year');
    Route::get('MY-CT-LIST/{year}/{month}', [WebController::class, 'userCountersList'])->name('my-counter-list-year-month');

    Route::get('CT-PS', [WebController::class, 'counterPatient'])->name('counter-select-patient');
    Route::get('CT-PS/{pYear}/{pMonth}/{number}', [WebController::class, 'counterPatient'])->name('counter-select-department');
    Route::get('CT-PS/{pYear}/{pMonth}/{number}/{departmentKey}', [WebController::class, 'counterPatient'])->name('counter-select-department-service');
    Route::get('CT-TR/{tYear}/{tMonth}/{tDay}/{tNumber}', [WebController::class, 'transactionView'])->name('transaction-view');

    Route::get('CT-EXP', [WebController::class, 'counterExpense'])->name('counter-expense');

    Route::post('TR-CREATE', [WebController::class, 'transactionStore'])->name('transaction-store');

    

    Route::get('appointments', [WebController::class, 'counter'])->name('appointments');
    Route::get('expenses', [WebController::class, 'counter'])->name('expenses');


    /**
     * Accounts routes
     */

    Route::get('ACC-CT-ALL', [WebController::class, 'countersList'])->name('counter-list-all');



    /**
     * Print routes (auth required for printing)
     */


    Route::get('PRINT/CT/{year}/{month}/{number}', [ClosingStatementPdfPrintController::class, 'stream'])
        ->name('print-closing-statement');

    Route::get('PRINT/TR/{year}/{month}/{day}/{number}', [TransactionPdfPrintController::class, 'stream'])
        ->name('print-transaction');
    
    Route::get('DOWNLOAD/TR/{year}/{month}/{day}/{number}', [TransactionPdfPrintController::class, 'download'])
        ->name('download-transaction');

    /**
     * Report routes
     */
    Route::get('reports/income-cash-flow', [IncomeCashFlowReportController::class, 'generate'])
        ->name('reports.income-cash-flow');
    
});

/**
 * Print routes (no auth required for printing)
 */

require __DIR__.'/settings.php';
