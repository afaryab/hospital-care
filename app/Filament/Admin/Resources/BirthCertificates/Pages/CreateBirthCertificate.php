<?php

namespace App\Filament\Admin\Resources\BirthCertificates\Pages;

use App\Filament\Admin\Resources\BirthCertificates\BirthCertificateResource;
use Filament\Resources\Pages\CreateRecord;

class CreateBirthCertificate extends CreateRecord
{
    protected static string $resource = BirthCertificateResource::class;
}
