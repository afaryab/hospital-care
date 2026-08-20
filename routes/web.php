<?php

use App\Http\Controllers\DentistController;
use App\Http\Controllers\Dms\DocumentDownloadController;
use App\Http\Controllers\Dms\FolderZipController;
use App\Http\Controllers\Dms\PreparedZipDownloadController;
use App\Http\Controllers\Dms\PublicShareDownloadController;
use App\Http\Controllers\EmergencyDoctorController;
use App\Http\Controllers\IndDoctorController;
use App\Http\Controllers\LabController;
use App\Http\Controllers\Migration\ImportController;
use App\Http\Controllers\OnlyOffice\CallbackController;
use App\Http\Controllers\OnlyOffice\DocumentContentController;
use App\Http\Controllers\OnlyOffice\EditorPageController;
use App\Http\Controllers\OpdDoctorController;
use App\Http\Controllers\Prints\ClosingStatementPdfPrintController;
use App\Http\Controllers\Prints\ServiceOrderPdfPrintController;
use App\Http\Controllers\Prints\TransactionPdfPrintController;
use App\Http\Controllers\PublicCertificateController;
use App\Http\Controllers\Reports\BankPaymentReportController;
use App\Http\Controllers\Reports\GenericReportPdfController;
use App\Http\Controllers\Reports\IncomeCashFlowReportController;
use App\Http\Controllers\Reports\PanelPaymentReportController;
use App\Http\Controllers\UltrasoundController;
use App\Http\Controllers\WebController;
use App\Http\Controllers\XrayController;
use Illuminate\Support\Facades\Route;

