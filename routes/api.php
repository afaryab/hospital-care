<?php

use App\Http\Controllers\Api\ExpenseCategoryController;
use App\Http\Controllers\Api\LookUpController;
use App\Http\Controllers\Api\PateintController;
use App\Http\Controllers\Api\ServiceOrderController;
use App\Http\Controllers\Api\TransactionController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/lookup', [LookUpController::class, 'index'])->name('lookup');

Route::post('/patients', [PateintController::class, 'index'])->name('api-patients-search');
Route::post('/patients/create', [PateintController::class, 'store'])->name('api-patients-store');
Route::post('/patients/edit/{id}', [PateintController::class, 'update'])->name('api-patients-edit');

Route::post('/expense-vouchers', [App\Http\Controllers\Api\ExpenseVoucherController::class, 'index'])->name('api-expense-vouchers-index');
Route::post('/expense-vouchers/search', [App\Http\Controllers\Api\ExpenseVoucherController::class, 'index'])->name('api-expense-vouchers-search');
Route::post('/expense-categories/search', [ExpenseCategoryController::class, 'index'])->name('api-expense-categories-search');
Route::post('/users/search', [UserController::class, 'index'])->name('api-users-search');
Route::post('/transactions/search', [TransactionController::class, 'index'])->name('api-transactions-search');
Route::post('/service-orders/search', [ServiceOrderController::class, 'index'])->name('api-service-orders-search');
Route::post('/service-orders/completed-unpaid', [ServiceOrderController::class, 'completedUnpaid'])->name('api-service-orders-completed-unpaid');
Route::post('/closings/search', [App\Http\Controllers\Api\ClosingController::class, 'index'])->name('api-closings-search');
