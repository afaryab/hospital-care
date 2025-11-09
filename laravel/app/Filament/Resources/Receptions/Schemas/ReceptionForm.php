<?php

namespace App\Filament\Resources\Receptions\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ReceptionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('allowed_departments'),
                TextInput::make('is_allowed_to_pay_voucher')
                    ->required()
                    ->numeric(),
                TextInput::make('is_allowed_to_pay_from_petty_cash')
                    ->required()
                    ->numeric(),
                TextInput::make('is_cash_allowed')
                    ->required()
                    ->numeric(),
                TextInput::make('is_cheques_allowed')
                    ->required()
                    ->numeric(),
                TextInput::make('is_card_allowed')
                    ->required()
                    ->numeric(),
            ]);
    }
}
