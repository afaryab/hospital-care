<?php

namespace App\Filament\Admin\Resources\BankAccounts\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class BankAccountForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Account Alias')
                    ->required()
                    ->maxLength(255),
                TextInput::make('bank_name')
                    ->required()
                    ->maxLength(255),
                TextInput::make('account_number')
                    ->maxLength(30),
                TextInput::make('branch_code')
                    ->maxLength(20),
                TextInput::make('iban')
                    ->label('IBAN')
                    ->maxLength(34),
                Toggle::make('is_active')
                    ->label('Active')
                    ->default(true),
            ]);
    }
}
