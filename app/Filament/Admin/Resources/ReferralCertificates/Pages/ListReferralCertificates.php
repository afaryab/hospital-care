<?php

namespace App\Filament\Admin\Resources\ReferralCertificates\Pages;

use App\Filament\Admin\Resources\ReferralCertificates\ReferralCertificateResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListReferralCertificates extends ListRecords
{
    protected static string $resource = ReferralCertificateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
