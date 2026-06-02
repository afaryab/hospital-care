<?php

namespace App\Exports;

use App\Helpers\DateHelper;
use App\Models\Receaveable;
use Illuminate\Contracts\Support\Responsable;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;

class ReceivablesReportExport implements FromQuery, Responsable, ShouldAutoSize, WithHeadings, WithMapping, WithTitle
{
    use Exportable;

    public string $fileName = 'receivables-report.xlsx';

    public function __construct(private readonly array $filters = []) {}

    public function query()
    {
        $query = Receaveable::query()->with(['transaction', 'patient', 'panel']);

        if ($this->filters['from'] ?? null) {
            $query->where('receaveables.created_at', '>=', DateHelper::dayStartUtc($this->filters['from']));
        }
        if ($this->filters['until'] ?? null) {
            $query->where('receaveables.created_at', '<=', DateHelper::dayEndUtc($this->filters['until']));
        }
        if ($this->filters['status'] ?? null) {
            $query->where('status', $this->filters['status']);
        }
        if ($this->filters['panel_id'] ?? null) {
            $query->where('panel_id', $this->filters['panel_id']);
        }

        return $query->orderByDesc('created_at');
    }

    public function headings(): array
    {
        return ['Date', 'TR#', 'Patient', 'Panel', 'Original Amount', 'Remaining Amount', 'Due Date', 'Status'];
    }

    public function map($row): array
    {
        return [
            DateHelper::pdfFormat($row->created_at, 'd M Y'),
            $row->transaction?->tr_number,
            $row->patient?->name,
            $row->panel?->name,
            number_format((float) $row->orignal_amount, 2),
            number_format((float) $row->amount, 2),
            DateHelper::pdfFormat($row->due_date, 'd M Y'),
            $row->status,
        ];
    }

    public function title(): string
    {
        return 'Receivables Report';
    }
}
