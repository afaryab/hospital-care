<?php

namespace App\Helpers;

use App\Models\HospitalSetting;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Writer\PngWriter;

class QrCodeHelper
{
    /**
     * Base64 PNG data URI for a QR code encoding the given URL, ready to
     * drop straight into an <img src="..."> — dompdf can't fetch external
     * images reliably at print time, so this avoids a network round trip.
     */
    public static function dataUri(string $url, int $size = 180): string
    {
        return (new Builder)
            ->build(
                writer: new PngWriter,
                data: $url,
                size: $size,
                margin: 2,
            )
            ->getDataUri();
    }

    /**
     * Build a short public verification URL from the configured domain
     * (falls back to this app's own URL when the hospital hasn't set one).
     */
    public static function verificationUrl(string $path): string
    {
        $domain = HospitalSetting::get('certificate_verification_domain', config('app.url'));
        $domain = is_string($domain) && $domain !== '' ? $domain : config('app.url');

        return rtrim((string) $domain, '/').'/'.ltrim($path, '/');
    }
}
