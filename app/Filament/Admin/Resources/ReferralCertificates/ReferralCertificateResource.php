<?php

namespace App\Filament\Admin\Resources\ReferralCertificates;

use App\Filament\Admin\Resources\ReferralCertificates\Pages\CreateReferralCertificate;
use App\Filament\Admin\Resources\ReferralCertificates\Pages\EditReferralCertificate;
use App\Filament\Admin\Resources\ReferralCertificates\Pages\ListReferralCertificates;
use App\Models\ReferralCertificate;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class ReferralCertificateResource extends Resource
{
    protected static ?string $model = ReferralCertificate::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPaperAirplane;

    protected static string|UnitEnum|null $navigationGroup = 'Clinical';

    protected static ?int $navigationSort = 3;

    protected static ?string $recordTitleAttribute = 'referral_number';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('service_order_id')
                ->relationship('serviceOrder', 'so_number')
                ->searchable()
                ->required()
                ->disabledOn('edit'),
            TextInput::make('referral_number')
                ->disabled()
                ->dehydrated(false)
                ->visibleOn('edit'),
            TextInput::make('receiving_facility_name')->maxLength(255),
            RichEditor::make('notes')->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('id', 'desc')
            ->columns([
                TextColumn::make('referral_number')->searchable()->sortable(),
                TextColumn::make('serviceOrder.so_number')->label('Service Order')->searchable(),
                TextColumn::make('serviceOrder.patient.name')->label('Patient')->searchable(),
                TextColumn::make('receiving_facility_name')->label('Receiving Facility')->searchable(),
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
            'index' => ListReferralCertificates::route('/'),
            'create' => CreateReferralCertificate::route('/create'),
            'edit' => EditReferralCertificate::route('/{record}/edit'),
        ];
    }
}
