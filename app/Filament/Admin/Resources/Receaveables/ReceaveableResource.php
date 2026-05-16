<?php

namespace App\Filament\Admin\Resources\Receaveables;

use App\Filament\Admin\Resources\Receaveables\Pages\ListReceaveables;
use App\Models\Panel;
use App\Models\Receaveable;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class ReceaveableResource extends Resource
{
    protected static ?string $model = Receaveable::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClock;

    protected static string|UnitEnum|null $navigationGroup = 'Finance';

    protected static ?string $label = 'Receivable';

    protected static ?string $pluralLabel = 'Receivables';

    protected static ?int $navigationSort = 12;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->query(
                Receaveable::query()
                    ->with(['patient:id,name,ps_number', 'panel:id,name', 'transaction:id,tr_number'])
            )
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('created_at')
                    ->label('Date')
                    ->date('d M Y')
                    ->sortable(),
                TextColumn::make('transaction.tr_number')
                    ->label('TR#')
                    ->searchable(),
                TextColumn::make('patient.name')
                    ->label('Patient')
                    ->description(fn (Receaveable $r) => $r->patient?->ps_number)
                    ->searchable()
                    ->sortable(),
                TextColumn::make('panel.name')
                    ->label('Panel')
                    ->badge()
                    ->placeholder('—'),
                TextColumn::make('orignal_amount')
                    ->label('Original')
                    ->numeric(2)
                    ->sortable(),
                TextColumn::make('amount')
                    ->label('Remaining')
                    ->numeric(2)
                    ->sortable()
                    ->color(fn (Receaveable $r) => $r->amount > 0 ? 'danger' : 'success'),
                TextColumn::make('due_date')
                    ->label('Due')
                    ->date('d M Y')
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match (strtolower($state)) {
                        'paid', 'payed' => 'success',
                        'pending' => 'warning',
                        'overdue' => 'danger',
                        default => 'gray',
                    })
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(['pending' => 'Pending', 'paid' => 'Paid', 'cancelled' => 'Cancelled']),
                SelectFilter::make('panel_id')
                    ->label('Panel')
                    ->options(fn () => Panel::orderBy('name')->pluck('name', 'id'))
                    ->searchable(),
                Filter::make('overdue')
                    ->label('Overdue only')
                    ->query(fn (Builder $q) => $q->where('due_date', '<', now())->whereNotIn('status', ['paid', 'cancelled'])),
            ])
            ->striped()
            ->paginated([25, 50, 100]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListReceaveables::route('/'),
        ];
    }
}
