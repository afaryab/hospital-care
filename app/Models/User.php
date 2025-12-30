<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;

class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'id',
        'name',
        'email',
        'password',
        'password_expired_at',
        'last_login',
        'last_activity',
        'last_login_attempt',
        'ip_address',
        'login_attempts',
        'profile_img_path',
        'profile_img_id',
        'is_active',
        'banned_message',
        'created_at',
        'updated_at'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'two_factor_confirmed_at' => 'datetime',
        ];
    }
    
    /**
     * Append the profiles attribute to the model's array form.
     *
     * @var array
     */
    protected $appends = ['profiles'];

    public function getProfilesAttribute(){

        return [
            'admin' => $this->adminProfiles,
            'accountant' => $this->accountantProfiles,
            'receptionist' => $this->receptionistProfiles,
            'opd_doctor' => $this->opdDoctorProfiles,
            'ind_doctor' => $this->indDoctorProfiles,
            'emergency_doctor' => $this->emergencyDoctorProfiles,
            'dentist' => $this->dentistProfiles,
            'ultrasound_doctor' => $this->ultrasoundDoctorProfiles,
            'xray_technician' => $this->xrayTechnicianProfiles,
            'nursing_staff' => $this->nursingStaffProfiles,
            'patient_manager' => $this->patientManagerProfiles,
        ];
    }

    public function adminProfiles(){

        return $this->hasMany(Administrator::class);
    }

    public function accountantProfiles(){

        return $this->hasMany(Accountant::class);
    }

    public function receptionistProfiles(){

        return $this->hasMany(Receptionist::class);
    }

    public function opdDoctorProfiles(){

        return $this->hasMany(OpdDoctor::class);
    }

    public function indDoctorProfiles(){

        return $this->hasMany(IndDoctor::class);
    }

    public function emergencyDoctorProfiles(){

        return $this->hasMany(EmergencyDoctor::class);
    }

    public function dentistProfiles(){

        return $this->hasMany(Dentist::class);
    }

    public function ultrasoundDoctorProfiles(){

        return $this->hasMany(UltrasoundDoctor::class);
    }

    public function xrayTechnicianProfiles(){

        return $this->hasMany(XrayTechnician::class);
    }

    public function nursingStaffProfiles(){

        return $this->hasMany(NursingStaff::class);
    }

    public function patientManagerProfiles(){

        return $this->hasMany(PatientManager::class);
    }

    /**
     * Determine if the user can access the given Filament panel.
     */
    public function canAccessPanel(Panel $panel): bool
    {
        // Check panel access based on panel ID and user profiles
        switch ($panel->getId()) {
            case 'admin':
                return $this->adminProfiles()->exists();
            case 'accounts':
                return $this->accountantProfiles()->exists();
            default:
                return false; // Deny access to unknown panels
        }
    }
}
