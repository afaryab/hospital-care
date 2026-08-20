<?php

namespace App\Helpers;

/**
 * Deterministic blind-index hashing for encrypted PII columns (e.g.
 * patients.cnic_hash, patients.contact_hash) so they remain searchable
 * without decrypting every row. Keyed off APP_KEY so the index can't be
 * rebuilt or brute-forced offline from a database dump alone.
 */
class PiiHasher
{
    public static function cnic(string $cnic): string
    {
        return static::hash(strtoupper(trim($cnic)));
    }

    public static function contact(string $contact): string
    {
        return static::hash(preg_replace('/\D+/', '', $contact) ?? '');
    }

    protected static function hash(string $normalized): string
    {
        return hash_hmac('sha256', $normalized, (string) config('app.key'));
    }
}
