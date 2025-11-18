<?php

namespace App\Filament\Accounts\Resources\Closings\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ClosingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('old_id')
                    ->numeric(),
                TextInput::make('ct_number')
                    ->required(),
                TextInput::make('reception_id')
                    ->required()
                    ->numeric(),
                TextInput::make('receptionist_id')
                    ->required()
                    ->numeric(),
                TextInput::make('status')
                    ->required()
                    ->default('OPEN'),
                TextInput::make('opening_amount')
                    ->required()
                    ->numeric()
                    ->default(0.0),
                TextInput::make('closing_amount')
                    ->required()
                    ->numeric()
                    ->default(0.0),
                TextInput::make('closing_amount_cash')
                    ->required()
                    ->numeric()
                    ->default(0.0),
                TextInput::make('closing_amount_cheque')
                    ->required()
                    ->numeric()
                    ->default(0.0),
                TextInput::make('closing_amount_card')
                    ->required()
                    ->numeric()
                    ->default(0.0),
                TextInput::make('expense_payed')
                    ->required()
                    ->numeric()
                    ->default(0.0),
                DateTimePicker::make('cash_recieving_time'),
                DateTimePicker::make('closed_at'),
            ]);
    }
}
