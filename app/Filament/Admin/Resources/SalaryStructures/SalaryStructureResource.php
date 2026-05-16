<?php

namespace App\Filament\Admin\Resources\SalaryStructures;

use App\Filament\Admin\Resources\SalaryStructures\Pages\CreateSalaryStructure;
use App\Filament\Admin\Resources\SalaryStructures\Pages\EditSalaryStructure;
use App\Filament\Admin\Resources\SalaryStructures\Pages\ListSalaryStructures;
use App\Models\SalaryStructure;
use App\Models\User;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class SalaryStructureResource extends Resource
{
    protected static ?string $model = SalaryStructure::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCurrencyDollar;

    protected static string|UnitEnum|null $navigationGroup = 'HR & Payroll';

    protected static ?string $label = 'Salary Structure';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('user_id')
                ->label('Employee')
                ->options(fn () => User::orderBy('name')->pluck('name', 'id'))
                ->searchable()
                ->required(),
            TextInput::make('basic_salary')->label('Basic Salary (PKR)')->numeric()->required()->minValue(0),
            TextInput::make('housing_allowance')->label('Housing Allowance (PKR)')->numeric()->default(0),
            TextInput::make('medical_allowance')->label('Medical Allowance (PKR)')->numeric()->default(0),
            TextInput::make('transport_allowance')->label('Transport Allowance (PKR)')->numeric()->default(0),
            DatePicker::make('effective_from')->label('Effective From')->required(),
            DatePicker::make('effective_to')->label('Effective To')->nullable(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('effective_from', 'desc')
            ->columns([
                TextColumn::make('user.name')->label('Employee')->searchable()->sortable(),
                TextColumn::make('basic_salary')->label('Basic (PKR)')->numeric(2)->sortable(),
                TextColumn::make('housing_allowance')->label('Housing (PKR)')->numeric(2)->toggleable(),
                TextColumn::make('medical_allowance')->label('Medical (PKR)')->numeric(2)->toggleable(),
                TextColumn::make('transport_allowance')->label('Transport (PKR)')->numeric(2)->toggleable(),
                TextColumn::make('effective_from')->label('From')->date('d M Y')->sortable(),
                TextColumn::make('effective_to')->label('To')->date('d M Y')->placeholder('Current')->sortable(),
            ])
            ->recordActions([EditAction::make()])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])])
            ->striped();
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSalaryStructures::route('/'),
            'create' => CreateSalaryStructure::route('/create'),
            'edit' => EditSalaryStructure::route('/{record}/edit'),
        ];
    }
}
