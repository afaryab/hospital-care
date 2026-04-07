<?php

namespace App\Filament\Admin\Resources\ServiceDepartments;

use App\Filament\Admin\Resources\ServiceDepartments\Pages\ManageServiceDepartments;
use App\Models\ServiceDepartment;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use UnitEnum;

class ServiceDepartmentResource extends Resource
{
    protected static ?string $model = ServiceDepartment::class;

    protected static ?int $navigationSort = 2;

    protected static string|UnitEnum|null $navigationGroup = 'Services';

    // protected static string|BackedEnum|null $navigationIcon = 'healthicons-f-hospital';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('slug')
                    ->required(),
                FileUpload::make('image')
                    ->image()
                    ->disk('public')
                    ->required(),
                TextInput::make('have_composit_services')
                    ->required()
                    ->numeric(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('slug')
                    ->searchable(),
                ImageColumn::make('image')
                    ->disk('public')
                    ->state(function ($record) {
                        // Edit the state before rendering
                        if (Str::startsWith($record->image, 'http://') || Str::startsWith($record->image, 'https://')) {
                            return $record->image;
                        } elseif (Str::startsWith($record->image, '/img/')) {
                            return asset($record->image);
                        }

                        return asset('storage/'.$record->image);
                    }),
                TextColumn::make('have_composit_services')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageServiceDepartments::route('/'),
        ];
    }
}
