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
    red: 'bg-red-100 text-red-700 ring-red-200',
    yellow: 'bg-yellow-100 text-yellow-700 ring-yellow-200',
    blue: 'bg-blue-100 text-blue-700 ring-blue-200',
    sky: 'bg-sky-100 text-sky-700 ring-sky-200',
    green: 'bg-green-100 text-green-700 ring-green-200',
};

/** Tailwind classes for a triage badge, keyed by the triage's stored color name. */
export function triageBadgeClass(color?: string | null): string {
    return (
        TRIAGE_BADGE_CLASSES[color ?? ''] ??
        'bg-slate-100 text-slate-600 ring-slate-200'
    );
}
