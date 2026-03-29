<?php

namespace App\Models;

use App\Enum\DepreciationMethod;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AssetCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'depreciation_method',
        'useful_life_years',
    ];

    protected function casts(): array
    {
        return [
            'depreciation_method' => DepreciationMethod::class,
        ];
    }

    public function assets(): HasMany
    {
        return $this->hasMany(Asset::class, 'category_id');
    }
}
