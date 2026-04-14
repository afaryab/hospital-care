<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PaymentMethod extends Model
{
    use HasFactory;

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
}
