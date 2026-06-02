<?php

namespace App\Exports;

use App\Helpers\DateHelper;
use App\Models\ServiceOrder;
use Illuminate\Contracts\Support\Responsable;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;

class ServicePerformanceReportExport implements FromQuery, Responsable, ShouldAutoSize, WithHeadings, WithMapping, WithTitle
{
    use Exportable;

    public string $fileName = 'service-performance-report.xlsx';

    public function __construct(private readonly array $filters = []) {}

    public function query()
    {
        return ServiceOrder::query()
            ->withSum(['transactionElements as income_total' => fn ($q) => $q->where('income_or_expense', 'INCOME')], 'amount')
            ->withSum(['expenseVouchers as voucher_total'], 'amount')
            ->with(['patient', 'service', 'doctor'])
            ->when($this->filters['from'] ?? null, fn ($q, $date) => $q->where('service_orders.created_at', '>=', DateHelper::dayStartUtc($date)))
            ->when($this->filters['until'] ?? null, fn ($q, $date) => $q->where('service_orders.created_at', '<=', DateHelper::dayEndUtc($date)))
            ->when($this->filters['type'] ?? null, fn ($q, $type) => $q->where('type', $type))
            ->when($this->filters['status'] ?? null, fn ($q, $status) => $q->where('status', $status))
            ->when($this->filters['service_id'] ?? null, fn ($q, $id) => $q->where('service_id', $id))
            ->when($this->filters['doctor_id'] ?? null, fn ($q, $id) => $q->where('doctor_id', $id))
            ->orderByDesc('created_at');
    }

    public function headings(): array
    {
        return ['Date', 'SO Number', 'Patient', 'Service', 'Provider', 'Department', 'Status', 'Income Collected', 'Provider Expenses'];
    }

    public function map($row): array
    {
        return [
            DateHelper::pdfFormat($row->created_at, 'd M Y'),
            $row->so_number,
            $row->patient?->name,
            $row->service?->name,
            $row->doctor?->name,
            $row->type,
            $row->status,
            number_format((float) ($row->income_total ?? 0), 2),
            number_format((float) ($row->voucher_total ?? 0), 2),
        ];
    }

    public function title(): string
    {
        return 'Service Performance Report';
    }
}
