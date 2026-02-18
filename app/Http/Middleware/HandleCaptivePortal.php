<?php

namespace App\Http\Middleware;

use App\Services\CaptivePortalService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class HandleCaptivePortal
{
    /**
     * Intercept captive portal params and handle redirect for authenticated users.
     */
    public function handle(Request $request, Closure $next)
    {
        $clientMac = $request->query('clientMac');
        $target = $request->query('target');

        // Persist params if present
        if ($clientMac) {
            $request->session()->put('captive.clientMac', $clientMac);
        }
        if ($target) {
            $request->session()->put('captive.target', $target);
        }

        // If already authenticated and we have a target, authorize and redirect immediately
        if ($target && $request->user()) {
            $mac = $request->session()->get('captive.clientMac', $clientMac);
            if ($mac) {
                /** @var CaptivePortalService $service */
                $service = app(CaptivePortalService::class);
                $service->authorizeClient($mac);
                // Clear one-time params
                $request->session()->forget(['captive.clientMac', 'captive.target']);
            }

            // If this is an Inertia request, instruct client to hard redirect
            if ($request->headers->has('X-Inertia')) {
                return Inertia::location($target);
            }

            return Redirect::to($target);
        }

        return $next($request);
    }
}
