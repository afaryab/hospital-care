<?php

namespace App\Filament\Resources\Receptions\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ReceptionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('is_allowed_to_pay_voucher')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('is_allowed_to_pay_from_petty_cash')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('is_cash_allowed')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('is_cheques_allowed')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('is_card_allowed')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
