<?php

namespace App\Filament\Resources\Receptions;

use App\Filament\Resources\Receptions\Pages\ManageReceptions;
use App\Models\Reception;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ReceptionResource extends Resource
{
    protected static ?string $model = Reception::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('allowed_departments'),
                TextInput::make('is_allowed_to_pay_voucher')
                    ->required()
                    ->numeric(),
                TextInput::make('is_allowed_to_pay_from_petty_cash')
                    ->required()
                    ->numeric(),
                TextInput::make('is_cash_allowed')
                    ->required()
                    ->numeric(),
                TextInput::make('is_cheques_allowed')
                    ->required()
                    ->numeric(),
                TextInput::make('is_card_allowed')
                    ->required()
                    ->numeric(),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('name'),
                TextEntry::make('is_allowed_to_pay_voucher')
                    ->numeric(),
                TextEntry::make('is_allowed_to_pay_from_petty_cash')
                    ->numeric(),
                TextEntry::make('is_cash_allowed')
                    ->numeric(),
                TextEntry::make('is_cheques_allowed')
                    ->numeric(),
                TextEntry::make('is_card_allowed')
                    ->numeric(),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
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
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageReceptions::route('/'),
        ];
    }
}
