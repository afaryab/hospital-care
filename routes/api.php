<?php

use App\Http\Controllers\Api\ClosingController;
use App\Http\Controllers\Api\ExpenseCategoryController;
use App\Http\Controllers\Api\ExpenseVoucherController;
use App\Http\Controllers\Api\IndController;
use App\Http\Controllers\Api\LookUpController;
use App\Http\Controllers\Api\PateintController;
use App\Http\Controllers\Api\ServiceOrderController;
use App\Http\Controllers\Api\TransactionController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/lookup', [LookUpController::class, 'index'])->name('lookup');

    Route::post('/patients', [PateintController::class, 'index'])->name('api-patients-search');
    Route::post('/patients/create', [PateintController::class, 'store'])->name('api-patients-store');
    Route::post('/patients/edit/{id}', [PateintController::class, 'update'])->name('api-patients-edit');

    Route::post('/expense-vouchers', [ExpenseVoucherController::class, 'index'])->name('api-expense-vouchers-index');
    Route::post('/expense-vouchers/search', [ExpenseVoucherController::class, 'index'])->name('api-expense-vouchers-search');
    Route::post('/expense-categories/search', [ExpenseCategoryController::class, 'index'])->name('api-expense-categories-search');
    Route::post('/users/search', [UserController::class, 'index'])->name('api-users-search');
    Route::post('/transactions/search', [TransactionController::class, 'index'])->name('api-transactions-search');
    Route::post('/transactions/{transaction}/refund', [TransactionController::class, 'refund'])->name('api-transactions-refund');
    Route::post('/service-orders/search', [ServiceOrderController::class, 'index'])->name('api-service-orders-search');
    Route::post('/service-orders/completed-unpaid', [ServiceOrderController::class, 'completedUnpaid'])->name('api-service-orders-completed-unpaid');
    Route::post('/closings/search', [ClosingController::class, 'index'])->name('api-closings-search');

    // IND Doctor API routes
    Route::post('/ind/search', [IndController::class, 'search'])->name('api-ind-search');
    Route::get('/ind/ward-snapshot', [IndController::class, 'wardSnapshot'])->name('api-ind-ward-snapshot');
    Route::post('/ind/service-orders/{serviceOrder}/assign-bed', [IndController::class, 'assignBed'])->name('api-ind-assign-bed');
    Route::post('/ind/service-orders/{serviceOrder}/discharge', [IndController::class, 'discharge'])->name('api-ind-discharge');
    Route::post('/ind/service-orders/{serviceOrder}/treatment-record', [IndController::class, 'saveTreatmentRecord'])->name('api-ind-save-treatment');
    Route::patch('/ind/service-orders/{serviceOrder}/status', [IndController::class, 'updateStatus'])->name('api-ind-update-status');
});
