<?php

namespace App\Filament\Admin\Resources\BankTransactions\Tables;

use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class BankTransactionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('bankAccount.name')
                    ->label('Bank Account')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('transaction_date')
                    ->date()
                    ->sortable(),
                TextColumn::make('description')
                    ->limit(40)
                    ->toggleable(),
                TextColumn::make('reference_number')
                    ->searchable()
                    ->copyable(),
                TextColumn::make('credit')
                    ->money('PKR')
                    ->sortable(),
                TextColumn::make('debit')
                    ->money('PKR')
                    ->sortable(),
                TextColumn::make('balance')
                    ->money('PKR'),
                IconColumn::make('linked_transaction_id')
                    ->label('Linked')
                    ->boolean()
                    ->trueIcon('heroicon-o-link')
                    ->falseIcon('heroicon-o-x-mark'),
            ])
            ->filters([
                SelectFilter::make('bank_account_id')
                    ->label('Bank Account')
                    ->relationship('bankAccount', 'name'),
                SelectFilter::make('linked')
                    ->label('Link Status')
                    ->options([
                        'linked' => 'Linked',
                        'unlinked' => 'Unlinked',
                    ])
                    ->query(fn ($query, $data) => match ($data['value'] ?? null) {
                        'linked' => $query->whereNotNull('linked_transaction_id'),
                        'unlinked' => $query->whereNull('linked_transaction_id'),
                        default => $query,
                    }),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->defaultSort('transaction_date', 'desc');
    }
}
