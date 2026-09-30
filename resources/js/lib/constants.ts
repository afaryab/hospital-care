/** Zero-padded month value → display name. Used by year/month filter dropdowns. */
export const MONTHS: { value: string; label: string }[] = [
    { value: '01', label: 'January' },
    { value: '02', label: 'February' },
    { value: '03', label: 'March' },
    { value: '04', label: 'April' },
    { value: '05', label: 'May' },
    { value: '06', label: 'June' },
    { value: '07', label: 'July' },
    { value: '08', label: 'August' },
    { value: '09', label: 'September' },
    { value: '10', label: 'October' },
    { value: '11', label: 'November' },
    { value: '12', label: 'December' },
];

export type PatientAgeInput = {
    age_dob?: string | null;
    age_days?: number | null;
    age?: number | null;
    age_group?: string | null;
};

/**
 * Compute a human-readable age string from any combination of fields
 * the backend may send (age_dob, age_days, age, age_group).
 */
export function formatPatientAge(patient: PatientAgeInput): string {
    if (patient.age_dob) {
        const dob = new Date(patient.age_dob);
        const now = new Date();
        const years =
            now.getFullYear() -
            dob.getFullYear() -
            (now < new Date(now.getFullYear(), dob.getMonth(), dob.getDate())
                ? 1
                : 0);
        if (years < 1) {
            const months =
                (now.getFullYear() - dob.getFullYear()) * 12 +
                (now.getMonth() - dob.getMonth());
            return months <= 1 ? '< 1 month' : `${months} months`;
        }
        return `${years} yrs`;
    }
    if (patient.age_days != null && patient.age_days > 0) {
        if (patient.age_days < 30) return `${patient.age_days} days`;
        if (patient.age_days < 365)
            return `${Math.floor(patient.age_days / 30)} months`;
        return `${Math.floor(patient.age_days / 365)} yrs`;
    }
    if (patient.age != null && patient.age > 0) return `${patient.age} yrs`;
    if (patient.age_group) return patient.age_group;
    return '—';
}

const TRIAGE_BADGE_CLASSES: Record<string, string> = {
    red: 'bg-red-100 dark:bg-red-900/40 text-red-700 dark:text-red-300 ring-red-200 dark:ring-red-800',
    yellow: 'bg-yellow-100 dark:bg-yellow-900/40 text-yellow-700 dark:text-yellow-300 ring-yellow-200 dark:ring-yellow-800',
    blue: 'bg-blue-100 dark:bg-blue-900/40 text-blue-700 dark:text-blue-300 ring-blue-200 dark:ring-blue-800',
    sky: 'bg-sky-100 dark:bg-sky-900/40 text-sky-700 dark:text-sky-300 ring-sky-200 dark:ring-sky-800',
    green: 'bg-green-100 dark:bg-green-900/40 text-green-700 dark:text-green-300 ring-green-200 dark:ring-green-800',
    black: 'bg-slate-800 text-white ring-slate-900',
};

/** Tailwind classes for a triage badge, keyed by the triage's stored color name. */
export function triageBadgeClass(color?: string | null): string {
    return (
        TRIAGE_BADGE_CLASSES[color ?? ''] ??
        'bg-slate-100 dark:bg-neutral-800 text-slate-600 dark:text-neutral-300 ring-slate-200 dark:ring-neutral-700'
    );
}

const TRIAGE_DOT_CLASSES: Record<string, string> = {
    red: 'bg-red-500',
    yellow: 'bg-yellow-500',
    blue: 'bg-blue-500',
    sky: 'bg-sky-500',
    green: 'bg-green-500',
    black: 'bg-slate-900',
};

/** Solid Tailwind background class for a small triage color dot/circle. */
export function triageDotClass(color?: string | null): string {
    return TRIAGE_DOT_CLASSES[color ?? ''] ?? 'bg-slate-400';
}

const TRIAGE_ACCENT_CLASSES: Record<string, string> = {
    red: 'text-red-600 dark:text-red-400',
    yellow: 'text-yellow-500',
    blue: 'text-blue-600 dark:text-blue-400',
    sky: 'text-sky-500',
    green: 'text-green-600 dark:text-green-400',
    black: 'text-slate-900 dark:text-neutral-100',
};

/** Tailwind text-color class controlling a native radio input's accent color, keyed by triage color name. */
export function triageAccentClass(color?: string | null): string {
    return (
        TRIAGE_ACCENT_CLASSES[color ?? ''] ??
        'text-slate-500 dark:text-neutral-400'
    );
}

const TRIAGE_SELECTED_CLASSES: Record<string, string> = {
    red: 'border-red-300 dark:border-red-800 bg-red-50 dark:bg-red-950/40 ring-1 ring-red-200 dark:ring-red-800',
    yellow: 'border-yellow-300 dark:border-yellow-800 bg-yellow-50 dark:bg-yellow-950/40 ring-1 ring-yellow-200 dark:ring-yellow-800',
    blue: 'border-blue-300 dark:border-blue-800 bg-blue-50 dark:bg-blue-950/40 ring-1 ring-blue-200 dark:ring-blue-800',
    sky: 'border-sky-300 dark:border-sky-800 bg-sky-50 dark:bg-sky-950/40 ring-1 ring-sky-200 dark:ring-sky-800',
    green: 'border-green-300 dark:border-green-800 bg-green-50 dark:bg-green-950/40 ring-1 ring-green-200 dark:ring-green-800',
    black: 'border-slate-500 bg-slate-100 dark:bg-neutral-800 ring-1 ring-slate-400',
};

/** Tailwind classes for a triage radio pill's selected state, keyed by triage color name. */
export function triageSelectedClass(color?: string | null): string {
    return (
        TRIAGE_SELECTED_CLASSES[color ?? ''] ??
        'border-slate-400 bg-slate-100 dark:bg-neutral-800 ring-1 ring-slate-300 dark:ring-neutral-700'
    );
}
