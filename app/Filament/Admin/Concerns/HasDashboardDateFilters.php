<?php

namespace App\Filament\Admin\Concerns;

use App\Helpers\DateHelper;
use Carbon\Carbon;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Pages\Dashboard\Actions\FilterAction;
use Filament\Pages\Dashboard\Concerns\HasFiltersAction;
use Filament\Schemas\Components\Utilities\Set;

trait HasDashboardDateFilters
{
    use HasFiltersAction;

    protected bool $persistsFiltersInSession = true;

    public function getColumns(): int|array
    {
        return [
            'sm' => 4,
            'md' => 4,
            'xl' => 6,
        ];
    }

    public function getHeaderActions(): array
    {
        return [
            FilterAction::make()
                ->schema([
                    Select::make('dateRange')
                        ->label('Date Range')
                        ->options([
                            'today' => 'Today',
                            'last_3_days' => 'Last 3 Days',
                            'this_week' => 'This Week',
                            'last_7_days' => 'Last 7 Days',
                            'this_month' => 'This Month',
                            'last_month' => 'Last Month',
                            'this_year' => 'This Year',
                            'last_year' => 'Last Year',
                            'last_financial_year' => 'Last Financial Year',
                            'custom' => 'Custom Range',
                        ])
                        ->default('this_month')
                        ->live()
                        ->afterStateUpdated(function ($state, Set $set) {
                            $dates = $this->calculateDateRange($state);
                            $set('startDate', $dates['start']);
                            $set('endDate', $dates['end']);
                        }),

                    DatePicker::make('startDate')
                        ->label('Start Date')
                        ->visible(fn ($get) => $get('dateRange') === 'custom')
                        ->default(Carbon::now(DateHelper::timezone())->startOfMonth()),

                    DatePicker::make('endDate')
                        ->label('End Date')
                        ->visible(fn ($get) => $get('dateRange') === 'custom')
                        ->default(Carbon::now(DateHelper::timezone())),
                ]),
        ];
    }

    protected function calculateDateRange(string $range): array
    {
        $now = Carbon::now(DateHelper::timezone());

        return match ($range) {
            'today' => [
                'start' => $now->copy()->startOfDay(),
                'end' => $now->copy()->endOfDay(),
            ],
            'last_3_days' => [
                'start' => $now->copy()->subDays(2)->startOfDay(),
                'end' => $now->copy()->endOfDay(),
            ],
            'this_week' => [
                'start' => $now->copy()->startOfWeek(),
                'end' => $now->copy()->endOfWeek(),
            ],
            'last_7_days' => [
                'start' => $now->copy()->subDays(6)->startOfDay(),
                'end' => $now->copy()->endOfDay(),
            ],
            'this_month' => [
                'start' => $now->copy()->startOfMonth(),
                'end' => $now->copy()->endOfMonth(),
            ],
            'last_month' => [
                'start' => $now->copy()->subMonth()->startOfMonth(),
                'end' => $now->copy()->subMonth()->endOfMonth(),
            ],
            'this_year' => [
                'start' => $now->copy()->startOfYear(),
                'end' => $now->copy()->endOfYear(),
            ],
            'last_year' => [
                'start' => $now->copy()->subYear()->startOfYear(),
                'end' => $now->copy()->subYear()->endOfYear(),
            ],
            'last_financial_year' => [
                'start' => $this->getLastFinancialYearStart(),
                'end' => $this->getLastFinancialYearEnd(),
            ],
            default => [
                'start' => $now->copy()->startOfMonth(),
                'end' => $now->copy()->endOfMonth(),
            ],
        };
    }

    protected function getLastFinancialYearStart(): Carbon
    {
        $now = Carbon::now(DateHelper::timezone());
        $currentFinancialYearStart = $now->copy()->month(7)->startOfMonth();

        if ($now->month < 7) {
            $currentFinancialYearStart->subYear();
        }

        return $currentFinancialYearStart->copy()->subYear();
    }

    protected function getLastFinancialYearEnd(): Carbon
    {
        return $this->getLastFinancialYearStart()->copy()->addYear()->month(6)->endOfMonth();
    }

    public function getStartDate(): Carbon
    {
        $filters = $this->getFilters();

        if (isset($filters['dateRange']) && $filters['dateRange'] === 'custom') {
            if (isset($filters['startDate'])) {
                return Carbon::parse($filters['startDate'], DateHelper::timezone());
            }
        }

        if (isset($filters['dateRange']) && $filters['dateRange'] !== 'custom') {
            return $this->calculateDateRange($filters['dateRange'])['start'];
        }

        return Carbon::now(DateHelper::timezone())->startOfMonth();
    }

    public function getEndDate(): Carbon
    {
        $filters = $this->getFilters();

        if (isset($filters['dateRange']) && $filters['dateRange'] === 'custom') {
            if (isset($filters['endDate'])) {
                return Carbon::parse($filters['endDate'], DateHelper::timezone());
            }
        }

        if (isset($filters['dateRange']) && $filters['dateRange'] !== 'custom') {
            return $this->calculateDateRange($filters['dateRange'])['end'];
        }

        return Carbon::now(DateHelper::timezone());
    }
}
