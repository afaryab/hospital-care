<?php

namespace App\Filament\Admin\Resources\Closings;

use App\Filament\Admin\Resources\Closings\Pages\ListClosings;
use App\Filament\Admin\Resources\Closings\Pages\ViewClosing;
use App\Filament\Admin\Resources\Closings\Schemas\ClosingInfolist;
use App\Filament\Admin\Resources\Closings\Tables\ClosingsTable;
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
            'view' => ViewClosing::route('/{record}'),
        ];
    }
}
