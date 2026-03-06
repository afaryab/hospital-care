<?php

namespace App\Filament\Accounts\Resources\Closings\Schemas;

use App\Models\Closing;
use Filament\Infolists\Components\ViewEntry;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;

class ClosingInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Tabs')
                    ->tabs([
                        Tabs\Tab::make('Summery')
                            ->schema([
                                ViewEntry::make('closing_overview')
                                    ->label(false)
                                    ->view('filament.accounts.closings.infolists.closing-overview')
                                    ->viewData(fn (Closing $record) => [
                                        'closing' => $record,
                                        'transactions' => $record->transactions(),
                                        'printUrl' => self::buildMiniPrintUrl($record),
                                    ])
                                    ->columnSpanFull(),  
                            ]),
                        Tabs\Tab::make('Detailed Summery')
                            ->schema([
                                ViewEntry::make('closing_overview')
                                    ->label(false)
                                    ->view('filament.accounts.closings.infolists.closing-overview')
                                    ->viewData(fn (Closing $record) => [
                                        'closing' => $record,
                                        'transactions' => $record->transactions(),
                                        'printUrl' => self::buildPrintUrl($record),
                                    ])
                                    ->columnSpanFull(),  
                            ]),
                        Tabs\Tab::make('Services Report')
                            ->schema([
                                ViewEntry::make('closing_overview')
                                    ->label(false)
                                    ->view('filament.accounts.closings.infolists.closing-overview')
                                    ->viewData(fn (Closing $record) => [
                                        'closing' => $record,
                                        'transactions' => $record->transactions(),
                                        'printUrl' => self::buildPrintUrl($record),
                                    ])
                                    ->columnSpanFull(),  
                            ]),
                        Tabs\Tab::make('Income Report')
                            ->schema([
                                // ...
                            ]),
                        Tabs\Tab::make('Expense Report')
                            ->schema([
                                // ...
                            ]),
                        Tabs\Tab::make('Receivables Report')
                            ->schema([
                                // ...
                            ]),
                    ])
                    ->columnSpanFull(),
                
            ]);
    }

    protected static function buildMiniPrintUrl(Closing $record): ?string
    {
        $parts = $record->ct_number_parts ?? [];

        if (empty($parts['year']) || empty($parts['month']) || empty($parts['number'])) {
            return null;
        }

        return route('print-closing-statement', [
            'year' => $parts['year'],
            'month' => $parts['month'],
            'number' => $parts['number'],
            'variant' => 'mini',
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
            'variant' => 'normal',
        ]);
    }
}
