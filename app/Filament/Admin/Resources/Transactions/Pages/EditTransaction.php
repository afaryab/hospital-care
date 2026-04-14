<?php

namespace App\Filament\Admin\Resources\Transactions\Pages;

use App\Filament\Admin\Resources\Transactions\TransactionResource;
use App\Models\TransactionElement;
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

            Action::make('updateServiceOrders')
                ->label('Update Service Orders')
                ->color('warning')
                ->requiresConfirmation()
                ->modalHeading('Update Related Service Orders')
                ->modalDescription('Update the doctor and service on all linked service orders to match the current transaction elements. This cannot be undone.')
                ->modalSubmitActionLabel('Yes, update service orders')
                ->visible(fn () => $this->record->elements()->whereNotNull('service_order_id')->exists())
                ->action(function (): void {
                    $this->record->elements()
                        ->whereNotNull('service_order_id')
                        ->with('serviceOrder')
                        ->get()
                        ->each(function (TransactionElement $element): void {
                            if ($element->serviceOrder) {
                                $element->serviceOrder->update([
                                    'doctor_id' => $element->doctor_id,
                                    'service_id' => $element->service_id,
                                ]);
                            }
                        });

                    Notification::make()
                        ->title('Service orders updated.')
                        ->success()
                        ->send();
                }),

            DeleteAction::make(),
        ];
    }

    protected function afterSave(): void
    {
        // Re-sync the transaction's total amount from its elements
        $newAmount = $this->record->elements()->where('income_or_expense', 'INCOME')->sum('amount')
            - $this->record->elements()->where('income_or_expense', 'EXPENSE')->sum('amount');

        $this->record->updateQuietly(['amount' => abs($newAmount)]);

        // Prompt to update service orders if any elements are linked
        if ($this->record->elements()->whereNotNull('service_order_id')->exists()) {
            Notification::make()
                ->title('Transaction saved.')
                ->body('Some elements are linked to service orders. Use "Update Service Orders" to sync changes.')
                ->warning()
                ->persistent()
                ->send();
        }
    }
}
