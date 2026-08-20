<?php

namespace App\Filament\Admin\Resources\DeathCertificates\Pages;

use App\Filament\Admin\Resources\DeathCertificates\DeathCertificateResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditDeathCertificate extends EditRecord
{
    protected static string $resource = DeathCertificateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('lock')
                ->label('Lock Certificate')
                ->icon('heroicon-o-lock-closed')
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading('Lock this death certificate?')
                ->modalDescription('Once locked, this certificate can no longer be edited.')
                ->visible(fn () => ! $this->record->is_locked)
                ->action(function (): void {
                    $this->record->update([
                        'is_locked' => true,
                        'locked_at' => now(),
                        'locked_by' => auth()->id(),
                    ]);

                    Notification::make()
                        ->title('Death certificate locked')
                        ->success()
                        ->send();

                    $this->fillForm();
                }),
            DeleteAction::make(),
        ];
    }
}
