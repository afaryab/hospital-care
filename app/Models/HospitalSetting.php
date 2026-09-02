<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class HospitalSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
    ];

    public static function get(string $key, mixed $default = null): mixed
    {
        return static::where('key', $key)->value('value') ?? $default;
    }

    public static function set(string $key, mixed $value): void
    {
        static::updateOrCreate(['key' => $key], ['value' => $value]);
    }

    /**
     * The hospital's own uploaded logo, resolved to a renderable URL — null
     * when the admin hasn't uploaded one, so callers fall back to the
     * default application branding.
     */
    public static function logoUrl(): ?string
    {
        $path = static::get('hospital_logo');

        if (empty($path)) {
            return null;
        }

        if (Str::startsWith($path, ['http://', 'https://'])) {
            return $path;
        }

        return Storage::disk('public')->url($path);
    }

    public static function name(): string
    {
        return static::get('hospital_name') ?: config('app.name');
    }

    /**
     * The hospital's logo as a base64 data URI, for embedding directly into
     * dompdf-rendered PDFs — dompdf can't reliably fetch remote images at
     * print time (see App\Helpers\QrCodeHelper for the same reasoning
     * around QR codes), so this reads the file straight off the local
     * "public" disk instead of going through logoUrl()'s HTTP URL. Returns
     * null when there's no logo, or when the stored value is itself a
     * remote URL (a legacy/edge case logoUrl() otherwise passes through).
     */
    public static function logoDataUri(): ?string
    {
        $path = static::get('hospital_logo');

        if (empty($path) || Str::startsWith($path, ['http://', 'https://'])) {
            return null;
        }

        if (! Storage::disk('public')->exists($path)) {
            return null;
        }

        $mimeType = Storage::disk('public')->mimeType($path) ?: 'image/png';

        return 'data:'.$mimeType.';base64,'.base64_encode(Storage::disk('public')->get($path));
    }
}
