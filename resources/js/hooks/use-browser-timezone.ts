import { useEffect } from 'react';

export function useBrowserTimezone(): void {
    useEffect(() => {
        const tz = Intl.DateTimeFormat().resolvedOptions().timeZone;
        if (tz && document.cookie.indexOf('browser_timezone=') === -1) {
            document.cookie = `browser_timezone=${tz};path=/;max-age=${60 * 60 * 24 * 365};SameSite=Lax`;
        }
    }, []);
}
