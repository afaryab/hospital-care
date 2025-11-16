<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceOrder extends Model
{
    protected $table = 'service_orders';

    protected $fillable = [
        'type',
        'so_number',
        'so_short',
        'created_by',
        'patient_id',
        'service_id',
        'service_recestation_id',
        'doctor_id',
        'is_composit',
        'notes',
        'notes_json'
    ];

    protected $casts = [
        'notes_json' => 'json',
        'is_composit' => 'boolean'
    ];

    protected $appends = [
        'year',
        'month',
        'number',
        'departmentKey',
        'serviceNumber',
    ];

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
}
