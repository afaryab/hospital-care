<?php

namespace App\Providers;

use App\Models\ExpenseVoucher;
use App\Models\Patient;
use App\Models\Transaction;
use App\Models\TransactionElement;
use App\Models\User;
use App\Observers\ExpenseVoucherObserver;
use App\Observers\PatientObserver;
use App\Observers\TransactionElementObserver;
use App\Observers\TransactionObserver;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        if ($this->app->environment('local') && class_exists(\Laravel\Telescope\TelescopeServiceProvider::class)) {
            $this->app->register(\Laravel\Telescope\TelescopeServiceProvider::class);
            $this->app->register(TelescopeServiceProvider::class);
        }
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

        Gate::define('viewPulse', function (User $user) {
            return $user->adminProfiles()->count() > 0;
        });
    }
}
