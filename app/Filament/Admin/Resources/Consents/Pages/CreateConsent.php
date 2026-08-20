<?php

namespace App\Filament\Admin\Resources\Consents\Pages;

use App\Filament\Admin\Resources\Consents\ConsentResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateConsent extends CreateRecord
{
    protected static string $resource = ConsentResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['recorded_by'] = Auth::id();

        return $data;
    }
}
