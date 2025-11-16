<?php

namespace App\Providers;

use App\Models\ExpenseVoucher;
use App\Models\Patient;
use App\Models\Transaction;
use App\Models\TransactionElement;
use App\Observers\ExpenseVoucherObserver;
use App\Observers\PatientObserver;
use App\Observers\TransactionElementObserver;
use App\Observers\TransactionObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register observers
        Patient::observe(PatientObserver::class);
        Transaction::observe(TransactionObserver::class);
        TransactionElement::observe(TransactionElementObserver::class);
        ExpenseVoucher::observe(ExpenseVoucherObserver::class);
    }
}
