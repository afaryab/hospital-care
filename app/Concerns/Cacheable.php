<?php

namespace App\Concerns;

use Illuminate\Support\Facades\Cache;
use Throwable;

/**
 * Gives a model a single well-known cache entry (its "listing" — whatever
 * cachedXxx() the model defines) that is automatically flushed whenever a
 * record of that model is created, updated, or deleted.
 *
 * Deliberately not built on Cache::tags(): tags require a taggable store
 * (redis/memcached/array) and throw on anything else, so a model using them
 * would hard-fail on save if the cache store were ever misconfigured. A
 * single plain key works identically on every driver and degrades to "just
 * query the database" if the cache backend is unavailable, which matters
 * here — a Redis blip must never block staff from saving a record.
 */
trait Cacheable
{
    protected static function bootCacheable(): void
    {
        static::saved(fn () => static::flushCache());
        static::deleted(fn () => static::flushCache());
    }

    /**
     * The single cache key this model's cached listing lives under. Override
     * per model only if the table name isn't a good fit.
     */
    public static function cacheKey(): string
    {
        return 'model-cache:'.(new static)->getTable();
    }

    /**
     * How long the cached listing lives before it expires on its own, as a
     * backstop in case a write ever bypasses Eloquent (e.g. a raw query).
     */
    public static function cacheTtl(): int
    {
        return 3600;
    }

    /**
     * Human-readable label for the cache settings page.
     */
    public static function cacheLabel(): string
    {
        return str(class_basename(static::class))->headline();
    }

    protected static function rememberCache(\Closure $callback): mixed
    {
        try {
            return Cache::remember(static::cacheKey(), static::cacheTtl(), $callback);
        } catch (Throwable $e) {
            report($e);

            return $callback();
        }
    }

    public static function flushCache(): void
    {
        try {
            Cache::forget(static::cacheKey());
        } catch (Throwable $e) {
            report($e);
        }
    }
}
