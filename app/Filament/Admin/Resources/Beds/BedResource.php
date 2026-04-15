<?php

namespace App\Filament\Admin\Resources\Beds;

use App\Filament\Admin\Resources\Beds\Pages\CreateBed;
use App\Filament\Admin\Resources\Beds\Pages\EditBed;
use App\Filament\Admin\Resources\Beds\Pages\ListBeds;
use App\Filament\Admin\Resources\Beds\Schemas\BedForm;
use App\Filament\Admin\Resources\Beds\Tables\BedsTable;
use App\Models\Bed;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BedResource extends Resource
{
    protected static ?string $model = Bed::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedQueueList;

    protected static string|\UnitEnum|null $navigationGroup = 'Indoor';

    protected static ?int $navigationSort = 3;

    protected static ?string $recordTitleAttribute = 'bed_number';

    public static function form(Schema $schema): Schema
    {
        return BedForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BedsTable::configure($table);
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
            'index' => ListBeds::route('/'),
            'create' => CreateBed::route('/create'),
            'edit' => EditBed::route('/{record}/edit'),
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
