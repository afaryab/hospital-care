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
use Filament\Actions\Action;
use Illuminate\Support\Collection;
use Barryvdh\DomPDF\Facade\Pdf;
use Symfony\Component\HttpFoundation\Response;
use UnitEnum;

class CashFlowStatement extends Page implements HasForms
{
    use InteractsWithForms;

    protected string $view = 'abacus::cash-flow-statement';
    protected static ?string $title = 'Cash Flow Statement';


    protected static UnitEnum|string|null $navigationGroup = 'Reporting';
    protected static ?int $navigationSort = 14;

    public ?int $yearId = null;
    public Collection $operatingCashFlows;
    public Collection $investingCashFlows;
    public Collection $financingCashFlows;
    public float $operatingTotal = 0;
    public float $investingTotal = 0;
    public float $financingTotal = 0;
    public float $netChange = 0;

    public function mount(): void
    {
        $this->operatingCashFlows = collect();
        $this->investingCashFlows = collect();
        $this->financingCashFlows = collect();
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
                                    'label' => $year->start_date->format('Y') . ' - ' . $year->end_date->format('Y'),
                                    'value' => $year->id,
                                ]
                            )->pluck('label', 'value')
                        ),

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
                ->visible(fn () => $this->operatingCashFlows->isNotEmpty() || $this->investingCashFlows->isNotEmpty() || $this->financingCashFlows->isNotEmpty()),
        ];
    }
    public function viewReport(): void
    {

        $cashAccountIds = AbacusChartOfAccount::whereIn('name', ['Cash in Hand', 'Bank Accounts'])->pluck('id');

        $transactions = AbacusTransaction::with('incoming', 'account')
            ->whereIn('abacus_chart_of_account_id', $cashAccountIds)
            ->where('abacus_year_id', $this->yearId)
            ->orderBy('date')
            ->get();

        foreach ($transactions as $trx) {
            $pair = AbacusTransaction::where('abacus_incoming_id', $trx->abacus_incoming_id)
                ->where('id', '!=', $trx->id)->first();

            if (!$pair) continue;

            $account = $pair->account;
            $flowType = match ($account->base_type) {
                'income', 'expense' => 'operating',
                'asset' => 'investing',
                'liability', 'equity' => 'financing',
                default => 'unknown',
            };

            $amount = $trx->entry_type === 'debit' ? $trx->amount : -$trx->amount;

            $this->{$flowType . 'CashFlows'}->push([
                'description' => $trx->incoming->description ?? $account->name,
                'amount' => $amount,
            ]);
        }

        $this->operatingTotal = $this->operatingCashFlows->sum('amount');
        $this->investingTotal = $this->investingCashFlows->sum('amount');
        $this->financingTotal = $this->financingCashFlows->sum('amount');
        $this->netChange = $this->operatingTotal + $this->investingTotal + $this->financingTotal;
    }

    public function downloadPdf(): Response
    {
        // Get year information
        $year = $this->yearId ? AbacusYear::find($this->yearId) : null;
        
        $data = [
            'operatingCashFlows' => $this->operatingCashFlows,
            'investingCashFlows' => $this->investingCashFlows,
            'financingCashFlows' => $this->financingCashFlows,
            'operatingTotal' => $this->operatingTotal,
            'investingTotal' => $this->investingTotal,
            'financingTotal' => $this->financingTotal,
            'netChange' => $this->netChange,
            'selectedYear' => AbacusYear::find($this->yearId),
            'generatedAt' => now()->format('Y-m-d H:i:s'),
        ];

        $pdf = \App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->loadView('abacus::cash-flow-statement-pdf', $data);

        $filename = 'cash-flow-statement-' . now()->format('Y-m-d') . '.pdf';

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, $filename, ['Content-Type' => 'application/pdf']);
    }

    public function streamPdf(): Response
    {
        $this->yearId = request()->input('yearId', $this->yearId);
        
        // Get year information
        $year = $this->yearId ? AbacusYear::find($this->yearId) : null;

        $this->mount();

        // Ensure rows are populated
        if ($this->operatingCashFlows->isEmpty() && $this->investingCashFlows->isEmpty() && $this->financingCashFlows->isEmpty()) {
            $this->viewReport();
        }
        
        $data = [
            'operatingCashFlows' => $this->operatingCashFlows,
            'investingCashFlows' => $this->investingCashFlows,
            'financingCashFlows' => $this->financingCashFlows,
            'operatingTotal' => $this->operatingTotal,
            'investingTotal' => $this->investingTotal,
            'financingTotal' => $this->financingTotal,
            'netChange' => $this->netChange,
            'selectedYear' => AbacusYear::find($this->yearId),
            'generatedAt' => now()->format('Y-m-d H:i:s'),
        ];

        $pdf = \App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->loadView('abacus::cash-flow-statement-pdf', $data);

        return response($pdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="cash-flow-statement-' . now()->format('Y-m-d') . '.pdf"'
        ]);
    }

}
