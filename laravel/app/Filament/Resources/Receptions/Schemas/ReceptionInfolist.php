<?php

namespace App\Filament\Resources\Receptions\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class ReceptionInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('name'),
                TextEntry::make('is_allowed_to_pay_voucher')
                    ->numeric(),
                TextEntry::make('is_allowed_to_pay_from_petty_cash')
                    ->numeric(),
                TextEntry::make('is_cash_allowed')
                    ->numeric(),
                TextEntry::make('is_cheques_allowed')
                    ->numeric(),
                TextEntry::make('is_card_allowed')
                    ->numeric(),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
