<?php

namespace App\Models;

use App\Concerns\Cacheable;
use App\Enum\DepreciationMethod;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

class AssetCategory extends Model
{
    use Cacheable, HasFactory;

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

    /**
     * The full asset category list, used across asset forms. Small and
     * rarely changes.
     */
    public static function cachedAll(): Collection
    {
        return static::rememberCache(fn () => static::query()->orderBy('name')->get());
    }
}
