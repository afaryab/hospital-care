<?php

namespace App\Filament\Admin\Resources\Icd10Codes;

use App\Filament\Admin\Resources\Icd10Codes\Pages\CreateIcd10Code;
use App\Filament\Admin\Resources\Icd10Codes\Pages\EditIcd10Code;
use App\Filament\Admin\Resources\Icd10Codes\Pages\ListIcd10Codes;
use App\Models\Icd10Code;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use UnitEnum;

class Icd10CodeResource extends Resource
{
    protected static ?string $model = Icd10Code::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentMagnifyingGlass;

    protected static string|UnitEnum|null $navigationGroup = 'Clinical';

    protected static ?string $label = 'ICD-10 Code';

    protected static ?string $pluralLabel = 'ICD-10 Codes';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('code')->required()->maxLength(20)->unique(ignoreRecord: true),
            TextInput::make('category')->maxLength(100),
            TextInput::make('description')->required()->maxLength(500)->columnSpan(2),
            Toggle::make('is_active')->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('code')
            ->columns([
                TextColumn::make('code')->searchable()->sortable()->fontFamily('mono'),
                TextColumn::make('description')->searchable()->limit(60),
                TextColumn::make('category')->searchable()->badge()->placeholder('—'),
                IconColumn::make('is_active')->label('Active')->boolean(),
            ])
            ->filters([
                TernaryFilter::make('is_active')->label('Active'),
            ])
            ->recordActions([EditAction::make(), DeleteAction::make()])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])])
            ->striped();
    }

    public static function getPages(): array
    {
        return [
            'index' => ListIcd10Codes::route('/'),
            'create' => CreateIcd10Code::route('/create'),
            'edit' => EditIcd10Code::route('/{record}/edit'),
        ];
    }
}
