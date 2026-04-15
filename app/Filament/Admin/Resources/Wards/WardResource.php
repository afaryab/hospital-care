<?php

namespace App\Filament\Admin\Resources\Wards;

use App\Filament\Admin\Resources\Wards\Pages\CreateWard;
use App\Filament\Admin\Resources\Wards\Pages\EditWard;
use App\Filament\Admin\Resources\Wards\Pages\ListWards;
use App\Filament\Admin\Resources\Wards\Schemas\WardForm;
use App\Filament\Admin\Resources\Wards\Tables\WardsTable;
use App\Models\Ward;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class WardResource extends Resource
{
    protected static ?string $model = Ward::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingOffice2;

    protected static string|\UnitEnum|null $navigationGroup = 'Indoor';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return WardForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return WardsTable::configure($table);
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
            'index' => ListWards::route('/'),
            'create' => CreateWard::route('/create'),
            'edit' => EditWard::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
