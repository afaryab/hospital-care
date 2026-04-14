<?php

namespace App\Filament\Admin\Resources\Transactions;

use App\Filament\Admin\Resources\Transactions\Pages\EditTransaction;
use App\Filament\Admin\Resources\Transactions\Pages\ListTransactions;
use App\Filament\Admin\Resources\Transactions\Pages\ViewTransaction;
use App\Filament\Admin\Resources\Transactions\Schemas\TransactionForm;
use App\Filament\Admin\Resources\Transactions\Schemas\TransactionInfolist;
use App\Models\Closing;
use App\Models\Patient;
use App\Models\Transaction;
use BackedEnum;
use Filament\Forms\Components\DatePicker;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

class TransactionResource extends Resource
{
    protected static ?string $model = Transaction::class;

    protected static ?int $navigationSort = 8;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedArrowsRightLeft;

    protected static ?string $navigationLabel = 'Transactions';

    public static function form(Schema $schema): Schema
    {
        return TransactionForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return TransactionInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->query(Transaction::query()->with(['patient', 'closing']))
            ->columns([
                TextColumn::make('tr_number')
                    ->label('TR Number')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('patient.name')
                    ->label('Patient')
                    ->searchable(),
                TextColumn::make('closing.ct_number')
                    ->label('Closing')
                    ->toggleable(),
                TextColumn::make('type')
                    ->badge()
                    ->sortable(),
                TextColumn::make('income_or_expense')
                    ->label('Direction')
                    ->badge()
                    ->color(fn (string $state): string => $state === 'INCOME' ? 'success' : 'danger')
                    ->sortable(),
                TextColumn::make('amount')
                    ->numeric(2)
                    ->sortable(),
                IconColumn::make('is_refunded')
                    ->label('Refunded')
                    ->boolean(),
                TextColumn::make('created_at')
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
                SelectFilter::make('type')
                    ->options(fn () => Transaction::query()->select('type')->distinct()->pluck('type', 'type')->toArray()),
                SelectFilter::make('income_or_expense')
                    ->options([
                        'INCOME' => 'Income',
                        'EXPENSE' => 'Expense',
                    ]),
                SelectFilter::make('patient_id')
                    ->label('Patient')
                    ->searchable()
                    ->getSearchResultsUsing(fn (string $search): array => Patient::query()
                        ->where('name', 'like', "%{$search}%")
                        ->limit(30)
                        ->pluck('name', 'id')
                        ->toArray())
                    ->getOptionLabelUsing(fn ($value): ?string => Patient::find($value)?->name),
                SelectFilter::make('closing_id')
                    ->label('Closing')
                    ->searchable()
                    ->getSearchResultsUsing(fn (string $search): array => Closing::query()
                        ->where('ct_number', 'like', "%{$search}%")
                        ->orderByDesc('id')
                        ->limit(30)
                        ->pluck('ct_number', 'id')
                        ->toArray())
                    ->getOptionLabelUsing(fn ($value): ?string => Closing::find($value)?->ct_number),
            ])
            ->defaultSort('id', 'desc')
            ->recordUrl(fn (Transaction $record): string => static::getUrl('view', ['record' => $record]));
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTransactions::route('/'),
            'view' => ViewTransaction::route('/{record}'),
            'edit' => EditTransaction::route('/{record}/edit'),
        ];
    }
}
