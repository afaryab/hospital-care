<?php

namespace App\Filament\Admin\Pages\Reports;

use App\Enum\TransactionElementType;
use App\Models\Closing;
use App\Models\Reception;
use App\Models\Service;
use App\Models\TransactionElement;
use App\Models\User;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class IncomeReport extends Page implements Tables\Contracts\HasTable
{
    use Tables\Concerns\InteractsWithTable;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-banknotes';
    protected static string | UnitEnum | null $navigationGroup = 'Reports';
    protected static ?int $navigationSort = 1;
    protected static ?string $title = 'Income Report';
    protected string $view = 'filament.accounts.pages.report-page';

    public ?array $filters = [];
    public string $activeTab = 'general';

    public function mount(): void
    {
        $this->filters = [
            'from' => now()->startOfMonth()->format('Y-m-d'),
            'until' => now()->format('Y-m-d'),
            'reception_id' => null,
            'type' => null,
            'service_id' => null,
            'doctor_id' => null,
        ];
    }

    public function filtersForm(Schema $schema): Schema
    {
        return $schema
            ->schema([
                DatePicker::make('from')->label('From'),
                DatePicker::make('until')->label('Until'),
                Select::make('reception_id')
                    ->label('Reception')
                    ->options(fn () => Reception::pluck('name', 'id'))
                    ->searchable()
                    ->placeholder('All Receptions'),
                Select::make('type')
                    ->label('Type')
                    ->options(collect(TransactionElementType::cases())->mapWithKeys(fn ($t) => [$t->name => $t->name]))
                    ->placeholder('All Types'),
                Select::make('service_id')
                    ->label('Service')
                    ->options(fn () => Service::pluck('name', 'id'))
                    ->searchable()
                    ->placeholder('All Services'),
                Select::make('doctor_id')
                    ->label('Provider')
                    ->options(fn () => User::whereHas('opdDoctorProfiles')->pluck('name', 'id'))
                    ->searchable()
                    ->placeholder('All Providers'),
            ])
            ->statePath('filters');
    }

    public function applyFilters(): void
    {
        $this->resetTable();
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(function (): Builder {
                $query = TransactionElement::query()->where('income_or_expense', 'INCOME');

                if ($this->filters['from'] ?? null) {
                    $query->whereDate('transaction_elements.created_at', '>=', $this->filters['from']);
                }
                if ($this->filters['until'] ?? null) {
                    $query->whereDate('transaction_elements.created_at', '<=', $this->filters['until']);
                }
                if ($this->filters['reception_id'] ?? null) {
                    $query->whereIn('closing_id', Closing::where('reception_id', $this->filters['reception_id'])->select('id'));
                }
                if ($this->filters['type'] ?? null) {
                    $query->where('type', $this->filters['type']);
                }
                if ($this->filters['service_id'] ?? null) {
                    $query->where('service_id', $this->filters['service_id']);
                }
                if ($this->filters['doctor_id'] ?? null) {
                    $query->where('doctor_id', $this->filters['doctor_id']);
                }

                return $query;
            })
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('created_at')
                    ->label('Date')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
                TextColumn::make('transaction.closing.ct_number')
                    ->label('Counter')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('transaction.tr_number')
                    ->label('TR#')
                    ->searchable(),
                TextColumn::make('type')
                    ->badge()
                    ->sortable(),
                TextColumn::make('service.name')
                    ->label('Service')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('doctor.name')
                    ->label('Provider')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('patient.name')
                    ->label('Patient')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('amount')
                    ->numeric(2)
                    ->sortable()
                    ->summarize(Sum::make()->numeric(2)->label('Total')),
                TextColumn::make('orignal_amount')
                    ->label('Original')
                    ->numeric(2)
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->groups([
                Group::make('type')->label('Type'),
                Group::make('service.name')->label('Service'),
                Group::make('doctor.name')->label('Provider'),
            ])
            ->striped()
            ->paginated([25, 50, 100])
            ->defaultPaginationPageOption(50);
    }

    public function getPdfUrl(): string
    {
        return url('/reports/generic/income') . '?' . http_build_query(array_filter($this->filters));
    }
}
