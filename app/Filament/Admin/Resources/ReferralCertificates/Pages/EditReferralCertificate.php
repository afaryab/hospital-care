<?php

namespace App\Filament\Admin\Resources\ReferralCertificates\Pages;

use App\Filament\Admin\Resources\ReferralCertificates\ReferralCertificateResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditReferralCertificate extends EditRecord
{
    protected static string $resource = ReferralCertificateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('lock')
                ->label('Lock Certificate')
                ->icon('heroicon-o-lock-closed')
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading('Lock this referral certificate?')
                ->modalDescription('Once locked, this certificate can no longer be edited.')
                ->visible(fn () => ! $this->record->is_locked)
                ->action(function (): void {
                    $this->record->update([
                        'is_locked' => true,
                        'locked_at' => now(),
                        'locked_by' => auth()->id(),
                    ]);

                    Notification::make()
                        ->title('Referral certificate locked')
                        ->success()
                        ->send();

                    $this->fillForm();
                }),
            DeleteAction::make(),
        ];
    }
}
