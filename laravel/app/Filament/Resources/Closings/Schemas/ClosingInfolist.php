<?php

namespace App\Filament\Resources\Closings\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class ClosingInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('reception_id')
                    ->numeric(),
                TextEntry::make('user_id')
                    ->numeric(),
                TextEntry::make('status'),
                TextEntry::make('opening_amount')
                    ->numeric(),
                TextEntry::make('closing_amount')
                    ->numeric(),
                TextEntry::make('closing_amount_cash')
                    ->numeric(),
                TextEntry::make('closing_amount_cheque')
                    ->numeric(),
                TextEntry::make('closing_amount_card')
                    ->numeric(),
                TextEntry::make('expense_payed')
                    ->numeric(),
                TextEntry::make('cash_recieving_time')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
