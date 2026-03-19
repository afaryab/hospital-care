<?php

namespace Processton\Abacus\Filament\Pages;

use Filament\Actions\Action;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Illuminate\Support\Collection;
use Processton\Abacus\Models\AbacusChartOfAccount;
use Processton\Abacus\Models\AbacusTransaction;
use Processton\Abacus\Models\AbacusYear;
use Symfony\Component\HttpFoundation\Response;
use UnitEnum;

class ProfitLossStatement extends Page implements HasForms
{
    use InteractsWithForms;

    protected string $view = 'abacus::profit-loss-statement';

    protected static ?string $title = 'Profit & Loss Statement';

    protected static UnitEnum|string|null $navigationGroup = 'Reporting';

    protected static ?int $navigationSort = 12;

    public ?int $yearId = null;

    public ?string $startDate = null;

    public ?string $endDate = null;

    public Collection $incomeRows;

    public Collection $expenseRows;

    public float $totalIncome = 0;

    public float $totalExpense = 0;

    public bool $showPdf = false;

    public function mount(): void
    {
        $this->incomeRows = collect();
        $this->expenseRows = collect();
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->schema(
                [
                    Forms\Components\Select::make('yearId')
                        ->label('Year')
                        ->required()
                        ->options(
                            AbacusYear::orderByDesc('start_date')->get()->map(
                                fn (AbacusYear $year) => [
                                    'label' => $year->start_date->format('Y').' - '.$year->end_date->format('Y'),
                                    'value' => $year->id,
                                ]
                            )->pluck('label', 'value')
                        ),

                    Forms\Components\DatePicker::make('startDate')->label('From'),
                    Forms\Components\DatePicker::make('endDate')->label('To'),
                ]
            )
            ->columns(1);
    }

    /**
     * @return array<Action>
     */
    protected function getFormActions(): array
    {
        return [
            Action::make('viewReport')
                ->label('View Report')
                ->submit('viewReport'),
            Action::make('downloadPdf')
                ->label('Download PDF')
                ->action('downloadPdf')
                ->color('success')
                ->icon('heroicon-o-document-arrow-down')
                ->visible(fn () => $this->incomeRows->isNotEmpty() || $this->expenseRows->isNotEmpty()),
        ];
    }

    public function viewReport(): void
    {
        $incomeAccounts = AbacusChartOfAccount::where('base_type', 'income')->whereNotNull('parent_id')->get();
        $expenseAccounts = AbacusChartOfAccount::where('base_type', 'expense')->whereNotNull('parent_id')->get();

        $this->incomeRows = $incomeAccounts->map(function ($account) {
            $query = AbacusTransaction::where('abacus_chart_of_account_id', $account->id);

            if ($this->yearId) {
                $query->where('abacus_year_id', $this->yearId);
            }

            if ($this->startDate) {
                $query->whereDate('date', '>=', $this->startDate);
            }

            if ($this->endDate) {
                $query->whereDate('date', '<=', $this->endDate);
            }

            $credit = (clone $query)->where('entry_type', 'credit')->sum('amount');
            $debit = (clone $query)->where('entry_type', 'debit')->sum('amount');

            $amount = $credit - $debit;

            return [
                'name' => $account->name,
                'code' => $account->code,
                'amount' => $amount > 0 ? $amount : 0, // show only positive income
            ];
        })->filter(fn ($row) => $row['amount'] > 0);

        // dd($this->incomeRows);

        $this->expenseRows = $expenseAccounts->map(function ($account) {
            $query = AbacusTransaction::where('abacus_chart_of_account_id', $account->id);

            if ($this->yearId) {
                $query->where('abacus_year_id', $this->yearId);
            }

            if ($this->startDate) {
                $query->whereDate('date', '>=', $this->startDate);
            }

            if ($this->endDate) {
                $query->whereDate('date', '<=', $this->endDate);
            }

            $debit = (clone $query)->where('entry_type', 'debit')->sum('amount');
            $credit = (clone $query)->where('entry_type', 'credit')->sum('amount');

            $amount = $debit - $credit;

            return [
                'name' => $account->name,
                'code' => $account->code,
                'amount' => $amount > 0 ? $amount : 0, // avoid showing negative expenses
            ];
        })->filter(fn ($row) => $row['amount'] > 0);

        $this->totalIncome = $this->incomeRows->sum('amount');
        $this->totalExpense = $this->expenseRows->sum('amount');
    }

    public function downloadPdf(): Response
    {
        // Get year information
        $year = $this->yearId ? AbacusYear::find($this->yearId) : null;

        $data = [
            'incomeRows' => $this->incomeRows,
            'expenseRows' => $this->expenseRows,
            'year' => $year,
            'startDate' => $this->startDate,
            'endDate' => $this->endDate,
            'totalIncome' => $this->totalIncome,
            'totalExpense' => $this->totalExpense,
            'generatedAt' => now()->format('Y-m-d H:i:s'),
        ];

        $pdf = \App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option('enable_php', true);
        $pdf->loadView('abacus::profit-loss-statement-pdf', $data);

        $filename = 'profit-loss-statement-'.now()->format('Y-m-d').'.pdf';

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, $filename, ['Content-Type' => 'application/pdf']);
    }

    public function streamPdf(): Response
    {
        $this->yearId = request()->input('yearId', $this->yearId);
        $this->startDate = request()->input('startDate', $this->startDate);
        $this->endDate = request()->input('endDate', $this->endDate);

        // Get year information
        $year = $this->yearId ? AbacusYear::find($this->yearId) : null;

        $this->mount();

        // Ensure rows are populated
        if ($this->incomeRows->isEmpty() && $this->expenseRows->isEmpty()) {
            $this->viewReport();
        }

        $data = [
            'incomeRows' => $this->incomeRows,
            'expenseRows' => $this->expenseRows,
            'year' => $year,
            'startDate' => $this->startDate,
            'endDate' => $this->endDate,
            'totalIncome' => $this->totalIncome,
            'totalExpense' => $this->totalExpense,
            'generatedAt' => now()->format('Y-m-d H:i:s'),
        ];

        $pdf = \App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option('enable_php', true);
        $pdf->loadView('abacus::profit-loss-statement-pdf', $data);

        return response($pdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="profit-loss-statement-'.now()->format('Y-m-d').'.pdf"',
        ]);
    }
}
