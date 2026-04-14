<?php

namespace App\Filament\Admin\Resources\PaymentMethods\Tables;

use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PaymentMethodsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('slug')
                    ->badge()
                    ->color('gray')
                    ->searchable(),
                IconColumn::make('id_required')
                    ->label('Ref Required')
                    ->boolean(),
                TextColumn::make('payables')
                    ->label('Requires Payable')
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'bank_account' => 'Bank Account',
                        'panel' => 'Panel',
                        default => '—',
                    })
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'bank_account' => 'info',
                        'panel' => 'warning',
                        default => 'gray',
                    }),
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
