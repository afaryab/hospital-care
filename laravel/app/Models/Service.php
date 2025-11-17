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

    protected $attributes = [
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
        if(!$this->have_service_provider || empty($this->service_provider_types)) {
            return collect();
        }

        $users = collect();
        
        foreach($this->service_provider_types as $providerType) {
            switch($providerType) {
                case OpdDoctor::class:
                    $users = $users->merge(User::whereHas('opdDoctorProfiles')->get());
                    break;
                case IndDoctor::class:
                    $users = $users->merge(User::whereHas('indDoctorProfiles')->get());
                    break;
                case EmergencyDoctor::class:
                    $users = $users->merge(User::whereHas('emergencyDoctorProfiles')->get());
                    break;
                case Dentist::class:
                    $users = $users->merge(User::whereHas('dentistProfiles')->get());
                    break;
                case UltrasoundDoctor::class:
                    $users = $users->merge(User::whereHas('ultrasoundDoctorProfiles')->get());
                    break;
            }
        }
        
        return $users->unique('id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }


}
