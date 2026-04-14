<?php

namespace App\Filament\Admin\Resources\ExpenseVouchers\Pages;

use App\Filament\Admin\Resources\ExpenseVouchers\ExpenseVoucherResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditExpenseVoucher extends EditRecord
{
    protected static string $resource = ExpenseVoucherResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make()
                ->hidden(fn () => $this->record->status === 'payed'),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if ($this->record->status === 'payed') {
            Notification::make()
                ->title('Cannot edit a paid voucher.')
                ->danger()
                ->send();

            $this->halt();
        }

        return $data;
    }
}
