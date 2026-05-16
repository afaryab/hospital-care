<?php

namespace App\Filament\Admin\Resources\PayrollPeriods;

use App\Enum\PayrollPeriodStatus;
use App\Filament\Admin\Resources\PayrollPeriods\Pages\CreatePayrollPeriod;
use App\Filament\Admin\Resources\PayrollPeriods\Pages\EditPayrollPeriod;
use App\Filament\Admin\Resources\PayrollPeriods\Pages\ListPayrollPeriods;
use App\Models\PayrollPeriod;
use App\Models\User;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use UnitEnum;

class PayrollPeriodResource extends Resource
{
    protected static ?string $model = PayrollPeriod::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCalendarDays;

    protected static string|UnitEnum|null $navigationGroup = 'HR & Payroll';

    protected static ?string $label = 'Payroll Period';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('period_number')->label('Period #')->disabled(),
            TextInput::make('year')->numeric()->required()->minValue(2000)->maxValue(2099),
            Select::make('month')
                ->options([
                    1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
                    5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August',
                    9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December',
                ])
                ->required(),
            Select::make('status')
                ->options(collect(PayrollPeriodStatus::cases())->mapWithKeys(fn ($c) => [$c->value => ucfirst($c->value)]))
                ->default(PayrollPeriodStatus::Draft->value)
                ->required(),
            Select::make('processed_by')
                ->label('Processed By')
                ->options(fn () => User::orderBy('name')->pluck('name', 'id'))
                ->searchable()
                ->nullable(),
            Select::make('approved_by')
                ->label('Approved By')
                ->options(fn () => User::orderBy('name')->pluck('name', 'id'))
                ->searchable()
                ->nullable(),
            Textarea::make('notes')->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('year', 'desc')
            ->columns([
                TextColumn::make('period_number')->label('Period #')->searchable()->sortable()->fontFamily('mono'),
                TextColumn::make('year')->sortable(),
                TextColumn::make('month')
                    ->formatStateUsing(fn ($state) => date('F', mktime(0, 0, 0, $state, 1)))
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'paid' => 'success',
                        'approved' => 'info',
                        'calculated' => 'warning',
                        'closed' => 'gray',
                        default => 'gray',
                    }),
                TextColumn::make('processedBy.name')->label('Processed By')->toggleable(),
                TextColumn::make('approvedBy.name')->label('Approved By')->toggleable(),
                TextColumn::make('approved_at')->label('Approved')->dateTime('d M Y')->toggleable(),
                TextColumn::make('paid_at')->label('Paid')->dateTime('d M Y')->toggleable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(collect(PayrollPeriodStatus::cases())->mapWithKeys(fn ($c) => [$c->value => ucfirst($c->value)])),
            ])
            ->recordActions([EditAction::make()])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])])
            ->striped();
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPayrollPeriods::route('/'),
            'create' => CreatePayrollPeriod::route('/create'),
            'edit' => EditPayrollPeriod::route('/{record}/edit'),
        ];
    }
}
