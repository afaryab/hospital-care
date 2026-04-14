<?php

namespace App\Filament\Admin\Resources\PanelCheques;

use App\Filament\Admin\Resources\PanelCheques\Pages\CreatePanelCheque;
use App\Filament\Admin\Resources\PanelCheques\Pages\EditPanelCheque;
use App\Filament\Admin\Resources\PanelCheques\Pages\ListPanelCheques;
use App\Filament\Admin\Resources\PanelCheques\Schemas\PanelChequeForm;
use App\Filament\Admin\Resources\PanelCheques\Tables\PanelChequesTable;
use App\Models\PanelCheque;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class PanelChequeResource extends Resource
{
    protected static ?string $model = PanelCheque::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentCheck;

    protected static string|\UnitEnum|null $navigationGroup = 'Finance';

    protected static ?int $navigationSort = 11;

    public static function form(Schema $schema): Schema
    {
        return PanelChequeForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PanelChequesTable::configure($table);
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
            'index' => ListPanelCheques::route('/'),
            'create' => CreatePanelCheque::route('/create'),
            'edit' => EditPanelCheque::route('/{record}/edit'),
        ];
    }
}
