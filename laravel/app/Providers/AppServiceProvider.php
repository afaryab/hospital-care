<?php

namespace App\Providers;

use App\Models\Patient;
use App\Models\TransactionElement;
use App\Observers\PatientObserver;
use App\Observers\TransactionElementObserver;
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
        TransactionElement::observe(TransactionElementObserver::class);
    }
}
