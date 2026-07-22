<?php

namespace Processton\Abacus\Filament\Pages;

use App\Helpers\DateHelper;
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

class GeneralLedger extends Page implements HasForms
{
    use InteractsWithForms;

    // protected static ?string $navigationIcon = 'heroicon-o-document-report';
    protected string $view = 'abacus::general-ledger';

    protected static ?string $title = 'General Ledger';

    protected static UnitEnum|string|null $navigationGroup = 'Reporting';

    protected static ?int $navigationSort = 10;

    public ?int $accountId = null;

    public Collection $accounts;

    public ?int $yearId = null;

    public ?string $startDate = null;

    public ?string $endDate = null;

    public Collection $entries;

    public bool $showPdf = false;

    public function mount(): void
    {
        $this->entries = collect();
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->schema(
                [
                    Forms\Components\Select::make('accountId')
                        ->label('Account')
                        ->options(AbacusChartOfAccount::orderBy('code')->pluck('name', 'id'))
                        ->searchable()
                        ->required(),

                    Forms\Components\Select::make('yearId')
                        ->label('Year')
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
                ->submit('viewPdf'),
            Action::make('downloadPdf')
                ->label('Download PDF')
                ->action('downloadPdf')
                ->color('success')
                ->icon('heroicon-o-document-arrow-down')
                ->visible(fn () => $this->entries->isNotEmpty()),
        ];
    }

    public function viewPdf(): void
    {
        $this->showPdf = true;
    }

    public function getChildrenIds($accountIds)
    {
        $accounts = AbacusChartOfAccount::whereIn('id', $accountIds)->get();
        foreach ($accounts as $account) {
            $accountIds[] = $account->id;
            if ($account->children->count() > 0) {
                $accountIds = [...$accountIds, ...$this->getChildrenIds($account->children->pluck('id')->toArray())];
            }
        }

        return $accountIds;
    }

    public function viewReport(): void
    {

        // Fetch all children accounts of the selected account nestedly
        $account = AbacusChartOfAccount::find($this->accountId);

        $accountIds = [$this->accountId];

        if ($account->children->count() > 0) {
            $accountIds = [...$accountIds, ...$this->getChildrenIds($accountIds)];
        }

        $accountIds = collect($accountIds)->unique()->toArray();

        $this->accounts = AbacusChartOfAccount::whereIn('id', $accountIds)
            ->orderBy('code')
            ->get();

        $query = AbacusTransaction::query()
            ->whereIn('abacus_chart_of_account_id', $accountIds)
            ->orderBy('date');

        if ($this->yearId) {
            $query->where('abacus_year_id', $this->yearId);
        }

        if ($this->startDate) {
            $query->whereDate('date', '>=', $this->startDate);
        }

        if ($this->endDate) {
            $query->whereDate('date', '<=', $this->endDate);
        }

        $this->entries = $query->get();
    }

    public function downloadPdf(): Response
    {
        // Get the account name for the filename
        $account = AbacusChartOfAccount::find($this->accountId);
        $accountName = $account ? $account->name : 'Unknown';

        // Get year information
        $year = $this->yearId ? AbacusYear::find($this->yearId) : null;

        // Calculate totals and running balance
        $entries = $this->entries;
        $runningBalance = 0;
        $processedEntries = $entries->map(function ($entry) use (&$runningBalance) {
            $amount = $entry->amount;
            if ($entry->entry_type === 'debit') {
                $runningBalance += $amount;
            } else {
                $runningBalance -= $amount;
            }

            return (object) [
                'date' => $entry->date,
                'year_id' => $entry->abacus_year_id,
                'incoming_id' => $entry->abacus_incoming_id,
                'debit' => $entry->entry_type === 'debit' ? $amount : 0,
                'credit' => $entry->entry_type === 'credit' ? $amount : 0,
                'running_balance' => $runningBalance,
            ];
        });

        $totalDebit = $entries->where('entry_type', 'debit')->sum('amount');
        $totalCredit = $entries->where('entry_type', 'credit')->sum('amount');

        $data = [
            'entries' => $processedEntries,
            'account' => $account,
            'year' => $year,
            'startDate' => $this->startDate,
            'endDate' => $this->endDate,
            'totalDebit' => $totalDebit,
            'totalCredit' => $totalCredit,
            'generatedAt' => DateHelper::pdfFormat(now(), 'Y-m-d H:i:s'),
        ];

        $pdf = \App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option('enable_php', true);
        $pdf->loadView('abacus::general-ledger-pdf', $data);

        $filename = 'general-ledger-'.str_replace(' ', '-', strtolower($accountName)).'-'.now()->format('Y-m-d').'.pdf';

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, $filename, ['Content-Type' => 'application/pdf']);
    }

    public function streamPdf(): Response
    {

        $this->mount();

        $this->accountId = request()->input('accountId', $this->accountId);
        $this->yearId = request()->input('yearId', $this->yearId);
        $this->startDate = request()->input('startDate', $this->startDate);
        $this->endDate = request()->input('endDate', $this->endDate);

        // Ensure rows are populated
        if ($this->entries->isEmpty()) {
            $this->viewReport();
        }
        $account = AbacusChartOfAccount::find($this->accountId);
        $accountName = $account ? $account->name : 'Unknown';
        $year = $this->yearId ? AbacusYear::find($this->yearId) : null;

        $entries = $this->entries;
        $runningBalance = 0;
        $processedEntries = $entries->map(function ($entry) use (&$runningBalance) {
            $amount = $entry->amount;
            if ($entry->entry_type === 'debit') {
                $runningBalance += $amount;
            } else {
                $runningBalance -= $amount;
            }

            return (object) [
                'date' => $entry->date,
                'year_id' => $entry->abacus_year_id,
                'incoming_id' => $entry->abacus_incoming_id,
                'debit' => $entry->entry_type === 'debit' ? $amount : 0,
                'credit' => $entry->entry_type === 'credit' ? $amount : 0,
                'running_balance' => $runningBalance,
                'account' => $this->accounts->firstWhere('id', $entry->abacus_chart_of_account_id) ?: (object) [
                    'name' => 'Unknown Account',
                    'code' => 'N/A',
                ],
            ];
        });

        $totalDebit = $entries->where('entry_type', 'debit')->sum('amount');
        $totalCredit = $entries->where('entry_type', 'credit')->sum('amount');

        $data = [
            'entries' => $processedEntries,
            'account' => $account,
            'accounts' => $this->accounts,
            'year' => $year,
            'startDate' => $this->startDate,
            'endDate' => $this->endDate,
            'totalDebit' => $totalDebit,
            'totalCredit' => $totalCredit,
            'generatedAt' => DateHelper::pdfFormat(now(), 'Y-m-d H:i:s'),
        ];

        $pdf = \App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option('enable_php', true);
        $pdf->loadView('abacus::general-ledger-pdf', $data);

        return response($pdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="general-ledger-'.str_replace(' ', '-', strtolower($accountName)).'-'.now()->format('Y-m-d').'.pdf"',
        ]);
    }
}
