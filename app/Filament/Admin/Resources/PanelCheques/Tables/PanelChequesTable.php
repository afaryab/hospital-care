<?php

namespace App\Filament\Admin\Resources\PanelCheques\Tables;

use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class PanelChequesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('panel.name')
                    ->label('Panel')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('cheque_number')
                    ->searchable()
                    ->copyable(),
                TextColumn::make('amount')
                    ->money('PKR')
                    ->sortable(),
                TextColumn::make('due_date')
                    ->date()
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'received' => 'success',
                        'bounced' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('bankAccount.name')
                    ->label('Bank Account')
                    ->toggleable(),
                TextColumn::make('received_at')
                    ->dateTime()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('panel_id')
                    ->label('Panel')
                    ->relationship('panel', 'name'),
                SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'received' => 'Received',
                        'bounced' => 'Bounced',
                    ]),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
