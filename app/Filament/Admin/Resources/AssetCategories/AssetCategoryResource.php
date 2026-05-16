<?php

namespace App\Filament\Admin\Resources\AssetCategories;

use App\Enum\DepreciationMethod;
use App\Filament\Admin\Resources\AssetCategories\Pages\CreateAssetCategory;
use App\Filament\Admin\Resources\AssetCategories\Pages\EditAssetCategory;
use App\Filament\Admin\Resources\AssetCategories\Pages\ListAssetCategories;
use App\Models\AssetCategory;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class AssetCategoryResource extends Resource
{
    protected static ?string $model = AssetCategory::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTag;

    protected static string|UnitEnum|null $navigationGroup = 'Assets';

    protected static ?string $label = 'Asset Category';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')
                ->required()
                ->maxLength(255),
            Select::make('depreciation_method')
                ->options(collect(DepreciationMethod::cases())->mapWithKeys(fn ($c) => [$c->value => ucwords(str_replace('_', ' ', $c->value))]))
                ->default(DepreciationMethod::StraightLine->value)
                ->required(),
            TextInput::make('useful_life_years')
                ->label('Useful Life (Years)')
                ->numeric()
                ->minValue(0)
                ->default(5),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('depreciation_method')
                    ->label('Depreciation')
                    ->badge()
                    ->formatStateUsing(fn ($state) => ucwords(str_replace('_', ' ', $state))),
                TextColumn::make('useful_life_years')->label('Life (yrs)')->sortable(),
                TextColumn::make('assets_count')->label('Assets')->counts('assets')->sortable(),
                TextColumn::make('created_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->recordActions([EditAction::make(), DeleteAction::make()])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAssetCategories::route('/'),
            'create' => CreateAssetCategory::route('/create'),
            'edit' => EditAssetCategory::route('/{record}/edit'),
        ];
    }
}
