<?php

namespace App\Filament\Admin\Resources\AdministrativeTransactions\Pages;

use App\Filament\Admin\Resources\AdministrativeTransactions\AdministrativeTransactionResource;
use App\Models\PaymentMethod;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateAdministrativeTransaction extends CreateRecord
{
    protected static string $resource = AdministrativeTransactionResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Ensure this transaction is never linked to a counter closing
        $data['closing_id'] = null;

        // Tag as administrative type
        $data['type'] = 'ADMIN';

        // Attribute to the authenticated admin
        $data['created_by'] = Auth::id();

        // Resolve the polymorph type from the selected payment method
        if (! empty($data['payment_method_id']) && ! empty($data['payable_id'])) {
            $method = PaymentMethod::find($data['payment_method_id']);
            if ($method && $method->requiresPayable()) {
                $data['payable_type'] = $method->getPayableModelClass();
            }
        }

        return $data;
    }
}
