<?php

namespace App\Filament\Admin\Resources\DeathCertificates\Pages;

use App\Filament\Admin\Resources\DeathCertificates\DeathCertificateResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditDeathCertificate extends EditRecord
{
    protected static string $resource = DeathCertificateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
