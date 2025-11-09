<?php

namespace App\Filament\Resources\Closings\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ClosingsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('reception_id')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('user_id')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('status')
                    ->searchable(),
                TextColumn::make('opening_amount')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('closing_amount')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('closing_amount_cash')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('closing_amount_cheque')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('closing_amount_card')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('expense_payed')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('cash_recieving_time')
                    ->dateTime()
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
