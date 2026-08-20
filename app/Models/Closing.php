<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Processton\Abacus\Models\AbacusIncoming;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class Closing extends Model
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
        'old_id',
        'reception_id',
        'receptionist_id',
        'ct_number',
        'status',
        'opening_amount',
        'closing_amount',
        'closing_amount_cash',
        'closing_amount_cheque',
        'closing_amount_card',
        'expense_payed',
        'amount_received',
        'closed_at',
        'cash_recieving_time',
        'reported_by',
        'created_at',
        'updated_at',
    ];

    protected $appends = [
        'year',
        'month',
        'number',
    ];

    protected $casts = [
    ];

    public function getYearAttribute()
    {
        return $this->ct_number_parts['year'] ?? null;
    }

    public function getMonthAttribute()
    {
        return $this->ct_number_parts['month'] ?? null;
    }

    public function getNumberAttribute()
    {
        return $this->ct_number_parts['number'] ?? null;
    }

    public function getCtNumberPartsAttribute()
    {
        if (empty($this->ct_number)) {
            return null;
        }

        $parts = explode('/', $this->ct_number);

        return [
            'year' => $parts[1] ?? null,
            'month' => $parts[2] ?? null,
            'number' => $parts[3] ?? null,
        ];
    }

    public static function generateCounterNumber(): string
    {
        return DB::transaction(function () {
            $now = Carbon::now();
            $year = $now->format('Y');
            $month = $now->format('m');
            $prefix = "CT/{$year}/{$month}/";

            $existingNumbers = self::where('ct_number', 'like', "{$prefix}%")
                ->lockForUpdate()
                ->pluck('ct_number');

            $maxSequence = 0;
            foreach ($existingNumbers as $ctNumber) {
                $parts = explode('/', (string) $ctNumber);
                $sequence = (int) ($parts[3] ?? 0);
                if ($sequence > $maxSequence) {
                    $maxSequence = $sequence;
                }
            }

            $nextSequence = $maxSequence + 1;
            $count = str_pad((string) $nextSequence, 4, '0', STR_PAD_LEFT);

            return "{$prefix}{$count}";
        });
    }

    public function reception()
    {
        return $this->belongsTo(Reception::class);
    }

    public function receptionist()
    {
        return $this->belongsTo(User::class, 'receptionist_id');
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'closing_id')->latest();
    }

    public function reportedBy()
    {
        return $this->belongsTo(User::class, 'reported_by');
    }

    public function abacusIncoming(): MorphOne
    {
        return $this->morphOne(AbacusIncoming::class, 'source');
    }
}
