<?php

namespace App\Filament\Admin\Pages\Reports;

use App\Enum\ServiceOrderStatus;
use App\Exports\ServiceProviderReportExport;
use App\Helpers\DateHelper;
use App\Models\Service;
use App\Models\ServiceOrder;
use App\Models\User;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use UnitEnum;

class ServiceProviderReport extends Page implements Tables\Contracts\HasTable
{
    use Tables\Concerns\InteractsWithTable;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-user-group';

    protected static string|UnitEnum|null $navigationGroup = 'Reports';

    protected static ?int $navigationSort = 7;

    protected static ?string $title = 'Service Provider Report';

    protected string $view = 'filament.accounts.pages.report-page';

    public ?array $filters = [];

    public string $activeTab = 'general';

    public array $accounts = [];

    public function mount(): void
    {
        $this->filters = [
            'from' => now(DateHelper::timezone())->startOfMonth()->format('Y-m-d'),
            'until' => now(DateHelper::timezone())->format('Y-m-d'),
            'doctor_id' => null,
            'service_id' => null,
            'status' => null,
        ];
        $this->accounts = [];
    }

    public function filtersForm(Schema $schema): Schema
    {
        return $schema
            ->schema([
                DatePicker::make('from')->label('From'),
                DatePicker::make('until')->label('Until'),
                Select::make('doctor_id')
                    ->label('Provider')
                    ->options(fn () => User::query()
                        ->whereHas('opdDoctorProfiles')
                        ->orWhereHas('indDoctorProfiles')
                        ->orWhereHas('emergencyDoctorProfiles')
                        ->orWhereHas('dentistProfiles')
                        ->orWhereHas('ultrasoundDoctorProfiles')
                        ->pluck('name', 'id'))
                    ->searchable()
                    ->placeholder('Select Provider'),
                Select::make('service_id')
                    ->label('Service')
                    ->options(fn () => Service::pluck('name', 'id'))
                    ->searchable()
                    ->placeholder('All Services'),
                Select::make('status')
                    ->label('Status')
                    ->options(
                        collect(ServiceOrderStatus::cases())
                            ->mapWithKeys(fn (ServiceOrderStatus $s) => [
                                strtolower($s->name) => ucwords(strtolower(str_replace('_', ' ', $s->name))),
                            ])
                            ->toArray()
                    )
                    ->placeholder('All Statuses'),
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
                return ServiceOrder::query()
                    ->withSum(['transactionElements as income_total' => function ($q) {
                        $q->where('income_or_expense', 'INCOME');
                    }], 'amount')
                    ->withVoucherExpenseTotal('voucher_total')
                    ->with(['patient:id,name', 'service:id,name'])
                    ->when($this->filters['from'] ?? null, fn (Builder $q, $date) => $q->where('service_orders.created_at', '>=', DateHelper::dayStartUtc($date)))
                    ->when($this->filters['until'] ?? null, fn (Builder $q, $date) => $q->where('service_orders.created_at', '<=', DateHelper::dayEndUtc($date)))
                    ->when($this->filters['doctor_id'] ?? null, fn (Builder $q, $id) => $q->where('doctor_id', $id))
                    ->when($this->filters['service_id'] ?? null, fn (Builder $q, $id) => $q->where('service_id', $id))
                    ->when($this->filters['status'] ?? null, fn (Builder $q, $status) => $q->where('status', $status));
            })
            ->deferLoading()
            ->defaultSort('created_at', 'desc')
            ->persistFiltersInSession()
            ->persistSortInSession()
            ->columns([
                TextColumn::make('created_at')
                    ->label('Date')
                    ->dateTime('d M Y')
                    ->sortable(),
                TextColumn::make('so_number')
                    ->label('SO Number')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->fontFamily('mono')
                    ->wrap(),
                TextColumn::make('patient.name')
                    ->label('Patient')
                    ->searchable(),
                TextColumn::make('service.name')
                    ->label('Service')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('type')
                    ->label('Department')
                    ->badge()
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'closed' => 'success',
                        'open' => 'warning',
                        'refunded' => 'danger',
                        'cancelled' => 'gray',
                        'treated' => 'info',
                        default => 'gray',
                    })
                    ->sortable(),
                TextColumn::make('income_total')
                    ->label('Income Collected')
                    ->numeric(2)
                    ->sortable()
                    ->placeholder('0.00')
                    ->color('success'),
                TextColumn::make('voucher_total')
                    ->label('Expenses Paid to Provider')
                    ->numeric(2)
                    ->sortable()
                    ->placeholder('0.00')
                    ->color('danger'),
            ])
            ->groups([
                Group::make('service.name')->label('Service'),
                Group::make('status')->label('Status'),
                Group::make('type')->label('Department'),
            ])
            ->striped()
            ->paginated([25, 50, 100])
            ->defaultPaginationPageOption(25);
    }

    public function getPdfUrl(): string
    {
        return url('/reports/generic/service-provider').'?'.http_build_query(array_filter($this->filters ?? []));
    }

    public function exportToExcel(): BinaryFileResponse
    {
        $from = $this->filters['from'] ?? now()->startOfMonth()->format('Y-m-d');
        $until = $this->filters['until'] ?? now()->format('Y-m-d');

        return (new ServiceProviderReportExport($this->filters))
            ->withFilename("service-provider-report_{$from}_{$until}.xlsx")
            ->download();
    }

    public function exportToCsv(): BinaryFileResponse
    {
        $from = $this->filters['from'] ?? now()->startOfMonth()->format('Y-m-d');
        $until = $this->filters['until'] ?? now()->format('Y-m-d');

        return (new ServiceProviderReportExport($this->filters))
            ->withFilename("service-provider-report_{$from}_{$until}.csv")
            ->download('', Excel::CSV);
    }
}
