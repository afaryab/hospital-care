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
                        Tab::make('Summary')
                            ->schema([
                                ViewEntry::make('closing_overview')
                                    ->label(false)
                                    ->view('filament.accounts.closings.infolists.closing-overview')
                                    ->viewData(fn (Closing $record) => [
                                        'closing' => $record,
                                        'transactions' => $record->transactions()->paginate(20),
                                        'printUrl' => self::buildMiniPrintUrl($record),
                                    ])
                                    ->columnSpanFull(),
                            ]),
                        Tab::make('Print')
                            ->schema([
                                ViewEntry::make('print_iframe')
                                    ->label(false)
                                    ->view('filament.admin.closings.infolists.report-iframe')
                                    ->viewData(fn (Closing $record) => [
                                        'src' => self::buildPrintUrl($record) ?? '',
                                        'title' => 'Closing Statement',
                                        'showVariant' => true,
                                    ])
                                    ->columnSpanFull(),
                            ]),
                        Tab::make('Income Report')
                            ->schema([
                                ViewEntry::make('income_report_iframe')
                                    ->label(false)
                                    ->view('filament.admin.closings.infolists.report-iframe')
                                    ->viewData(fn (Closing $record) => [
                                        'src' => self::buildReportUrl($record, 'income') ?? '',
                                        'title' => 'Income Report',
                                        'showVariant' => false,
                                    ])
                                    ->columnSpanFull(),
                            ]),
                        Tab::make('Expense Report')
                            ->schema([
                                ViewEntry::make('expense_report_iframe')
                                    ->label(false)
                                    ->view('filament.admin.closings.infolists.report-iframe')
                                    ->viewData(fn (Closing $record) => [
                                        'src' => self::buildReportUrl($record, 'expense') ?? '',
                                        'title' => 'Expense Report',
                                        'showVariant' => false,
                                    ])
                                    ->columnSpanFull(),
                            ]),
                        Tab::make('Receivables Report')
                            ->schema([
                                ViewEntry::make('receivables_report_iframe')
                                    ->label(false)
                                    ->view('filament.admin.closings.infolists.report-iframe')
                                    ->viewData(fn (Closing $record) => [
                                        'src' => self::buildReportUrl($record, 'receivables') ?? '',
                                        'title' => 'Receivables Report',
                                        'showVariant' => false,
                                    ])
                                    ->columnSpanFull(),
                            ]),
                        Tab::make('Services Report')
                            ->schema([
                                ViewEntry::make('services_report_iframe')
                                    ->label(false)
                                    ->view('filament.admin.closings.infolists.report-iframe')
                                    ->viewData(fn (Closing $record) => [
                                        'src' => self::buildReportUrl($record, 'services') ?? '',
                                        'title' => 'Services Report',
                                        'showVariant' => false,
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
