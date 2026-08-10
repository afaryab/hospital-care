<?php

namespace App\Filament\Admin\Resources\ServiceDepartments;

use App\Enum\ServiceOrderTemplate;
use App\Filament\Admin\Resources\ServiceDepartments\Pages\ManageServiceDepartments;
use App\Models\ServiceDepartment;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
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
                    ->directory('service-departments')
                    ->visibility('public')
                    ->required(),
                TextInput::make('have_composit_services')
                    ->required()
                    ->numeric(),
                Select::make('service_order_template')
                    ->label('Service Order Print Template')
                    ->helperText('Template used when staff print a service order for this department. Leave empty to use the default detailed template.')
                    ->native(false)
                    ->options(collect(ServiceOrderTemplate::cases())
                        ->mapWithKeys(fn (ServiceOrderTemplate $template) => [$template->value => $template->label()])
                        ->toArray())
                    ->placeholder('Default ('.ServiceOrderTemplate::default()->label().')'),
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
                ImageColumn::make('image_url')
                    ->label('Image'),
                TextColumn::make('have_composit_services')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('service_order_template')
                    ->label('Print Template')
                    ->formatStateUsing(fn (?ServiceOrderTemplate $state) => $state?->label() ?? 'Default ('.ServiceOrderTemplate::default()->label().')')
                    ->badge(),
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
