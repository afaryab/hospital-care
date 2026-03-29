<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StockCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'parent_id',
        'is_medicine',
    ];

    protected function casts(): array
    {
        return [
            'is_medicine' => 'boolean',
        ];
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(StockCategory::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(StockCategory::class, 'parent_id');
    }

    public function stockItems(): HasMany
    {
        return $this->hasMany(StockItem::class, 'category_id');
    }
}
