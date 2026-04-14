<?php

namespace App\Filament\Admin\Resources\AdministrativeTransactions\Pages;

use App\Filament\Admin\Resources\AdministrativeTransactions\AdministrativeTransactionResource;
use App\Models\PaymentMethod;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditAdministrativeTransaction extends EditRecord
{
    protected static string $resource = AdministrativeTransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Ensure closing_id stays null — admin transactions never belong to a counter
        $data['closing_id'] = null;

        // Resolve the polymorph type from the selected payment method
        if (! empty($data['payment_method_id']) && ! empty($data['payable_id'])) {
            $method = PaymentMethod::find($data['payment_method_id']);
            if ($method && $method->requiresPayable()) {
                $data['payable_type'] = $method->getPayableModelClass();
            }
        } else {
            $data['payable_type'] = null;
            $data['payable_id'] = null;
        }

        return $data;
    }
}
