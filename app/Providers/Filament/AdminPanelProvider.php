<?php

namespace App\Providers\Filament;

use Andreia\FilamentUiSwitcher\FilamentUiSwitcherPlugin;
use App\Filament\Admin\Widgets\AdminStatsOverview;
use App\Filament\Admin\Widgets\MigrationStatsOverview;
use App\Filament\Pages\Dashboard;
use App\Services\Filament\FilamentThemeService;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Panel;
use Filament\PanelProvider;
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

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {

        $widgets = [
            AdminStatsOverview::class,
            // AccountWidget::class,
            // FilamentInfoWidget::class,
        ];

        if (env('ENABLE_OLD_SYNC', false) !== false) {
            $widgets[] = MigrationStatsOverview::class;
        }

        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->brandLogo(fn () => view('filament.admin.logo'))
            ->maxContentWidth('full')
            ->login()
            ->authGuard('web')
            ->authPasswordBroker('users')
            ->viteTheme('resources/css/filament/theme.css')
            ->discoverResources(in: app_path('Filament/Admin/Resources'), for: 'App\\Filament\\Admin\\Resources')
            ->discoverPages(in: app_path('Filament/Admin/Pages'), for: 'App\\Filament\\Admin\\Pages')
            // ->pages([
            //     // Dashboard::class
            // ])
            ->discoverWidgets(in: app_path('Filament/Admin/Widgets'), for: 'App\\Filament\\Admin\\Widgets')
            ->widgets($widgets)
            ->resources([
                // Resources will be auto-discovered from Admin/Resources
            ])
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
            ])
            ->plugins([
                // FilamentUiSwitcherPlugin::make()->withModeSwitcher(),
            ]);
    }

    public function boot(): void
    {
        // FilamentView::registerRenderHook(
        //     PanelsRenderHook::HEAD_END,
        //     fn (): string => FilamentThemeService::getCustomStyles()
        // );
    }
}
