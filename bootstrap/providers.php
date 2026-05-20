<?php

use App\Providers\AppServiceProvider;
use App\Providers\Filament\AccountsPanelProvider;
use App\Providers\Filament\AdminPanelProvider;
use App\Providers\FortifyServiceProvider;
use App\Providers\TelescopeServiceProvider;

return [
    AppServiceProvider::class,
    AccountsPanelProvider::class,
    AdminPanelProvider::class,
    FortifyServiceProvider::class,
    TelescopeServiceProvider::class,
];
