/**
 * Wrapper around fetch() for Sanctum SPA API calls.
 * Automatically reads the XSRF-TOKEN cookie and attaches it as the
 * X-XSRF-TOKEN header so Laravel's CSRF middleware accepts the request.
 */
export async function apiFetch(url: string, options: RequestInit = {}): Promise<Response> {
    const xsrfToken = decodeURIComponent(
        document.cookie
            .split('; ')
            .find((row) => row.startsWith('XSRF-TOKEN='))
            ?.split('=')[1] ?? '',
    );

    return fetch(url, {
        ...options,
        credentials: 'include',
        headers: {
            'Content-Type': 'application/json',
            Accept: 'application/json',
            'X-XSRF-TOKEN': xsrfToken,
            ...(options.headers ?? {}),
        },
    });
}
