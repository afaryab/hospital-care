<?php

return [

    /*
    |--------------------------------------------------------------------------
    | OnlyOffice Document Server URLs
    |--------------------------------------------------------------------------
    |
    | `internal_url` is how Laravel (and the OnlyOffice container itself)
    | reach the Document Server over the internal Docker network — used when
    | building the document/callback URLs handed to OnlyOffice. `public_path`
    | is the path nginx proxies through to the same container for the
    | browser-side editor JS, so only APP_URL is ever exposed publicly.
    |
    */

    'internal_url' => env('ONLYOFFICE_INTERNAL_URL', 'http://onlyoffice-documentserver'),

    /*
    | Set ONLYOFFICE_ENABLED=false to run without a document server at all.
    | When enabled, reachability is probed (GET {internal_url}/healthcheck)
    | and cached, and editing is disabled while the server is down.
    */
    'enabled' => (bool) env('ONLYOFFICE_ENABLED', true),

    'health_cache_seconds' => (int) env('ONLYOFFICE_HEALTH_CACHE_SECONDS', 30),

    'public_path' => env('ONLYOFFICE_PUBLIC_PATH', '/onlyoffice'),

    /*
    |--------------------------------------------------------------------------
    | JWT
    |--------------------------------------------------------------------------
    |
    | OnlyOffice's standard security mechanism: every editor config Laravel
    | hands to the browser, and every callback/content request the Document
    | Server makes back to Laravel, is signed with this shared secret.
    | Without it, anyone who can reach the container could load or alter
    | arbitrary documents.
    |
    */

    'jwt_secret' => env('ONLYOFFICE_JWT_SECRET'),

    'jwt_header' => env('ONLYOFFICE_JWT_HEADER', 'Authorization'),

    /*
    | How long a signed content-fetch/callback token stays valid for.
    */
    'token_ttl_minutes' => (int) env('ONLYOFFICE_TOKEN_TTL_MINUTES', 30),
];
