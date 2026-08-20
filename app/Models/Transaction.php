<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class Transaction extends Model
{
    use HasFactory, LogsActivity, SoftDeletes;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontLogEmptyChanges();
    }

    protected $fillable = [
        'id',
        'tr_number',
        'old_id',
        'closing_id',
        'created_by',
        'patient_id',
        'panel_id',
        'receaveable_id',
        'type',
        'payment_method_id',
        'payable_type',
        'payable_id',
        'reference_number',
        'income_or_expense',
        'amount',
        'amount_alphabetical',
        'orignal_amount',
        'customer_payed',
        'change',
        'edited_amount',
        'is_refunded',
        'expense_category_id',
        'exp_voucher_id',
        'notes',
        'created_at',
        'updated_at',
    ];

    protected $appends = [
        'year',
        'month',
        'day',
        'number',
    ];

    protected function casts(): array
    {
        return [
            'is_refunded' => 'boolean',
        ];
    }

    public function getYearAttribute()
    {
        return $this->tr_number_parts['year'] ?? null;
    }

    public function getMonthAttribute()
    {
        return $this->tr_number_parts['month'] ?? null;
    }

    public function getDayAttribute()
    {
        return $this->tr_number_parts['day'] ?? null;
    }

    public function getNumberAttribute()
    {
        return $this->tr_number_parts['number'] ?? null;
    }

    public function getTrNumberPartsAttribute()
    {
        if (empty($this->tr_number)) {
            return null;
        }

        $parts = explode('/', $this->tr_number);

        return [
            'year' => $parts[1] ?? null,
            'month' => $parts[2] ?? null,
            'day' => $parts[3] ?? null,
            'number' => $parts[4] ?? null,
        ];
    }

    public function updateCounter(): void
    {
        $closing = $this->closing;
        if ($closing) {
            $totalIncAmount = $closing->transactions()->where('income_or_expense', 'INCOME')->sum('amount');
            $totalExpAmount = $closing->transactions()->where('income_or_expense', 'EXPENSE')->sum('amount');
            $closing->closing_amount = $totalIncAmount - $totalExpAmount;
            $closing->expense_payed = $totalExpAmount;
            $closing->saveQuietly();
        }
    }

    public function elements()
    {
        return $this->hasMany(TransactionElement::class);
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function receaveable()
    {
        return $this->hasOne(Receaveable::class, 'transaction_id');
    }

    /**
     * The receivable this transaction settles, when this transaction is a
     * receivable collection payment (see WebController::receaveablesPayment()).
     * The inverse of receaveable() — that one is the receivable this
     * transaction originated, this one is the receivable this transaction
     * pays down.
     */
    public function settledReceaveable()
    {
        return $this->belongsTo(Receaveable::class, 'receaveable_id');
    }

    public function closing()
    {
        return $this->belongsTo(Closing::class, 'closing_id');
    }

    public function panel()
    {
        return $this->belongsTo(Panel::class, 'panel_id');
    }

    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    public function expenseCategory()
    {
        return $this->belongsTo(ExpenseCategory::class);
    }

    public function payable()
    {
        return $this->morphTo();
    }

    public function refundBy(User $user): void
    {
        if ($this->is_refunded) {
            throw ValidationException::withMessages([
                'transaction' => 'Transaction is already refunded.',
            ]);
        }

        DB::transaction(function () use ($user): void {
            $this->update([
                'is_refunded' => true,
            ]);

            if ($this->receaveable) {
                $this->receaveable->update([
                    'status' => 'cancelled',
                    'amount' => 0,
                ]);
            }

            TransactionElement::create([
                'closing_id' => $this->closing_id,
                'transaction_id' => $this->id,
                'created_by' => $user->id,
                'patient_id' => $this->patient_id,
                'type' => 'REFUND',
                'income_or_expense' => 'EXPENSE',
                'amount' => $this->amount,
                'orignal_amount' => $this->orignal_amount ?? $this->amount,
                'refunded_transaction_id' => $this->id,
                'notes' => 'Refund generated by admin action.',
            ]);
        });
    }

    /**
     * Recalculate the amount recognized as revenue, the change owed to the
     * customer, and any outstanding receivable, from the current line items
     * (elements) and the customer_payed figure already on the record.
     *
     * Mirrors the creation-time math in WebController@patientPayment: the
     * total billed is the net of INCOME minus EXPENSE elements; whatever the
     * customer paid above that is change, whatever is short becomes (or
     * updates) a receivable. Not applicable to transactions that are
     * themselves a payment against an existing receivable — see
     * applyCollectedAmountDelta() for that case.
     */
    public function recalculatePayment(): void
    {
        if ($this->receaveable_id) {
            return;
        }

        $incomeSum = (float) $this->elements()->where('income_or_expense', 'INCOME')->sum('amount');
        $expenseSum = (float) $this->elements()->where('income_or_expense', 'EXPENSE')->sum('amount');
        $netTotal = $incomeSum - $expenseSum;

        $totalOrignal = (float) $this->elements()->sum('orignal_amount');

        if ($this->income_or_expense !== 'INCOME') {
            // Expense-dominant transactions net negative (expense elements
            // outweigh income ones) — abs() restores the original "resync
            // from elements" behaviour rather than clamping to zero.
            $this->updateQuietly([
                'amount' => abs($netTotal),
                'orignal_amount' => $totalOrignal,
            ]);

            return;
        }

        $totalBilled = max($netTotal, 0);
        $customerPayed = (float) $this->customer_payed;

        if ($customerPayed >= $totalBilled) {
            $recognized = $totalBilled;
            $change = $customerPayed - $totalBilled;
            $shortfall = 0.0;
        } else {
            $recognized = $customerPayed;
            $change = 0.0;
            $shortfall = $totalBilled - $customerPayed;
        }

        $this->updateQuietly([
            'amount' => $recognized,
            'change' => $change,
            'orignal_amount' => $totalOrignal,
        ]);

        $this->syncReceivable($shortfall);
    }

    /**
     * Create, update, or settle the receivable this transaction originated
     * (the inverse of settledReceaveable — see receaveable()), preserving
     * whatever has already been collected against it.
     */
    protected function syncReceivable(float $shortfall): void
    {
        if (! $this->patient_id) {
            return;
        }

        $receivable = $this->receaveable()->first();

        if ($shortfall <= 0) {
            if ($receivable && strtolower((string) $receivable->status) !== 'cancelled') {
                $receivable->update(['amount' => 0, 'status' => 'paid']);
            }

            return;
        }

        if ($receivable) {
            if (strtolower((string) $receivable->status) === 'cancelled') {
                return;
            }

            $collected = max(($receivable->orignal_amount ?? $receivable->amount) - $receivable->amount, 0);
            $outstanding = max($shortfall - $collected, 0);

            $receivable->update([
                'orignal_amount' => $shortfall,
                'amount' => $outstanding,
                'status' => $outstanding <= 0 ? 'paid' : 'unpaid',
            ]);

            return;
        }

        Receaveable::create([
            'patient_id' => $this->patient_id,
            'panel_id' => $this->panel_id,
            'transaction_id' => $this->id,
            'amount' => $shortfall,
            'orignal_amount' => $shortfall,
            'status' => 'unpaid',
        ]);
    }

    /**
     * When this transaction is itself a payment collected against a prior
     * receivable (receaveable_id set), apply the change in the collected
     * amount to that receivable's remaining balance.
     */
    public function applyCollectedAmountDelta(float $delta): void
    {
        if (! $this->receaveable_id || abs($delta) < 0.01) {
            return;
        }

        $receivable = $this->settledReceaveable;

        if (! $receivable || strtolower((string) $receivable->status) === 'cancelled') {
            return;
        }

        $outstanding = max($receivable->amount - $delta, 0);

        $receivable->update([
            'amount' => $outstanding,
            'status' => $outstanding <= 0 ? 'paid' : 'unpaid',
        ]);
    }

    public static function generateTransactionNumber(): string
    {
        return DB::transaction(function () {
            $now = Carbon::now();
            $year = $now->format('Y');
            $month = $now->format('m');
            $day = $now->format('d');
            $prefix = "TR/{$year}/{$month}/{$day}/";

            $existingNumbers = self::where('tr_number', 'like', "{$prefix}%")
                ->lockForUpdate()
                ->pluck('tr_number');

            $maxSequence = 0;
            foreach ($existingNumbers as $trNumber) {
                $parts = explode('/', (string) $trNumber);
                $sequence = (int) ($parts[4] ?? 0);
                if ($sequence > $maxSequence) {
                    $maxSequence = $sequence;
                }
            }

            $nextSequence = $maxSequence + 1;
            $count = str_pad((string) $nextSequence, 4, '0', STR_PAD_LEFT);

            return "{$prefix}{$count}";
        });
    }
}
