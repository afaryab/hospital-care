<?php

use App\Http\Controllers\Migration\ImportController;
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

    
    /**
     * Counter routes
     */
    
    Route::get('CT-NEW', [WebController::class, 'counterOpen'])->name('counter-open');
    Route::post('CT-NEW', [WebController::class, 'counterStore'])->name('counter-store');
    Route::get('CT-CLOSE', [WebController::class, 'counterClose'])->name('counter-close');
    Route::get('CT', [WebController::class, 'counter'])->name('counter');
    
    Route::get('CT/{ctYear}', [WebController::class, 'countersList'])->name('counters-year');
    Route::get('CT/{ctYear}/{ctMonth}', [WebController::class, 'countersList'])->name('counters-year-month');
    Route::get('CT/{ctYear}/{ctMonth}/{ctNumber}', [WebController::class, 'counterView'])->name('counter-view');

    Route::get('MY-CT-LIST', [WebController::class, 'countersList'])->name('counter-list');

    Route::get('CT-PS', [WebController::class, 'counterPatient'])->name('counter-select-patient');
    Route::get('CT-PS/{pYear}/{pMonth}/{number}', [WebController::class, 'counterPatient'])->name('counter-select-department');
    Route::get('CT-PS/{pYear}/{pMonth}/{number}/{departmentKey}', [WebController::class, 'counterPatient'])->name('counter-select-department-service');
    Route::get('CT-PS/{pYear}/{pMonth}/{number}/RECES-{departmentKey}', [WebController::class, 'counterPatient'])->name('counter-select-department-recistation');

    Route::get('TR/{pYear}/{pMonth}/{number}', [WebController::class, 'counter'])->name('transaction-view');

    Route::get('appointments', [WebController::class, 'counter'])->name('appointments');
    Route::get('expenses', [WebController::class, 'counter'])->name('expenses');


    /**
     * Accounts routes
     */

    Route::get('ACC-CT-ALL', [WebController::class, 'countersList'])->name('counter-list-all');
    
});

require __DIR__.'/settings.php';
