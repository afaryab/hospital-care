<?php

namespace App\Filament\Admin\Resources\ServiceOrders\Pages;

use App\Filament\Admin\Resources\ServiceOrders\ServiceOrderResource;
use Filament\Resources\Pages\ListRecords;

class ListServiceOrders extends ListRecords
{
    protected static string $resource = ServiceOrderResource::class;
}
