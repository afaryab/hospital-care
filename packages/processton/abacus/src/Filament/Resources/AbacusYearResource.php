<?php

namespace Processton\Abacus\Filament\Resources;

use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Processton\Abacus\Filament\Resources\AbacusYearResource\Pages;
use Processton\Abacus\Models\AbacusYear;
use UnitEnum;

class AbacusYearResource extends Resource
{
    protected static ?string $model = AbacusYear::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-book-open';

    protected static ?string $navigationLabel = 'Books';

    protected static UnitEnum|string|null $navigationGroup = 'Abacus';

    protected static ?int $navigationSort = 5;

    public static function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Forms\Components\DatePicker::make('start_date')
                    ->required(),

                Forms\Components\DatePicker::make('end_date')
                    ->required(),

                Forms\Components\Select::make('status')
                    ->label('Status')
                    ->options([
                        1 => 'Active',
                        2 => 'Archived',
                        0 => 'Inactive',
                    ])
                    ->default(1)
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('start_date')
                    ->label('Start Date')
                    ->date()
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('end_date')
                    ->label('End Date')
                    ->date()
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->formatStateUsing(function (AbacusYear $record) {
                        if ($record->status == 1) {
                            return 'Active';
                        } elseif ($record->status == 2) {
                            return 'Archived';
                        } else {
                            return 'Inactive';
                        }
                    })
                    ->color(fn ($state): string => match ($state) {
                        1 => 'success',
                        0 => 'warning',
                        2 => 'info',
                        default => 'secondary',
                    })
                    ->icon(fn ($state): string => match ($state) {
                        1 => 'heroicon-o-check-circle',
                        2 => 'heroicon-s-archive-box',
                        0 => 'heroicon-o-x-circle',
                        default => 'heroicon-o-x-circle'
                    })->sortable(),
                Tables\Columns\TextColumn::make('debit')
                    ->label('Debit'),
                Tables\Columns\TextColumn::make('credit')
                    ->label('Credit'),
                Tables\Columns\TextColumn::make('balance')
                    ->label('Balance')
                    ->formatStateUsing(function (AbacusYear $record) {
                        return $record->credit - $record->debit;
                    }),
            ])
            ->filters([
                //
            ])
            ->actions([
                EditAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAbacusYears::route('/'),
        ];
    }
}
