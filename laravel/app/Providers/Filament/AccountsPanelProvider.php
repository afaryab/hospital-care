<?php

namespace App\Providers\Filament;

use App\Filament\Pages\Dashboard;
use App\Services\Filament\FilamentThemeService;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Facades\FilamentView;
use Filament\View\PanelsRenderHook;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AccountsPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('accounts')
            ->path('accounts')
            ->brandLogo(fn () => view('filament.accounts.logo'))
            ->maxContentWidth('full')
            ->login()
            ->authGuard('web')
            ->viteTheme('resources/css/filament/theme.css')
            ->colors(FilamentThemeService::getBrandColors())
            ->font('Inter')
            ->discoverResources(in: app_path('Filament/Accounts/Resources'), for: 'App\\Filament\\Accounts\\Resources')
            ->discoverPages(in: app_path('Filament/Accounts/Pages'), for: 'App\\Filament\\Accounts\\Pages')
            // ->pages([
            //     // Dashboard::class
            // ])
            ->discoverWidgets(in: app_path('Filament/Accounts/Widgets'), for: 'App\\Filament\\Accounts\\Widgets')
            // ->widgets([
            //     // AccountWidget::class,
            //     // FilamentInfoWidget::class,
            // ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }

    public function boot(): void
    {
        FilamentView::registerRenderHook(
            PanelsRenderHook::HEAD_END,
            fn (): string => FilamentThemeService::getCustomStyles()
        );
    }
}
