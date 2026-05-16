<?php

namespace App\Filament\Admin\Resources\PayrollPeriods\Pages;

use App\Filament\Admin\Resources\PayrollPeriods\PayrollPeriodResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPayrollPeriod extends EditRecord
{
    protected static string $resource = PayrollPeriodResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
