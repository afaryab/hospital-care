<?php

namespace App\Filament\Admin\Resources\AdministrativeTransactions\Pages;

use App\Filament\Admin\Resources\AdministrativeTransactions\AdministrativeTransactionResource;
use App\Filament\Admin\Resources\AdministrativeTransactions\Schemas\AdministrativeTransactionForm;
use App\Models\PaymentMethod;
use App\Services\ReceivableSettlementService;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;

class CreateAdministrativeTransaction extends CreateRecord
{
    protected static string $resource = AdministrativeTransactionResource::class;

    protected int $settledCount = 0;

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

    /**
     * A panel receivable payment settles each chosen receivable with its own
     * transaction; everything else is a single administrative transaction.
     */
    protected function handleRecordCreation(array $data): Model
    {
        if (($this->data['income_or_expense'] ?? null) !== 'INCOME'
            || ($this->data['income_type'] ?? null) !== AdministrativeTransactionForm::INCOME_PANEL_RECEIVABLE) {
            return parent::handleRecordCreation(Arr::except($data, ['panel_id', 'allocations']));
        }

        $transactions = app(ReceivableSettlementService::class)->settleForPanel(
            (int) $data['panel_id'],
            array_values($data['allocations'] ?? []),
            Arr::only($data, ['closing_id', 'type', 'created_by', 'notes', 'payment_method_id', 'payable_type', 'payable_id', 'reference_number']),
        );

        $this->settledCount = count($transactions);

        return $transactions[0];
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return $this->settledCount > 1
            ? "Payment recorded against {$this->settledCount} receivables."
            : parent::getCreatedNotificationTitle();
    }
}
