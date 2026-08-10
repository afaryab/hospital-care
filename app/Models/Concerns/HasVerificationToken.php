<?php

namespace App\Models\Concerns;

use Illuminate\Support\Str;

trait HasVerificationToken
{
    protected static function bootHasVerificationToken(): void
    {
        static::creating(function ($model): void {
            if (empty($model->verification_token)) {
                $model->verification_token = static::generateVerificationToken();
            }
        });
    }

    public static function generateVerificationToken(): string
    {
        do {
            $token = Str::random(40);
        } while (static::withTrashed()->where('verification_token', $token)->exists());

        return $token;
    }
}
