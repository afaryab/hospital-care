<?php

namespace App\Filament\Admin\Resources\Triages;

use App\Filament\Admin\Resources\Triages\Pages\ManageTriages;
use App\Models\Triage;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use UnitEnum;

class TriageResource extends Resource
{
    protected static ?string $model = Triage::class;

    protected static string|UnitEnum|null $navigationGroup = 'Services';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-exclamation-triangle';

    protected static ?int $navigationSort = 4;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')->required()->maxLength(255),
            Select::make('color')
                ->label('Color')
                ->options(Triage::colorOptions())
                ->required()
                ->searchable(),
            TextInput::make('priority')
                ->label('Priority')
                ->helperText('Lower number = more urgent. Controls display and sort order.')
                ->required()
                ->numeric()
                ->default(99),
            Textarea::make('description')->rows(3)->maxLength(1000),
            Toggle::make('is_active')->label('Active')->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('priority')
            ->columns([
                TextColumn::make('priority')->sortable(),
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('color')->badge()->sortable(),
                TextColumn::make('description')->limit(60)->toggleable(),
                IconColumn::make('is_active')->label('Active')->boolean()->sortable(),
                TextColumn::make('created_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TernaryFilter::make('is_active')->label('Active'),
            ])
            ->recordActions([EditAction::make(), DeleteAction::make()])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return ['index' => ManageTriages::route('/')];
    }
}
