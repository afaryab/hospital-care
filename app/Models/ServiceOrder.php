<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class ServiceOrder extends Model
{
    use HasFactory, LogsActivity, SoftDeletes;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontLogEmptyChanges();
    }

    protected $table = 'service_orders';

    protected $fillable = [
        'type',
        'token',
        'so_number',
        'so_short',
        'created_by',
        'patient_id',
        'service_id',
        'service_recestation_id',
        'doctor_id',
        'is_composit',
        'notes',
        'notes_json',
        'payee_type',
        'payee_id',
    ];

    protected $casts = [
        'notes_json' => 'encrypted:json',
        'is_composit' => 'boolean',
    ];

    protected $appends = [
        'year',
        'month',
        'number',
        'departmentKey',
        'serviceNumber',
    ];

    protected static function booted(): void
    {
        static::updating(function (ServiceOrder $serviceOrder): void {
            ServiceOrderVersion::query()->create([
                'service_order_id' => $serviceOrder->id,
                'snapshot' => $serviceOrder->getOriginal(),
                'change_reason' => 'record_update',
                'changed_by' => auth()->id(),
                'changed_at' => now(),
            ]);
        });

        static::deleting(function (ServiceOrder $serviceOrder): void {
            if ($serviceOrder->isForceDeleting()) {
                throw new \RuntimeException('Hard delete is not allowed for service order records.');
            }
        });
    }

    public function getYearAttribute()
    {
        return $this->so_number_parts['year'] ?? null;
    }

    public function getMonthAttribute()
    {
        return $this->so_number_parts['month'] ?? null;
    }

    public function getNumberAttribute()
    {
        return $this->so_number_parts['number'] ?? null;
    }

    public function getDepartmentKeyAttribute()
    {
        return $this->so_number_parts['departmentKey'] ?? null;
    }

    public function getServiceNumberAttribute()
    {
        return $this->so_number_parts['serviceNumber'] ?? null;
    }

    public function getSoNumberPartsAttribute()
    {
        if (empty($this->so_number)) {
            return null;
        }

        $parts = explode('/', $this->so_number);

        return [
            'year' => $parts[1] ?? null,
            'month' => $parts[2] ?? null,
            'number' => $parts[3] ?? null,
            'departmentKey' => $parts[4] ?? null,
            'serviceNumber' => $parts[5] ?? null,
        ];
    }

    /**
     * Get the user who created the service order.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the patient associated with the service order.
     */
    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    /**
     * Get the service associated with the service order.
     */
    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    /**
     * Get the doctor assigned to the service order.
     */
    public function doctor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    /**
     * Get the payee of the service order (could be a patient, insurance company, etc.)
     */
    public function payee()
    {
        return $this->morphTo();
    }

    /**
     * Generate a unique service order number
     */
    public static function generateServiceOrderNumber($type): string
    {
        return DB::transaction(function () use ($type) {
            $count = ServiceOrder::where('type', $type)
                ->where('created_at', '>=', Carbon::now()->startOfMonth())
                ->where('created_at', '<=', Carbon::now()->endOfMonth())
                ->lockForUpdate()
                ->count();

            $count += 1;

            $count = str_pad($count, 8, '0', STR_PAD_LEFT);

            return $count;
        });
    }

    public static function generateShortServiceOrderNumber($type): string
    {
        return DB::transaction(function () use ($type) {
            $count = ServiceOrder::where('type', $type)
                ->lockForUpdate()
                ->count();

            $count += 1;

            $count = str_pad($count, 8, '0', STR_PAD_LEFT);

            return $count;
        });
    }

    public function expenseVouchers(): BelongsToMany
    {
        return $this->belongsToMany(ExpenseVoucher::class, 'expense_voucher_service_order')
            ->withTimestamps();
    }

    public function transactionElements(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(TransactionElement::class, 'service_order_id');
    }

    public function versions(): HasMany
    {
        return $this->hasMany(ServiceOrderVersion::class)->latest('changed_at');
    }
}
