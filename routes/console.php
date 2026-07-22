<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('app:close-old-service-orders')->everyFiveSeconds()->runInBackground()->withoutOverlapping();

Schedule::command('telescope:prune')->daily();

Schedule::command('bank:link-transactions')->hourly()->runInBackground()->withoutOverlapping();

// Temporarily disabled for manual testing
// Schedule::command('app:sync-old-hims --entity=all')
//     ->everyTenSeconds()
//     ->runInBackground()
//     ->withoutOverlapping()
//     ->onOneServer();

Schedule::command('backup:run')
    ->dailyAt('01:00')
    ->runInBackground()
    ->withoutOverlapping();

Schedule::command('backup:clean')
    ->dailyAt('01:30')
    ->runInBackground()
    ->withoutOverlapping();

Schedule::command('backup:monitor')
    ->dailyAt('02:00')
    ->runInBackground()
    ->withoutOverlapping();
