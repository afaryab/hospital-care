<?php

namespace Processton\Abacus\Filament\Resources;

use BackedEnum;
use Processton\Abacus\Filament\Resources\AbacusIncomingResource\Pages;
use Processton\Abacus\Models\AbacusIncoming;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Processton\Abacus\Models\Currency;

class AbacusIncomingResource extends Resource
{
    protected static ?string $model = AbacusIncoming::class;

    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-inbox-arrow-down';

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationLabel = 'Incoming';

    protected static ?string $pluralModelLabel = 'Incomings';

    protected static ?string $breadcrumb = 'Incoming';

    public static function form(Schema $form): Schema
    {
        $currency = Currency::find(config('org.primary_currency'));
        return $form
            ->schema([
                Forms\Components\TextInput::make('amount')
                    ->label('Amount')
                    ->suffix($currency?->code, true)
                    ->required()
                    ->numeric()
                    ->minValue(0),
                Forms\Components\DatePicker::make('date')
                    ->label('Date')
                    ->required()
                    ->default(now()),
                Forms\Components\TextInput::make('reference')
                    ->label('Reference')
                    ->required()
                    ->columnSpanFull()
                    ->maxLength(255),
                Forms\Components\Textarea::make('description')
                    ->label('Description')
                    ->maxLength(500)
                    ->columnSpanFull()
                    ->nullable(),
                
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        $currency = Currency::find(config('org.primary_currency')) ?? Currency::where('code', 'PKR')->first();
        return $schema
            ->schema([
                \Filament\Infolists\Components\TextEntry::make('amount')
                    ->label('Amount')
                    ->numeric()
                    ->money($currency?->code, true),
                \Filament\Infolists\Components\TextEntry::make('reference')
                    ->label('Reference'),
                \Filament\Infolists\Components\TextEntry::make('date')
                    ->label('Date')
                    ->formatStateUsing(fn ($state) => $state?->format('d-m-Y')),
                \Filament\Infolists\Components\TextEntry::make('description')
                    ->label('Description')
                    ->columnSpanFull(),
                \Filament\Infolists\Components\RepeatableEntry::make('transactions')
                    ->label('Transactions')
                    ->schema([
                        \Filament\Infolists\Components\TextEntry::make('date')
                            ->label('Date')
                            ->formatStateUsing(fn ($state) => $state?->format('d-m-Y')),
                        \Filament\Infolists\Components\TextEntry::make('amount')
                            ->label('Amount')
                            ->numeric()
                            ->money($currency?->code, true),
                        \Filament\Infolists\Components\TextEntry::make('entry_type')
                            ->label('Type'),
                        \Filament\Infolists\Components\TextEntry::make('account.name')
                            ->label('Account')
                    ])->columns(4)->columnSpanFull(),
            ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        $currency = Currency::find(config('org.primary_currency'));
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('amount')
                    ->label('Amount')
                    ->money($currency?->code, true)
                    ->sortable(),
                Tables\Columns\TextColumn::make('reference')
                    ->label('Reference')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('date')
                    ->label('Date')
                    ->date()
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('transactions_count')
                    ->label('Status')
                    ->counts('transactions')
                    ->badge()
                    ->color(fn (int $state): string => $state > 0 ? 'success' : 'gray')
                    ->formatStateUsing(fn (int $state): string => $state > 0 ? 'Converted' : 'Pending'),
            ])
            ->filters([
                Tables\Filters\Filter::make('converted')
                    ->label('Converted Only')
                    ->query(fn (Builder $query): Builder => $query->has('transactions')),
                Tables\Filters\Filter::make('pending')
                    ->label('Pending Only')
                    ->query(fn (Builder $query): Builder => $query->doesntHave('transactions')),
            ])
            ->recordAction(fn ($record) => $record->transactions_count > 0 ? 'view' : 'edit')
            ->recordUrl(fn ($record) => $record->transactions_count <= 0 ? static::getUrl('edit', ['record' => $record]) : null)
            ->actions([
                \Filament\Actions\EditAction::make()
                    ->label('')
                    ->icon('heroicon-o-arrow-right-circle')
                    ->iconPosition('after')
                    ->visible(fn ($record) => $record->transactions_count === 0),
                \Filament\Actions\ViewAction::make()->slideOver()
                    ->modalHeading('Incoming Transaction Details')
                    ->label('')
                    ->iconPosition('after')
                    ->visible(fn ($record) => $record->transactions_count !== 0),
            ])
            ->bulkActions([
                // Tables\Actions\BulkActionGroup::make([
                //     Tables\Actions\DeleteBulkAction::make(),
                // ]),
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
            'index' => Pages\ListAbacusIncomings::route('/'),
            // 'create' => Pages\CreateAbacusIncoming::route('/create'),
            'edit' => Pages\EditAbacusIncoming::route('/{record}/edit'),
        ];
    }
}
