<?php

namespace App\Exports;

use App\Helpers\DateHelper;
use App\Models\Closing;
use App\Models\TransactionElement;
use Illuminate\Contracts\Support\Responsable;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;

class ExpenseReportExport implements FromQuery, Responsable, ShouldAutoSize, WithHeadings, WithMapping, WithTitle
{
    use Exportable;

    public string $fileName = 'expense-report.xlsx';

    public function __construct(private readonly array $filters = []) {}

    public function query()
    {
        $query = TransactionElement::query()
            ->with(['transaction.closing', 'expenseCategory', 'expVoucher.payedTo'])
            ->where('income_or_expense', 'EXPENSE');

        if ($this->filters['from'] ?? null) {
            $query->where('transaction_elements.created_at', '>=', DateHelper::dayStartUtc($this->filters['from']));
        }
        if ($this->filters['until'] ?? null) {
            $query->where('transaction_elements.created_at', '<=', DateHelper::dayEndUtc($this->filters['until']));
        }
        if ($this->filters['reception_id'] ?? null) {
            $query->whereIn('closing_id', Closing::where('reception_id', $this->filters['reception_id'])->select('id'));
        }
        if ($this->filters['type'] ?? null) {
            $query->where('type', $this->filters['type']);
        }
        if ($this->filters['expense_category_id'] ?? null) {
            $query->where('expense_category_id', $this->filters['expense_category_id']);
        }

        return $query->orderByDesc('created_at');
    }

    public function headings(): array
    {
        return ['Date', 'Counter', 'TR#', 'Type', 'Category', 'Voucher#', 'Paid To', 'Notes', 'Amount'];
    }

    public function map($row): array
    {
        return [
            DateHelper::pdfFormat($row->created_at, 'd M Y H:i'),
            $row->transaction?->closing?->ct_number,
            $row->transaction?->tr_number,
            $row->type,
            $row->expenseCategory?->name,
            $row->expVoucher?->vc_number,
            $row->expVoucher?->payedTo?->name,
            $row->notes,
            number_format((float) $row->amount, 2),
        ];
    }

    public function title(): string
    {
        return 'Expense Report';
    }
}
