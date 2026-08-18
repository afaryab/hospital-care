<?php

namespace App\Models;

use App\Concerns\Cacheable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

class PaymentMethod extends Model
{
    use Cacheable, HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'id_required',
        'payables',
    ];

    protected function casts(): array
    {
        return [
            'id_required' => 'boolean',
        ];
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function transactionElements(): HasMany
    {
        return $this->hasMany(TransactionElement::class);
    }

    public function requiresPayable(): bool
    {
        return $this->payables !== null;
    }

    public function getPayableModelClass(): ?string
    {
        return match ($this->payables) {
            'bank_account' => BankAccount::class,
            'panel' => Panel::class,
            default => null,
        };
    }

    /**
     * The full payment methods list, used across counter and transaction
     * forms. Small and rarely changes.
     */
    public static function cachedAll(): Collection
    {
        return static::rememberCache(fn () => static::query()->orderBy('name')->get());
    }
}
