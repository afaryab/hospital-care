<?php

namespace App\Filament\Resources\Closings;

use App\Filament\Resources\Closings\Pages\CreateClosing;
use App\Filament\Resources\Closings\Pages\EditClosing;
use App\Filament\Resources\Closings\Pages\ListClosings;
use App\Filament\Resources\Closings\Pages\ViewClosing;
use App\Filament\Resources\Closings\Schemas\ClosingForm;
use App\Filament\Resources\Closings\Schemas\ClosingInfolist;
use App\Filament\Resources\Closings\Tables\ClosingsTable;
use App\Models\Closing;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ClosingResource extends Resource
{
    protected static ?string $model = Closing::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'ct_number';

    public static function form(Schema $schema): Schema
    {
        return ClosingForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ClosingInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ClosingsTable::configure($table);
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
            'index' => ListClosings::route('/'),
            'create' => CreateClosing::route('/create'),
            'view' => ViewClosing::route('/{record}'),
            'edit' => EditClosing::route('/{record}/edit'),
        ];
    }
}
