<?php

use App\Http\Controllers\Api\ClosingController;
use App\Http\Controllers\Api\DepartmentController;
use App\Http\Controllers\Api\DrugController;
use App\Http\Controllers\Api\ExpenseCategoryController;
use App\Http\Controllers\Api\ExpenseVoucherController;
use App\Http\Controllers\Api\Icd10CodeController;
use App\Http\Controllers\Api\IndController;
use App\Http\Controllers\Api\LookUpController;
use App\Http\Controllers\Api\OpdController;
use App\Http\Controllers\Api\PateintController;
use App\Http\Controllers\Api\ServiceOrderController;
use App\Http\Controllers\Api\TransactionController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/lookup', [LookUpController::class, 'index'])->name('lookup');
    Route::get('/icd10-codes', [Icd10CodeController::class, 'index'])->name('api-icd10-codes');
    Route::get('/drugs/search', [DrugController::class, 'search'])->name('api-drugs-search');

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

    Route::get('/attachments/{attachment}', [DepartmentController::class, 'showAttachment'])->name('api-attachments-show');

    // IND Doctor API routes
    Route::post('/ind/search', [IndController::class, 'search'])->name('api-ind-search');
    Route::get('/ind/ward-snapshot', [IndController::class, 'wardSnapshot'])->name('api-ind-ward-snapshot');
    Route::post('/ind/service-orders/{serviceOrder}/assign-bed', [IndController::class, 'assignBed'])->name('api-ind-assign-bed');
    Route::post('/ind/service-orders/{serviceOrder}/discharge', [IndController::class, 'discharge'])->name('api-ind-discharge');
    Route::post('/ind/service-orders/{serviceOrder}/treatment-record', [IndController::class, 'saveTreatmentRecord'])->name('api-ind-save-treatment');
    Route::patch('/ind/service-orders/{serviceOrder}/status', [IndController::class, 'updateStatus'])->name('api-ind-update-status');

    // OPD Doctor API routes
    Route::post('/opd/search', [OpdController::class, 'search'])->name('api-opd-search');
    Route::get('/opd/my-queue', [OpdController::class, 'myQueue'])->name('api-opd-my-queue');
    Route::post('/opd/service-orders/{serviceOrder}/treatment-record', [OpdController::class, 'saveTreatmentRecord'])->name('api-opd-save-treatment');
    Route::patch('/opd/service-orders/{serviceOrder}/status', [OpdController::class, 'updateStatus'])->name('api-opd-update-status');
    Route::post('/closings/search', [ClosingController::class, 'index'])->name('api-closings-search');

    // Shared treatment-record API for EMG, DNT, LAB, ULT, XRAY
    Route::get('/emg/my-queue', [DepartmentController::class, 'myQueue'])->name('api-emg-my-queue');
    Route::post('/emg/search', [DepartmentController::class, 'search'])->name('api-emg-search');
    Route::post('/emg/service-orders/{serviceOrder}/treatment-record', [DepartmentController::class, 'saveTreatmentRecord'])->name('api-emg-save-treatment');
    Route::patch('/emg/service-orders/{serviceOrder}/status', [DepartmentController::class, 'updateStatus'])->name('api-emg-update-status');
    Route::post('/emg/service-orders/{serviceOrder}/attachments', [DepartmentController::class, 'uploadAttachment'])->name('api-emg-upload-attachment');
    Route::delete('/emg/attachments/{attachment}', [DepartmentController::class, 'deleteAttachment'])->name('api-emg-delete-attachment');

    Route::get('/dnt/my-queue', [DepartmentController::class, 'myQueue'])->name('api-dnt-my-queue');
    Route::post('/dnt/search', [DepartmentController::class, 'search'])->name('api-dnt-search');
    Route::post('/dnt/service-orders/{serviceOrder}/treatment-record', [DepartmentController::class, 'saveTreatmentRecord'])->name('api-dnt-save-treatment');
    Route::patch('/dnt/service-orders/{serviceOrder}/status', [DepartmentController::class, 'updateStatus'])->name('api-dnt-update-status');
    Route::post('/dnt/service-orders/{serviceOrder}/attachments', [DepartmentController::class, 'uploadAttachment'])->name('api-dnt-upload-attachment');
    Route::delete('/dnt/attachments/{attachment}', [DepartmentController::class, 'deleteAttachment'])->name('api-dnt-delete-attachment');

    Route::get('/lab/my-queue', [DepartmentController::class, 'myQueue'])->name('api-lab-my-queue');
    Route::post('/lab/search', [DepartmentController::class, 'search'])->name('api-lab-search');
    Route::post('/lab/service-orders/{serviceOrder}/treatment-record', [DepartmentController::class, 'saveTreatmentRecord'])->name('api-lab-save-treatment');
    Route::patch('/lab/service-orders/{serviceOrder}/status', [DepartmentController::class, 'updateStatus'])->name('api-lab-update-status');
    Route::post('/lab/service-orders/{serviceOrder}/attachments', [DepartmentController::class, 'uploadAttachment'])->name('api-lab-upload-attachment');
    Route::delete('/lab/attachments/{attachment}', [DepartmentController::class, 'deleteAttachment'])->name('api-lab-delete-attachment');

    Route::get('/ult/my-queue', [DepartmentController::class, 'myQueue'])->name('api-ult-my-queue');
    Route::post('/ult/search', [DepartmentController::class, 'search'])->name('api-ult-search');
    Route::post('/ult/service-orders/{serviceOrder}/treatment-record', [DepartmentController::class, 'saveTreatmentRecord'])->name('api-ult-save-treatment');
    Route::patch('/ult/service-orders/{serviceOrder}/status', [DepartmentController::class, 'updateStatus'])->name('api-ult-update-status');
    Route::post('/ult/service-orders/{serviceOrder}/attachments', [DepartmentController::class, 'uploadAttachment'])->name('api-ult-upload-attachment');
    Route::delete('/ult/attachments/{attachment}', [DepartmentController::class, 'deleteAttachment'])->name('api-ult-delete-attachment');

    Route::get('/xray/my-queue', [DepartmentController::class, 'myQueue'])->name('api-xray-my-queue');
    Route::post('/xray/search', [DepartmentController::class, 'search'])->name('api-xray-search');
    Route::post('/xray/service-orders/{serviceOrder}/treatment-record', [DepartmentController::class, 'saveTreatmentRecord'])->name('api-xray-save-treatment');
    Route::patch('/xray/service-orders/{serviceOrder}/status', [DepartmentController::class, 'updateStatus'])->name('api-xray-update-status');
    Route::post('/xray/service-orders/{serviceOrder}/attachments', [DepartmentController::class, 'uploadAttachment'])->name('api-xray-upload-attachment');
    Route::delete('/xray/attachments/{attachment}', [DepartmentController::class, 'deleteAttachment'])->name('api-xray-delete-attachment');
});
