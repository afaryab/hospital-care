<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceRecestation extends Model
{

    protected $fillable = [
        'name',
        'slug',
        'service_department_id',
        'charges',
        'charges_include_tax',
        'tax_rate',
        'have_service_provider',
        'service_provider_types',
        'created_by'
    ];

    protected $casts = [
        'have_composit_services' => 'boolean',
        'charges_include_tax' => 'boolean',
        'have_service_provider' => 'boolean',
        'service_provider_types' => 'json'
    ];


}
