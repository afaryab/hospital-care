<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PatientManager extends Model
{
    protected $fillable = [
        'user_id',
        'patient_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }
}
