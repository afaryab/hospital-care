<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DrugCategory extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description'];

    public function drugs(): HasMany
    {
        return $this->hasMany(Drug::class);
    }
}
