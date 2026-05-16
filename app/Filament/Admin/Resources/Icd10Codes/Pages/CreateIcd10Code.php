<?php

namespace App\Filament\Admin\Resources\Icd10Codes\Pages;

use App\Filament\Admin\Resources\Icd10Codes\Icd10CodeResource;
use Filament\Resources\Pages\CreateRecord;

class CreateIcd10Code extends CreateRecord
{
    protected static string $resource = Icd10CodeResource::class;
}
