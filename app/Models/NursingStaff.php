<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NursingStaff extends Model
{
    protected $fillable = [
        'user_id',
        'authority'
    ];
}
