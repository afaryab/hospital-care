<?php

namespace App\Filament\Admin\Pages;

use App\Services\Compliance\ComplianceService;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class Compliance extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedShieldCheck;

    protected static string|UnitEnum|null $navigationGroup = 'Compliance';

    protected static ?int $navigationSort = 0;

    protected static ?string $navigationLabel = 'Compliance Status';

    protected static ?string $title = 'Compliance Status';

    protected string $view = 'filament.admin.pages.compliance';

    public static function canAccess(): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function getChecks(): array
    {
        return app(ComplianceService::class)->checks();
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('refresh')
                ->label('Re-run checks')
                ->icon(Heroicon::OutlinedArrowPath)
                ->color('gray')
                ->action(fn () => null),
        ];
    }

    public function attestAction(): Action
    {
        return Action::make('attest')
            ->label('Attest')
            ->icon(Heroicon::OutlinedCheckBadge)
            ->size('sm')
            ->modalHeading(fn (array $arguments): string => 'Attest: '.(ComplianceService::MANUAL_ITEMS[$arguments['key']]['title'] ?? ''))
            ->modalDescription('Confirm this safeguard is in place. Your name and the date are recorded in the audit log; attestations expire after one year.')
            ->schema([
                Textarea::make('note')
                    ->label('Evidence / note')
                    ->placeholder('e.g. Risk assessment signed off by the MS on 12 Sep, filed in DMS')
                    ->maxLength(1000),
            ])
            ->action(function (array $data, array $arguments): void {
                app(ComplianceService::class)->attest($arguments['key'], auth()->user(), $data['note'] ?? null);

                Notification::make()->title('Attestation recorded.')->success()->send();
            });
    }
}
