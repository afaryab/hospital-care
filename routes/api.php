<?php

use App\Http\Controllers\Api\LookUpController;
use App\Http\Controllers\Api\PateintController;
use Illuminate\Support\Facades\Route;

Route::get('/lookup', [LookUpController::class, 'index'])->name('lookup');

Route::post('/patients', [PateintController::class, 'index'])->name('api-patients-search');
Route::post('/patients/create', [PateintController::class, 'store'])->name('api-patients-store');
Route::post('/patients/edit/{id}', [PateintController::class, 'update'])->name('api-patients-edit');

Route::post('/expense-vouchers', [App\Http\Controllers\Api\ExpenseVoucherController::class, 'index'])->name('api-expense-vouchers-index');
