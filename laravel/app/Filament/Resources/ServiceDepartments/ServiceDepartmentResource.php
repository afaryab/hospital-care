<?php

namespace App\Filament\Resources\ServiceDepartments;

use App\Filament\Resources\ServiceDepartments\Pages\CreateServiceDepartment;
use App\Filament\Resources\ServiceDepartments\Pages\EditServiceDepartment;
use App\Filament\Resources\ServiceDepartments\Pages\ListServiceDepartments;
use App\Filament\Resources\ServiceDepartments\Pages\ViewServiceDepartment;
use App\Filament\Resources\ServiceDepartments\Schemas\ServiceDepartmentForm;
use App\Filament\Resources\ServiceDepartments\Schemas\ServiceDepartmentInfolist;
use App\Filament\Resources\ServiceDepartments\Tables\ServiceDepartmentsTable;
use App\Models\ServiceDepartment;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ServiceDepartmentResource extends Resource
{
    protected static ?string $model = ServiceDepartment::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return ServiceDepartmentForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ServiceDepartmentInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ServiceDepartmentsTable::configure($table);
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
            'index' => ListServiceDepartments::route('/'),
            'create' => CreateServiceDepartment::route('/create'),
            'view' => ViewServiceDepartment::route('/{record}'),
            'edit' => EditServiceDepartment::route('/{record}/edit'),
        ];
    }
}
