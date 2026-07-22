<?php

namespace App\Filament\Admin\Resources\Triages\Pages;

use App\Filament\Admin\Resources\Triages\TriageResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageTriages extends ManageRecords
{
    protected static string $resource = TriageResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