Route::get('/import-old', [ImportController::class, 'index'])->name('import-old');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/', [WebController::class, 'index'])->name('home');

    /**
     * Doctor: own service orders & expense vouchers
     */
    Route::get('my-patients', [WebController::class, 'myPatients'])->name('my-patients');
    Route::get('my-payments', [WebController::class, 'myPayments'])->name('my-payments');

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

    // Route::get('CT/{ctYear}', [WebController::class, 'countersList'])->name('counters-year');
    // Route::get('CT/{ctYear}/{ctMonth}', [WebController::class, 'countersList'])->name('counters-year-month');
    Route::get('CT/{ctYear}/{ctMonth}/{ctNumber}', [WebController::class, 'counterView'])->name('counter-view');

    Route::get('MY-CT-LIST', [WebController::class, 'userCountersList'])->name('my-counter-list');
    Route::get('MY-CT-LIST/{year}', [WebController::class, 'userCountersList'])->name('my-counter-list-year');
    Route::get('MY-CT-LIST/{year}/{month}', [WebController::class, 'userCountersList'])->name('my-counter-list-year-month');

    Route::get('CT-PS', [WebController::class, 'counterPatient'])->name('counter-select-patient');
    Route::get('CT-PS/{pYear}/{pMonth}/{number}', [WebController::class, 'counterPatient'])->name('counter-select-department');
    Route::get('CT-PS/{pYear}/{pMonth}/{number}/{departmentKey}', [WebController::class, 'counterPatient'])->name('counter-select-department-service');
    Route::get('CT-TR/{tYear}/{tMonth}/{tDay}/{tNumber}', [WebController::class, 'transactionView']);
    Route::get('CT-TR/{tYear}/{tMonth}/{tDay}/{tNumber}/edit', [WebController::class, 'transactionEdit'])->name('transaction-edit');
    Route::get('CT-TR/edit', [WebController::class, 'transactionEdit'])->name('transaction-edit-search');

    Route::get('TR', [WebController::class, 'transactionView'])->name('transaction-search');
    Route::get('TR/{tYear}/{tMonth}/{tDay}/{tNumber}', [WebController::class, 'transactionView'])->name('transaction-view');

    Route::get('service-orders', [WebController::class, 'serviceOrdersOverview'])->name('service-orders-overview');
    Route::patch('service-orders/{serviceOrder}/status', [WebController::class, 'updateServiceOrderStatus'])->name('service-orders.update-status');

    Route::get('RECEAVEABLES', [WebController::class, 'receaveables'])->name('receaveables');

    Route::get('CT-EXP', [WebController::class, 'counterExpense'])->name('counter-expense');
    Route::get('CT-EXP-VOUCHER', [WebController::class, 'vouchersList'])->name('counter-expense-vouchers-list');
    Route::get('CT-USER-EXP-VOUCHER/NEW', [WebController::class, 'newVoucherForUser'])->name('counter-expense-new-user-voucher');
    Route::get('CT-DOCTOR-EXP-VOUCHER/NEW', [WebController::class, 'newVoucherForDoctor'])->name('counter-expense-new-doctor-voucher');
    Route::get('CT-EXP-VOUCHER/NEW', [WebController::class, 'newVoucher'])->name('counter-expense-new-voucher');
    Route::post('CT-EXP-VOUCHER/NEW', [WebController::class, 'storeVoucher'])->name('counter-expense-store-voucher');

    Route::post('TR-CREATE', [WebController::class, 'transactionStore'])->name('transaction-store');

    Route::post('RECEAVEABLES-PAYMENT', [WebController::class, 'receaveablesPayment'])->name('receaveables-payment');

    Route::post('APT-CREATE', [WebController::class, 'appointmentStore'])->name('appointment-store');
    Route::post('APT-CANCEL/{appointment}', [WebController::class, 'appointmentCancel'])->name('appointment-cancel');
    Route::get('appointments', [WebController::class, 'appointmentsCalendar'])->name('appointments-calendar');
    Route::get('expenses', [WebController::class, 'counter'])->name('expenses');

    /**
     * OPD Doctor routes
     */
    Route::get('OPD', [OpdDoctorController::class, 'index'])->name('opd-dashboard');
    Route::get('OPD/search', [OpdDoctorController::class, 'search'])->name('opd-search');
    Route::get('OPD/{id}', [OpdDoctorController::class, 'show'])->name('opd-patient');

    /**
     * IND Doctor routes
     */
    Route::get('IND', [IndDoctorController::class, 'index'])->name('ind-dashboard');
    Route::get('IND/{id}', [IndDoctorController::class, 'show'])->name('ind-patient');

    /**
     * Emergency Doctor routes
     */
    Route::get('EMG', [EmergencyDoctorController::class, 'index'])->name('emg-dashboard');
    Route::get('EMG/search', [EmergencyDoctorController::class, 'search'])->name('emg-search');
    Route::get('EMG/{id}', [EmergencyDoctorController::class, 'show'])->name('emg-patient');

    /**
     * Dental routes
     */
    Route::get('DNT', [DentistController::class, 'index'])->name('dnt-dashboard');
    Route::get('DNT/search', [DentistController::class, 'search'])->name('dnt-search');
    Route::get('DNT/{id}', [DentistController::class, 'show'])->name('dnt-patient');

    /**
     * Laboratory (Pathology) routes
     */
    Route::get('LAB', [LabController::class, 'index'])->name('lab-dashboard');
    Route::get('LAB/search', [LabController::class, 'search'])->name('lab-search');
    Route::get('LAB/{id}', [LabController::class, 'show'])->name('lab-patient');

    /**
     * Ultrasound routes
     */
    Route::get('ULT', [UltrasoundController::class, 'index'])->name('ult-dashboard');
    Route::get('ULT/search', [UltrasoundController::class, 'search'])->name('ult-search');
    Route::get('ULT/{id}', [UltrasoundController::class, 'show'])->name('ult-patient');

    /**
     * Radiology / X-Ray routes
     */
    Route::get('XRAY', [XrayController::class, 'index'])->name('xray-dashboard');
    Route::get('XRAY/search', [XrayController::class, 'search'])->name('xray-search');
    Route::get('XRAY/{id}', [XrayController::class, 'show'])->name('xray-patient');

    /**
     * Hospital Routes
     */
    Route::get('/que/opd', [WebController::class, 'opdQueue'])->name('hospital-opd-queue');
    Route::get('/que/indoor', [WebController::class, 'indoorQueue'])->name('hospital-indoor-queue');
    Route::get('/que/emergency', [WebController::class, 'emergencyQueue'])->name('hospital-emergency-queue');
    Route::get('/que/dental', [WebController::class, 'dentalQueue'])->name('hospital-dental-queue');
    Route::get('/que/lab', [WebController::class, 'laboratoryQueue'])->name('hospital-laboratory-queue');
    Route::get('/que/ultrasound', [WebController::class, 'ultrasoundQueue'])->name('hospital-ultrasound-queue');
    Route::get('/que/radiology', [WebController::class, 'radiologyQueue'])->name('hospital-radiology-queue');

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

    // Service Order PDF (stream)
    Route::get('PRINT/SO/{id}', [ServiceOrderPdfPrintController::class, 'stream'])
        ->name('print-serviceorder');

    /**
     * Report routes
     */
    Route::get('reports/income-cash-flow', [IncomeCashFlowReportController::class, 'generate'])
        ->name('reports.income-cash-flow');

    Route::get('reports/generic/income', [GenericReportPdfController::class, 'income'])
        ->name('reports.generic.income');
    Route::get('reports/generic/expense', [GenericReportPdfController::class, 'expense'])
        ->name('reports.generic.expense');
    Route::get('reports/generic/receivables', [GenericReportPdfController::class, 'receivables'])
        ->name('reports.generic.receivables');
    Route::get('reports/generic/services', [GenericReportPdfController::class, 'services'])
        ->name('reports.generic.services');
    Route::get('reports/generic/service-orders', [GenericReportPdfController::class, 'serviceOrders'])
        ->name('reports.generic.service-orders');
    Route::get('reports/generic/service-order/{id}', [GenericReportPdfController::class, 'serviceOrder'])
        ->name('reports.generic.service-order');
    Route::get('reports/generic/service-performance', [GenericReportPdfController::class, 'servicePerformance'])
        ->name('reports.generic.service-performance');
    Route::get('reports/generic/service-provider', [GenericReportPdfController::class, 'serviceProvider'])
        ->name('reports.generic.service-provider');

    // Bank & panel payment reports
    Route::get('reports/bank-payments/pending', [BankPaymentReportController::class, 'pending'])
        ->name('reports.bank-payments.pending');
    Route::get('reports/bank-payments/received', [BankPaymentReportController::class, 'received'])
        ->name('reports.bank-payments.received');
    Route::get('reports/panel-payments/pending', [PanelPaymentReportController::class, 'pending'])
        ->name('reports.panel-payments.pending');

    /**
     * Document Management System — session-authed actions. Folder/document
     * CRUD lives in the Filament admin page's Livewire actions; these are
     * the plain-HTTP endpoints browsers navigate to directly (downloads,
     * the OnlyOffice editor tab).
     */
    Route::get('dms/documents/{document:uuid}/download', DocumentDownloadController::class)
        ->name('dms.documents.download');
    Route::get('dms/folders/{folder:uuid}/zip', FolderZipController::class)
        ->name('dms.folders.zip');
    Route::get('onlyoffice/editor/{document:uuid}', EditorPageController::class)
        ->name('onlyoffice.editor');
});

