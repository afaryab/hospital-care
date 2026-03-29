<?php

namespace App\Filament\Admin\Resources\Transactions;

use App\Filament\Admin\Resources\Transactions\Pages\EditTransaction;
use App\Filament\Admin\Resources\Transactions\Pages\ListTransactions;
use App\Filament\Admin\Resources\Transactions\Pages\ViewTransaction;
use App\Models\Closing;
use App\Models\Patient;
use App\Models\Transaction;
use BackedEnum;
use Filament\Forms\Components\DatePicker;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class TransactionResource extends Resource
{
    protected static ?string $model = Transaction::class;

    protected static ?int $navigationSort = 8;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedArrowsRightLeft;

    protected static ?string $navigationLabel = 'Transactions';

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
                    ->searchable()
                    ->sortable(),
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
                        ->when($data['from'] ?? null, fn (Builder $innerQuery, $date) => $innerQuery->whereDate('created_at', '>=', $date))
                        ->when($data['until'] ?? null, fn (Builder $innerQuery, $date) => $innerQuery->whereDate('created_at', '<=', $date))),
                SelectFilter::make('type')
                    ->options(fn () => Transaction::query()->select('type')->distinct()->pluck('type', 'type')->toArray()),
                SelectFilter::make('income_or_expense')
                    ->options([
                        'INCOME' => 'Income',
                        'EXPENSE' => 'Expense',
                    ]),
                SelectFilter::make('patient_id')
                    ->label('Patient')
                    ->options(fn () => Patient::query()->orderBy('name')->pluck('name', 'id')->toArray())
                    ->searchable(),
                SelectFilter::make('closing_id')
                    ->label('Closing')
                    ->options(fn () => Closing::query()->orderByDesc('id')->pluck('ct_number', 'id')->toArray())
                    ->searchable(),
            ])
            ->defaultSort('id', 'desc')
            ->recordUrl(fn (Transaction $record): string => static::getUrl('view', ['record' => $record]));
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            TextEntry::make('tr_number')->label('TR Number')->copyable(),
            TextEntry::make('patient.name')->label('Patient'),
            TextEntry::make('closing.ct_number')->label('Closing'),
            TextEntry::make('type')->badge(),
            TextEntry::make('income_or_expense')->label('Direction')->badge(),
            TextEntry::make('amount')->numeric(2),
            TextEntry::make('is_refunded')->label('Refunded')->badge()->formatStateUsing(fn (bool $state): string => $state ? 'Yes' : 'No'),
            TextEntry::make('elements_summary')
                ->label('Elements')
                ->state(fn (Transaction $record): string => $record->elements()
                    ->latest('id')
                    ->get()
                    ->map(fn ($element): string => sprintf('%s (%s) %.2f', (string) $element->type, (string) $element->income_or_expense, (float) $element->amount))
                    ->implode(', '))
                ->placeholder('No transaction elements found.'),
        ]);
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
