<?php

namespace App\Filament\Admin\Resources\BankAccounts\Tables;

use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class BankAccountsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Alias')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('bank_name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('account_number')
                    ->toggleable(),
                TextColumn::make('iban')
                    ->label('IBAN')
                    ->toggleable(),
                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),
                TextColumn::make('bank_transactions_count')
                    ->counts('bankTransactions')
                    ->label('Statements'),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([])
            ->recordActions([
                EditAction::make(),
            ])
            ->defaultSort('id');
    }
}
