<?php

namespace App\Http\Middleware;

use Illuminate\Http\Middleware\TrustProxies as Middleware;
use Illuminate\Http\Request;

class TrustProxies extends Middleware
{
    /**
     * Trust only proxies on private/reserved network ranges (Docker network,
     * LAN reverse proxy, Cloudflare Tunnel container, loopback). Trusting
     * '*' let ANY client — including a browser on the local network hitting
     * the app directly with no proxy in between — set its own
     * X-Forwarded-Proto: https header and have Laravel believe the request
     * was secure, which forced https-only behavior (secure cookies, https
     * asset/storage URLs) even for plain http LAN access.
     */
    protected $proxies = [
        '127.0.0.1/8',
        '10.0.0.0/8',
        '172.16.0.0/12',
        '192.168.0.0/16',
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
