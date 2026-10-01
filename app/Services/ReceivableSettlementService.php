<?php

namespace App\Services;

use App\Models\Receaveable;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * Records a payment against a receivable: one INCOME Transaction linked via
 * receaveable_id, and the receivable's remaining balance and status updated.
 * Revenue was already recognised on the originating transaction, so no
 * TransactionElement is created here.
 */
class ReceivableSettlementService
{
    /**
     * @param  array<string, mixed>  $attributes  Extra Transaction columns (closing_id, type, panel_id, payment method, ...).
     */
    public function settle(Receaveable $receivable, float $amount, array $attributes = []): Transaction
    {
        return DB::transaction(function () use ($receivable, $amount, $attributes): Transaction {
            $receivable = Receaveable::query()->lockForUpdate()->findOrFail($receivable->id);

            $this->ensureSettleable($receivable, $amount);

            $transaction = Transaction::create([
                ...$attributes,
                'patient_id' => $receivable->patient_id,
                'income_or_expense' => 'INCOME',
                'amount' => $amount,
                'receaveable_id' => $receivable->id,
            ]);

            $remaining = round((float) $receivable->amount - $amount, 2);

            $receivable->update([
                'amount' => max($remaining, 0),
                'status' => $remaining <= 0 ? 'paid' : 'unpaid',
            ]);

            return $transaction;
        });
    }

    /**
     * Settle several receivables of one panel from a single payment.
     *
     * @param  array<int, array{receaveable_id: int|string, amount: float|int|string}>  $allocations
     * @param  array<string, mixed>  $attributes
     * @return array<int, Transaction>
     */
    public function settleForPanel(int $panelId, array $allocations, array $attributes = []): array
    {
        $ids = array_map(fn (array $allocation): int => (int) $allocation['receaveable_id'], $allocations);

        if (count($ids) !== count(array_unique($ids))) {
            throw ValidationException::withMessages(['allocations' => 'Each receivable can only be allocated once.']);
        }

        return DB::transaction(function () use ($panelId, $allocations, $attributes): array {
            return array_map(function (array $allocation) use ($panelId, $attributes): Transaction {
                $receivable = Receaveable::query()->findOrFail($allocation['receaveable_id']);

                if ((int) $receivable->panel_id !== $panelId) {
                    throw ValidationException::withMessages(['allocations' => "Receivable #{$receivable->id} does not belong to the selected panel."]);
                }

                return $this->settle($receivable, (float) $allocation['amount'], [...$attributes, 'panel_id' => $panelId]);
            }, $allocations);
        });
    }

    private function ensureSettleable(Receaveable $receivable, float $amount): void
    {
        $status = strtolower((string) $receivable->status);

        if (in_array($status, ['paid', 'cancelled', 'draft'], true)) {
            throw ValidationException::withMessages(['amount' => "Receivable #{$receivable->id} is {$status} and cannot take a payment."]);
        }

        if ($amount <= 0) {
            throw ValidationException::withMessages(['amount' => 'The payment amount must be greater than zero.']);
        }

        if ($amount - (float) $receivable->amount > 0.009) {
            throw ValidationException::withMessages(['amount' => "Receivable #{$receivable->id} only has ".number_format((float) $receivable->amount, 2).' outstanding.']);
        }
    }
}
