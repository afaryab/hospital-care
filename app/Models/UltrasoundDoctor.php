<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UltrasoundDoctor extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'authority',
        'pmdc_number',
    ];
}
