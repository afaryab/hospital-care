<?php

namespace App\Filament\Admin\Resources\DeathCertificates;

use App\Enum\DeathCertificateManner;
use App\Filament\Admin\Resources\DeathCertificates\Pages\CreateDeathCertificate;
use App\Filament\Admin\Resources\DeathCertificates\Pages\EditDeathCertificate;
use App\Filament\Admin\Resources\DeathCertificates\Pages\ListDeathCertificates;
use App\Models\DeathCertificate;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class DeathCertificateResource extends Resource
{
    protected static ?string $model = DeathCertificate::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentText;

    protected static string|UnitEnum|null $navigationGroup = 'Clinical';

    protected static ?int $navigationSort = 2;

    protected static ?string $recordTitleAttribute = 'certificate_number';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('service_order_id')
                ->relationship('serviceOrder', 'so_number')
                ->searchable()
                ->required()
                ->disabledOn('edit'),
            TextInput::make('certificate_number')
                ->disabled()
                ->dehydrated(false)
                ->visibleOn('edit'),
            DatePicker::make('date_of_death'),
            TimePicker::make('time_of_death'),
            TextInput::make('place_of_death')->maxLength(255),
            Select::make('manner_of_death')
                ->options(collect(DeathCertificateManner::cases())
                    ->mapWithKeys(fn (DeathCertificateManner $manner) => [$manner->value => $manner->label()])
                    ->toArray())
                ->native(false),
            Textarea::make('antecedent_cause')->columnSpanFull(),
            TextInput::make('informant_name')->maxLength(255),
            TextInput::make('informant_relation')->maxLength(255),
            TextInput::make('informant_cnic')->maxLength(20),
            Textarea::make('remarks')->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('id', 'desc')
            ->columns([
                TextColumn::make('certificate_number')->searchable()->sortable(),
                TextColumn::make('serviceOrder.so_number')->label('Service Order')->searchable(),
                TextColumn::make('serviceOrder.patient.name')->label('Patient')->searchable(),
                TextColumn::make('date_of_death')->date()->sortable(),
                TextColumn::make('manner_of_death')->badge(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
            'index' => ListDeathCertificates::route('/'),
            'create' => CreateDeathCertificate::route('/create'),
            'edit' => EditDeathCertificate::route('/{record}/edit'),
        ];
    }
}
