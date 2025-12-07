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

    // Auto-append computed attributes (accessors) when serializing
    protected $appends = [
        'available_providers'
    ];

    public function department()
    {
        return $this->belongsTo(ServiceDepartment::class, 'service_department_id');
    }

    public function getOpdDoctorsAttribute()
    {
        // Return collection of users who have OPD doctor profiles
        // when this service allows OPD doctors as providers
        if($this->have_service_provider && in_array(OpdDoctor::class, $this->service_provider_types ?? [])){
            return User::whereHas('opdDoctorProfiles')->get();
        }

        
        return collect(); // Return empty collection if not applicable
    }

    /**
     * Get all users who have OPD profiles (can provide OPD services)
     * This method can be used for querying and filtering
     */
    public function opdDoctors()
    {
        if($this->have_service_provider && in_array(OpdDoctor::class, $this->service_provider_types ?? [])){
            return User::whereHas('opdDoctorProfiles');
        }
        
        return User::whereRaw('1=0'); // Return empty query if not applicable
    }

    /**
     * Get all users who can provide this service based on service provider types
     */
    public function getAvailableProvidersAttribute()
    {
        $userIds = collect();

        foreach ($this->service_provider_types ?? [] as $providerType) {
            // Each providerType is expected to be a model class with a user_id field
            $userIds = $userIds->merge($providerType::query()->pluck('user_id'));
        }

        // Return a collection of unique user IDs; callers can map/load as needed
        return $userIds->toArray();
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }


}
