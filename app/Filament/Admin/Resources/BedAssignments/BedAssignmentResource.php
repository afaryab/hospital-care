<?php

namespace App\Filament\Admin\Resources\BedAssignments;

use App\Filament\Admin\Resources\BedAssignments\Pages\CreateBedAssignment;
use App\Filament\Admin\Resources\BedAssignments\Pages\EditBedAssignment;
use App\Filament\Admin\Resources\BedAssignments\Pages\ListBedAssignments;
use App\Filament\Admin\Resources\BedAssignments\Schemas\BedAssignmentForm;
use App\Filament\Admin\Resources\BedAssignments\Tables\BedAssignmentsTable;
use App\Models\BedAssignment;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BedAssignmentResource extends Resource
{
    protected static ?string $model = BedAssignment::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserGroup;

    protected static string|\UnitEnum|null $navigationGroup = 'Indoor';

    protected static ?int $navigationSort = 4;

    protected static ?string $navigationLabel = 'Admissions';

    public static function form(Schema $schema): Schema
    {
        return BedAssignmentForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BedAssignmentsTable::configure($table);
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
            'index' => ListBedAssignments::route('/'),
            'create' => CreateBedAssignment::route('/create'),
            'edit' => EditBedAssignment::route('/{record}/edit'),
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
