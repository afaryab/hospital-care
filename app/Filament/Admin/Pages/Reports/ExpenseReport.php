<?php

namespace App\Filament\Admin\Pages\Reports;

use App\Enum\TransactionElementType;
use App\Models\Closing;
use App\Models\ExpenseCategory;
use App\Models\Reception;
use App\Models\TransactionElement;
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

class ExpenseReport extends Page implements Tables\Contracts\HasTable
{
    use Tables\Concerns\InteractsWithTable;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-credit-card';

    protected static string|UnitEnum|null $navigationGroup = 'Reports';

    protected static ?int $navigationSort = 2;

    protected static ?string $title = 'Expense Report';

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
            'expense_category_id' => null,
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
                Select::make('expense_category_id')
                    ->label('Category')
                    ->options(fn () => ExpenseCategory::pluck('name', 'id'))
                    ->searchable()
                    ->placeholder('All Categories'),
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
                $query = TransactionElement::query()->where('income_or_expense', 'EXPENSE');

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
                if ($this->filters['expense_category_id'] ?? null) {
                    $query->where('expense_category_id', $this->filters['expense_category_id']);
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
                TextColumn::make('expenseCategory.name')
                    ->label('Category')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('expVoucher.vc_number')
                    ->label('Voucher#')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('expVoucher.payedTo.name')
                    ->label('Paid To')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('notes')
                    ->label('Notes')
                    ->limit(30)
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('amount')
                    ->numeric(2)
                    ->sortable()
                    ->summarize(Sum::make()->numeric(2)->label('Total')),
            ])
            ->groups([
                Group::make('type')->label('Type'),
                Group::make('expenseCategory.name')->label('Category'),
            ])
            ->striped()
            ->paginated([25, 50, 100])
            ->defaultPaginationPageOption(50);
    }

    public function getPdfUrl(): string
    {
        return url('/reports/generic/expense').'?'.http_build_query(array_filter($this->filters));
    }
}
