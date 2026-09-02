<?php

namespace App\Http\Middleware;

use Illuminate\Http\Middleware\TrustProxies as Middleware;
use Illuminate\Http\Request;

class TrustProxies extends Middleware
{
    /**
     * Trust only known proxy sources instead of '*'. Trusting '*' let ANY
     * client — including a browser on the local network hitting the app
     * directly with no proxy in between — set its own X-Forwarded-Proto:
     * https header and have Laravel believe the request was secure, which
     * forced https-only behavior (secure cookies, https asset/storage
     * URLs) even for plain http LAN/direct-IP access.
     *
     * The app is reachable several ways, each needing a different entry:
     *  - Direct IP / direct DNS A record (no proxy in front): no forwarded
     *    headers are sent at all, so nothing needs to be trusted here —
     *    Laravel reads the real connection scheme.
     *  - Cloudflare DNS proxy (orange-clouded record): Cloudflare's edge
     *    terminates TLS and connects to this origin from its own public IP
     *    ranges (published at cloudflare.com/ips), setting
     *    X-Forwarded-Proto itself.
     *  - Cloudflare Tunnel (cloudflared) / any reverse proxy on the same
     *    Docker network: connects from a private/internal IP.
     */
    protected $proxies = [
        // Private/reserved ranges — Docker network, Cloudflare Tunnel
        // sidecar, any LAN reverse proxy.
        '127.0.0.1/8',
        '10.0.0.0/8',
        '172.16.0.0/12',
        '192.168.0.0/16',

        // Cloudflare's published edge IP ranges — https://www.cloudflare.com/ips/
        '173.245.48.0/20',
        '103.21.244.0/22',
        '103.22.200.0/22',
        '103.31.4.0/22',
        '141.101.64.0/18',
        '108.162.192.0/18',
        '190.93.240.0/20',
        '188.114.96.0/20',
        '197.234.240.0/22',
        '198.41.128.0/17',
        '162.158.0.0/15',
        '104.16.0.0/13',
        '104.24.0.0/14',
        '172.64.0.0/13',
        '131.0.72.0/22',
        '2400:cb00::/32',
        '2606:4700::/32',
        '2803:f800::/32',
        '2405:b500::/32',
        '2405:8100::/32',
        '2a06:98c0::/29',
        '2c0f:f248::/32',
    ];

    /**
     * Headers used to detect original client protocol/host/port.
     * Ensures HTTPS detection when behind Cloudflare.
     */
    protected $headers = Request::HEADER_X_FORWARDED_FOR
        | Request::HEADER_X_FORWARDED_HOST
        | Request::HEADER_X_FORWARDED_PORT
        | Request::HEADER_X_FORWARDED_PROTO;
}
