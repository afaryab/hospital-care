<?php

namespace App\Filament\Admin\Resources\PayrollPeriods\Pages;

use App\Filament\Admin\Resources\PayrollPeriods\PayrollPeriodResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPayrollPeriods extends ListRecords
{
    protected static string $resource = PayrollPeriodResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
