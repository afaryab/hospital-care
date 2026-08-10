<?php

namespace App\Filament\Admin\Resources\BirthCertificates;

use App\Filament\Admin\Resources\BirthCertificates\Pages\CreateBirthCertificate;
use App\Filament\Admin\Resources\BirthCertificates\Pages\EditBirthCertificate;
use App\Filament\Admin\Resources\BirthCertificates\Pages\ListBirthCertificates;
use App\Models\BirthCertificate;
use App\Models\ServiceOrder;
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
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class BirthCertificateResource extends Resource
{
    protected static ?string $model = BirthCertificate::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedGift;

    protected static string|UnitEnum|null $navigationGroup = 'Clinical';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'birth_certificate_number';

    public static function form(Schema $schema): Schema
    {
        $isLocked = fn (?BirthCertificate $record) => $record?->is_locked ?? false;

        return $schema->components([
            Select::make('service_order_id')
                ->label('Service Order (SO number or SO short code)')
                ->searchable()
                ->getSearchResultsUsing(fn (string $search) => ServiceOrder::query()
                    ->where('so_number', 'like', "%{$search}%")
                    ->orWhere('so_short', 'like', "%{$search}%")
                    ->limit(50)
                    ->get()
                    ->mapWithKeys(fn (ServiceOrder $serviceOrder) => [
                        $serviceOrder->id => "{$serviceOrder->so_number} ({$serviceOrder->so_short})",
                    ]))
                ->getOptionLabelUsing(function ($value) {
                    $serviceOrder = ServiceOrder::find($value);

                    return $serviceOrder ? "{$serviceOrder->so_number} ({$serviceOrder->so_short})" : null;
                })
                ->required()
                ->disabledOn('edit'),
            TextInput::make('birth_certificate_number')
                ->disabled()
                ->dehydrated(false)
                ->visibleOn('edit'),
            TextInput::make('child_name')->maxLength(255)->disabled($isLocked),
            DatePicker::make('date_of_birth')->disabled($isLocked),
            TimePicker::make('time_of_birth')->disabled($isLocked),
            Select::make('gender')
                ->options(['m' => 'Male', 'f' => 'Female', 't' => 'Transgender', 'o' => 'Other'])
                ->native(false)
                ->disabled($isLocked),
            TextInput::make('place_of_birth')->maxLength(255)->disabled($isLocked),
            TextInput::make('weight_at_birth')->numeric()->suffix('kg')->disabled($isLocked),
            TextInput::make('mother_name')->maxLength(255)->disabled($isLocked),
            TextInput::make('mother_cnic')->maxLength(20)->disabled($isLocked),
            TextInput::make('father_name')->maxLength(255)->disabled($isLocked),
            TextInput::make('father_cnic')->maxLength(20)->disabled($isLocked),
            Select::make('attending_doctor_id')
                ->relationship('attendingDoctor', 'name')
                ->searchable()
                ->disabled($isLocked),
            Textarea::make('remarks')->columnSpanFull()->disabled($isLocked),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('id', 'desc')
            ->columns([
                TextColumn::make('birth_certificate_number')->searchable()->sortable(),
                TextColumn::make('serviceOrder.so_number')->label('Service Order')->searchable(),
                TextColumn::make('child_name')->searchable(),
                TextColumn::make('date_of_birth')->date()->sortable(),
                IconColumn::make('is_locked')->label('Locked')->boolean(),
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
            'index' => ListBirthCertificates::route('/'),
            'create' => CreateBirthCertificate::route('/create'),
            'edit' => EditBirthCertificate::route('/{record}/edit'),
        ];
    }
}
