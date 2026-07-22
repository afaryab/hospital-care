<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceRecestation extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'service_department_id',
        'service_id',
        'charges',
        'charges_include_tax',
        'tax_rate',
        'have_service_provider',
        'service_provider_types',
        'color',
        'created_by',
    ];

    protected $casts = [
        'charges_include_tax' => 'boolean',
        'have_service_provider' => 'boolean',
        'service_provider_types' => 'json',
    ];

    public function department(): BelongsTo
    {
        return $this->belongsTo(ServiceDepartment::class, 'service_department_id');
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
