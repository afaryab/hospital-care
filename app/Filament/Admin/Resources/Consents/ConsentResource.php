<?php

namespace App\Filament\Admin\Resources\Consents;

use App\Enum\ConsentMethod;
use App\Enum\ConsentType;
use App\Filament\Admin\Resources\Consents\Pages\CreateConsent;
use App\Filament\Admin\Resources\Consents\Pages\ListConsents;
use App\Filament\Admin\Resources\Consents\Pages\ViewConsent;
use App\Filament\Admin\Resources\Consents\Schemas\ConsentInfolist;
use App\Models\Consent;
use App\Models\Patient;
use App\Models\ServiceOrder;
use BackedEnum;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
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
        return $schema->components([
            Select::make('patient_id')
                ->label('Patient')
                ->searchable()
                ->required()
                ->getSearchResultsUsing(fn (string $search): array => Patient::query()
                    ->where('name', 'like', "%{$search}%")
                    ->limit(30)
                    ->pluck('name', 'id')
                    ->toArray())
                ->getOptionLabelUsing(fn ($value): ?string => Patient::find($value)?->name),
            Select::make('service_order_id')
                ->label('Service Order (Optional)')
                ->searchable()
                ->nullable()
                ->getSearchResultsUsing(fn (string $search): array => ServiceOrder::query()
                    ->where('so_number', 'like', "%{$search}%")
                    ->limit(30)
                    ->pluck('so_number', 'id')
                    ->toArray())
                ->getOptionLabelUsing(fn ($value): ?string => ServiceOrder::find($value)?->so_number),
            Select::make('consent_type')
                ->label('Type')
                ->options(collect(ConsentType::cases())->mapWithKeys(fn (ConsentType $t) => [$t->value => $t->label()]))
                ->required(),
            Select::make('consent_method')
                ->label('Method')
                ->options(collect(ConsentMethod::cases())->mapWithKeys(fn (ConsentMethod $m) => [$m->value => $m->label()]))
                ->required(),
            DateTimePicker::make('consented_at')
                ->label('Consented At')
                ->required()
                ->default(now()),
            Textarea::make('notes')
                ->columnSpanFull(),
        ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ConsentInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->query(Consent::query()->with(['patient:id,name,ps_number', 'serviceOrder:id,so_number,service_id', 'serviceOrder.service:id,name', 'recordedBy:id,name']))
            ->defaultSort('consented_at', 'desc')
            ->columns([
                TextColumn::make('consented_at')->label('Date')->dateTime('d M Y H:i')->sortable(),
                TextColumn::make('patient.name')
                    ->label('Patient')
                    ->description(fn (Consent $c) => $c->patient?->ps_number)
                    ->searchable(),
                TextColumn::make('serviceOrder.service.name')->label('Service')->searchable(),
                TextColumn::make('consent_type')
                    ->label('Type')
                    ->badge()
                    ->formatStateUsing(fn (ConsentType $state): string => $state->label())
                    ->color(fn (ConsentType $state): string => match ($state) {
                        ConsentType::Treatment => 'info',
                        ConsentType::Procedure => 'warning',
                        ConsentType::DataSharing => 'gray',
                    }),
                TextColumn::make('consent_method')
                    ->label('Method')
                    ->badge()
                    ->formatStateUsing(fn (ConsentMethod $state): string => $state->label()),
                TextColumn::make('recordedBy.name')->label('Recorded By')->toggleable(),
            ])
            ->filters([
                SelectFilter::make('consent_type')
                    ->label('Type')
                    ->options(collect(ConsentType::cases())->mapWithKeys(fn (ConsentType $t) => [$t->value => $t->label()])),
                SelectFilter::make('consent_method')
                    ->label('Method')
                    ->options(collect(ConsentMethod::cases())->mapWithKeys(fn (ConsentMethod $m) => [$m->value => $m->label()])),
            ])
            ->recordUrl(fn (Consent $record) => ViewConsent::getUrl([$record->id]))
            ->striped()
            ->paginated([25, 50, 100]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListConsents::route('/'),
            'create' => CreateConsent::route('/create'),
            'view' => ViewConsent::route('/{record}'),
        ];
    }
}
