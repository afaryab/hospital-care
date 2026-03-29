import { useEffect, useMemo, useState } from 'react';

type DateInput = Date | string | number | null | undefined;

export interface DateTimeProps {
    /** Date value to display (Date, ISO string, or epoch ms/seconds) */
    value: DateInput;
    /** Optional CSS class names */
    className?: string;
    /** Locale used for formatting */
    locale?: string;
    /** Enable live updates to keep the relative time fresh */
    refresh?: boolean;
    /** Custom formatter for tooltip text (exact date/time) */
    tooltipFormatter?: (date: Date) => string;
}

type Unit = 'second' | 'minute' | 'hour' | 'day' | 'week' | 'month' | 'year';

function parseToDate(value: DateInput): Date | null {
    if (value == null) return null;
    if (value instanceof Date) return isNaN(value.getTime()) ? null : value;
    if (typeof value === 'number') {
        // Heuristic: treat values < 10^12 as seconds, otherwise as milliseconds
        const ms = value < 1_000_000_000_000 ? value * 1000 : value;
        const d = new Date(ms);
        return isNaN(d.getTime()) ? null : d;
    }
    if (typeof value === 'string') {
        // Try native Date parsing (handles ISO and many common formats)
        const d = new Date(value);
        if (!isNaN(d.getTime())) return d;

        // Fallback: if format is like "YYYY-MM-DD HH:mm:ss", replace space with 'T'
        const normalized = value.replace(/^(\d{4}-\d{2}-\d{2})\s+/, '$1T');
        const d2 = new Date(normalized);
        return isNaN(d2.getTime()) ? null : d2;
    }
    return null;
}

function formatExact(date: Date, locale: string): string {
    // Example: Feb 08, 2026, 13:45:02
    const fmt = new Intl.DateTimeFormat(locale, {
        year: 'numeric',
        month: 'short',
        day: '2-digit',
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit',
    });
    return fmt.format(date);
}

function diffToUnit(now: Date, date: Date): { value: number; unit: Unit } {
    const ms = date.getTime() - now.getTime();
    const secs = Math.round(ms / 1000);
    const absSecs = Math.abs(secs);

    if (absSecs < 60) return { value: secs, unit: 'second' };
    const mins = Math.round(secs / 60);
    const absMins = Math.abs(mins);
    if (absMins < 60) return { value: mins, unit: 'minute' };

    const hours = Math.round(mins / 60);
    const absHours = Math.abs(hours);
    if (absHours < 24) return { value: hours, unit: 'hour' };

    const days = Math.round(absHours / 24) * Math.sign(hours);
    const absDays = Math.abs(days);
    if (absDays < 7) return { value: days, unit: 'day' };

    const weeks = Math.round(days / 7);
    const absWeeks = Math.abs(weeks);
    if (absWeeks < 4) return { value: weeks, unit: 'week' };

    const months = Math.round(days / 30);
    const absMonths = Math.abs(months);
    if (absMonths < 12) return { value: months, unit: 'month' };

    const years = Math.round(days / 365);
    return { value: years, unit: 'year' };
}

function refreshIntervalFor(unit: Unit): number {
    switch (unit) {
        case 'second':
            return 1000;
        case 'minute':
            return 60_000;
        case 'hour':
            return 3_600_000;
        case 'day':
            return 86_400_000;
        case 'week':
            return 604_800_000;
        case 'month':
            return 2_592_000_000; // ~30 days
        case 'year':
            return 31_536_000_000; // ~365 days
        default:
            return 60_000;
    }
}

/**
 * Displays a human-friendly relative time (e.g., "3 minutes ago")
 * with a tooltip showing the exact formatted date/time.
 */
export default function DateTime({
    value,
    className,
    locale = 'en-US',
    refresh = true,
    tooltipFormatter,
}: DateTimeProps) {
    const parsed = useMemo(() => parseToDate(value), [value]);

    const [now, setNow] = useState<Date>(() => new Date());

    const rel = useMemo(() => {
        if (!parsed) return null;
        return diffToUnit(now, parsed);
    }, [now, parsed]);

    useEffect(() => {
        if (!refresh || !parsed || !rel) return;
        const interval = refreshIntervalFor(rel.unit);
        const id = setInterval(() => setNow(new Date()), interval);
        return () => clearInterval(id);
    }, [refresh, parsed, rel?.unit]);

    const rtf = useMemo(
        () => new Intl.RelativeTimeFormat(locale, { numeric: 'auto' }),
        [locale],
    );
    const title = useMemo(
        () =>
            parsed
                ? tooltipFormatter
                    ? tooltipFormatter(parsed)
                    : formatExact(parsed, locale)
                : '',
        [parsed, tooltipFormatter, locale],
    );

    if (!parsed || !rel) {
        return (
            <span className={className} title="Invalid date">
                -
            </span>
        );
    }

    const label = rtf.format(rel.value, rel.unit);

    return (
        <span className={className} title={title} aria-label={title}>
            {label}
        </span>
    );
}
