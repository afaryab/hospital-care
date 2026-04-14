<?php

namespace App\Filament\Admin\Resources\BankTransactions\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class BankTransactionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('bank_account_id')
                    ->label('Bank Account')
                    ->relationship('bankAccount', 'name')
                    ->required(),
                DatePicker::make('transaction_date')
                    ->required(),
                TextInput::make('description')
                    ->maxLength(500),
                TextInput::make('debit')
                    ->numeric()
                    ->nullable(),
                TextInput::make('credit')
                    ->numeric()
                    ->nullable(),
                TextInput::make('balance')
                    ->numeric()
                    ->nullable(),
                TextInput::make('reference_number')
                    ->maxLength(100),
                Select::make('linked_transaction_id')
                    ->label('Linked Transaction')
                    ->relationship('linkedTransaction', 'tr_number')
                    ->searchable()
                    ->nullable(),
            ]);
    }
}
