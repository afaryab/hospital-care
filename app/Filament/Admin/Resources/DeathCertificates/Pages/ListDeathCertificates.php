<?php

namespace App\Filament\Admin\Resources\DeathCertificates\Pages;

use App\Filament\Admin\Resources\DeathCertificates\DeathCertificateResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListDeathCertificates extends ListRecords
{
    protected static string $resource = DeathCertificateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
