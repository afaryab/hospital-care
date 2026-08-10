<?php

namespace App\Filament\Admin\Resources\BirthCertificates\Pages;

use App\Filament\Admin\Resources\BirthCertificates\BirthCertificateResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditBirthCertificate extends EditRecord
{
    protected static string $resource = BirthCertificateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('lock')
                ->label('Lock Certificate')
                ->icon('heroicon-o-lock-closed')
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading('Lock this birth certificate?')
                ->modalDescription('Once locked, this certificate can no longer be edited, and it will only be included when printing the service order once locked.')
                ->visible(fn () => ! $this->record->is_locked)
                ->action(function (): void {
                    $this->record->update([
                        'is_locked' => true,
                        'locked_at' => now(),
                        'locked_by' => auth()->id(),
                    ]);

                    Notification::make()
                        ->title('Birth certificate locked')
                        ->success()
                        ->send();

                    $this->fillForm();
                }),
            DeleteAction::make(),
        ];
    }
}
