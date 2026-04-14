<?php

namespace App\Imports;

use App\Models\BankTransaction;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class BankStatementImport implements ShouldQueue, SkipsEmptyRows, SkipsOnError, ToCollection, WithChunkReading, WithHeadingRow
{
    use Importable, SkipsErrors;

    public function __construct(
        private readonly int $bankAccountId
    ) {}

    public function collection(Collection $rows): void
    {
        foreach ($rows as $row) {
            $referenceNumber = $this->extractReference($row);

            // Skip rows without a reference number to avoid unidentifiable duplicates
            if (empty($referenceNumber)) {
                continue;
            }

            BankTransaction::withoutEvents(function () use ($row, $referenceNumber): void {
                BankTransaction::updateOrCreate(
                    [
                        'bank_account_id' => $this->bankAccountId,
                        'reference_number' => $referenceNumber,
                    ],
                    [
                        'transaction_date' => $this->parseDate($row['date'] ?? $row['transaction_date'] ?? null),
                        'description' => $row['description'] ?? $row['narration'] ?? $row['particulars'] ?? null,
                        'debit' => $this->parseAmount($row['debit'] ?? $row['withdrawal'] ?? null),
                        'credit' => $this->parseAmount($row['credit'] ?? $row['deposit'] ?? null),
                        'balance' => $this->parseAmount($row['balance'] ?? $row['running_balance'] ?? null),
                    ],
                );
            });
        }
    }

    public function chunkSize(): int
    {
        return 500;
    }

    private function extractReference(Collection|array $row): ?string
    {
        $row = is_array($row) ? $row : $row->toArray();
        $candidate = $row['reference'] ?? $row['reference_number'] ?? $row['ref'] ?? $row['txn_id'] ?? $row['transaction_id'] ?? null;

        return $candidate ? trim((string) $candidate) : null;
    }

    private function parseDate(mixed $value): ?string
    {
        if (empty($value)) {
            return null;
        }

        if (is_numeric($value)) {
            // Excel serial date
            return Date::excelToDateTimeObject($value)->format('Y-m-d');
        }

        try {
            return Carbon::parse($value)->format('Y-m-d');
        } catch (\Throwable) {
            return null;
        }
    }

    private function parseAmount(mixed $value): ?string
    {
        if ($value === null || $value === '' || $value === '-') {
            return null;
        }

        // Remove commas and currency symbols
        $cleaned = preg_replace('/[^0-9.\-]/', '', (string) $value);

        return is_numeric($cleaned) ? $cleaned : null;
    }
}
