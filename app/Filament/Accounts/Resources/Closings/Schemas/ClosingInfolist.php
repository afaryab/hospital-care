<?php

namespace App\Filament\Accounts\Resources\Closings\Schemas;

use App\Models\Closing;
use Filament\Infolists\Components\ViewEntry;
use Filament\Schemas\Schema;

class ClosingInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                ViewEntry::make('closing_overview')
                    ->label(false)
                    ->view('filament.accounts.closings.infolists.closing-overview')
                    ->viewData(fn (Closing $record) => [
                        'closing' => $record,
                        'printUrl' => self::buildPrintUrl($record),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    protected static function buildPrintUrl(Closing $record): ?string
    {
        $parts = $record->ct_number_parts ?? [];

        if (empty($parts['year']) || empty($parts['month']) || empty($parts['number'])) {
            return null;
        }

        return route('print-closing-statement', [
            'year' => $parts['year'],
            'month' => $parts['month'],
            'number' => $parts['number'],
        ]);
    }
}
