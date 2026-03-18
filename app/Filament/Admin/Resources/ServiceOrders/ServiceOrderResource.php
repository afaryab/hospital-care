<?php

namespace App\Filament\Admin\Resources\ServiceOrders;

use App\Models\Service;
use App\Models\ServiceOrder;
use App\Models\User;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class ServiceOrderResource extends Resource
{
    protected static ?string $model = ServiceOrder::class;

    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-document-text';

    protected static string | UnitEnum | null $navigationGroup = 'Reports';

    protected static ?int $navigationSort = 5;

    protected static ?string $label = 'Service Order';

    protected static ?string $pluralLabel = 'Service Orders';

    public static function table(Table $table): Table
    {
        return $table
            ->query(
                ServiceOrder::query()
                    ->withSum(['transactionElements as income_total' => function ($q) {
                        $q->where('income_or_expense', 'INCOME');
                    }], 'amount')
                    ->withSum(['transactionElements as expense_total' => function ($q) {
                        $q->where('income_or_expense', 'EXPENSE');
                    }], 'amount')
                    ->withCount('expenseVouchers')
                    ->withSum('expenseVouchers', 'amount')
            )
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
            ->filters([
                Filter::make('date_range')
                    ->form([
                        DatePicker::make('from')->default(now()->startOfMonth()),
                        DatePicker::make('until')->default(now()),
                    ])
                    ->query(fn (Builder $query, array $data): Builder => $query
                        ->when($data['from'] ?? null, fn (Builder $q, $date) => $q->whereDate('service_orders.created_at', '>=', $date))
                        ->when($data['until'] ?? null, fn (Builder $q, $date) => $q->whereDate('service_orders.created_at', '<=', $date))
                    ),
                SelectFilter::make('status')
                    ->options(['open' => 'Open', 'closed' => 'Closed']),
                SelectFilter::make('service_id')
                    ->label('Service')
                    ->options(fn () => Service::pluck('name', 'id'))
                    ->searchable(),
                SelectFilter::make('doctor_id')
                    ->label('Provider')
                    ->options(fn () => User::whereHas('opdDoctorProfiles')->pluck('name', 'id'))
                    ->searchable(),
            ])
            ->groups([
                Group::make('service.name')->label('Service'),
                Group::make('doctor.name')->label('Provider'),
                Group::make('status')->label('Status'),
            ])
            ->striped()
            ->paginated([25, 50, 100])
            ->defaultPaginationPageOption(50)
            ->recordUrl(fn (ServiceOrder $record): string => static::getUrl('view', ['record' => $record]))
            ->openRecordUrlInNewTab(false);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->schema([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Admin\Resources\ServiceOrders\Pages\ListServiceOrders::route('/'),
            'view' => \App\Filament\Admin\Resources\ServiceOrders\Pages\ViewServiceOrder::route('/{record}'),
        ];
    }
}
