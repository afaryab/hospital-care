<?php

namespace App\Filament\Admin\Resources\BankTransactions\Pages;

use App\Filament\Admin\Resources\BankTransactions\BankTransactionResource;
use App\Imports\BankStatementImport;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Maatwebsite\Excel\Facades\Excel;

class ListBankTransactions extends ListRecords
{
    protected static string $resource = BankTransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('uploadStatement')
                ->label('Upload Bank Statement')
                ->icon('heroicon-o-arrow-up-tray')
                ->form([
                    Select::make('bank_account_id')
                        ->label('Bank Account')
                        ->relationship('bankAccount', 'name')
                        ->required(),
                    FileUpload::make('file')
                        ->label('Statement File (CSV / Excel)')
                        ->acceptedFileTypes([
                            'text/csv',
                            'application/vnd.ms-excel',
                            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                        ])
                        ->storeFiles(false)
                        ->required(),
                ])
                ->action(function (array $data): void {
                    Excel::import(
                        new BankStatementImport($data['bank_account_id']),
                        $data['file'],
                    );

                    Notification::make()
                        ->title('Bank statement queued for import. Duplicates will be skipped.')
                        ->success()
                        ->send();
                }),
            CreateAction::make(),
        ];
    }
}
