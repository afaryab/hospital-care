<?php

namespace App\Models;

use App\Concerns\Cacheable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Collection;

class Panel extends Model
{
    use Cacheable, HasFactory;

    protected $fillable = [
        'name',
        'code',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function receaveables(): HasMany
    {
        return $this->hasMany(Receaveable::class, 'panel_id');
    }

    public function panelCheques(): HasMany
    {
        return $this->hasMany(PanelCheque::class);
    }

    public function transactions(): MorphMany
    {
        return $this->morphMany(Transaction::class, 'payable');
    }

    public function transactionElements(): MorphMany
    {
        return $this->morphMany(TransactionElement::class, 'payable');
    }

    /**
     * Active insurance panels, used across transaction, receivable, and
     * cheque forms. Small and rarely changes.
     */
    public static function cachedActive(): Collection
    {
        return static::rememberCache(fn () => static::query()->where('is_active', true)->orderBy('name')->get());
    }
}
