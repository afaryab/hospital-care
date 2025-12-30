<?php

namespace App\Filament\Accounts\Pages;

use Filament\Pages\Page;

class IncomeCashFlow extends Page
{
    // protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';

    protected static ?string $navigationLabel = 'Income Cash Flow';

    protected static ?string $title = 'Income Cash Flow Report';

    protected static ?int $navigationSort = 2;

    public $date_from = null;
    public $date_to = null;
    public $closing_id = null;
    public $service_id = null;
    public $service_order_id = null;
    public $doctor_id = null;
    public $patient_id = null;
    public $group_by = 'none';
    
    // Column visibility
    public $show_patient_name = true;
    public $show_service_name = true;
    public $show_service_order = true;
    public $show_provider_name = true;
    public $show_original_amount = false;
    public $show_edited_amount = true;
    public $show_customer_payed = true;
    public $show_change = true;
    public $show_balance = true;
    public $show_transaction_number = true;
    public $show_date = true;

    public function mount(): void
    {
        // Set default date range (current month)
        $this->date_from = now()->startOfMonth()->format('Y-m-d');
        $this->date_to = now()->endOfMonth()->format('Y-m-d');
    }

    public function getView(): string
    {
        return 'filament.accounts.pages.income-cash-flow';
    }

    public function getReportUrl(): string
    {
        $params = [
            'date_from' => $this->date_from,
            'date_to' => $this->date_to,
            'closing_id' => $this->closing_id,
            'service_id' => $this->service_id,
            'service_order_id' => $this->service_order_id,
            'doctor_id' => $this->doctor_id,
            'patient_id' => $this->patient_id,
            'group_by' => $this->group_by,
            'show_patient_name' => $this->show_patient_name,
            'show_service_name' => $this->show_service_name,
            'show_service_order' => $this->show_service_order,
            'show_provider_name' => $this->show_provider_name,
            'show_original_amount' => $this->show_original_amount,
            'show_edited_amount' => $this->show_edited_amount,
            'show_customer_payed' => $this->show_customer_payed,
            'show_change' => $this->show_change,
            'show_balance' => $this->show_balance,
            'show_transaction_number' => $this->show_transaction_number,
            'show_date' => $this->show_date,
        ];

        return route('reports.income-cash-flow', array_filter($params));
    }

    public static function canView(): bool
    {
        return true;
    }
}
