<?php

use App\Http\Middleware\HandleAppearance;
use App\Http\Middleware\HandleCaptivePortal;
use App\Http\Middleware\HandleInertiaRequests;
use App\Http\Middleware\TrustProxies;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets;
use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;
use Sentry\Laravel\Integration;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Trust Cloudflare / upstream proxy headers for correct scheme detection
        $middleware->use([
            TrustProxies::class,
        ]);

        $middleware->encryptCookies(except: ['appearance', 'sidebar_state', 'browser_timezone']);

        // The OnlyOffice Document Server posts its save callback directly
        // (no browser, no Laravel session, no CSRF token) — it's protected
        // instead by the signed URL plus OnlyOffice's own JWT, checked
        // inside CallbackController.
        $middleware->validateCsrfTokens(except: ['onlyoffice/callback/*']);

        $middleware->web(append: [
            HandleAppearance::class,
            // Process captive portal redirects early
            HandleCaptivePortal::class,
            HandleInertiaRequests::class,
            AddLinkHeadersForPreloadedAssets::class,
        ]);

        $middleware->api(prepend: [
            EnsureFrontendRequestsAreStateful::class,
        ]);

        // No RateLimiter::for('api', ...) was ever registered, so this was
        // previously a no-op — every one of the 55 routes in routes/api.php
        // (patient search/create/edit, transaction refund, service-order
        // status changes, bed discharge, treatment records) could be hit
        // without limit by any authenticated token holder. The actual
        // limiter is registered in AppServiceProvider::boot().
        $middleware->throttleApi();
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        Integration::handles($exceptions);
    })->create();
