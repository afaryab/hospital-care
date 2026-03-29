<?php

namespace App\Filament\Admin\Resources\Closings\Schemas;

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
                        Tab::make('Summery')
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
                        Tab::make('Detailed Summery')
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
                        Tab::make('Services Report')
                            ->schema([
                                ViewEntry::make('services_report')
                                    ->label(false)
                                    ->view('filament.accounts.closings.infolists.closing-overview')
                                    ->viewData(fn (Closing $record) => [
                                        'closing' => $record,
                                        'transactions' => $record->transactions(),
                                        'printUrl' => self::buildReportUrl($record, 'services'),
                                    ])
                                    ->columnSpanFull(),
                            ]),
                        Tab::make('Income Report')
                            ->schema([
                                ViewEntry::make('income_report')
                                    ->label(false)
                                    ->view('filament.accounts.closings.infolists.closing-overview')
                                    ->viewData(fn (Closing $record) => [
                                        'closing' => $record,
                                        'transactions' => $record->transactions(),
                                        'printUrl' => self::buildReportUrl($record, 'income'),
                                    ])
                                    ->columnSpanFull(),
                            ]),
                        Tab::make('Expense Report')
                            ->schema([
                                ViewEntry::make('expense_report')
                                    ->label(false)
                                    ->view('filament.accounts.closings.infolists.closing-overview')
                                    ->viewData(fn (Closing $record) => [
                                        'closing' => $record,
                                        'transactions' => $record->transactions(),
                                        'printUrl' => self::buildReportUrl($record, 'expense'),
                                    ])
                                    ->columnSpanFull(),
                            ]),
                        Tab::make('Receivables Report')
                            ->schema([
                                ViewEntry::make('receivables_report')
                                    ->label(false)
                                    ->view('filament.accounts.closings.infolists.closing-overview')
                                    ->viewData(fn (Closing $record) => [
                                        'closing' => $record,
                                        'transactions' => $record->transactions(),
                                        'printUrl' => self::buildReportUrl($record, 'receivables'),
                                    ])
                                    ->columnSpanFull(),
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

    protected static function buildReportUrl(Closing $record, string $report): ?string
    {
        $parts = $record->ct_number_parts ?? [];

        if (empty($parts['year']) || empty($parts['month']) || empty($parts['number'])) {
            return null;
        }

        return route('print-closing-statement', [
            'year' => $parts['year'],
            'month' => $parts['month'],
            'number' => $parts['number'],
            'report' => $report,
        ]);
    }
}
