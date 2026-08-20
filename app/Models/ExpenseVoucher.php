<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class ExpenseVoucher extends Model
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
        'vc_number',
        'old_id',
        'exp_category_id',
        'service_order_id',
        'payed_to',
        'payed_to_name',
        'amount',
        'edited_amount',
        'notes',
        'transaction_id',
        'transaction_element_id',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    protected $appends = ['status'];

    public function getStatusAttribute(): string
    {
        return ($this->transaction_id && $this->transaction_element_id) ? 'payed' : 'pending';
    }

    protected static function booted(): void
    {
        static::creating(function (self $voucher): void {
            if (blank($voucher->vc_number)) {
                $voucher->vc_number = self::generateExpenseVoucherNumber();
            }
        });
    }

    /**
     * Generate a unique expense voucher number.
     */
    public static function generateExpenseVoucherNumber(): string
    {
        return DB::transaction(function () {
            $now = Carbon::now();
            $year = $now->format('Y');
            $month = $now->format('m');

            $latestVoucher = self::query()
                ->where('vc_number', 'like', "VC/{$year}/{$month}/%")
                ->lockForUpdate()
                ->latest('id')
                ->first();

            $nextSequence = 1;

            if ($latestVoucher?->vc_number) {
                $parts = explode('/', $latestVoucher->vc_number);
                $lastSequence = (int) ($parts[3] ?? 0);
                $nextSequence = $lastSequence + 1;
            }

            $count = str_pad((string) $nextSequence, 4, '0', STR_PAD_LEFT);

            return "VC/{$year}/{$month}/{$count}";
        });
    }

    public function expCategory(): BelongsTo
    {
        return $this->belongsTo(ExpenseCategory::class, 'exp_category_id');
    }

    public function serviceOrder(): BelongsTo
    {
        return $this->belongsTo(ServiceOrder::class, 'service_order_id');
    }

    public function payedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'payed_to');
    }

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }

    public function transactionElement(): BelongsTo
    {
        return $this->belongsTo(TransactionElement::class);
    }

    public function serviceOrders(): BelongsToMany
    {
        return $this->belongsToMany(ServiceOrder::class, 'expense_voucher_service_order')
            ->withTimestamps();
    }
}
