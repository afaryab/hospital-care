<?php

namespace App\Filament\Admin\Resources\Consents;

use App\Filament\Admin\Resources\Consents\Pages\ListConsents;
use App\Models\Consent;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use UnitEnum;

class ConsentResource extends Resource
{
    protected static ?string $model = Consent::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCheckBadge;

    protected static string|UnitEnum|null $navigationGroup = 'Compliance';

    protected static ?string $label = 'Consent';

    protected static ?int $navigationSort = 5;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->query(Consent::query()->with(['patient:id,name,ps_number', 'serviceOrder:id,so_number', 'recordedBy:id,name']))
            ->defaultSort('consented_at', 'desc')
            ->columns([
                TextColumn::make('consented_at')->label('Date')->dateTime('d M Y H:i')->sortable(),
                TextColumn::make('patient.name')
                    ->label('Patient')
                    ->description(fn (Consent $c) => $c->patient?->ps_number)
                    ->searchable(),
                TextColumn::make('serviceOrder.so_number')->label('Service Order')->searchable()->fontFamily('mono'),
                TextColumn::make('consent_type')
                    ->label('Type')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'treatment' => 'info',
                        'procedure' => 'warning',
                        'data_sharing' => 'gray',
                        default => 'gray',
                    }),
                TextColumn::make('consent_method')->label('Method')->badge(),
                TextColumn::make('recordedBy.name')->label('Recorded By')->toggleable(),
            ])
            ->filters([
                SelectFilter::make('consent_type')
                    ->label('Type')
                    ->options(['treatment' => 'Treatment', 'procedure' => 'Procedure', 'data_sharing' => 'Data Sharing']),
                SelectFilter::make('consent_method')
                    ->label('Method')
                    ->options(['digital_checkbox' => 'Digital', 'paper_signed' => 'Paper', 'verbal_recorded' => 'Verbal']),
            ])
            ->striped()
            ->paginated([25, 50, 100]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListConsents::route('/'),
        ];
    }
}
