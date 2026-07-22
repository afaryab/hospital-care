<?php

namespace App\Filament\Admin\Resources\ServiceOrders;

use App\Filament\Admin\Resources\ServiceOrders\Pages\ListServiceOrders;
use App\Filament\Admin\Resources\ServiceOrders\Pages\ViewServiceOrder;
use App\Models\Service;
use App\Models\ServiceDepartment;
use App\Models\ServiceOrder;
use App\Models\User;
use App\Services\ServiceOrderMerger;
use BackedEnum;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\HtmlString;
use UnitEnum;

class ServiceOrderResource extends Resource
{
    protected static ?string $model = ServiceOrder::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-document-text';

    protected static string|UnitEnum|null $navigationGroup = 'Reports';

    protected static ?int $navigationSort = 5;

    protected static ?string $label = 'Service Order';

    protected static ?string $pluralLabel = 'Service Orders';

    public static function table(Table $table): Table
    {
        return $table
            ->query(
                ServiceOrder::query()
                    // Eager-load relationships used in the description closure to
                    // eliminate N+1 queries (3 relations × page size = 150 queries).
                    ->with([
                        'patient:id,name',
                        'service:id,name,service_department_id',
                        'doctor:id,name',
                    ])
                    ->withSum(['transactionElements as income_total' => function ($q) {
                        $q->where('income_or_expense', 'INCOME');
                    }], 'amount')
                    ->withSum(['transactionElements as expense_total' => function ($q) {
                        $q->where('income_or_expense', 'EXPENSE');
                    }], 'amount')
                    ->withCount('expenseVouchers')
                    ->withSum('expenseVouchers', 'amount')
            )
            // Render the page skeleton immediately; data loads in a follow-up
            // Livewire request so the initial HTTP response never times out.
            ->deferLoading()
            ->defaultSort('created_at', 'desc')
            ->persistFiltersInSession()
            ->persistSortInSession()
            ->columns([
                TextColumn::make('created_at')
                    ->label('Date & Time')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
                TextColumn::make('so_number')
                    ->label('SO#')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->fontFamily('mono')
                    ->wrap(),
                TextColumn::make('patient.name')
                    ->label('Patient')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('service.name')
                    ->label('Service')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('type')
                    ->label('Department')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'OPD' => 'info',
                        'IND' => 'purple',
                        'EMG' => 'danger',
                        'DNT' => 'warning',
                        'PTH', 'LAB' => 'gray',
                        'ULT' => 'success',
                        'XRAY', 'RAD' => 'orange',
                        default => 'gray',
                    })
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('doctor.name')
                    ->label('Provider')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
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
                    ->label('Income')
                    ->numeric(2)
                    ->sortable()
                    ->placeholder('0.00')
                    ->color('success'),
                TextColumn::make('expense_total')
                    ->label('Expense')
                    ->numeric(2)
                    ->sortable()
                    ->placeholder('0.00')
                    ->color('danger'),
                TextColumn::make('expense_vouchers_sum_amount')
                    ->label('Vouchers Amount')
                    ->numeric(2)
                    ->sortable()
                    ->placeholder('0.00')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('expense_vouchers_count')
                    ->label('Vouchers')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Filter::make('date_range')
                    ->label('Date Range')
                    ->schema([
                        DatePicker::make('from')->default(now()->startOfMonth()),
                        DatePicker::make('until')->default(now()),
                    ])
                    ->query(fn (Builder $query, array $data): Builder => $query
                        ->when($data['from'] ?? null, fn (Builder $q, $date) => $q->whereDate('service_orders.created_at', '>=', $date))
                        ->when($data['until'] ?? null, fn (Builder $q, $date) => $q->whereDate('service_orders.created_at', '<=', $date))
                    ),
                SelectFilter::make('type')
                    ->label('Department')
                    ->options(fn () => ServiceDepartment::orderBy('name')->pluck('name', 'slug'))
                    ->searchable(),
                SelectFilter::make('status')
                    ->options([
                        'open' => 'Open',
                        'closed' => 'Closed',
                        'treated' => 'Treated',
                        'cancelled' => 'Cancelled',
                        'refunded' => 'Refunded',
                    ]),
                SelectFilter::make('service_id')
                    ->label('Service')
                    ->options(fn () => Service::orderBy('name')->pluck('name', 'id'))
                    ->searchable(),
                SelectFilter::make('doctor_id')
                    ->label('Provider')
                    ->options(fn () => User::query()
                        ->where(fn ($q) => $q
                            ->whereHas('opdDoctorProfiles')
                            ->orWhereHas('indDoctorProfiles')
                            ->orWhereHas('emergencyDoctorProfiles')
                            ->orWhereHas('dentistProfiles')
                            ->orWhereHas('ultrasoundDoctorProfiles')
                            ->orWhereHas('xrayTechnicianProfiles')
                        )
                        ->orderBy('name')
                        ->pluck('name', 'id'))
                    ->searchable(),
            ])
            ->groups([
                Group::make('type')->label('Department'),
                Group::make('service.name')->label('Service'),
                Group::make('doctor.name')->label('Provider'),
                Group::make('status')->label('Status'),
            ])
            ->defaultGroup('type')
            ->striped()
            ->paginated([25, 50, 100])
            ->defaultPaginationPageOption(25)
            ->recordUrl(fn (ServiceOrder $record): string => static::getUrl('view', ['record' => $record]))
            ->openRecordUrlInNewTab(false)
            ->toolbarActions([
                BulkActionGroup::make([
                    static::mergeDuplicatesBulkAction(),
                ]),
            ]);
    }

    protected static function mergeDuplicatesBulkAction(): BulkAction
    {
        return BulkAction::make('merge_duplicates')
            ->label('Merge Duplicates')
            ->icon('heroicon-o-arrows-pointing-in')
            ->color('warning')
            ->visible(fn () => auth()->user()?->isAdmin() ?? false)
            ->modalHeading('Merge Duplicate Service Orders')
            ->modalDescription('Pick which selected service order should remain as the primary. Everything else (transaction lines, vouchers, consents, bed assignments, versions, treatment record) will be re-pointed to it, and the rest will be soft-deleted.')
            ->modalWidth('2xl')
            ->schema(fn (BulkAction $action): array => [
                TextEntry::make('selected_summary')
                    ->label('Selected')
                    ->html()
                    ->state(function () use ($action): HtmlString {
                        $records = $action->getSelectedRecords();
                        $rows = $records
                            ->map(fn (ServiceOrder $so) => sprintf(
                                '<tr><td class="px-2 py-1 font-mono text-xs">%s</td><td class="px-2 py-1">%s</td><td class="px-2 py-1 text-xs">%s</td><td class="px-2 py-1 text-xs">%s</td></tr>',
                                e($so->so_number),
                                e($so->patient?->name ?? '—'),
                                e($so->service?->name ?? '—'),
                                e($so->created_at?->format('d M Y H:i') ?? '—'),
                            ))
                            ->implode('');

                        return new HtmlString(
                            '<div class="overflow-x-auto"><table class="w-full text-sm border border-gray-200"><thead class="bg-gray-50"><tr>'
                            .'<th class="px-2 py-1 text-left">SO#</th>'
                            .'<th class="px-2 py-1 text-left">Patient</th>'
                            .'<th class="px-2 py-1 text-left">Service</th>'
                            .'<th class="px-2 py-1 text-left">Created</th>'
                            .'</tr></thead><tbody>'.$rows.'</tbody></table></div>'
                        );
                    }),
                Select::make('primary_id')
                    ->label('Keep as primary')
                    ->required()
                    ->native(false)
                    ->options(function () use ($action) {
                        return $action->getSelectedRecords()
                            ->mapWithKeys(fn (ServiceOrder $so) => [
                                $so->id => "{$so->so_number} — {$so->patient?->name} — {$so->service?->name}",
                            ])
                            ->toArray();
                    }),
                Textarea::make('reason')
                    ->label('Reason for merge')
                    ->rows(2)
                    ->maxLength(1000)
                    ->required()
                    ->helperText('Required for audit trail.'),
            ])
            ->action(function (array $data, Collection $records, BulkAction $action) {
                if ($records->count() < 2) {
                    Notification::make()
                        ->title('Select at least 2 service orders to merge.')
                        ->danger()
                        ->send();

                    return;
                }

                $patientIds = $records->pluck('patient_id')->unique()->filter()->values();
                if ($patientIds->count() > 1) {
                    Notification::make()
                        ->title('All selected service orders must belong to the same patient.')
                        ->body('Found patient IDs: '.$patientIds->implode(', '))
                        ->danger()
                        ->send();

                    return;
                }

                $primary = $records->firstWhere('id', (int) $data['primary_id']);
                if (! $primary) {
                    Notification::make()
                        ->title('Primary service order must be one of the selected rows.')
                        ->danger()
                        ->send();

                    return;
                }

                $result = app(ServiceOrderMerger::class)->merge($primary, $records, $data['reason'] ?? null);

                Notification::make()
                    ->title('Merged into '.$primary->so_number)
                    ->body(count($result['merged_ids']).' duplicate(s) merged and soft-deleted.')
                    ->success()
                    ->send();

                $action->deselectAllRecords();
            });
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->schema([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListServiceOrders::route('/'),
            'view' => ViewServiceOrder::route('/{record}'),
        ];
    }
}
