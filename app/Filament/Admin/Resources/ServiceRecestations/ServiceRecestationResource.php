<?php

namespace App\Filament\Admin\Resources\ServiceRecestations;

use App\Filament\Admin\Resources\ServiceRecestations\Pages\ManageServiceRecestations;
use App\Models\ServiceRecestation;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class ServiceRecestationResource extends Resource
{
    protected static ?string $model = ServiceRecestation::class;

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?int $navigationSort = 4;

    protected static string|UnitEnum|null $navigationGroup = 'Services';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return ServiceRecestationForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ServiceRecestationsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageServiceRecestations::route('/'),
        ];
    }
}
