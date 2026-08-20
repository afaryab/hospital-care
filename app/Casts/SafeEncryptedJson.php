<?php

namespace App\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

/**
 * JSON-aware sibling of SafeEncrypted, for array/JSON-cast columns that
 * need the same at-rest encryption with a graceful fallback for legacy
 * plain-JSON rows written before encryption was added.
 */
class SafeEncryptedJson implements CastsAttributes
{
    public function get(Model $model, string $key, mixed $value, array $attributes): ?array
    {
        if ($value === null) {
            return null;
        }

        try {
            $value = Crypt::decryptString($value);
        } catch (DecryptException) {
            // Old data stored as plain JSON — fall through and decode as-is.
        }

        $decoded = json_decode($value, true);

        return is_array($decoded) ? $decoded : null;
    }

    public function set(Model $model, string $key, mixed $value, array $attributes): ?string
    {
        if ($value === null) {
            return null;
        }

        return Crypt::encryptString(json_encode($value));
    }
}
