<?php

namespace App\Services\OnlyOffice;

use App\Models\DmsDocument;
use App\Models\User;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

/**
 * Builds the config handed to the OnlyOffice browser editor and verifies the
 * JWT the Document Server signs on every callback/content-fetch request —
 * OnlyOffice's standard security mechanism. Without it, anyone who could
 * reach the container could load or alter arbitrary documents.
 */
class OnlyOfficeConfigService
{
    public function editorConfig(DmsDocument $document, User $actor): array
    {
        $media = $document->currentVersionMedia();

        if (! $media) {
            throw ValidationException::withMessages(['document' => 'This document has no content to edit.']);
        }

        $extension = strtolower(pathinfo($document->name, PATHINFO_EXTENSION) ?: $media->extension);

        $config = [
            'document' => [
                'fileType' => $extension,
                'key' => $this->documentKey($document),
                'title' => $document->name,
                'url' => $this->contentUrl($document, $actor),
                'permissions' => [
                    'edit' => ! $document->is_locked || $document->locked_by === $actor->id,
                    'download' => true,
                    'print' => true,
                ],
            ],
            'documentType' => $this->documentType($extension),
            'editorConfig' => [
                'callbackUrl' => $this->callbackUrl($document, $actor),
                'user' => [
                    'id' => (string) $actor->id,
                    'name' => $actor->name,
                ],
                'mode' => 'edit',
            ],
        ];

        $secret = config('onlyoffice.jwt_secret');
        if ($secret) {
            $config['token'] = JWT::encode($config, $secret, 'HS256');
        }

        return $config;
    }

    /**
     * A cache-busting key unique per version — without this, OnlyOffice may
     * keep serving a stale cached copy of a document after it's been
     * replaced with a new version.
     */
    public function documentKey(DmsDocument $document): string
    {
        return Str::substr(sprintf('%s-v%d', $document->uuid, $document->current_version), 0, 128);
    }

    public function documentType(string $extension): string
    {
        return match ($extension) {
            'doc', 'docx', 'odt', 'rtf', 'txt' => 'word',
            'xls', 'xlsx', 'ods', 'csv' => 'cell',
            'ppt', 'pptx', 'odp' => 'slide',
            default => 'word',
        };
    }

    protected function contentUrl(DmsDocument $document, User $actor): string
    {
        return $this->signedInternalUrl('onlyoffice.content', $document, $actor);
    }

    protected function callbackUrl(DmsDocument $document, User $actor): string
    {
        return $this->signedInternalUrl('onlyoffice.callback', $document, $actor);
    }

    /**
     * Content-fetch/callback URLs are built against ONLYOFFICE_INTERNAL_URL
     * semantics implicitly (the app's own APP_URL, reachable from the
     * Document Server container over the internal Docker network) and
     * carry a short-lived signed token in lieu of a browser session, since
     * the Document Server — not the user's browser — makes these requests.
     */
    protected function signedInternalUrl(string $routeName, DmsDocument $document, User $actor): string
    {
        return url()->temporarySignedRoute(
            $routeName,
            now()->addMinutes((int) config('onlyoffice.token_ttl_minutes')),
            ['document' => $document->uuid, 'actor' => $actor->id]
        );
    }

    /**
     * Verifies the JWT OnlyOffice attaches to callback/content requests
     * (either in the configured header, or in the request body's `token`
     * field per OnlyOffice's callback payload contract).
     */
    public function verifyToken(Request $request): bool
    {
        $secret = config('onlyoffice.jwt_secret');
        if (! $secret) {
            // No secret configured — signed-URL verification (checked
            // separately by the route's `signed` middleware) is the only
            // guard. Documented as a degraded-but-functional mode for
            // deployments that haven't set ONLYOFFICE_JWT_SECRET yet.
            return true;
        }

        $token = $this->extractToken($request);

        if (! $token) {
            return false;
        }

        try {
            JWT::decode($token, new Key($secret, 'HS256'));

            return true;
        } catch (\Throwable) {
            return false;
        }
    }

    protected function extractToken(Request $request): ?string
    {
        $header = (string) config('onlyoffice.jwt_header', 'Authorization');
        $headerValue = $request->header($header);

        if ($headerValue) {
            return Str::startsWith($headerValue, 'Bearer ') ? Str::substr($headerValue, 7) : $headerValue;
        }

        return $request->input('token');
    }
}
