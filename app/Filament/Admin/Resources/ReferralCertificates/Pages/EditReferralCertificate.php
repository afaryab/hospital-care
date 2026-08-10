<?php

namespace App\Filament\Admin\Resources\ReferralCertificates\Pages;

use App\Filament\Admin\Resources\ReferralCertificates\ReferralCertificateResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditReferralCertificate extends EditRecord
{
    protected static string $resource = ReferralCertificateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
