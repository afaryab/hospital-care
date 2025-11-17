<?php

namespace App\Filament\Accounts\Pages;

use Carbon\Carbon;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Pages\Dashboard\Concerns\HasFiltersAction;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Pages\Dashboard\Actions\FilterAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Hidden;


class Dashboard extends BaseDashboard
{
    use HasFiltersAction;


    protected static ?int $sort = 1;

    // protected static string $routePath = '/';

    protected static ?int $navigationSort = -15;

    protected static ?string $title = 'Dashboard';

    protected bool $persistsFiltersInSession = true;

    public function getColumns(): int | array
    {
        return [
            'sm' => 4,
            'md' => 4,
            'xl' => 6,
        ];
    }

    public static function canView(): bool
    {
        return true; // || auth()->user()->isAdmin();
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
                        ->default('last_7_days')
                        ->live()
                        ->afterStateUpdated(function ($state, callable $set) {
                            $dates = $this->calculateDateRange($state);
                            $set('startDate', $dates['start']);
                            $set('endDate', $dates['end']);
                        }),
                    
                    DatePicker::make('startDate')
                        ->label('Start Date')
                        ->visible(fn ($get) => $get('dateRange') === 'custom')
                        ->default(Carbon::now()->subDays(7)),
                    
                    DatePicker::make('endDate')
                        ->label('End Date')
                        ->visible(fn ($get) => $get('dateRange') === 'custom')
                        ->default(Carbon::now()),
                    
                    // Hidden fields to always have startDate and endDate available for widgets
                    Hidden::make('computed_startDate'),
                    Hidden::make('computed_endDate'),
                ])
        ];
    }

    /**
     * Calculate date range based on predefined options
     */
    protected function calculateDateRange(string $range): array
    {
        $now = Carbon::now();
        
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
                'start' => $now->copy()->subDays(7),
                'end' => $now,
            ],
        };
    }

    /**
     * Get last financial year start date (assuming financial year starts in July)
     * Adjust the month (7 = July) according to your financial year
     */
    protected function getLastFinancialYearStart(): Carbon
    {
        $now = Carbon::now();
        $currentFinancialYearStart = $now->copy()->month(7)->startOfMonth();
        
        if ($now->month < 7) {
            // If we're before July, current financial year started last year
            $currentFinancialYearStart->subYear();
        }
        
        // Last financial year started one year before current financial year
        return $currentFinancialYearStart->copy()->subYear();
    }

    /**
     * Get last financial year end date
     */
    protected function getLastFinancialYearEnd(): Carbon
    {
        return $this->getLastFinancialYearStart()->copy()->addYear()->month(6)->endOfMonth();
    }

    /**
     * Get the start date for widgets to use
     */
    public function getStartDate(): Carbon
    {
        $filters = $this->getFilters();
        
        // Handle custom date range
        if (isset($filters['dateRange']) && $filters['dateRange'] === 'custom') {
            if (isset($filters['startDate'])) {
                return Carbon::parse($filters['startDate']);
            }
        }
        
        // Handle predefined date ranges
        if (isset($filters['dateRange']) && $filters['dateRange'] !== 'custom') {
            $dates = $this->calculateDateRange($filters['dateRange']);
            return $dates['start'];
        }
        
        // Default fallback
        return Carbon::now()->subDays(7);
    }

    /**
     * Get the end date for widgets to use
     */
    public function getEndDate(): Carbon
    {
        $filters = $this->getFilters();
        
        // Handle custom date range
        if (isset($filters['dateRange']) && $filters['dateRange'] === 'custom') {
            if (isset($filters['endDate'])) {
                return Carbon::parse($filters['endDate']);
            }
        }
        
        // Handle predefined date ranges
        if (isset($filters['dateRange']) && $filters['dateRange'] !== 'custom') {
            $dates = $this->calculateDateRange($filters['dateRange']);
            return $dates['end'];
        }
        
        // Default fallback
        return Carbon::now();
    }
}