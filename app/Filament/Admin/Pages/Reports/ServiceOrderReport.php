<?php

namespace App\Filament\Admin\Pages\Reports;

use App\Models\Service;
use App\Models\ServiceOrder;
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

class ServiceOrderReport extends Page implements Tables\Contracts\HasTable
{
    use Tables\Concerns\InteractsWithTable;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-document-text';
    protected static string | UnitEnum | null $navigationGroup = 'Reports';
    protected static ?int $navigationSort = 5;
    protected static ?string $title = 'Service Order Report';
    protected string $view = 'filament.accounts.pages.report-page';

    public ?array $filters = [];
    public string $activeTab = 'general';

    public function mount(): void
    {
        $this->filters = [
            'from' => now()->startOfMonth()->format('Y-m-d'),
            'until' => now()->format('Y-m-d'),
            'status' => null,
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
                Select::make('status')
                    ->label('Status')
                    ->options(['open' => 'Open', 'closed' => 'Closed'])
                    ->placeholder('All'),
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
                $query = ServiceOrder::query()
                    ->withSum(['transactionElements as income_total' => function ($q) {
                        $q->where('income_or_expense', 'INCOME');
                    }], 'amount')
                    ->withSum(['transactionElements as expense_total' => function ($q) {
                        $q->where('income_or_expense', 'EXPENSE');
                    }], 'amount')
                    ->withCount('expenseVouchers')
                    ->withSum('expenseVouchers', 'amount');

                if ($this->filters['from'] ?? null) {
                    $query->whereDate('service_orders.created_at', '>=', $this->filters['from']);
                }
                if ($this->filters['until'] ?? null) {
                    $query->whereDate('service_orders.created_at', '<=', $this->filters['until']);
                }
                if ($this->filters['status'] ?? null) {
                    $query->where('status', $this->filters['status']);
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
                    ->dateTime('d M Y')
                    ->sortable(),
                TextColumn::make('so_number')
                    ->label('Service Order')
                    ->searchable()
                    ->sortable()
                    ->description(fn (ServiceOrder $record): string => collect([
                        $record->patient?->name,
                        $record->service?->name,
                        $record->doctor?->name,
                    ])->filter()->implode(' · '))
                    ->wrap(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'closed' => 'success',
                        'open' => 'warning',
                        default => 'gray',
                    })
                    ->sortable(),
                TextColumn::make('income_total')
                    ->label('Income')
                    ->numeric(2)
                    ->sortable()
                    ->placeholder('0.00')
                    ->color('success')
                    ->summarize(Sum::make()->numeric(2)->label('Total Income')),
                TextColumn::make('expense_total')
                    ->label('Expense')
                    ->numeric(2)
                    ->sortable()
                    ->placeholder('0.00')
                    ->color('danger')
                    ->summarize(Sum::make()->numeric(2)->label('Total Expense')),
                TextColumn::make('expense_vouchers_sum_amount')
                    ->label('Vouchers Amount')
                    ->numeric(2)
                    ->sortable()
                    ->placeholder('0.00')
                    ->toggleable(),
                TextColumn::make('expense_vouchers_count')
                    ->label('Vouchers')
                    ->sortable()
                    ->toggleable(),
            ])
            ->groups([
                Group::make('service.name')->label('Service'),
                Group::make('doctor.name')->label('Provider'),
                Group::make('status')->label('Status'),
            ])
            ->striped()
            ->paginated([25, 50, 100])
            ->defaultPaginationPageOption(50)
            ->recordUrl(fn (ServiceOrder $record): string => route('reports.generic.service-order', ['id' => $record->id]))
            ->openRecordUrlInNewTab();
    }

    public function getPdfUrl(): string
    {
        return url('/reports/generic/service-orders') . '?' . http_build_query(array_filter($this->filters));
    }
}
