<?php

namespace App\Filament\Admin\Resources\StockItems;

use App\Filament\Admin\Resources\StockItems\Pages\CreateStockItem;
use App\Filament\Admin\Resources\StockItems\Pages\EditStockItem;
use App\Filament\Admin\Resources\StockItems\Pages\ListStockItems;
use App\Models\StockCategory;
use App\Models\StockItem;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
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
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use UnitEnum;

class StockItemResource extends Resource
{
    protected static ?string $model = StockItem::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedArchiveBox;

    protected static string|UnitEnum|null $navigationGroup = 'Inventory';

    protected static ?string $label = 'Stock Item';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')->required()->maxLength(255)->columnSpan(2),
            TextInput::make('sku')->label('SKU')->maxLength(255),
            Select::make('category_id')
                ->label('Category')
                ->options(fn () => StockCategory::orderBy('name')->pluck('name', 'id'))
                ->searchable()
                ->required(),
            TextInput::make('unit')->label('Unit (e.g. tablets, ml)')->maxLength(50)->default('units'),
            TextInput::make('reorder_level')->label('Reorder Level')->numeric()->default(0),
            TextInput::make('default_vendor')->label('Default Vendor')->maxLength(255),
            Toggle::make('is_active')->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('name')
            ->columns([
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('sku')->label('SKU')->searchable()->fontFamily('mono')->toggleable(),
                TextColumn::make('category.name')->label('Category')->sortable(),
                TextColumn::make('unit')->sortable(),
                TextColumn::make('reorder_level')->label('Reorder at')->numeric()->sortable(),
                TextColumn::make('default_vendor')->label('Vendor')->toggleable(),
                IconColumn::make('is_active')->label('Active')->boolean(),
            ])
            ->filters([
                SelectFilter::make('category_id')
                    ->label('Category')
                    ->options(fn () => StockCategory::orderBy('name')->pluck('name', 'id'))
                    ->searchable(),
                TernaryFilter::make('is_active')->label('Active'),
            ])
            ->recordActions([EditAction::make()])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])])
            ->striped();
    }

    public static function getPages(): array
    {
        return [
            'index' => ListStockItems::route('/'),
            'create' => CreateStockItem::route('/create'),
            'edit' => EditStockItem::route('/{record}/edit'),
        ];
    }
}
