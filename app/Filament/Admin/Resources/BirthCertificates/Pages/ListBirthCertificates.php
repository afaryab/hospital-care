<?php

namespace App\Filament\Admin\Resources\BirthCertificates\Pages;

use App\Filament\Admin\Resources\BirthCertificates\BirthCertificateResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListBirthCertificates extends ListRecords
{
    protected static string $resource = BirthCertificateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
