<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Receptionist extends Model
{
    protected $fillable = [
        'user_id',
        'authority',
    ];
}
