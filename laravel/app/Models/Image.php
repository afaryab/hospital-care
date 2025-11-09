<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    protected $fillable = [
        'owner_id',
        'path',
        'file_type',
        'file_size',
    ];
}
