<?php

namespace App\Filament\Resources\ServiceRecestations;

use App\Filament\Resources\ServiceRecestations\Pages\CreateServiceRecestation;
use App\Filament\Resources\ServiceRecestations\Pages\EditServiceRecestation;
use App\Filament\Resources\ServiceRecestations\Pages\ListServiceRecestations;
use App\Filament\Resources\ServiceRecestations\Pages\ViewServiceRecestation;
use App\Filament\Resources\ServiceRecestations\Schemas\ServiceRecestationForm;
use App\Filament\Resources\ServiceRecestations\Schemas\ServiceRecestationInfolist;
use App\Filament\Resources\ServiceRecestations\Tables\ServiceRecestationsTable;
use App\Models\ServiceRecestation;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ServiceRecestationResource extends Resource
{
    protected static ?string $model = ServiceRecestation::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return ServiceRecestationForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ServiceRecestationInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ServiceRecestationsTable::configure($table);
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
            'index' => ListServiceRecestations::route('/'),
            'create' => CreateServiceRecestation::route('/create'),
            'view' => ViewServiceRecestation::route('/{record}'),
            'edit' => EditServiceRecestation::route('/{record}/edit'),
        ];
    }
}
