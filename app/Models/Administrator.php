<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Administrator extends Model
{
    protected $fillable = [
        'user_id',
        'authority',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
