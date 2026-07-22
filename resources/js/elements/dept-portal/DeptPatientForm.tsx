import DentalChart, {
    type DentalChartValue,
} from '@/components/ui/dental-chart';
import DrugPicker from '@/components/ui/drug-picker';
import Icd10Picker from '@/components/ui/icd10-picker';
import TreatmentAttachments, {
    type TreatmentAttachmentData,
} from '@/components/ui/treatment-attachments';
import { formatPatientAge, triageBadgeClass } from '@/lib/constants';
import { router } from '@inertiajs/react';
import { clsx } from 'clsx';
import {
    Activity,
    AlertCircle,
    ArrowLeft,
    Calendar,
    CheckCircle,
    ChevronDown,
    ChevronUp,
    Clock,
    FileText,
    Heart,
    History,
    Lock,
    Plus,
    Save,
    Siren,
    Stethoscope,
    Thermometer,
    Trash2,
    User,
    Wind,
    X,
} from 'lucide-react';
import { useCallback, useState } from 'react';
import { toast } from 'sonner';

// ─── Types ────────────────────────────────────────────────────────────────────

interface Patient {
    id: number;
    name: string;
    ps_number: string;
    gender?: string;
    age_days?: number;
    age_dob?: string;
    contact?: string;
}

interface VitalSign {
    temperature?: number | string;
    bp_systolic?: number | string;
    bp_diastolic?: number | string;
    pulse_rate?: number | string;
    respiratory_rate?: number | string;
    oxygen_saturation?: number | string;
    weight?: number | string;
    height?: number | string;
}

interface Prescription {
    drug_name: string;
    dose?: string;
    frequency?: string;
    duration?: string;
    route?: string;
    instructions?: string;
}

export interface Triage {
    id: number;
    name: string;
    color: string;
    priority?: number;
}

interface TriageHistoryEntry {
    id: number;
    changed_at: string;
    old_triage?: Triage | null;
    new_triage?: Triage | null;
    changed_by?: { id: number; name: string } | null;
}

interface TreatmentRecord {
    id?: number;
    chief_complaint?: string;
    history_of_present_illness?: string;
    examination_findings?: Record<string, string>;
    diagnosis_code?: string;
    icd10_code_id?: number | null;
    diagnosis_text?: string;
    treatment_plan?: string;
    prescriptions?: Prescription[];
    follow_up_date?: string;
    outcome?: string;
    referral_to?: string;
    is_finalized?: boolean;
    treated_at?: string;
    vital_signs?: VitalSign[];
    triage_id?: number | null;
    triage?: Triage | null;
    triage_histories?: TriageHistoryEntry[];
    dental_chart?: DentalChartValue | null;
    attachments?: TreatmentAttachmentData[];
}

interface PreviousVisit {
    id: number;
    so_number: string;
    status: string;
    created_at: string;
    treatment_record?: {
        diagnosis_text?: string;
        chief_complaint?: string;
        is_finalized?: boolean;
        triage?: Triage | null;
        dental_chart?: DentalChartValue | null;
    } | null;
}

export interface DeptPatientFormProps {
    deptName: string;
    accentColor: string; // Tailwind bg color, e.g. 'bg-red-600'
    dashboardUrl: string;
    saveApiUrl: string;
    updateStatusUrl: string;
    serviceOrder: {
        id: number;
        so_number: string;
        so_short?: string;
        status: string;
        token?: string | number;
        created_at?: string;
        patient?: Patient | null;
        service?: { id: number; name: string } | null;
        doctor?: { id: number; name: string } | null;
        treatment_record?: TreatmentRecord | null;
    };
    previousVisits: PreviousVisit[];
    // Feature flags — department-specific
    showVitals?: boolean;
    showExamFindings?: boolean;
    showPrescriptions?: boolean;
    showFollowUp?: boolean;
    showTriage?: boolean;
    requireTreatmentTime?: boolean;
    showAttachments?: boolean;
    showDentalChart?: boolean;
    treatmentPlanLabel?: string; // e.g. "Imaging Report" for ULT/XRAY
    treatmentPlanPlaceholder?: string; // pre-filled template text
    chiefComplaintLabel?: string;
    examSystems?: string[]; // custom systems for exam findings
    triages?: Triage[]; // active triage options, required when showTriage
    uploadAttachmentUrl?: string;
    deleteAttachmentUrl?: (attachmentId: number) => string;
}

// ─── Helpers ──────────────────────────────────────────────────────────────────

function getCsrf() {
    return decodeURIComponent(
        document.cookie.split('XSRF-TOKEN=')[1]?.split(';')[0] ?? '',
    );
}

function formatDate(d?: string) {
    if (!d) return '—';
    return new Date(d).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
    });
}

function statusColor(status: string) {
    const s = status.toLowerCase();
    if (s === 'in-progress')
        return 'bg-blue-100 text-blue-700 ring-1 ring-blue-200';
    if (s === 'open')
        return 'bg-amber-100 text-amber-700 ring-1 ring-amber-200';
    if (s === 'treated' || s === 'closed')
        return 'bg-emerald-100 text-emerald-700 ring-1 ring-emerald-200';
    return 'bg-slate-100 text-slate-600';
}

function blankRx(): Prescription {
    return {
        drug_name: '',
        dose: '',
        frequency: '',
        duration: '',
        route: '',
        instructions: '',
    };
}

