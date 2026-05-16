<?php

namespace App\Filament\Admin\Resources\StockCategories;

use App\Filament\Admin\Resources\StockCategories\Pages\CreateStockCategory;
use App\Filament\Admin\Resources\StockCategories\Pages\EditStockCategory;
use App\Filament\Admin\Resources\StockCategories\Pages\ListStockCategories;
use App\Models\StockCategory;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class StockCategoryResource extends Resource
{
    protected static ?string $model = StockCategory::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedSquares2x2;

    protected static string|UnitEnum|null $navigationGroup = 'Inventory';

    protected static ?string $label = 'Stock Category';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')->required()->maxLength(255),
            Select::make('parent_id')
                ->label('Parent Category')
                ->options(fn () => StockCategory::orderBy('name')->pluck('name', 'id'))
                ->searchable()
                ->nullable(),
            Toggle::make('is_medicine')->label('Is Medicine / Consumable')->default(false),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('parent.name')->label('Parent')->placeholder('—')->sortable(),
                IconColumn::make('is_medicine')->label('Medicine')->boolean(),
                TextColumn::make('children_count')->label('Sub-categories')->counts('children'),
                TextColumn::make('stock_items_count')->label('Items')->counts('stockItems'),
            ])
            ->recordActions([EditAction::make(), DeleteAction::make()])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListStockCategories::route('/'),
            'create' => CreateStockCategory::route('/create'),
            'edit' => EditStockCategory::route('/{record}/edit'),
        ];
    }
}
