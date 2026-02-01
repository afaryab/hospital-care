<?php

namespace Processton\Abacus\Filament\Pages;

use Processton\Abacus\Models\AbacusTransaction;
use Processton\Abacus\Models\AbacusChartOfAccount;
use Processton\Abacus\Models\AbacusYear;
use Filament\Pages\Page;
use Filament\Forms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Schemas\Schema;
use Illuminate\Support\Collection;
use Barryvdh\DomPDF\Facade\Pdf;
use Symfony\Component\HttpFoundation\Response;
use UnitEnum;

class BalanceSheet extends Page implements HasForms
{
    use InteractsWithForms;

    // protected static ?string $navigationIcon = 'heroicon-o-chart-bar';

    protected string $view = 'abacus::balance-sheet';
    protected static ?string $title = 'Balance Sheet';

    protected static UnitEnum|string|null $navigationGroup = 'Reporting';
    protected static ?int $navigationSort = 13;

    public ?int $yearId = null;
    public ?float $netProfit = null;

    public Collection $assets;
    public Collection $liabilities;
    public Collection $equity;

    public function mount(): void
    {
        $this->assets = collect();
        $this->liabilities = collect();
        $this->equity = collect();
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Forms\Components\Select::make('yearId')
                    ->label('Year')
                    ->required()
                    ->live()
                    ->afterStateUpdated(function ($state) {
                        $this->yearId = $state;
                        $this->generateReport();
                    })
                    ->options(
                        AbacusYear::orderByDesc('start_date')->get()->map(
                            fn (AbacusYear $year) => [
                                'label' => $year->start_date->format('Y') . ' - ' . $year->end_date->format('Y'),
                                'value' => $year->id,
                            ]
                        )->pluck('label', 'value')
                    ),
            ])
            ->columns(1);
    }

    public function generateReport(): void
    {
        $this->assets = $this->buildHierarchy('asset');
        $this->liabilities = $this->buildHierarchy('liability');
        $this->equity = $this->buildHierarchy('equity');
        $this->netProfit = $this->calculateNetProfit();
        $this->equity->push([
            'name' => 'Net Profit',
            'is_group' => false,
            'children' => collect([[
                'name' => 'Retained Earnings (Auto)',
                'amount' => $this->netProfit,
            ]]),
        ]);

    }

    protected function calculateNetProfit(): float
    {
        $incomeAccounts = AbacusChartOfAccount::where('base_type', 'income')->whereNotNull('parent_id')->get();
        $expenseAccounts = AbacusChartOfAccount::where('base_type', 'expense')->whereNotNull('parent_id')->get();
        
        $incomeRows = $incomeAccounts->map(function ($account) {
            $query = AbacusTransaction::where('abacus_chart_of_account_id', $account->id);

            if ($this->yearId) {
                $query->where('abacus_year_id', $this->yearId);
            }

            $credit = (clone $query)->where('entry_type', 'credit')->sum('amount');
            $debit  = (clone $query)->where('entry_type', 'debit')->sum('amount');

            $amount = $credit - $debit;

            return [
                'name' => $account->name,
                'code' => $account->code,
                'amount' => $amount > 0 ? $amount : 0, // show only positive income
            ];
        })->filter(fn($row) => $row['amount'] > 0);

        // dd($this->incomeRows);


        $expenseRows = $expenseAccounts->map(function ($account) {
            $query = AbacusTransaction::where('abacus_chart_of_account_id', $account->id);

            if ($this->yearId) {
                $query->where('abacus_year_id', $this->yearId);
            }

            $debit = (clone $query)->where('entry_type', 'debit')->sum('amount');
            $credit = (clone $query)->where('entry_type', 'credit')->sum('amount');

            $amount = $debit - $credit;

            return [
                'name' => $account->name,
                'code' => $account->code,
                'amount' => $amount > 0 ? $amount : 0, // avoid showing negative expenses
            ];
        })->filter(fn($row) => $row['amount'] > 0);

        $totalIncome = $incomeRows->sum('amount');
        $totalExpense = $expenseRows->sum('amount');

        return $totalIncome - $totalExpense;
    }

    public function getChildrenIds($accountIds){
        $accounts = AbacusChartOfAccount::whereIn('id', $accountIds)->get();
        foreach ($accounts as $account) {
            $accountIds[] = $account->id;
            if($account->children->count() > 0) {
                $accountIds = [...$accountIds, ...$this->getChildrenIds($account->children->pluck('id')->toArray())];
            }
        }
        return $accountIds;
    }

    private function buildHierarchy(string $baseType): Collection
    {
        $accounts = AbacusChartOfAccount::with('children')
            ->where('base_type', $baseType)
            // ->whereNull('parent_id')
            ->orderBy('code')
            ->get();

        return $accounts->map(function ($account) {
            return [
                'name' => $account->name,
                'is_group' => true,
                'children' => $account->children->map(function ($child) {
                    $debit = AbacusTransaction::where('abacus_chart_of_account_id', $child->id)
                        ->where('abacus_year_id', $this->yearId)
                        ->where('entry_type', 'debit')
                        ->sum('amount');

                    $credit = AbacusTransaction::where('abacus_chart_of_account_id', $child->id)
                        ->where('abacus_year_id', $this->yearId)
                        ->where('entry_type', 'credit')
                        ->sum('amount');

                    $balance = match ($child->base_type) {
                        'asset' => $debit - $credit,
                        'liability', 'equity' => $credit - $debit,
                    };

                    return [
                        'name' => $child->name,
                        'amount' => $balance,
                    ];
                }), // Remove filter to show all accounts including zero balances
            ];
        }); // Remove filter to show all groups including empty ones
    }

    public function downloadPdf(): Response
    {
        // Get year information
        $year = $this->yearId ? AbacusYear::find($this->yearId) : null;
        
        // Calculate totals
        $totalAssets = $this->assets->sum(function ($group) {
            return $group['children']->sum('amount');
        });
        
        $totalLiabilities = $this->liabilities->sum(function ($group) {
            return $group['children']->sum('amount');
        });
        
        $totalEquity = $this->equity->sum(function ($group) {
            return $group['children']->sum('amount');
        });
        
        $data = [
            'assets' => $this->assets,
            'liabilities' => $this->liabilities,
            'equity' => $this->equity,
            'year' => $year,
            'totalAssets' => $totalAssets,
            'totalLiabilities' => $totalLiabilities,
            'totalEquity' => $totalEquity,
            'generatedAt' => now()->format('Y-m-d H:i:s'),
        ];

        $pdf = \App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->loadView('abacus::balance-sheet-pdf', $data);
        
        $filename = 'balance-sheet-' . now()->format('Y-m-d') . '.pdf';
        
        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, $filename, ['Content-Type' => 'application/pdf']);
    }
