<?php

namespace App\Filament\Admin\Resources\AdministrativeTransactions\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class AdministrativeTransactionInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Transaction Overview')
                ->schema([
                    TextEntry::make('tr_number')
                        ->label('TR Number')
                        ->copyable(),
                    TextEntry::make('income_or_expense')
                        ->label('Direction')
                        ->badge()
                        ->color(fn (string $state): string => $state === 'INCOME' ? 'success' : 'danger'),
                    TextEntry::make('amount')
                        ->label('Amount')
                        ->money('PKR'),
                    TextEntry::make('expenseCategory.name')
                        ->label('Expense Category')
                        ->placeholder('—'),
                    TextEntry::make('paymentMethod.name')
                        ->label('Payment Method')
                        ->placeholder('—'),
                    TextEntry::make('reference_number')
                        ->label('Reference / ID')
                        ->placeholder('—'),
                    TextEntry::make('patient.name')
                        ->label('Patient')
                        ->placeholder('—'),
                    TextEntry::make('is_refunded')
                        ->label('Refunded')
                        ->badge()
                        ->formatStateUsing(fn (bool $state): string => $state ? 'Yes' : 'No')
                        ->color(fn (bool $state): string => $state ? 'danger' : 'success'),
                    TextEntry::make('notes')
                        ->placeholder('—')
                        ->columnSpanFull(),
                    TextEntry::make('created_at')
                        ->label('Created At')
                        ->dateTime('d M Y, H:i'),
                ])
                ->columns(3),
        ]);
    }
}
