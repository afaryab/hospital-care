<?php

namespace App\Filament\Admin\Resources\Receaveables;

use App\Filament\Admin\Resources\Receaveables\Pages\CreateReceaveable;
use App\Filament\Admin\Resources\Receaveables\Pages\EditReceaveable;
use App\Filament\Admin\Resources\Receaveables\Pages\ListReceaveables;
use App\Models\Panel;
use App\Models\Patient;
use App\Models\Receaveable;
use App\Models\Transaction;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class ReceaveableResource extends Resource
{
    protected static ?string $model = Receaveable::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClock;

    protected static string|UnitEnum|null $navigationGroup = 'Finance';

    protected static ?string $label = 'Receivable';

    protected static ?string $pluralLabel = 'Receivables';

    protected static ?int $navigationSort = 12;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Receivable Details')
                ->schema([
                    Select::make('patient_id')
                        ->label('Patient')
                        ->required()
                        ->searchable()
                        ->preload()
                        ->getSearchResultsUsing(fn (string $search) => Patient::query()
                            ->where('name', 'like', "%{$search}%")
                            ->orWhere('ps_number', 'like', "%{$search}%")
                            ->limit(25)
                            ->get()
                            ->mapWithKeys(fn (Patient $p) => [$p->id => "{$p->name} ({$p->ps_number})"])
                            ->toArray())
                        ->getOptionLabelUsing(fn ($value): ?string => Patient::find($value)?->name),
                    Select::make('panel_id')
                        ->label('Panel')
                        ->options(fn () => Panel::orderBy('name')->pluck('name', 'id'))
                        ->searchable()
                        ->preload(),
                    Select::make('transaction_id')
                        ->label('Linked Transaction')
                        ->searchable()
                        ->preload(false)
                        ->getSearchResultsUsing(fn (string $search) => Transaction::query()
                            ->where('tr_number', 'like', "%{$search}%")
                            ->limit(25)
                            ->pluck('tr_number', 'id')
                            ->toArray())
                        ->getOptionLabelUsing(fn ($value): ?string => Transaction::find($value)?->tr_number)
                        ->helperText('The transaction that produced this receivable (optional).'),
                    TextInput::make('orignal_amount')
                        ->label('Original Amount')
                        ->required()
                        ->numeric()
                        ->step('0.01')
                        ->minValue(0)
                        ->live(onBlur: true)
                        ->afterStateUpdated(function ($state, Set $set, Get $get): void {
                            if ($get('amount') === null || $get('amount') === '') {
                                $set('amount', $state);
                            }
                        }),
                    TextInput::make('amount')
                        ->label('Remaining Amount')
                        ->required()
                        ->numeric()
                        ->step('0.01')
                        ->minValue(0),
                    DatePicker::make('due_date')
                        ->label('Due Date'),
                    Select::make('status')
                        ->required()
                        ->options([
                            'pending' => 'Pending',
                            'paid' => 'Paid',
                            'cancelled' => 'Cancelled',
                        ])
                        ->default('pending')
                        ->native(false),
                ])
                ->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->query(
                Receaveable::query()
                    ->with(['patient:id,name,ps_number', 'panel:id,name', 'transaction:id,tr_number'])
            )
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('created_at')
                    ->label('Date')
                    ->date('d M Y')
                    ->sortable(),
                TextColumn::make('transaction.tr_number')
                    ->label('TR#')
                    ->searchable(),
                TextColumn::make('patient.name')
                    ->label('Patient')
                    ->description(fn (Receaveable $r) => $r->patient?->ps_number)
                    ->searchable()
                    ->sortable(),
                TextColumn::make('panel.name')
                    ->label('Panel')
                    ->badge()
                    ->placeholder('—'),
                TextColumn::make('orignal_amount')
                    ->label('Original')
                    ->numeric(2)
                    ->sortable(),
                TextColumn::make('amount')
                    ->label('Remaining')
                    ->numeric(2)
                    ->sortable()
                    ->color(fn (Receaveable $r) => $r->amount > 0 ? 'danger' : 'success'),
                TextColumn::make('due_date')
                    ->label('Due')
                    ->date('d M Y')
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match (strtolower($state)) {
                        'paid', 'payed' => 'success',
                        'unpaid', 'partial', 'pending' => 'warning',
                        'overdue' => 'danger',
                        'cancelled' => 'gray',
                        default => 'gray',
                    })
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(['unpaid' => 'Unpaid', 'paid' => 'Paid', 'cancelled' => 'Cancelled']),
                SelectFilter::make('panel_id')
                    ->label('Panel')
                    ->options(fn () => Panel::orderBy('name')->pluck('name', 'id'))
                    ->searchable(),
                Filter::make('overdue')
                    ->label('Overdue only')
                    ->query(fn (Builder $q) => $q->where('due_date', '<', now())->whereNotIn('status', ['paid', 'cancelled'])),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->striped()
            ->paginated([25, 50, 100]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListReceaveables::route('/'),
            'create' => CreateReceaveable::route('/create'),
            'edit' => EditReceaveable::route('/{record}/edit'),
        ];
    }
}