// 
    public function streamPdf(): Response
    {
        $this->yearId = request()->input('yearId', $this->yearId);
        
        // Get year information
        $year = $this->yearId ? AbacusYear::find($this->yearId) : null;

        $this->mount();

        // Ensure data is populated
        if ($this->assets->isEmpty() && $this->liabilities->isEmpty() && $this->equity->isEmpty()) {
            $this->generateReport();
        }
        
        // Calculate totals
        $totalAssets = $this->assets->sum(function ($group) {
            return $group['children']->sum('amount');
        });
        
        $totalLiabilities = $this->liabilities->sum(function ($group) {
            return $group['children']->sum('amount');
        });
        
        $totalEquity = $this->equity->sum(function ($group) {
            return $group['children']->sum('amount');
        });
        
        $data = [
            'assets' => $this->assets,
            'liabilities' => $this->liabilities,
            'equity' => $this->equity,
            'year' => $year,
            'totalAssets' => $totalAssets,
            'totalLiabilities' => $totalLiabilities,
            'totalEquity' => $totalEquity,
            'generatedAt' => now()->format('Y-m-d H:i:s'),
        ];

        // return view('abacus::balance-sheet-pdf', $data);

        $pdf = \App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->loadView('abacus::balance-sheet-pdf', $data)->setWarnings(true);
        
        return response($pdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="balance-sheet-' . now()->format('Y-m-d') . '.pdf"'
        ]);
    }
}
