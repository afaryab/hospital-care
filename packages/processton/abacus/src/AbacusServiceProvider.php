<?php
namespace Processton\Abacus;


use Illuminate\Support\ServiceProvider;

class AbacusServiceProvider extends ServiceProvider
{

    public function boot()
    {

        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'abacus');

        if ($this->app->runningInConsole()) {
            // Export the migration
            $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
        }
        $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');
    }
}
