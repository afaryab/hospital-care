<?php

namespace App\Filament\Admin\Resources\Drugs\Pages;

use App\Filament\Admin\Resources\Drugs\DrugResource;
use App\Imports\DrugImport;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Maatwebsite\Excel\Facades\Excel;

class ListDrugs extends ListRecords
{
    protected static string $resource = DrugResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('importDrugs')
                ->label('Import CSV / Excel')
                ->icon('heroicon-o-arrow-up-tray')
                ->color('gray')
                ->form([
                    FileUpload::make('file')
                        ->label('Drug File (CSV or Excel)')
                        ->helperText('Columns: name, generic_name, type, category, strength, manufacturer, default_dose, default_frequency, default_duration, default_route, usage_instructions, contraindications, side_effects')
                        ->acceptedFileTypes([
                            'text/csv',
                            'text/plain',
                            'application/vnd.ms-excel',
                            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                        ])
                        ->storeFiles(false)
                        ->required(),
                ])
                ->action(function (array $data): void {
                    $import = new DrugImport;
                    Excel::import($import, $data['file']);

                    Notification::make()
                        ->title("Import complete — {$import->imported} drugs imported.")
                        ->success()
                        ->send();
                }),

            CreateAction::make(),
        ];
    }
}