/**
 * Document Management System — endpoints reached without a browser session:
 * hit by the OnlyOffice Document Server container itself (content fetch,
 * save callback), by a signed download-when-ready email link, or by an
 * externally shared document link. All are gated by a signed URL rather
 * than `auth`/`verified`, with the OnlyOffice pair additionally verifying
 * OnlyOffice's own JWT inside the controller.
 */
Route::get('onlyoffice/content/{document:uuid}', DocumentContentController::class)
    ->name('onlyoffice.content');
Route::post('onlyoffice/callback/{document:uuid}', CallbackController::class)
    ->name('onlyoffice.callback');
Route::get('dms/zip-download/{filename}', PreparedZipDownloadController::class)
    ->name('dms.folders.zip-download');
Route::get('dms/shares/{share}/download', PublicShareDownloadController::class)
    ->name('dms.shares.download');

/**
 * Print routes (no auth required for printing)
 */

// Public QR-code verification pages for Death/Birth certificates — gated by
// an unguessable token, not authentication. Short path on purpose: keeps the
// printed QR code small and easy to scan.
Route::get('v/dc/{token}', [PublicCertificateController::class, 'deathCertificate'])
    ->name('public-death-certificate');
Route::get('v/bc/{token}', [PublicCertificateController::class, 'birthCertificate'])
    ->name('public-birth-certificate');

require __DIR__.'/settings.php';
