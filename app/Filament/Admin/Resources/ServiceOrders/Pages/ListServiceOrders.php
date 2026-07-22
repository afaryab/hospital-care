<?php

namespace App\Filament\Admin\Resources\ServiceOrders\Pages;

use App\Filament\Admin\Resources\ServiceOrders\ServiceOrderResource;
use Filament\Resources\Pages\ListRecords;

class ListServiceOrders extends ListRecords
{
    protected static string $resource = ServiceOrderResource::class;

    /**
     * Allow deep-linking a pre-filtered list, e.g. from the Triage Dashboard:
     * /admin/service-orders?triage=3
     */
    public function mount(): void
    {
        parent::mount();

        if ($triageId = request()->query('triage')) {
            $this->tableFilters['triage']['value'] = $triageId;
        }
    }
}
