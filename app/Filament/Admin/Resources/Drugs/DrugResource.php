<?php

namespace App\Filament\Admin\Resources\Drugs;

use App\Filament\Admin\Resources\Drugs\Pages\CreateDrug;
use App\Filament\Admin\Resources\Drugs\Pages\EditDrug;
use App\Filament\Admin\Resources\Drugs\Pages\ListDrugs;
use App\Models\Drug;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use UnitEnum;

class DrugResource extends Resource
{
    protected static ?string $model = Drug::class;

    protected static string|UnitEnum|null $navigationGroup = 'Pharmacy';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-beaker';

    protected static ?int $navigationSort = 2;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        $types = array_combine(Drug::types(), Drug::types());
        $routes = array_combine(Drug::routes(), Drug::routes());
        $frequencies = array_combine(Drug::frequencies(), Drug::frequencies());

        return $schema->components([
            Section::make('Identity')->schema([
                TextInput::make('name')
                    ->label('Drug Name (Brand)')
                    ->required()
                    ->maxLength(255),
                TextInput::make('generic_name')
                    ->label('Generic Name / Salt')
                    ->maxLength(255),
                Select::make('type')
                    ->options($types)
                    ->searchable(),
                Select::make('drug_category_id')
                    ->label('Category')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload()
                    ->createOptionForm([
                        TextInput::make('name')->required(),
                    ]),
                TextInput::make('strength')->maxLength(100)->placeholder('500mg, 250mg/5ml'),
                TextInput::make('manufacturer')->maxLength(255),
                Toggle::make('is_active')->default(true)->inline(false),
            ])->columns(2),

            Section::make('Default Prescription')->schema([
                TextInput::make('default_dose')->label('Default Dose')->maxLength(100)->placeholder('1 tablet'),
                Select::make('default_frequency')
                    ->label('Default Frequency')
                    ->options($frequencies)
                    ->searchable(),
                TextInput::make('default_duration')->label('Default Duration')->maxLength(100)->placeholder('5 days'),
                Select::make('default_route')
                    ->label('Default Route')
                    ->options($routes)
                    ->searchable(),
            ])->columns(2),

            Section::make('Clinical Notes')->schema([
                Textarea::make('usage_instructions')->label('Usage Instructions')->rows(3),
                Textarea::make('contraindications')->rows(3),
                Textarea::make('side_effects')->label('Side Effects')->rows(3),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->description(fn ($record) => $record->generic_name),
                TextColumn::make('type')->badge()->sortable(),
                TextColumn::make('category.name')->label('Category')->sortable()->toggleable(),
                TextColumn::make('strength')->sortable()->toggleable(),
                TextColumn::make('default_frequency')->label('Frequency')->sortable()->toggleable(),
                TextColumn::make('default_route')->label('Route')->sortable()->toggleable(),
                IconColumn::make('is_active')->label('Active')->boolean()->sortable(),
            ])
            ->filters([
                SelectFilter::make('drug_category_id')
                    ->label('Category')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('type')
                    ->options(array_combine(Drug::types(), Drug::types())),
                TernaryFilter::make('is_active')->label('Active'),
            ])
            ->recordActions([EditAction::make()])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListDrugs::route('/'),
            'create' => CreateDrug::route('/create'),
            'edit' => EditDrug::route('/{record}/edit'),
        ];
    }
}
