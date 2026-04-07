<?php

namespace App\Filament\Admin\Resources\Patients;

use App\Filament\Admin\Resources\Patients\Pages\ListPatients;
use App\Filament\Admin\Resources\Patients\Pages\ViewPatient;
use App\Models\Patient;
use App\Models\Receaveable;
use App\Models\TreatmentRecord;
use BackedEnum;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PatientResource extends Resource
{
    protected static ?string $model = Patient::class;

    protected static ?int $navigationSort = 3;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserGroup;

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('ps_number')
                    ->label('PS Number')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('gender')
                    ->badge(),
                TextColumn::make('contact')
                    ->searchable(),
                TextColumn::make('outstandings')
                    ->label('Outstanding')
                    ->numeric(2)
                    ->placeholder('0.00'),
            ])
            ->defaultSort('id', 'desc')
            ->recordUrl(fn (Patient $record): string => static::getUrl('view', ['record' => $record]));
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Tabs::make('Patient Tabs')
                ->tabs([
                    Tab::make('Overview')
                        ->schema([
                            TextEntry::make('ps_number')->label('PS Number')->copyable(),
                            TextEntry::make('name')->label('Name'),
                            TextEntry::make('gender')->badge(),
                            TextEntry::make('contact')->label('Contact')->placeholder('-'),
                            TextEntry::make('age')->label('Age')->placeholder('-'),
                            TextEntry::make('outstandings')->label('Outstanding')->money('PKR')->placeholder('0'),
                        ])
                        ->columns(3),
                    Tab::make('Service Orders')
                        ->schema([
                            TextEntry::make('service_orders_summary')
                                ->label('Related Service Orders')
                                ->state(fn (Patient $record): string => $record->treatments()
                                    ->latest('id')
                                    ->limit(30)
                                    ->pluck('so_number')
                                    ->implode(', '))
                                ->placeholder('No service orders found.'),
                        ]),
                    Tab::make('Transactions')
                        ->schema([
                            TextEntry::make('transactions_summary')
                                ->label('Related Transactions')
                                ->state(fn (Patient $record): string => $record->transactions()
                                    ->latest('id')
                                    ->limit(30)
                                    ->pluck('tr_number')
                                    ->implode(', '))
                                ->placeholder('No transactions found.'),
                        ]),
                    Tab::make('Receivables')
                        ->schema([
                            TextEntry::make('receivables_summary')
                                ->label('Outstanding Receivables')
                                ->state(function (Patient $record): string {
                                    return Receaveable::query()
                                        ->where('patient_id', $record->id)
                                        ->whereIn('status', ['unpaid', 'PENDING'])
                                        ->where('amount', '>', 0)
                                        ->latest('id')
                                        ->limit(30)
                                        ->get()
                                        ->map(fn (Receaveable $receaveable): string => sprintf(
                                            'ID %d - %s - %.2f',
                                            $receaveable->id,
                                            (string) $receaveable->status,
                                            (float) $receaveable->amount,
                                        ))
                                        ->implode(', ');
                                })
                                ->placeholder('No outstanding receivables found.'),
                        ]),
                    Tab::make('Treatment History')
                        ->schema([
                            TextEntry::make('treatment_history_summary')
                                ->label('Treatment Records')
                                ->state(function (Patient $record): string {
                                    $serviceOrderIds = $record->treatments()->pluck('id');

                                    if ($serviceOrderIds->isEmpty()) {
                                        return '';
                                    }

                                    return TreatmentRecord::query()
                                        ->whereIn('service_order_id', $serviceOrderIds)
                                        ->latest('id')
                                        ->limit(30)
                                        ->get()
                                        ->map(fn (TreatmentRecord $treatmentRecord): string => sprintf(
                                            'SO#%d - %s',
                                            $treatmentRecord->service_order_id,
                                            (string) ($treatmentRecord->chief_complaint ?: 'No complaint'),
                                        ))
                                        ->implode(', ');
                                })
                                ->placeholder('No treatment history found.'),
                        ]),
                ])
                ->columnSpanFull(),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPatients::route('/'),
            'view' => ViewPatient::route('/{record}'),
        ];
    }
}
