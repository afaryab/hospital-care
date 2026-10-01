<?php

namespace App\Http\Middleware;

use App\Services\OnlyOffice\OnlyOfficeHealth;
use Closure;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;

/**
 * The Documents section is switched off while the OnlyOffice Document
 * Server is unavailable, instead of the outage affecting the whole app.
 */
class EnsureDocumentsAvailable
{
    public const MESSAGE = 'Documents are temporarily unavailable because the document server is not responding. Please try again later.';

    public function __construct(private OnlyOfficeHealth $health) {}

    public function handle(Request $request, Closure $next): Response
    {
        if ($this->health->available()) {
            return $next($request);
        }

        if ($request->isMethod('GET') && ! $request->expectsJson()) {
            return Inertia::render('dms/unavailable', ['message' => self::MESSAGE])
                ->toResponse($request)
                ->setStatusCode(503);
        }

        abort(503, self::MESSAGE);
    }
}
