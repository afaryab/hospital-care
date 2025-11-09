<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $table = 'services';

    protected $fillable = [
        'name',
        'slug',
        'service_department_id',
        'charges',
        'charges_include_tax',
        'tax_rate',
        'have_service_provider',
        'service_provider_types',
        'is_composit_service',
        'created_by'
    ];

    protected $casts = [
        'have_composit_services' => 'boolean',
        'charges_include_tax' => 'boolean',
        'have_service_provider' => 'boolean',
        'service_provider_types' => 'json',
        'is_composit_service' => 'boolean'
    ];

    public function department()
    {
        return $this->belongsTo(ServiceDepartment::class, 'service_department_id');
    }


}
