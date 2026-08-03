<?php

namespace App\Providers;

use App\Helpers\UserTimezone;
use App\Models\Appointment;
use App\Models\Asset;
use App\Models\Closing;
use App\Models\ExpenseVoucher;
use App\Models\Patient;
use App\Models\PurchaseOrder;
use App\Models\Receaveable;
use App\Models\ServiceOrder;
use App\Models\Task;
use App\Models\Transaction;
use App\Models\TransactionElement;
use App\Models\User;
use App\Observers\AppointmentObserver;
use App\Observers\AssetObserver;
use App\Observers\ClosingObserver;
use App\Observers\ExpenseVoucherObserver;
use App\Observers\PatientObserver;
use App\Observers\PurchaseOrderObserver;
use App\Observers\TaskObserver;
use App\Observers\TransactionElementObserver;
use App\Observers\TransactionObserver;
use App\Policies\ClosingPolicy;
use App\Policies\ExpenseVoucherPolicy;
use App\Policies\PatientPolicy;
use App\Policies\ReceaveablePolicy;
use App\Policies\ServiceOrderPolicy;
use App\Policies\TransactionPolicy;
use App\Policies\UserPolicy;
use App\Services\BreachDetectionService;
use BezhanSalleh\PanelSwitch\PanelSwitch;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Spatie\Activitylog\Models\Activity;

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
        Closing::observe(ClosingObserver::class);
        Transaction::observe(TransactionObserver::class);
        TransactionElement::observe(TransactionElementObserver::class);
        ExpenseVoucher::observe(ExpenseVoucherObserver::class);
        PurchaseOrder::observe(PurchaseOrderObserver::class);
        Asset::observe(AssetObserver::class);
        Task::observe(TaskObserver::class);
        Appointment::observe(AppointmentObserver::class);

        Gate::define('viewPulse', function (User $user) {
            return $user->adminProfiles()->count() > 0;
        });

        Gate::policy(Closing::class, ClosingPolicy::class);
        Gate::policy(Transaction::class, TransactionPolicy::class);
        Gate::policy(Patient::class, PatientPolicy::class);
        Gate::policy(ServiceOrder::class, ServiceOrderPolicy::class);
        Gate::policy(ExpenseVoucher::class, ExpenseVoucherPolicy::class);
        Gate::policy(Receaveable::class, ReceaveablePolicy::class);
        Gate::policy(User::class, UserPolicy::class);

        Activity::created(function (Activity $activity): void {
            // properties is cast to a Collection — (array) on it would dump the
            // object's protected internals into the payload. Always go through
            // toArray() so the stored JSON stays a flat associative array.
            $properties = $activity->properties?->toArray() ?? [];

            if (array_key_exists('ip_address', $properties) && array_key_exists('user_agent', $properties)) {
                return;
            }

            $properties['ip_address'] = request()->ip();
            $properties['user_agent'] = request()->userAgent();

            $activity->properties = $properties;
            $activity->saveQuietly();
        });

        // PanelSwitch::configureUsing(function (PanelSwitch $panelSwitch): void {
        //     $panelSwitch
        //         // ->visible(fn (): bool => (bool) auth()->user()?->adminProfiles()?->count())
        //         ->panels(['admin', 'accounts'])
        //         ->modalHeading('Switch Panel')
        //         ->labels([
        //             'admin' => 'Admin',
        //             'accounts' => 'Accounts',
        //         ])
        //         ->icons([
        //             'admin' => 'heroicon-o-chart-pie',
        //             'accounts' => 'heroicon-o-calculator',
        //         ]);
        // });

        Event::listen(Failed::class, function (Failed $event): void {
            app(BreachDetectionService::class)->recordFailedLogin(
                request(),
                $event->user,
                (string) request()->input('email')
            );
        });

        Event::listen(Login::class, function (Login $event): void {
            app(BreachDetectionService::class)->recordSuccessfulLogin($event->user, request());
        });

        TextColumn::configureUsing(function (TextColumn $column): void {
            $column->timezone(UserTimezone::current());
        });

        Blade::directive('hdate', function (string $expression): string {
            return "<?php echo \\App\\Helpers\\DateHelper::pdfFormat({$expression}); ?>";
        });
    }
}
