<?php

namespace App\Filament\Admin\Resources\ExpenseCategories;

use App\Filament\Admin\Resources\ExpenseCategories\Pages\ManageExpenseCategories;
use App\Models\ExpenseCategory;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class ExpenseCategoryResource extends Resource
{
    protected static ?string $model = ExpenseCategory::class;

    protected static ?int $navigationSort = 6;

    protected static string|UnitEnum|null $navigationGroup = 'Expenses';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBanknotes;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                TextInput::make('type')
                    ->maxLength(255),
                Toggle::make('pay_doc')
                    ->label('Doctor Payment'),
                Toggle::make('pay_others')
                    ->label('Other Payment'),
                Toggle::make('pay_users')
                    ->label('User Payment'),
                Toggle::make('pay_patient')
                    ->label('Patient Payment'),
                Toggle::make('allow_petty_cash')
                    ->label('Allow Petty Cash')
                    ->default(true),
                Toggle::make('allow_voucher')
                    ->label('Allow Voucher')
                    ->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('type')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                IconColumn::make('pay_doc')
                    ->label('Doctor')
                    ->boolean(),
                IconColumn::make('pay_others')
                    ->label('Others')
                    ->boolean(),
                IconColumn::make('pay_users')
                    ->label('Users')
                    ->boolean(),
                IconColumn::make('pay_patient')
                    ->label('Patient')
                    ->boolean(),
                IconColumn::make('allow_petty_cash')
                    ->label('Petty Cash')
                    ->boolean(),
                IconColumn::make('allow_voucher')
                    ->label('Voucher')
                    ->boolean(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->recordActions([
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
            'index' => ManageExpenseCategories::route('/'),
        ];
    }
}
