<?php

namespace App\Filament\Admin\Resources\AuditLogs\Tables;

use App\Models\User;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class AuditLogsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('created_at')
                    ->label('Date / Time')
                    ->dateTime('d M Y, H:i:s')
                    ->sortable(),

                TextColumn::make('causer.name')
                    ->label('User')
                    ->default('System')
                    ->searchable(),

                TextColumn::make('event')
                    ->label('Action')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'created' => 'success',
                        'updated' => 'warning',
                        'deleted' => 'danger',
                        default => 'gray',
                    }),

                TextColumn::make('subject_type')
                    ->label('Model')
                    ->formatStateUsing(fn ($state) => $state ? class_basename($state) : 'System')
                    ->searchable(),

                TextColumn::make('subject_id')
                    ->label('Record ID')
                    ->sortable(),

                TextColumn::make('log_name')
                    ->label('Log')
                    ->badge()
                    ->color('gray'),

                TextColumn::make('description')
                    ->label('Description')
                    ->limit(60)
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = (string) ($column->getState() ?? '');

                        return strlen($state) > 60 ? $state : null;
                    }),
            ])
            ->filters([
                SelectFilter::make('event')
                    ->options([
                        'created' => 'Created',
                        'updated' => 'Updated',
                        'deleted' => 'Deleted',
                    ]),

                SelectFilter::make('causer_id')
                    ->label('User')
                    ->options(fn () => User::orderBy('name')->pluck('name', 'id')->toArray())
                    ->searchable(),

                SelectFilter::make('subject_type')
                    ->label('Model')
                    ->options([
                        'App\\Models\\Patient' => 'Patient',
                        'App\\Models\\Transaction' => 'Transaction',
                        'App\\Models\\TransactionElement' => 'Transaction Element',
                        'App\\Models\\Closing' => 'Closing',
                        'App\\Models\\ServiceOrder' => 'Service Order',
                        'App\\Models\\ExpenseVoucher' => 'Expense Voucher',
                        'App\\Models\\Receaveable' => 'Receivable',
                        'App\\Models\\User' => 'User',
                        'App\\Models\\Service' => 'Service',
                        'App\\Models\\Reception' => 'Reception',
                    ]),

                Filter::make('date_range')
                    ->form([
                        DatePicker::make('from')->label('From'),
                        DatePicker::make('until')->label('Until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['from'], fn (Builder $q, $date) => $q->whereDate('created_at', '>=', $date))
                            ->when($data['until'], fn (Builder $q, $date) => $q->whereDate('created_at', '<=', $date));
                    }),
            ])
            ->defaultSort('created_at', 'desc')
            ->recordAction(null)
            ->recordUrl(null);
    }
}
