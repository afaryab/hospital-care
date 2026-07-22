<?php

namespace App\Filament\Admin\Resources\Drugs\Pages;

use App\Filament\Admin\Resources\Drugs\DrugResource;
use Filament\Resources\Pages\CreateRecord;

class CreateDrug extends CreateRecord
{
    protected static string $resource = DrugResource::class;
}
