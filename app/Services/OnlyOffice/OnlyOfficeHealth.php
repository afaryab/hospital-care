<?php

namespace App\Services\OnlyOffice;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Throwable;

/**
 * Whether the OnlyOffice Document Server can be used right now. The app
 * must keep working without it, so callers use this to disable document
 * editing instead of failing.
 */
class OnlyOfficeHealth
{
    public const CACHE_KEY = 'onlyoffice:available';

    public function available(): bool
    {
        if (! config('onlyoffice.enabled')) {
            return false;
        }

        try {
            return Cache::remember(self::CACHE_KEY, config('onlyoffice.health_cache_seconds'), fn (): bool => $this->probe());
        } catch (Throwable) {
            return $this->probe();
        }
    }

    public function forget(): void
    {
        Cache::forget(self::CACHE_KEY);
    }

    private function probe(): bool
    {
        try {
            $response = Http::connectTimeout(2)->timeout(3)
                ->get(rtrim((string) config('onlyoffice.internal_url'), '/').'/healthcheck');

            return $response->successful() && trim($response->body()) === 'true';
        } catch (Throwable) {
            return false;
        }
    }
}
