<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Symfony\Component\HttpFoundation\Response;

/**
 * Two-factor authentication has been available since Fortify's
 * twoFactorAuthentication() feature was enabled, but nothing ever required
 * it — an admin or accountant account reaches every patient record, the
 * full financial ledger, and user management on password alone. Applied to
 * the admin/accounts Filament panels' auth middleware.
 *
 * Deliberately doesn't block the panel's own logout route — a locked-out
 * admin who hasn't set up 2FA yet must still be able to sign out, not get
 * trapped in a redirect loop with no way out except setting up 2FA first.
 */
class EnsureTwoFactorAuthenticationIsEnabled
{
    protected const EXEMPT_ROUTE_NAMES = [
        'filament.admin.auth.logout',
        'filament.accounts.auth.logout',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || $user->hasEnabledTwoFactorAuthentication()) {
            return $next($request);
        }

        if ($request->routeIs(self::EXEMPT_ROUTE_NAMES)) {
            return $next($request);
        }

        return Redirect::route('two-factor.show')
            ->with('status', 'Two-factor authentication is required for administrator and accountant accounts. Please set it up to continue.');
    }
}
