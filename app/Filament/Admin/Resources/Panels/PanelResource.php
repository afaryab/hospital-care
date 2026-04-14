<?php

namespace App\Filament\Admin\Resources\Panels;

use App\Filament\Admin\Resources\Panels\Pages\CreatePanel;
use App\Filament\Admin\Resources\Panels\Pages\EditPanel;
use App\Filament\Admin\Resources\Panels\Pages\ListPanels;
use App\Filament\Admin\Resources\Panels\Pages\ViewPanel;
use App\Models\Panel;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PanelResource extends Resource
{
    protected static ?string $model = Panel::class;

    protected static ?int $navigationSort = 7;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingOffice;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                TextInput::make('code')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),
                Toggle::make('is_active')
                    ->label('Active')
                    ->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('code')
                    ->searchable()
                    ->sortable(),
                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),
                TextColumn::make('pending_receivables')
                    ->label('Pending Receivables')
                    ->state(fn (Panel $record): int => $record->receaveables()->whereIn('status', ['unpaid', 'PENDING'])->count()),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPanels::route('/'),
            'create' => CreatePanel::route('/create'),
            'view' => ViewPanel::route('/{record}'),
            'edit' => EditPanel::route('/{record}/edit'),
        ];
    }
}
