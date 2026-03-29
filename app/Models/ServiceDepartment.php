<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceDepartment extends Model
{
    use HasFactory;

    protected $table = 'service_departments';

    protected $fillable = [
        'name',
        'slug',
        'image',
        'have_composit_services',
    ];

    protected $casts = [
        'have_composit_services' => 'boolean',
    ];

    public function services()
    {
        return $this->hasMany(Service::class, 'service_department_id');
    }
}
