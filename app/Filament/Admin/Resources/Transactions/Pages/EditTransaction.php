<?php

namespace App\Filament\Admin\Resources\Transactions\Pages;

use App\Filament\Admin\Resources\Transactions\TransactionResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class EditTransaction extends EditRecord
{
    protected static string $resource = TransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('refundTransaction')
                ->label('Refund')
                ->color('danger')
                ->requiresConfirmation()
                ->hidden(fn () => (bool) $this->record->is_refunded)
                ->action(function (): void {
                    try {
                        $this->record->refundBy(Auth::user());

                        Notification::make()
                            ->title('Transaction refunded successfully.')
                            ->success()
                            ->send();

                        $this->refreshFormData(['is_refunded']);
                    } catch (ValidationException $exception) {
                        Notification::make()
                            ->title('Unable to refund transaction.')
                            ->body(collect($exception->errors())->flatten()->implode(' '))
                            ->danger()
                            ->send();
                    }
                }),
            DeleteAction::make(),
        ];
    }
}