// datetime-local inputs need "YYYY-MM-DDTHH:mm"; ISO strings from the backend
// include seconds/timezone, so trim to the minute for the input value.
function toDatetimeLocal(value?: string | null) {
    if (!value) return '';
    const d = new Date(value);
    if (Number.isNaN(d.getTime())) return '';
    const pad = (n: number) => String(n).padStart(2, '0');
    return `${d.getFullYear()}-${pad(d.getMonth() + 1)}-${pad(d.getDate())}T${pad(d.getHours())}:${pad(d.getMinutes())}`;
}

// ─── Sub-components ───────────────────────────────────────────────────────────

function FormSection({
    icon,
    title,
    children,
}: {
    icon: React.ReactNode;
    title: string;
    children: React.ReactNode;
}) {
    const [collapsed, setCollapsed] = useState(false);
    return (
        <div className="rounded-2xl border border-slate-200 bg-white shadow-sm">
            <button
                type="button"
                onClick={() => setCollapsed((c) => !c)}
                className="flex w-full items-center justify-between px-4 py-3 md:px-5"
            >
                <div className="flex items-center gap-2">
                    {icon}
                    <span className="text-sm font-semibold text-slate-900">
                        {title}
                    </span>
                </div>
                {collapsed ? (
                    <ChevronDown className="h-4 w-4 text-slate-400" />
                ) : (
                    <ChevronUp className="h-4 w-4 text-slate-400" />
                )}
            </button>
            {!collapsed && (
                <>
                    <div className="h-px bg-slate-100" />
                    <div className="p-4 md:p-5">{children}</div>
                </>
            )}
        </div>
    );
}

function InfoCell({ label, value }: { label: string; value?: string }) {
    return (
        <div>
            <p className="text-xs text-slate-500">{label}</p>
            <p className="mt-0.5 text-sm font-semibold text-slate-800">
                {value ?? '—'}
            </p>
        </div>
    );
}

function textareaClass(disabled: boolean) {
    return clsx(
        'w-full resize-y rounded-xl border px-3 py-2.5 text-sm text-slate-800 placeholder:text-slate-400 focus:border-slate-400 focus:ring-2 focus:ring-slate-100 focus:outline-none',
        disabled
            ? 'cursor-not-allowed border-slate-100 bg-slate-50 text-slate-600'
            : 'border-slate-200 bg-white hover:border-slate-300',
    );
}

function inputClass(disabled: boolean) {
    return clsx(
        'w-full rounded-xl border px-3 py-2 text-sm text-slate-800 placeholder:text-slate-400 focus:border-slate-400 focus:ring-2 focus:ring-slate-100 focus:outline-none',
        disabled
            ? 'cursor-not-allowed border-slate-100 bg-slate-50 text-slate-600'
            : 'border-slate-200 bg-white hover:border-slate-300',
    );
}

function tableInputClass(disabled: boolean) {
    return clsx(
        'w-full rounded-lg border px-2 py-1.5 text-xs text-slate-800 placeholder:text-slate-400 focus:border-slate-300 focus:ring-1 focus:ring-slate-200 focus:outline-none',
        disabled
            ? 'cursor-not-allowed border-transparent bg-transparent text-slate-600'
            : 'border-slate-200 bg-white hover:border-slate-300',
    );
}

// ─── Main Component ───────────────────────────────────────────────────────────

