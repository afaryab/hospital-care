<?php

namespace App\Filament\Accounts\Resources\ExpenseVouchers\Tables;

use App\Models\ExpenseVoucher;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ExpenseVouchersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('vc_number')
                    ->label('Voucher Number')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('expCategory.name')
                    ->label('Expense Category')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('serviceOrder.so_number')
                    ->label('Service Order')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('payed_to')
                    ->label('Paid To')
                    ->formatStateUsing(function (ExpenseVoucher $record) {
                        return $record->payedTo?->name ?? $record->payed_to_name ?? 'N/A';
                    })
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query->where(function (Builder $subQuery) use ($search) {
                            $subQuery
                                ->whereHas('payedTo', fn (Builder $payeeQuery) => $payeeQuery->where('name', 'like', "%{$search}%"))
                                ->orWhere('payed_to_name', 'like', "%{$search}%");
                        });
                    }),
                TextColumn::make('amount')
                    ->numeric(decimalPlaces: 2)
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('exp_category_id')
                    ->label('Expense Category')
                    ->relationship('expCategory', 'name')
                    ->searchable()
                    ->preload()
                    ->columnSpanFull(),
                SelectFilter::make('payed_to')
                    ->label('Paid To')
                    ->relationship('payedTo', 'name')
                    ->searchable()
                    ->preload()
                    ->columnSpanFull(),
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
