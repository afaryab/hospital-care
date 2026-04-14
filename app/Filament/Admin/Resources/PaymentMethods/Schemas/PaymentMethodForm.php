<?php

namespace App\Filament\Admin\Resources\PaymentMethods\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class PaymentMethodForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                TextInput::make('slug')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->helperText('UPPERCASE slug used as the payment type identifier, e.g. CASH, BANK_TRANSFER')
                    ->maxLength(50),
                Toggle::make('id_required')
                    ->label('Reference ID Required')
                    ->helperText('When enabled, the user must enter a reference/transaction ID at payment time.'),
                Select::make('payables')
                    ->label('Requires Payable Selection')
                    ->options([
                        'bank_account' => 'Bank Account',
                        'panel' => 'Panel',
                    ])
                    ->nullable()
                    ->placeholder('None')
                    ->helperText('If set, the user must select a linked record (bank account or panel) when using this method.')
                    ->disabled(fn ($record) => $record !== null),
            ]);
    }
}
