<?php

namespace App\Filament\Accounts\Pages\Reports;

use App\Models\Closing;
use App\Models\Reception;
use App\Models\TransactionElement;
use Carbon\Carbon;
use Filament\Forms\Components\DatePicker;
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class ServicesReport extends Page implements Tables\Contracts\HasTable
{
    use Tables\Concerns\InteractsWithTable;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static string | UnitEnum | null $navigationGroup = 'Reports';
    protected static ?int $navigationSort = 4;
    protected static ?string $title = 'Services Report';
    protected string $view = 'filament.accounts.pages.report-page';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                TransactionElement::query()
                    ->whereNotNull('service_id')
            )
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('created_at')
                    ->label('Date')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
                TextColumn::make('transaction.tr_number')
                    ->label('TR#')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('service.name')
                    ->label('Service')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('service.department.name')
                    ->label('Department')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('doctor.name')
                    ->label('Provider')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('type')
                    ->badge()
                    ->sortable(),
                TextColumn::make('income_or_expense')
                    ->label('Flow')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'INCOME' => 'success',
                        'EXPENSE' => 'danger',
                        default => 'gray',
                    })
                    ->sortable(),
                TextColumn::make('patient.name')
                    ->label('Patient')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('amount')
                    ->numeric(2)
                    ->sortable()
                    ->summarize(Sum::make()->numeric(2)->label('Total')),
            ])
            ->filters([
                Filter::make('date_range')
                    ->form([
                        DatePicker::make('from')->default(now()->startOfMonth()),
                        DatePicker::make('until')->default(now()),
                    ])
                    ->query(fn (Builder $query, array $data): Builder => $query
                        ->when($data['from'] ?? null, fn (Builder $q, $date) => $q->whereDate('transaction_elements.created_at', '>=', $date))
                        ->when($data['until'] ?? null, fn (Builder $q, $date) => $q->whereDate('transaction_elements.created_at', '<=', $date))
                    )
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['from'] ?? null) {
                            $indicators['from'] = 'From: ' . Carbon::parse($data['from'])->format('d M Y');
                        }
                        if ($data['until'] ?? null) {
                            $indicators['until'] = 'Until: ' . Carbon::parse($data['until'])->format('d M Y');
                        }
                        return $indicators;
                    }),
                SelectFilter::make('reception')
                    ->label('Reception')
                    ->options(fn () => Reception::pluck('name', 'id'))
                    ->query(fn (Builder $query, array $data): Builder => $query
                        ->when($data['value'] ?? null, fn (Builder $q, $value) => $q
                            ->whereIn('closing_id', Closing::where('reception_id', $value)->select('id'))
                        )
                    )
                    ->searchable()
                    ->preload(),
                SelectFilter::make('income_or_expense')
                    ->options(['INCOME' => 'Income', 'EXPENSE' => 'Expense'])
                    ->label('Flow'),
                SelectFilter::make('service_id')
                    ->relationship('service', 'name')
                    ->searchable()
                    ->preload()
                    ->label('Service'),
                SelectFilter::make('doctor_id')
                    ->relationship('doctor', 'name')
                    ->searchable()
                    ->preload()
                    ->label('Provider'),
            ])
            ->groups([
                Group::make('service.name')->label('Service'),
                Group::make('doctor.name')->label('Provider'),
                Group::make('income_or_expense')->label('Income/Expense'),
                Group::make('type')->label('Type'),
            ])
            ->striped()
            ->paginated([25, 50, 100])
            ->defaultPaginationPageOption(50);
    }
}
