<?php

declare(strict_types=1);

namespace Processton\Abacus;

use Filament\Pages;
use Filament\Contracts\Plugin;
use Filament\Panel;
use Filament\Support\Concerns\EvaluatesClosures;
use Processton\Abacus\Filament\Resources\AbacusChartOfAccountResource;
use Processton\Abacus\Filament\Resources\AbacusIncomingResource;
use Processton\Abacus\Filament\Resources\AbacusTransactionResource;
use Processton\Abacus\Filament\Resources\AbacusYearResource;

use Processton\Abacus\Filament\Pages\BalanceSheet;
use Processton\Abacus\Filament\Pages\CashFlowStatement;
use Processton\Abacus\Filament\Pages\GeneralLedger;
use Processton\Abacus\Filament\Pages\ProfitLossStatement;
use Processton\Abacus\Filament\Pages\TrialBalance;


class AbacusPlugin implements Plugin
{
    use EvaluatesClosures;

    public static function make(): static
    {
        return app(static::class);
    }

    public function getId(): string
    {
        return 'abacus';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([
            AbacusChartOfAccountResource::class,
            AbacusIncomingResource::class,
            AbacusTransactionResource::class,
            AbacusYearResource::class
        ]);

        $panel->pages([
            BalanceSheet::class,
            GeneralLedger::class,
            ProfitLossStatement::class,
            TrialBalance::class,
            CashFlowStatement::class
        ]);
    }

    public function boot(Panel $panel): void
    {
        //
    }

    public static function get(): static
    {
        /** @var static $plugin */
        $plugin = filament(app(static::class)->getId());

        return $plugin;
    }
}
