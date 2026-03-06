<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule::everyFiveSeconds()
//     ->runInBackground()
//     ->withoutOverlapping()
//     ->command('app:fetch-old --batch-size=3000');

// Schedule::command('app:close-old-service-orders')->everyFiveSeconds()->runInBackground()->withoutOverlapping();

Schedule::command('telescope:prune')->daily();