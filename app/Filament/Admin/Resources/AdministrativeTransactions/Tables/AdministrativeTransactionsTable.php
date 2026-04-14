<?php

namespace App\Filament\Admin\Resources\AdministrativeTransactions\Tables;

use App\Models\ExpenseCategory;
use App\Models\Patient;
use App\Models\PaymentMethod;
use App\Models\Transaction;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

class AdministrativeTransactionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->query(
                Transaction::query()
                    ->with(['patient', 'paymentMethod', 'expenseCategory'])
                    ->whereNull('closing_id')
                    ->latest('id')
            )
            ->columns([
                TextColumn::make('tr_number')
                    ->label('TR Number')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('income_or_expense')
                    ->label('Direction')
                    ->badge()
                    ->color(fn (string $state): string => $state === 'INCOME' ? 'success' : 'danger')
                    ->sortable(),
                TextColumn::make('expenseCategory.name')
                    ->label('Category')
                    ->placeholder('—'),
                TextColumn::make('patient.name')
                    ->label('Patient')
                    ->placeholder('—')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('paymentMethod.name')
                    ->label('Payment Method'),
                TextColumn::make('reference_number')
                    ->label('Reference')
                    ->placeholder('—')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('amount')
                    ->label('Amount (PKR)')
                    ->numeric(2)
                    ->sortable(),
                IconColumn::make('is_refunded')
                    ->label('Refunded')
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('notes')
                    ->label('Notes')
                    ->placeholder('—')
                    ->limit(40)
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->label('Date')
                    ->dateTime('d M Y, H:i')
                    ->sortable(),
            ])
            ->filters([
                Filter::make('date_range')
                    ->schema([
                        DatePicker::make('from'),
                        DatePicker::make('until'),
                    ])
                    ->query(fn (Builder $query, array $data): Builder => $query
                        ->when($data['from'] ?? null, fn (Builder $q, $date) => $q->where('created_at', '>=', Carbon::parse($date)->startOfDay()))
                        ->when($data['until'] ?? null, fn (Builder $q, $date) => $q->where('created_at', '<=', Carbon::parse($date)->endOfDay()))),
                SelectFilter::make('income_or_expense')
                    ->label('Direction')
                    ->options([
                        'INCOME' => 'Income',
                        'EXPENSE' => 'Expense',
                    ]),
                SelectFilter::make('payment_method_id')
                    ->label('Payment Method')
                    ->options(fn () => PaymentMethod::query()->orderBy('name')->pluck('name', 'id')->toArray()),
                SelectFilter::make('expense_category_id')
                    ->label('Expense Category')
                    ->options(fn () => ExpenseCategory::query()->orderBy('name')->pluck('name', 'id')->toArray())
                    ->searchable(),
                SelectFilter::make('patient_id')
                    ->label('Patient')
                    ->searchable()
                    ->getSearchResultsUsing(fn (string $search): array => Patient::query()
                        ->where('name', 'like', "%{$search}%")
                        ->limit(30)
                        ->pluck('name', 'id')
                        ->toArray())
                    ->getOptionLabelUsing(fn ($value): ?string => Patient::find($value)?->name),
            ])
            ->defaultSort('id', 'desc');
    }
}
