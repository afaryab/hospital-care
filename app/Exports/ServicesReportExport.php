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

class ServicesReportExport implements FromQuery, Responsable, ShouldAutoSize, WithHeadings, WithMapping, WithTitle
{
    use Exportable;

    public string $fileName = 'services-report.xlsx';

    public function __construct(private readonly array $filters = []) {}

    public function query()
    {
        $query = TransactionElement::query()
            ->with(['transaction', 'service.department', 'doctor', 'expVoucher.payedTo', 'patient'])
            ->where(function ($q) {
                $q->whereNotNull('service_id')
                    ->orWhere(function ($q2) {
                        $q2->where('income_or_expense', 'EXPENSE')
                            ->whereNotNull('exp_voucher_id');
                    });
            });

        if ($this->filters['from'] ?? null) {
            $query->where('transaction_elements.created_at', '>=', DateHelper::dayStartUtc($this->filters['from']));
        }
        if ($this->filters['until'] ?? null) {
            $query->where('transaction_elements.created_at', '<=', DateHelper::dayEndUtc($this->filters['until']));
        }
        if ($this->filters['reception_id'] ?? null) {
            $query->whereIn('closing_id', Closing::where('reception_id', $this->filters['reception_id'])->select('id'));
        }
        if ($this->filters['income_or_expense'] ?? null) {
            $query->where('income_or_expense', $this->filters['income_or_expense']);
        }
        if ($this->filters['service_id'] ?? null) {
            $query->where('service_id', $this->filters['service_id']);
        }
        if ($this->filters['doctor_id'] ?? null) {
            $query->where('doctor_id', $this->filters['doctor_id']);
        }

        return $query->orderByDesc('created_at');
    }

    public function headings(): array
    {
        return ['Date', 'TR#', 'Service', 'Department', 'Provider', 'Paid To', 'Type', 'Flow', 'Patient', 'Amount'];
    }

    public function map($row): array
    {
        return [
            DateHelper::pdfFormat($row->created_at, 'd M Y H:i'),
            $row->transaction?->tr_number,
            $row->service?->name,
            $row->service?->department?->name,
            $row->doctor?->name,
            $row->expVoucher?->payedTo?->name,
            $row->type,
            $row->income_or_expense,
            $row->patient?->name,
            number_format((float) $row->amount, 2),
        ];
    }

    public function title(): string
    {
        return 'Services Report';
    }
}
