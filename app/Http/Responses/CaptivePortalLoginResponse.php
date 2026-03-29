<?php

namespace App\Http\Responses;

use App\Services\CaptivePortalService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;

class CaptivePortalLoginResponse implements LoginResponseContract
{
    public function toResponse($request)
    {
        $this->handleCaptivePortal($request);

        $target = $request->session()->pull('captive.target');

        if ($target) {
            return Inertia::location($target);
        }

        return Redirect::intended(config('fortify.home'));
    }

    private function handleCaptivePortal(Request $request): void
    {
        $mac = $request->session()->pull('captive.clientMac');

        if (! $mac) {
            return;
        }

        /** @var CaptivePortalService $service */
        $service = app(CaptivePortalService::class);
        $service->authorizeClient($mac);
    }
}
