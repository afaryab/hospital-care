<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Panel extends Model
{
    protected $fillable = [
        'name',
        'code',
        'is_active',
    ];
}
