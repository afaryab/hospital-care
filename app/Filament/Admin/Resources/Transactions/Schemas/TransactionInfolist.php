<?php

namespace App\Filament\Admin\Resources\Transactions\Schemas;

use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;

class TransactionInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Tabs::make('Transaction')
                ->tabs([
                    Tab::make('Overview')
                        ->schema([
                            Section::make()
                                ->schema([
                                    TextEntry::make('tr_number')->label('TR Number')->copyable(),
                                    TextEntry::make('patient.name')->label('Patient'),
                                    TextEntry::make('closing.ct_number')->label('Closing'),
                                    TextEntry::make('type')->badge(),
                                    TextEntry::make('income_or_expense')->label('Direction')
                                        ->badge()
                                        ->color(fn (string $state): string => $state === 'INCOME' ? 'success' : 'danger'),
                                    TextEntry::make('amount')->label('Recognized Amount')->money('PKR'),
                                    TextEntry::make('is_refunded')->label('Refunded')
                                        ->badge()
                                        ->formatStateUsing(fn (bool $state): string => $state ? 'Yes' : 'No')
                                        ->color(fn (bool $state): string => $state ? 'danger' : 'success'),
                                    TextEntry::make('notes')->placeholder('-')->columnSpanFull(),
                                    TextEntry::make('created_at')->dateTime('d M Y, H:i'),
                                ])
                                ->columns(3),

                            Section::make('Payment')
                                ->schema([
                                    TextEntry::make('customer_payed')->label('Amount Customer Paid')->money('PKR')
                                        ->visible(fn ($record) => ! $record->receaveable_id),
                                    TextEntry::make('change')->label('Change Given')->money('PKR')
                                        ->visible(fn ($record) => ! $record->receaveable_id),
                                    TextEntry::make('receaveable.amount')->label('Outstanding Receivable')->money('PKR')
                                        ->placeholder('None')
                                        ->visible(fn ($record) => ! $record->receaveable_id),
                                    TextEntry::make('receaveable.status')->label('Receivable Status')
                                        ->badge()
                                        ->placeholder('-')
                                        ->visible(fn ($record) => ! $record->receaveable_id),
                                    TextEntry::make('settledReceaveable.transaction.tr_number')
                                        ->label('Settles Receivable From')
                                        ->placeholder('-')
                                        ->visible(fn ($record) => (bool) $record->receaveable_id),
                                    TextEntry::make('settledReceaveable.amount')
                                        ->label('Receivable Remaining Balance')
                                        ->money('PKR')
                                        ->visible(fn ($record) => (bool) $record->receaveable_id),
                                ])
                                ->columns(3),
                        ]),

                    Tab::make('Line Items')
                        ->schema([
                            RepeatableEntry::make('elements')
                                ->schema([
                                    TextEntry::make('type')->badge(),
                                    TextEntry::make('income_or_expense')->label('Direction')
                                        ->badge()
                                        ->color(fn (string $state): string => $state === 'INCOME' ? 'success' : 'danger'),
                                    TextEntry::make('service.name')->label('Service')->placeholder('-'),
                                    TextEntry::make('doctor.name')->label('Doctor')->placeholder('-'),
                                    TextEntry::make('amount')->money('PKR'),
                                    TextEntry::make('orignal_amount')->label('Original Amount')->money('PKR')->placeholder('-'),
                                    TextEntry::make('expenseCategory.name')->label('Expense Category')->placeholder('-'),
                                    TextEntry::make('notes')->placeholder('-')->columnSpanFull(),
                                ])
                                ->columns(3)
                                ->columnSpanFull(),
                        ]),
                ])
                ->columnSpanFull(),
        ]);
    }
}
