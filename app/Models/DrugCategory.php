<?php

namespace App\Models;

use App\Concerns\Cacheable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

class DrugCategory extends Model
{
    use Cacheable, HasFactory;

    protected $fillable = ['name', 'description'];

    public function drugs(): HasMany
    {
        return $this->hasMany(Drug::class);
    }

    /**
     * The full drug category list, used across pharmacy forms. Small and
     * rarely changes.
     */
    public static function cachedAll(): Collection
    {
        return static::rememberCache(fn () => static::query()->orderBy('name')->get());
    }
}
