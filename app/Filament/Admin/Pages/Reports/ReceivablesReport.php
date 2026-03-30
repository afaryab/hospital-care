<?php

namespace App\Filament\Admin\Pages\Reports;

use App\Exports\ReceivablesReportExport;
use App\Models\Panel;
use App\Models\Receaveable;
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
use Maatwebsite\Excel\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use UnitEnum;

class ReceivablesReport extends Page implements Tables\Contracts\HasTable
{
    use Tables\Concerns\InteractsWithTable;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-clock';

    protected static string|UnitEnum|null $navigationGroup = 'Reports';

    protected static ?int $navigationSort = 3;

    protected static ?string $title = 'Receivables Report';

    protected string $view = 'filament.accounts.pages.report-page';

    public ?array $filters = [];

    public string $activeTab = 'general';

    public function mount(): void
    {
        $this->filters = [
            'from' => now()->startOfMonth()->format('Y-m-d'),
            'until' => now()->format('Y-m-d'),
            'status' => null,
            'panel_id' => null,
        ];
        // Always provide accounts key for view safety
        $this->accounts = [];
    }

    // Always provide accounts property for view safety
    public array $accounts = [];

    public function filtersForm(Schema $schema): Schema
    {
        return $schema
            ->schema([
                DatePicker::make('from')->label('From'),
                DatePicker::make('until')->label('Until'),
                Select::make('status')
                    ->label('Status')
                    ->options(fn () => Receaveable::query()->distinct()->pluck('status', 'status')->toArray())
                    ->placeholder('All Statuses'),
                Select::make('panel_id')
                    ->label('Panel')
                    ->options(fn () => Panel::pluck('name', 'id'))
                    ->searchable()
                    ->placeholder('All Panels'),
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
                $query = Receaveable::query();

                if ($this->filters['from'] ?? null) {
                    $query->whereDate('receaveables.created_at', '>=', $this->filters['from']);
                }
                if ($this->filters['until'] ?? null) {
                    $query->whereDate('receaveables.created_at', '<=', $this->filters['until']);
                }
                if ($this->filters['status'] ?? null) {
                    $query->where('status', $this->filters['status']);
                }
                if ($this->filters['panel_id'] ?? null) {
                    $query->where('panel_id', $this->filters['panel_id']);
                }

                return $query;
            })
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('created_at')
                    ->label('Date')
                    ->dateTime('d M Y')
                    ->sortable(),
                TextColumn::make('transaction.tr_number')
                    ->label('TR#')
                    ->searchable(),
                TextColumn::make('patient.name')
                    ->label('Patient')
                    ->searchable(),
                TextColumn::make('panel.name')
                    ->label('Panel')
                    ->searchable()
                    ->badge(),
                TextColumn::make('orignal_amount')
                    ->label('Orignal')
                    ->numeric(2)
                    ->sortable()
                    ->placeholder('-')
                    ->summarize(Sum::make()->numeric(2)->label('Total Orignal')),
                TextColumn::make('amount')
                    ->label('Remaining')
                    ->numeric(2)
                    ->sortable()
                    ->summarize(Sum::make()->numeric(2)->label('Total Remaining')),
                TextColumn::make('due_date')
                    ->label('Due Date')
                    ->date('d M Y')
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match (strtolower($state)) {
                        'paid', 'payed' => 'success',
                        'pending' => 'warning',
                        default => 'gray',
                    })
                    ->sortable(),
            ])
            ->groups([
                Group::make('status')->label('Status'),
                Group::make('panel.name')->label('Panel'),
            ])
            ->striped()
            ->paginated([25, 50, 100])
            ->defaultPaginationPageOption(50);
    }

    public function getPdfUrl(): string
    {
        return url('/reports/generic/receivables').'?'.http_build_query(array_filter($this->filters));
    }

    public function exportToExcel(): BinaryFileResponse
    {
        $from = $this->filters['from'] ?? now()->startOfMonth()->format('Y-m-d');
        $until = $this->filters['until'] ?? now()->format('Y-m-d');

        return (new ReceivablesReportExport($this->filters))
            ->withFilename("receivables-report_{$from}_{$until}.xlsx")
            ->download();
    }

    public function exportToCsv(): BinaryFileResponse
    {
        $from = $this->filters['from'] ?? now()->startOfMonth()->format('Y-m-d');
        $until = $this->filters['until'] ?? now()->format('Y-m-d');

        return (new ReceivablesReportExport($this->filters))
            ->withFilename("receivables-report_{$from}_{$until}.csv")
            ->download('', Excel::CSV);
    }
}
