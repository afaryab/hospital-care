<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;

class CaptivePortalService
{
    private Client $http;

    public function __construct(?Client $client = null)
    {
        $this->http = $client ?? new Client([
            'timeout' => 5,
        ]);
    }

    public function isEnabled(): bool
    {
        return (bool) config('services.captive_portal.enabled');
    }

    public function getEndpoint(): ?string
    {
        $endpoint = config('services.captive_portal.endpoint');
        return $endpoint ?: null;
    }

    public function getDuration(): int
    {
        return (int) config('services.captive_portal.duration', 3600);
    }

    /**
     * Authorize a client device with the captive portal.
     *
     * @param string $mac MAC address of the client (e.g., AA:BB:CC:DD:EE:FF)
     * @param int|null $duration seconds to authorize; defaults to configured duration
     */
    public function authorizeClient(string $mac, ?int $duration = null): void
    {
        if (!$this->isEnabled()) {
            return;
        }

        $endpoint = $this->getEndpoint();
        if (!$endpoint) {
            Log::warning('Captive portal endpoint missing; skipping authorization.');
            return;
        }

        $payload = [
            'mac' => $mac,
            'duration' => $duration ?? $this->getDuration(),
        ];

        try {
            $this->http->post($endpoint, [
                'json' => $payload,
            ]);
        } catch (GuzzleException $e) {
            // Log and continue; do not block user login flow
            Log::error('Captive portal authorization failed: '.$e->getMessage());
        }
    }
}