export default function DeptPatientForm({
    deptName,
    accentColor,
    dashboardUrl,
    saveApiUrl,
    updateStatusUrl,
    serviceOrder,
    previousVisits,
    showVitals = true,
    showExamFindings = false,
    showPrescriptions = true,
    showFollowUp = true,
    showTriage = false,
    requireTreatmentTime = false,
    showAttachments = false,
    showDentalChart = false,
    treatmentPlanLabel = 'Treatment Plan / Notes',
    treatmentPlanPlaceholder = 'Management plan, investigations, advice…',
    chiefComplaintLabel = 'Chief Complaint',
    examSystems = [
        'General',
        'Cardiovascular',
        'Respiratory',
        'Abdomen',
        'CNS',
        'Other',
    ],
    triages = [],
    uploadAttachmentUrl,
    deleteAttachmentUrl,
}: DeptPatientFormProps) {
    const existing = serviceOrder.treatment_record;
    const isFinalized = existing?.is_finalized ?? false;
    const patient = serviceOrder.patient;

    const [chiefComplaint, setChiefComplaint] = useState(
        existing?.chief_complaint ?? '',
    );
    const [hpi, setHpi] = useState(existing?.history_of_present_illness ?? '');
    const [diagnosisCode, setDiagnosisCode] = useState(
        existing?.diagnosis_code ?? '',
    );
    const [icd10CodeId, setIcd10CodeId] = useState<number | null>(
        existing?.icd10_code_id ?? null,
    );
    const [diagnosisText, setDiagnosisText] = useState(
        existing?.diagnosis_text ?? '',
    );
    const [treatmentPlan, setTreatmentPlan] = useState(
        existing?.treatment_plan ?? '',
    );
    const [followUpDate, setFollowUpDate] = useState(
        existing?.follow_up_date ?? '',
    );
    const [outcome, setOutcome] = useState(existing?.outcome ?? '');
    const [referralTo, setReferralTo] = useState(existing?.referral_to ?? '');
    const [examFindings, setExamFindings] = useState<Record<string, string>>(
        existing?.examination_findings ?? {},
    );
    const [triageId, setTriageId] = useState<number | null>(
        existing?.triage_id ?? null,
    );
    const [treatedAt, setTreatedAt] = useState(
        () =>
            toDatetimeLocal(existing?.treated_at) ||
            toDatetimeLocal(new Date().toISOString()),
    );
    const [dentalChart, setDentalChart] = useState<DentalChartValue>(
        existing?.dental_chart ?? {},
    );
    const [attachments, setAttachments] = useState<TreatmentAttachmentData[]>(
        existing?.attachments ?? [],
    );
    const lastVital =
        existing?.vital_signs?.[existing.vital_signs.length - 1] ?? {};
    const [vitals, setVitals] = useState({
        temperature: lastVital.temperature ?? '',
        bp_systolic: lastVital.bp_systolic ?? '',
        bp_diastolic: lastVital.bp_diastolic ?? '',
        pulse_rate: lastVital.pulse_rate ?? '',
        respiratory_rate: lastVital.respiratory_rate ?? '',
        oxygen_saturation: lastVital.oxygen_saturation ?? '',
        weight: lastVital.weight ?? '',
        height: lastVital.height ?? '',
    });
    const [prescriptions, setPrescriptions] = useState<Prescription[]>(
        existing?.prescriptions?.length ? existing.prescriptions : [blankRx()],
    );
    const [saving, setSaving] = useState(false);
    const [finalizing, setFinalizing] = useState(false);
    const [showHistory, setShowHistory] = useState(false);
    const [calling, setCalling] = useState(false);

    const buildPayload = useCallback(
        (finalize = false) => ({
            chief_complaint: chiefComplaint,
            history_of_present_illness: hpi,
            examination_findings: showExamFindings ? examFindings : undefined,
            diagnosis_code: diagnosisCode,
            icd10_code_id: icd10CodeId,
            diagnosis_text: diagnosisText,
            treatment_plan: treatmentPlan,
            prescriptions: showPrescriptions
                ? prescriptions.filter((p) => p.drug_name.trim())
                : [],
            follow_up_date: showFollowUp ? followUpDate || null : null,
            outcome: showFollowUp ? outcome || null : null,
            referral_to: showFollowUp ? referralTo || null : null,
            vitals:
                showVitals && Object.values(vitals).some((v) => v !== '')
                    ? vitals
                    : null,
            dental_chart: showDentalChart ? dentalChart : undefined,
            // Omitted entirely (not just null) for departments that don't use triage/treatment-time,
            // so the backend's "did triage_id change" check and its default treated_at timestamping
            // are left untouched for those departments.
            triage_id: showTriage ? triageId : undefined,
            treated_at: requireTreatmentTime
                ? treatedAt
                    ? new Date(treatedAt).toISOString()
                    : null
                : undefined,
            finalize,
        }),
        [
            chiefComplaint,
            hpi,
            examFindings,
            diagnosisCode,
            icd10CodeId,
            diagnosisText,
            treatmentPlan,
            prescriptions,
            followUpDate,
            outcome,
            referralTo,
            vitals,
            dentalChart,
            triageId,
            treatedAt,
            showVitals,
            showExamFindings,
            showPrescriptions,
            showFollowUp,
            showDentalChart,
            showTriage,
            requireTreatmentTime,
        ],
    );

    const save = useCallback(
        async (finalize: boolean) => {
            if (finalize) setFinalizing(true);
            else setSaving(true);
            try {
                const res = await fetch(saveApiUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        Accept: 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-XSRF-TOKEN': getCsrf(),
                    },
                    body: JSON.stringify(buildPayload(finalize)),
                });
                const json = await res.json();
                if (!res.ok) {
                    toast.error(json.message ?? 'Save failed');
                    return;
                }
                toast.success(finalize ? 'Record finalized' : 'Draft saved');
                if (finalize) window.location.reload();
            } catch {
                toast.error('Network error — please try again');
            } finally {
                setSaving(false);
                setFinalizing(false);
            }
        },
        [buildPayload, saveApiUrl],
    );

    const callPatient = async () => {
        setCalling(true);
        try {
            await fetch(updateStatusUrl, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    Accept: 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-XSRF-TOKEN': getCsrf(),
                },
                body: JSON.stringify({ status: 'in-progress' }),
            });
            toast.success(`${patient?.name ?? 'Patient'} called`);
            window.location.reload();
        } catch {
            toast.error('Failed to update status');
        } finally {
            setCalling(false);
        }
    };

    const updateRx = (
        idx: number,
        field: keyof Prescription,
        value: string,
    ) => {
        setPrescriptions((p) =>
            p.map((row, i) => (i === idx ? { ...row, [field]: value } : row)),
        );
    };

    return (
        <div className="min-h-full bg-gradient-to-br from-slate-50 via-white to-slate-50">
            {/* ── Sticky Patient Banner ───────────────────────────────── */}
            <div className="sticky top-0 z-10 border-b border-slate-100 bg-white shadow-sm">
                <div className="mx-auto max-w-5xl px-4 py-3 md:px-6">
                    <div className="flex flex-wrap items-center justify-between gap-3">
                        <div className="flex items-center gap-3">
                            <button
                                onClick={() => router.visit(dashboardUrl)}
                                className="flex h-8 w-8 items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-600 hover:bg-slate-50"
                            >
                                <ArrowLeft className="h-4 w-4" />
                            </button>
                            <div
                                className={clsx(
                                    'flex h-10 w-10 items-center justify-center rounded-full text-base font-bold text-white shadow-sm',
                                    accentColor,
                                )}
                            >
                                {patient?.name?.charAt(0)?.toUpperCase() ?? '?'}
                            </div>
                            <div>
                                <div className="flex items-center gap-2">
                                    <h1 className="text-base font-bold text-slate-900 md:text-lg">
                                        {patient?.name}
                                    </h1>
                                    <span
                                        className={clsx(
                                            'rounded-full px-2 py-0.5 text-xs font-semibold',
                                            statusColor(serviceOrder.status),
                                        )}
                                    >
                                        {serviceOrder.status}
                                    </span>
                                    {isFinalized && (
                                        <span className="flex items-center gap-1 rounded-full bg-emerald-50 px-2 py-0.5 text-xs font-semibold text-emerald-700 ring-1 ring-emerald-200">
                                            <Lock className="h-3 w-3" />{' '}
                                            Finalized
                                        </span>
                                    )}
                                </div>
                                <div className="flex flex-wrap items-center gap-x-2 text-xs text-slate-500">
                                    <span>{patient?.ps_number}</span>
                                    {patient && (
                                        <>
                                            <span>&bull;</span>
                                            <span>
                                                {formatPatientAge(patient)}
                                            </span>
                                        </>
                                    )}
                                    {patient?.gender && (
                                        <>
                                            <span>&bull;</span>
                                            <span>
                                                {patient.gender.toUpperCase()}
                                            </span>
                                        </>
                                    )}
                                    {patient?.contact && (
                                        <>
                                            <span>&bull;</span>
                                            <span>{patient.contact}</span>
                                        </>
                                    )}
                                </div>
                            </div>
                        </div>

                        <div className="flex items-center gap-2">
                            <button
                                type="button"
                                onClick={() => setShowHistory((v) => !v)}
                                className="flex items-center gap-1.5 rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-xs font-medium text-slate-600 hover:bg-slate-50"
                            >
                                <History className="h-3.5 w-3.5" /> History (
                                {previousVisits.length})
                            </button>

                            {!isFinalized &&
                                serviceOrder.status.toLowerCase() ===
                                    'open' && (
                                    <button
                                        type="button"
                                        disabled={calling}
                                        onClick={callPatient}
                                        className={clsx(
                                            'rounded-lg px-3 py-1.5 text-xs font-semibold text-white transition-colors disabled:opacity-50',
                                            accentColor,
                                            'hover:opacity-90',
                                        )}
                                    >
                                        {calling ? 'Calling…' : 'Call Patient'}
                                    </button>
                                )}

                            {!isFinalized && (
                                <>
                                    <button
                                        type="button"
                                        disabled={saving}
                                        onClick={() => save(false)}
                                        className="flex items-center gap-1.5 rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-50 disabled:opacity-50"
                                    >
                                        <Save className="h-3.5 w-3.5" />{' '}
                                        {saving ? 'Saving…' : 'Save Draft'}
                                    </button>
                                    <button
                                        type="button"
                                        disabled={finalizing}
                                        onClick={() => save(true)}
                                        className={clsx(
                                            'flex items-center gap-1.5 rounded-lg px-3 py-1.5 text-xs font-semibold text-white disabled:opacity-50',
                                            accentColor,
                                            'hover:opacity-90',
                                        )}
                                    >
                                        <CheckCircle className="h-3.5 w-3.5" />{' '}
                                        {finalizing
                                            ? 'Finalizing…'
                                            : 'Finalize'}
                                    </button>
                                </>
                            )}
                        </div>
                    </div>
                </div>
            </div>

            <div className="mx-auto max-w-5xl px-4 pt-4 pb-10 md:px-6">
                {/* Finalized banner */}
                {isFinalized && (
                    <div className="mb-4 flex items-center gap-3 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3">
                        <Lock className="h-5 w-5 text-emerald-600" />
                        <p className="text-sm font-medium text-emerald-800">
                            This treatment record has been finalized and is
                            read-only.
                        </p>
                    </div>
                )}

                {/* Previous Visits panel */}
                {showHistory && (
                    <div className="mb-4 rounded-2xl border border-slate-200 bg-white shadow-sm">
                        <div className="flex items-center justify-between border-b border-slate-100 px-4 py-3">
                            <h3 className="flex items-center gap-2 text-sm font-semibold text-slate-900">
                                <History className="h-4 w-4 text-slate-500" />{' '}
                                Previous {deptName} Visits
                            </h3>
                            <button
                                type="button"
                                onClick={() => setShowHistory(false)}
                                className="text-slate-400 hover:text-slate-600"
                            >
                                <X className="h-4 w-4" />
                            </button>
                        </div>
                        {previousVisits.length === 0 ? (
                            <p className="px-4 py-6 text-center text-sm text-slate-500">
                                No previous visits.
                            </p>
                        ) : (
                            <div className="divide-y divide-slate-100">
                                {previousVisits.map((v) => (
                                    <div
                                        key={v.id}
                                        className="flex items-start justify-between px-4 py-3"
                                    >
                                        <div>
                                            <div className="flex items-center gap-2">
                                                <p className="text-xs font-semibold text-slate-800">
                                                    {v.so_number}
                                                </p>
                                                {v.treatment_record?.triage && (
                                                    <span
                                                        className={clsx(
                                                            'rounded-full px-1.5 py-0.5 text-[10px] font-semibold ring-1',
                                                            triageBadgeClass(
                                                                v
                                                                    .treatment_record
                                                                    .triage
                                                                    .color,
                                                            ),
                                                        )}
                                                    >
                                                        {
                                                            v.treatment_record
                                                                .triage.name
                                                        }
                                                    </span>
                                                )}
                                                {v.treatment_record
                                                    ?.dental_chart &&
                                                    Object.keys(
                                                        v.treatment_record
                                                            .dental_chart,
                                                    ).length > 0 && (
                                                        <span className="rounded-full bg-teal-50 px-1.5 py-0.5 text-[10px] font-medium text-teal-700">
                                                            {
                                                                Object.keys(
                                                                    v
                                                                        .treatment_record
                                                                        .dental_chart,
                                                                ).length
                                                            }{' '}
                                                            teeth
                                                        </span>
                                                    )}
                                            </div>
                                            <p className="mt-0.5 text-xs text-slate-500">
                                                {formatDate(v.created_at)}
                                            </p>
                                            {v.treatment_record
                                                ?.diagnosis_text && (
                                                <p className="mt-1 text-xs text-slate-600">
                                                    {
                                                        v.treatment_record
                                                            .diagnosis_text
                                                    }
                                                </p>
                                            )}
                                            {v.treatment_record
                                                ?.chief_complaint && (
                                                <p className="mt-0.5 text-xs text-slate-400 italic">
                                                    CC:{' '}
                                                    {
                                                        v.treatment_record
                                                            .chief_complaint
                                                    }
                                                </p>
                                            )}
                                        </div>
                                        <span
                                            className={clsx(
                                                'rounded-full px-2 py-0.5 text-xs font-semibold',
                                                statusColor(v.status),
                                            )}
                                        >
                                            {v.status}
                                        </span>
                                    </div>
                                ))}
                            </div>
                        )}
                    </div>
                )}

                {/* SO info row */}
                <div className="mb-4 grid grid-cols-2 gap-3 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:grid-cols-4">
                    <InfoCell
                        label="SO Number"
                        value={serviceOrder.so_number}
                    />
                    <InfoCell
                        label="Token"
                        value={
                            serviceOrder.token
                                ? `#${serviceOrder.token}`
                                : (serviceOrder.so_short ?? '—')
                        }
                    />
                    <InfoCell
                        label="Doctor"
                        value={serviceOrder.doctor?.name}
                    />
                    <InfoCell
                        label="Date"
                        value={formatDate(serviceOrder.created_at)}
                    />
                </div>

                {/* Form sections */}
                <div className="space-y-4">
                    <FormSection
                        icon={<AlertCircle className="h-4 w-4 text-red-500" />}
                        title={chiefComplaintLabel}
                    >
                        <textarea
                            disabled={isFinalized}
                            value={chiefComplaint}
                            rows={2}
                            onChange={(e) => setChiefComplaint(e.target.value)}
                            placeholder="Patient's primary complaint…"
                            className={textareaClass(isFinalized)}
                        />
                    </FormSection>

                    {(showTriage || requireTreatmentTime) && (
                        <FormSection
                            icon={<Siren className="h-4 w-4 text-red-600" />}
                            title="Triage & Treatment Time"
                        >
                            <div className="grid grid-cols-1 gap-3 sm:grid-cols-2">
                                {showTriage && (
                                    <div>
                                        <label className="mb-1 block text-xs font-medium text-slate-500">
                                            Triage Level{' '}
                                            <span className="text-red-500">
                                                *
                                            </span>
                                        </label>
                                        <select
                                            disabled={isFinalized}
                                            value={triageId ?? ''}
                                            onChange={(e) =>
                                                setTriageId(
                                                    e.target.value
                                                        ? Number(e.target.value)
                                                        : null,
                                                )
                                            }
                                            className={inputClass(isFinalized)}
                                            required
                                        >
                                            <option value="">
                                                Select triage level…
                                            </option>
                                            {triages.map((t) => (
                                                <option key={t.id} value={t.id}>
                                                    {t.name}
                                                </option>
                                            ))}
                                        </select>
                                        {triageId && (
                                            <span
                                                className={clsx(
                                                    'mt-1.5 inline-block rounded-full px-2 py-0.5 text-xs font-semibold ring-1',
                                                    triageBadgeClass(
                                                        triages.find(
                                                            (t) =>
                                                                t.id ===
                                                                triageId,
                                                        )?.color,
                                                    ),
                                                )}
                                            >
                                                {
                                                    triages.find(
                                                        (t) =>
                                                            t.id === triageId,
                                                    )?.name
                                                }
                                            </span>
                                        )}
                                    </div>
                                )}
                                {requireTreatmentTime && (
                                    <div>
                                        <label className="mb-1 block text-xs font-medium text-slate-500">
                                            Time of Treatment{' '}
                                            <span className="text-red-500">
                                                *
                                            </span>
                                        </label>
                                        <input
                                            disabled={isFinalized}
                                            type="datetime-local"
                                            value={treatedAt}
                                            onChange={(e) =>
                                                setTreatedAt(e.target.value)
                                            }
                                            className={inputClass(isFinalized)}
                                            required
                                        />
                                    </div>
                                )}
                            </div>

                            {showTriage &&
                                (existing?.triage_histories?.length ?? 0) >
                                    0 && (
                                    <div className="mt-4 border-t border-slate-100 pt-3">
                                        <p className="mb-2 flex items-center gap-1.5 text-xs font-semibold text-slate-600">
                                            <Clock className="h-3.5 w-3.5" />{' '}
                                            Triage Change History
                                        </p>
                                        <ul className="space-y-1.5">
                                            {existing?.triage_histories?.map(
                                                (h) => (
                                                    <li
                                                        key={h.id}
                                                        className="flex flex-wrap items-center gap-1.5 text-xs text-slate-500"
                                                    >
                                                        <span>
                                                            {formatDate(
                                                                h.changed_at,
                                                            )}
                                                        </span>
                                                        {h.old_triage ? (
                                                            <>
                                                                <span
                                                                    className={clsx(
                                                                        'rounded-full px-1.5 py-0.5 text-[10px] font-semibold ring-1',
                                                                        triageBadgeClass(
                                                                            h
                                                                                .old_triage
                                                                                .color,
                                                                        ),
                                                                    )}
                                                                >
                                                                    {
                                                                        h
                                                                            .old_triage
                                                                            .name
                                                                    }
                                                                </span>
                                                                <span>→</span>
                                                            </>
                                                        ) : (
                                                            <span className="italic">
                                                                Initial:
                                                            </span>
                                                        )}
                                                        <span
                                                            className={clsx(
                                                                'rounded-full px-1.5 py-0.5 text-[10px] font-semibold ring-1',
                                                                triageBadgeClass(
                                                                    h.new_triage
                                                                        ?.color,
                                                                ),
                                                            )}
                                                        >
                                                            {h.new_triage?.name}
                                                        </span>
                                                        {h.changed_by && (
                                                            <span className="text-slate-400">
                                                                by{' '}
                                                                {
                                                                    h.changed_by
                                                                        .name
                                                                }
                                                            </span>
                                                        )}
                                                    </li>
                                                ),
                                            )}
                                        </ul>
                                    </div>
                                )}
                        </FormSection>
                    )}

                    <FormSection
                        icon={<FileText className="h-4 w-4 text-slate-500" />}
                        title="History of Present Illness"
                    >
                        <textarea
                            disabled={isFinalized}
                            value={hpi}
                            rows={3}
                            onChange={(e) => setHpi(e.target.value)}
                            placeholder="Onset, duration, progression, associated symptoms…"
                            className={textareaClass(isFinalized)}
                        />
                    </FormSection>

                    {showVitals && (
                        <FormSection
                            icon={<Heart className="h-4 w-4 text-rose-500" />}
                            title="Vital Signs"
                        >
                            <div className="grid grid-cols-2 gap-3 sm:grid-cols-4">
                                {[
                                    {
                                        key: 'temperature',
                                        label: 'Temp (°C)',
                                        placeholder: '37.0',
                                        icon: (
                                            <Thermometer className="h-3.5 w-3.5" />
                                        ),
                                    },
                                    {
                                        key: 'bp_systolic',
                                        label: 'BP Systolic',
                                        placeholder: '120',
                                        icon: <Heart className="h-3.5 w-3.5" />,
                                    },
                                    {
                                        key: 'bp_diastolic',
                                        label: 'BP Diastolic',
                                        placeholder: '80',
                                        icon: <Heart className="h-3.5 w-3.5" />,
                                    },
                                    {
                                        key: 'pulse_rate',
                                        label: 'Pulse (bpm)',
                                        placeholder: '72',
                                        icon: (
                                            <Activity className="h-3.5 w-3.5" />
                                        ),
                                    },
                                    {
                                        key: 'respiratory_rate',
                                        label: 'Resp. Rate',
                                        placeholder: '16',
                                        icon: <Wind className="h-3.5 w-3.5" />,
                                    },
                                    {
                                        key: 'oxygen_saturation',
                                        label: 'SpO2 (%)',
                                        placeholder: '98',
                                        icon: (
                                            <Activity className="h-3.5 w-3.5" />
                                        ),
                                    },
                                    {
                                        key: 'weight',
                                        label: 'Weight (kg)',
                                        placeholder: '70',
                                        icon: <User className="h-3.5 w-3.5" />,
                                    },
                                    {
                                        key: 'height',
                                        label: 'Height (cm)',
                                        placeholder: '170',
                                        icon: <User className="h-3.5 w-3.5" />,
                                    },
                                ].map(
                                    ({
                                        key,
                                        label,
                                        placeholder,
                                        icon: vIcon,
                                    }) => (
                                        <div key={key}>
                                            <label className="mb-1 flex items-center gap-1 text-xs font-medium text-slate-500">
                                                {vIcon} {label}
                                            </label>
                                            <input
                                                disabled={isFinalized}
                                                type="number"
                                                step="any"
                                                value={String(
                                                    vitals[
                                                        key as keyof typeof vitals
                                                    ],
                                                )}
                                                onChange={(e) =>
                                                    setVitals((s) => ({
                                                        ...s,
                                                        [key]: e.target.value,
                                                    }))
                                                }
                                                placeholder={placeholder}
                                                className={inputClass(
                                                    isFinalized,
                                                )}
                                            />
                                        </div>
                                    ),
                                )}
                            </div>
                        </FormSection>
                    )}

                    {showExamFindings && (
                        <FormSection
                            icon={
                                <Stethoscope className="h-4 w-4 text-teal-600" />
                            }
                            title="Examination Findings"
                        >
                            <div className="grid grid-cols-1 gap-3 sm:grid-cols-2">
                                {examSystems.map((sys) => (
                                    <div key={sys}>
                                        <label className="mb-1 block text-xs font-medium text-slate-500">
                                            {sys}
                                        </label>
                                        <input
                                            disabled={isFinalized}
                                            value={examFindings[sys] ?? ''}
                                            onChange={(e) =>
                                                setExamFindings((f) => ({
                                                    ...f,
                                                    [sys]: e.target.value,
                                                }))
                                            }
                                            placeholder={`${sys} findings…`}
                                            className={inputClass(isFinalized)}
                                        />
                                    </div>
                                ))}
                            </div>
                        </FormSection>
                    )}

                    {showDentalChart && (
                        <FormSection
                            icon={
                                <Stethoscope className="h-4 w-4 text-teal-600" />
                            }
                            title="Dental Chart"
                        >
                            <DentalChart
                                value={dentalChart}
                                onChange={setDentalChart}
                                disabled={isFinalized}
                            />
                        </FormSection>
                    )}

                    <FormSection
                        icon={<FileText className="h-4 w-4 text-indigo-500" />}
                        title="Diagnosis"
                    >
                        <div className="grid grid-cols-1 gap-3 sm:grid-cols-3">
                            <div>
                                <label className="mb-1 block text-xs font-medium text-slate-500">
                                    ICD-10 Code
                                </label>
                                <Icd10Picker
                                    value={diagnosisCode}
                                    disabled={isFinalized}
                                    placeholder="Search code or diagnosis…"
                                    className={inputClass(isFinalized)}
                                    onSelect={(code, description) => {
                                        setDiagnosisCode(code);
                                        setDiagnosisText(description);
                                    }}
                                />
                            </div>
                            <div className="sm:col-span-2">
                                <label className="mb-1 block text-xs font-medium text-slate-500">
                                    Diagnosis
                                </label>
                                <input
                                    disabled={isFinalized}
                                    value={diagnosisText}
                                    onChange={(e) =>
                                        setDiagnosisText(e.target.value)
                                    }
                                    placeholder="Auto-filled from ICD-10 or type manually"
                                    className={inputClass(isFinalized)}
                                />
                            </div>
                        </div>
                    </FormSection>

                    {showAttachments &&
                        uploadAttachmentUrl &&
                        deleteAttachmentUrl && (
                            <FormSection
                                icon={
                                    <FileText className="h-4 w-4 text-blue-500" />
                                }
                                title="Images & Reports"
                            >
                                <TreatmentAttachments
                                    attachments={attachments}
                                    uploadUrl={uploadAttachmentUrl}
                                    deleteUrlFor={deleteAttachmentUrl}
                                    disabled={isFinalized}
                                    onUploaded={(a) =>
                                        setAttachments((prev) => [...prev, a])
                                    }
                                    onDeleted={(id) =>
                                        setAttachments((prev) =>
                                            prev.filter((a) => a.id !== id),
                                        )
                                    }
                                />
                            </FormSection>
                        )}

                    <FormSection
                        icon={<FileText className="h-4 w-4 text-violet-500" />}
                        title={treatmentPlanLabel}
                    >
                        <textarea
                            disabled={isFinalized}
                            value={treatmentPlan}
                            rows={6}
                            onChange={(e) => setTreatmentPlan(e.target.value)}
                            placeholder={treatmentPlanPlaceholder}
                            className={textareaClass(isFinalized)}
                        />
                    </FormSection>

                    {showPrescriptions && (
                        <FormSection
                            icon={
                                <FileText className="h-4 w-4 text-emerald-600" />
                            }
                            title="Prescription"
                        >
                            <div className="overflow-x-auto rounded-xl border border-slate-200">
                                <table className="w-full min-w-[700px] text-sm">
                                    <thead className="bg-slate-50">
                                        <tr>
                                            {[
                                                'Drug Name',
                                                'Dose',
                                                'Frequency',
                                                'Duration',
                                                'Route',
                                                'Instructions',
                                                '',
                                            ].map((h) => (
                                                <th
                                                    key={h}
                                                    className="px-3 py-2 text-left text-xs font-semibold text-slate-600"
                                                >
                                                    {h}
                                                </th>
                                            ))}
                                        </tr>
                                    </thead>
                                    <tbody className="divide-y divide-slate-100">
                                        {prescriptions.map((row, idx) => (
                                            <tr
                                                key={idx}
                                                className="bg-white hover:bg-slate-50"
                                            >
                                                <td className="px-2 py-1.5">
                                                    {isFinalized ? (
                                                        <input
                                                            disabled
                                                            value={
                                                                row.drug_name
                                                            }
                                                            className={tableInputClass(
                                                                true,
                                                            )}
                                                        />
                                                    ) : (
                                                        <DrugPicker
                                                            value={
                                                                row.drug_name
                                                            }
                                                            onChange={(name) =>
                                                                updateRx(
                                                                    idx,
                                                                    'drug_name',
                                                                    name,
                                                                )
                                                            }
                                                            onSelect={(
                                                                drug,
                                                            ) => {
                                                                setPrescriptions(
                                                                    (prev) =>
                                                                        prev.map(
                                                                            (
                                                                                r,
                                                                                i,
                                                                            ) =>
                                                                                i !==
                                                                                idx
                                                                                    ? r
                                                                                    : {
                                                                                          ...r,
                                                                                          drug_name:
                                                                                              drug.name,
                                                                                          dose:
                                                                                              drug.default_dose ??
                                                                                              r.dose,
                                                                                          frequency:
                                                                                              drug.default_frequency ??
                                                                                              r.frequency,
                                                                                          duration:
                                                                                              drug.default_duration ??
                                                                                              r.duration,
                                                                                          route:
                                                                                              drug.default_route ??
                                                                                              r.route,
                                                                                      },
                                                                        ),
                                                                );
                                                            }}
                                                        />
                                                    )}
                                                </td>
                                                <td className="px-2 py-1.5">
                                                    <input
                                                        disabled={isFinalized}
                                                        value={row.dose ?? ''}
                                                        onChange={(e) =>
                                                            updateRx(
                                                                idx,
                                                                'dose',
                                                                e.target.value,
                                                            )
                                                        }
                                                        placeholder="500mg"
                                                        className={tableInputClass(
                                                            isFinalized,
                                                        )}
                                                    />
                                                </td>
                                                <td className="px-2 py-1.5">
                                                    <input
                                                        disabled={isFinalized}
                                                        value={
                                                            row.frequency ?? ''
                                                        }
                                                        onChange={(e) =>
                                                            updateRx(
                                                                idx,
                                                                'frequency',
                                                                e.target.value,
                                                            )
                                                        }
                                                        placeholder="TDS"
                                                        className={tableInputClass(
                                                            isFinalized,
                                                        )}
                                                    />
                                                </td>
                                                <td className="px-2 py-1.5">
                                                    <input
                                                        disabled={isFinalized}
                                                        value={
                                                            row.duration ?? ''
                                                        }
                                                        onChange={(e) =>
                                                            updateRx(
                                                                idx,
                                                                'duration',
                                                                e.target.value,
                                                            )
                                                        }
                                                        placeholder="5 days"
                                                        className={tableInputClass(
                                                            isFinalized,
                                                        )}
                                                    />
                                                </td>
                                                <td className="px-2 py-1.5">
                                                    <input
                                                        disabled={isFinalized}
                                                        value={row.route ?? ''}
                                                        onChange={(e) =>
                                                            updateRx(
                                                                idx,
                                                                'route',
                                                                e.target.value,
                                                            )
                                                        }
                                                        placeholder="Oral"
                                                        className={tableInputClass(
                                                            isFinalized,
                                                        )}
                                                    />
                                                </td>
                                                <td className="px-2 py-1.5">
                                                    <input
                                                        disabled={isFinalized}
                                                        value={
                                                            row.instructions ??
                                                            ''
                                                        }
                                                        onChange={(e) =>
                                                            updateRx(
                                                                idx,
                                                                'instructions',
                                                                e.target.value,
                                                            )
                                                        }
                                                        placeholder="After meals"
                                                        className={tableInputClass(
                                                            isFinalized,
                                                        )}
                                                    />
                                                </td>
                                                <td className="px-2 py-1.5">
                                                    {!isFinalized &&
                                                        prescriptions.length >
                                                            1 && (
                                                            <button
                                                                type="button"
                                                                onClick={() =>
                                                                    setPrescriptions(
                                                                        (p) =>
                                                                            p.filter(
                                                                                (
                                                                                    _,
                                                                                    i,
                                                                                ) =>
                                                                                    i !==
                                                                                    idx,
                                                                            ),
                                                                    )
                                                                }
                                                                className="text-slate-400 transition-colors hover:text-red-500"
                                                            >
                                                                <Trash2 className="h-4 w-4" />
                                                            </button>
                                                        )}
                                                </td>
                                            </tr>
                                        ))}
                                    </tbody>
                                </table>
                            </div>
                            {!isFinalized && (
                                <button
                                    type="button"
                                    onClick={() =>
                                        setPrescriptions((p) => [
                                            ...p,
                                            blankRx(),
                                        ])
                                    }
                                    className="mt-2 flex items-center gap-1.5 rounded-lg border border-dashed border-slate-300 px-3 py-2 text-xs font-medium text-slate-500 transition-colors hover:border-slate-400 hover:text-slate-700"
                                >
                                    <Plus className="h-3.5 w-3.5" /> Add Drug
                                </button>
                            )}
                        </FormSection>
                    )}

                    {showFollowUp && (
                        <FormSection
                            icon={
                                <Calendar className="h-4 w-4 text-orange-500" />
                            }
                            title="Follow-up & Outcome"
                        >
                            <div className="grid grid-cols-1 gap-3 sm:grid-cols-3">
                                <div>
                                    <label className="mb-1 block text-xs font-medium text-slate-500">
                                        Follow-up Date
                                    </label>
                                    <input
                                        disabled={isFinalized}
                                        type="date"
                                        value={followUpDate}
                                        onChange={(e) =>
                                            setFollowUpDate(e.target.value)
                                        }
                                        className={inputClass(isFinalized)}
                                    />
                                </div>
                                <div>
                                    <label className="mb-1 block text-xs font-medium text-slate-500">
                                        Outcome
                                    </label>
                                    <select
                                        disabled={isFinalized}
                                        value={outcome}
                                        onChange={(e) =>
                                            setOutcome(e.target.value)
                                        }
                                        className={inputClass(isFinalized)}
                                    >
                                        <option value="">Select outcome</option>
                                        <option value="improved">
                                            Improved
                                        </option>
                                        <option value="unchanged">
                                            Unchanged
                                        </option>
                                        <option value="deteriorated">
                                            Deteriorated
                                        </option>
                                        <option value="referred">
                                            Referred
                                        </option>
                                        <option value="expired">Expired</option>
                                    </select>
                                </div>
                                <div>
                                    <label className="mb-1 block text-xs font-medium text-slate-500">
                                        Referred To
                                    </label>
                                    <input
                                        disabled={isFinalized}
                                        value={referralTo}
                                        onChange={(e) =>
                                            setReferralTo(e.target.value)
                                        }
                                        placeholder="Department / Hospital"
                                        className={inputClass(isFinalized)}
                                    />
                                </div>
                            </div>
                        </FormSection>
                    )}

                    {/* Bottom Save Bar */}
                    {!isFinalized && (
                        <div className="flex items-center justify-end gap-3 rounded-2xl border border-slate-200 bg-white px-4 py-3 shadow-sm">
                            <button
                                type="button"
                                disabled={saving}
                                onClick={() => save(false)}
                                className="flex items-center gap-1.5 rounded-lg border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50 disabled:opacity-50"
                            >
                                <Save className="h-4 w-4" />{' '}
                                {saving ? 'Saving…' : 'Save Draft'}
                            </button>
                            <button
                                type="button"
                                disabled={finalizing}
                                onClick={() => save(true)}
                                className={clsx(
                                    'flex items-center gap-1.5 rounded-lg px-4 py-2 text-sm font-semibold text-white disabled:opacity-50',
                                    accentColor,
                                    'hover:opacity-90',
                                )}
                            >
                                <CheckCircle className="h-4 w-4" />{' '}
                                {finalizing ? 'Finalizing…' : 'Finalize Record'}
                            </button>
                        </div>
                    )}
                </div>
            </div>
        </div>
    );
}
