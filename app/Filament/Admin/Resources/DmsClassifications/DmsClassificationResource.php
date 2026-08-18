<?php

namespace App\Filament\Admin\Resources\DmsClassifications;

use App\Filament\Admin\Resources\DmsClassifications\Pages\CreateDmsClassification;
use App\Filament\Admin\Resources\DmsClassifications\Pages\EditDmsClassification;
use App\Filament\Admin\Resources\DmsClassifications\Pages\ListDmsClassifications;
use App\Models\DmsClassification;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class DmsClassificationResource extends Resource
{
    protected static ?string $model = DmsClassification::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTag;

    protected static string|UnitEnum|null $navigationGroup = 'Documents';

    protected static ?string $label = 'Document Classification';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')
                ->required()
                ->maxLength(255),
            TextInput::make('code')
                ->required()
                ->unique(ignoreRecord: true)
                ->maxLength(50),
            Select::make('security_level')
                ->options([
                    'public' => 'Public',
                    'internal' => 'Internal',
                    'confidential' => 'Confidential',
                    'restricted' => 'Restricted',
                ])
                ->default('internal')
                ->required(),
            TextInput::make('retention_years')
                ->label('Retention (Years)')
                ->numeric()
                ->minValue(1),
            Textarea::make('description')
                ->maxLength(1000)
                ->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('code')->badge()->searchable()->sortable(),
                TextColumn::make('security_level')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'public' => 'success',
                        'internal' => 'info',
                        'confidential' => 'warning',
                        'restricted' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state) => ucfirst($state)),
                TextColumn::make('retention_years')->label('Retention (yrs)')->sortable(),
                TextColumn::make('folders_count')->label('Folders')->counts('folders')->sortable(),
                TextColumn::make('documents_count')->label('Documents')->counts('documents')->sortable(),
            ])
            ->recordActions([EditAction::make(), DeleteAction::make()])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListDmsClassifications::route('/'),
            'create' => CreateDmsClassification::route('/create'),
            'edit' => EditDmsClassification::route('/{record}/edit'),
        ];
    }
}
