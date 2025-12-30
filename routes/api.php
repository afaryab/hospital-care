<?php

use App\Http\Controllers\Api\PateintController;
use Illuminate\Support\Facades\Route;

Route::post('/patients', [PateintController::class, 'index'])->name('api-patients-search');
Route::post('/patients/create', [PateintController::class, 'store'])->name('api-patients-store');

Route::post('/expense-vouchers', [App\Http\Controllers\Api\ExpenseVoucherController::class, 'index'])->name('api-expense-vouchers-index');
