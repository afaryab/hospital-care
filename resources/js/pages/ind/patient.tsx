import { Button } from '@/components/ui/button';
import Icd10Picker from '@/components/ui/icd10-picker';
import AppLayout from '@/layouts/app-layout';
import {
    apiIndAssignBed,
    apiIndDischarge,
    apiIndSaveTreatment,
    indDashboard,
    indPatient,
} from '@/routes';
import { type BreadcrumbItem } from '@/types';
import { Head, usePage } from '@inertiajs/react';
import { clsx } from 'clsx';
import {
    Activity,
    AlertCircle,
    ArrowLeft,
    BedDouble,
    Calendar,
    CheckCircle,
    ChevronDown,
    ChevronUp,
    ClipboardList,
    FileText,
    Heart,
    History,
    Lock,
    Plus,
    Save,
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

interface PatientData {
    id: number;
    name: string;
    ps_number: string;
    gender: 'm' | 'f' | 't' | 'o';
    age_days?: number;
    age_dob?: string;
    contact?: string;
}

interface BedAssignmentData {
    id: number;
    status: string;
    admitted_at: string;
    discharged_at?: string;
    notes?: string;
    bed?: {
        id: number;
        bed_number: string;
        room?: {
            id: number;
            name: string;
            ward?: { id: number; name: string };
        };
    };
    ward?: { id: number; name: string };
    room?: { id: number; name: string };
}

interface VitalSign {
    id?: number;
    temperature?: number | string;
    bp_systolic?: number | string;
    bp_diastolic?: number | string;
    pulse_rate?: number | string;
    respiratory_rate?: number | string;
    oxygen_saturation?: number | string;
    weight?: number | string;
    height?: number | string;
    recorded_at?: string;
}

interface Prescription {
    drug_name: string;
    dose: string;
    frequency: string;
    duration: string;
    route: string;
    instructions: string;
}

interface TreatmentRecordData {
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
    department_specific_data?: Record<string, string>;
    is_finalized?: boolean;
    treated_at?: string;
    vital_signs?: VitalSign[];
}

interface AvailableBed {
    id: number;
    bed_number: string;
    room_id: number;
    ward_id: number;
    room?: { id: number; name: string; ward?: { id: number; name: string } };
}

interface ServiceOrderData {
    id: number;
    so_number: string;
    so_short: string;
    type: string;
    status: string;
    token?: string | number;
    created_at?: string;
    doctor?: { id: number; name: string };
    service?: { id: number; name: string };
    patient?: PatientData;
    treatment_record?: TreatmentRecordData | null;
}

interface PreviousVisit {
    id: number;
    so_number: string;
    status: string;
    created_at: string;
    treatment_record?: {
        chief_complaint?: string;
        diagnosis_text?: string;
        is_finalized: boolean;
    } | null;
}

interface IndPatientProps {
    serviceOrder: ServiceOrderData;
    bedAssignment: BedAssignmentData | null;
    availableBeds: AvailableBed[];
    previousVisits: PreviousVisit[];
    [key: string]: unknown;
}

// ─── Helpers ──────────────────────────────────────────────────────────────────

function ageDisplay(patient?: PatientData): string {
    if (!patient) return '—';
    if (patient.age_dob) {
        const years = Math.floor(
            (Date.now() - new Date(patient.age_dob).getTime()) / 31557600000,
        );
        return `${years} yrs`;
    }
    if (patient.age_days) {
        if (patient.age_days >= 365)
            return `${Math.floor(patient.age_days / 365)} yrs`;
        if (patient.age_days >= 30)
            return `${Math.floor(patient.age_days / 30)} mo`;
        return `${patient.age_days} days`;
    }
    return '—';
}

function genderLabel(g?: string) {
    return g === 'm'
        ? 'Male'
        : g === 'f'
          ? 'Female'
          : g === 't'
            ? 'Transgender'
            : 'Other';
}

function formatDate(d?: string): string {
    if (!d) return '—';
    return new Date(d).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
    });
}

function formatDateTime(d?: string): string {
    if (!d) return '—';
    return new Date(d).toLocaleString('en-US', {
        month: 'short',
        day: 'numeric',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
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

function blankPrescription(): Prescription {
    return {
        drug_name: '',
        dose: '',
        frequency: '',
        duration: '',
        route: '',
        instructions: '',
    };
}

function getCsrfToken(): string {
    return decodeURIComponent(
        document.cookie.split('XSRF-TOKEN=')[1]?.split(';')[0] ?? '',
    );
}

const EXAM_SYSTEMS = [
    'General',
    'Cardiovascular',
    'Respiratory',
    'Abdomen',
    'CNS',
    'Musculoskeletal',
    'Other',
];
const IND_SPECIFIC_FIELDS = [
    'Admission Type',
    'Surgeon',
    'Anaesthetist',
    'Procedure',
    'Blood Group',
    'Allergies',
];

// ─── Main Component ───────────────────────────────────────────────────────────

export default function IndPatient() {
    const { serviceOrder, bedAssignment, availableBeds, previousVisits } =
        usePage<IndPatientProps>().props;
    const patient = serviceOrder.patient;
    const existingRecord = serviceOrder.treatment_record;
    const isFinalized = existingRecord?.is_finalized ?? false;

    // Treatment form state
    const [chiefComplaint, setChiefComplaint] = useState(
        existingRecord?.chief_complaint ?? '',
    );
    const [hpi, setHpi] = useState(
        existingRecord?.history_of_present_illness ?? '',
    );
    const [diagnosisCode, setDiagnosisCode] = useState(
        existingRecord?.diagnosis_code ?? '',
    );
    const [icd10CodeId, setIcd10CodeId] = useState<number | null>(
        existingRecord?.icd10_code_id ?? null,
    );
    const [diagnosisText, setDiagnosisText] = useState(
        existingRecord?.diagnosis_text ?? '',
    );
    const [treatmentPlan, setTreatmentPlan] = useState(
        existingRecord?.treatment_plan ?? '',
    );
    const [followUpDate, setFollowUpDate] = useState(
        existingRecord?.follow_up_date ?? '',
    );
    const [outcome, setOutcome] = useState(existingRecord?.outcome ?? '');
    const [referralTo, setReferralTo] = useState(
        existingRecord?.referral_to ?? '',
    );
    const [examFindings, setExamFindings] = useState<Record<string, string>>(
        existingRecord?.examination_findings ?? {},
    );
    const [indSpecificData, setIndSpecificData] = useState<
        Record<string, string>
    >(existingRecord?.department_specific_data ?? {});
    const [prescriptions, setPrescriptions] = useState<Prescription[]>(
        existingRecord?.prescriptions?.length
            ? existingRecord.prescriptions
            : [blankPrescription()],
    );

    // Vitals
    const lastVital =
        existingRecord?.vital_signs?.[existingRecord.vital_signs.length - 1] ??
        {};
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

    // Bed assignment state
    const [showBedModal, setShowBedModal] = useState(false);
    const [selectedBedId, setSelectedBedId] = useState<number | ''>('');
    const [assigning, setAssigning] = useState(false);
    const [discharging, setDischarging] = useState(false);
    const [currentAssignment, setCurrentAssignment] =
        useState<BedAssignmentData | null>(bedAssignment);
    const availableBedOptions = (availableBeds ?? []).map((b) => ({
        id: b.id,
        label: `${b.room?.ward?.name ?? 'Ward'} → ${b.room?.name ?? 'Room'} → Bed ${b.bed_number}`,
    }));

    // UI state
    const [saving, setSaving] = useState(false);
    const [finalizing, setFinalizing] = useState(false);
    const [showHistory, setShowHistory] = useState(false);

    const breadcrumbs: BreadcrumbItem[] = [
        { title: 'Dashboard', href: '/' },
        { title: 'Indoor', href: indDashboard().url },
        {
            title: patient?.name ?? 'Patient',
            href: indPatient({ id: serviceOrder.id }).url,
        },
    ];

    const buildPayload = useCallback(
        (finalize = false) => ({
            chief_complaint: chiefComplaint,
            history_of_present_illness: hpi,
            examination_findings: examFindings,
            diagnosis_code: diagnosisCode,
            icd10_code_id: icd10CodeId,
            diagnosis_text: diagnosisText,
            treatment_plan: treatmentPlan,
            prescriptions: prescriptions.filter((p) => p.drug_name.trim()),
            follow_up_date: followUpDate || null,
            outcome: outcome || null,
            referral_to: referralTo || null,
            department_specific_data: indSpecificData,
            vitals: Object.values(vitals).some((v) => v !== '') ? vitals : null,
            finalize,
        }),
        [
            chiefComplaint,
            hpi,
            examFindings,
            diagnosisCode,
            diagnosisText,
            treatmentPlan,
            prescriptions,
            followUpDate,
            outcome,
            referralTo,
            indSpecificData,
            vitals,
        ],
    );

    const save = useCallback(
        async (finalize = false) => {
            finalize ? setFinalizing(true) : setSaving(true);
            try {
                const res = await fetch(
                    apiIndSaveTreatment({ serviceOrder: serviceOrder.id }).url,
                    {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            Accept: 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-XSRF-TOKEN': getCsrfToken(),
                        },
                        body: JSON.stringify(buildPayload(finalize)),
                    },
                );
                const json = await res.json();
                if (!res.ok) {
                    toast.error(json.message ?? 'Failed to save');
                    return;
                }
                toast.success(finalize ? 'Record finalized' : 'Draft saved');
                if (finalize) window.location.reload();
            } catch {
                toast.error('Network error');
            } finally {
                setSaving(false);
                setFinalizing(false);
            }
        },
        [buildPayload, serviceOrder.id],
    );

    const assignBed = async () => {
        if (!selectedBedId) return;
        setAssigning(true);
        try {
            const res = await fetch(
                apiIndAssignBed({ serviceOrder: serviceOrder.id }).url,
                {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        Accept: 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-XSRF-TOKEN': getCsrfToken(),
                    },
                    body: JSON.stringify({ bed_id: selectedBedId }),
                },
            );
            const json = await res.json();
            if (!res.ok) {
                toast.error(json.message ?? 'Failed to assign bed');
                return;
            }
            toast.success(json.message ?? 'Bed assigned');
            setCurrentAssignment(json.data);
            setShowBedModal(false);
            setSelectedBedId('');
        } catch {
            toast.error('Network error');
        } finally {
            setAssigning(false);
        }
    };

    const discharge = async () => {
        if (!confirm('Confirm discharge this patient?')) return;
        setDischarging(true);
        try {
            const res = await fetch(
                apiIndDischarge({ serviceOrder: serviceOrder.id }).url,
                {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        Accept: 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-XSRF-TOKEN': getCsrfToken(),
                    },
                    body: '{}',
                },
            );
            const json = await res.json();
            if (!res.ok) {
                toast.error(json.message ?? 'Failed to discharge');
                return;
            }
            toast.success('Patient discharged from bed');
            setCurrentAssignment(null);
        } catch {
            toast.error('Network error');
        } finally {
            setDischarging(false);
        }
    };

    const addPrescription = () =>
        setPrescriptions((p) => [...p, blankPrescription()]);
    const removePrescription = (idx: number) =>
        setPrescriptions((p) => p.filter((_, i) => i !== idx));
    const updatePrescription = (
        idx: number,
        field: keyof Prescription,
        value: string,
    ) =>
        setPrescriptions((p) =>
            p.map((row, i) => (i === idx ? { ...row, [field]: value } : row)),
        );

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title={`Indoor — ${patient?.name ?? 'Patient'}`} />

            <div className="min-h-full bg-gradient-to-br from-indigo-50 via-white to-blue-50">
                {/* ── Sticky Header ─────────────────────────────────────── */}
                <div className="sticky top-0 z-10 border-b border-indigo-100 bg-white shadow-sm">
                    <div className="mx-auto max-w-5xl px-4 py-3 md:px-6">
                        <div className="flex flex-wrap items-center justify-between gap-3">
                            <div className="flex items-center gap-3">
                                <a
                                    href={indDashboard().url}
                                    className="flex h-8 w-8 items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-600 hover:bg-slate-50"
                                >
                                    <ArrowLeft className="h-4 w-4" />
                                </a>
                                <div className="flex h-10 w-10 items-center justify-center rounded-full bg-indigo-600 text-base font-bold text-white shadow-sm">
                                    {patient?.name?.charAt(0)?.toUpperCase() ??
                                        '?'}
                                </div>
                                <div>
                                    <div className="flex flex-wrap items-center gap-2">
                                        <h1 className="text-base font-bold text-slate-900 md:text-lg">
                                            {patient?.name}
                                        </h1>
                                        <span
                                            className={clsx(
                                                'rounded-full px-2 py-0.5 text-xs font-semibold',
                                                statusColor(
                                                    serviceOrder.status,
                                                ),
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
                                    <p className="text-xs text-slate-500">
                                        {patient?.ps_number} &bull;{' '}
                                        {ageDisplay(patient)} &bull;{' '}
                                        {genderLabel(patient?.gender)}
                                        {patient?.contact &&
                                            ` · ${patient.contact}`}
                                    </p>
                                </div>
                            </div>
                            <div className="flex flex-wrap items-center gap-2">
                                <button
                                    type="button"
                                    onClick={() => setShowHistory((v) => !v)}
                                    className="flex items-center gap-1.5 rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-xs font-medium text-slate-600 hover:bg-slate-50"
                                >
                                    <History className="h-3.5 w-3.5" />
                                    History ({previousVisits.length})
                                </button>
                                {!isFinalized && (
                                    <>
                                        <Button
                                            size="sm"
                                            variant="outline"
                                            disabled={saving}
                                            onClick={() => save(false)}
                                            className="gap-1.5 text-xs"
                                        >
                                            <Save className="h-3.5 w-3.5" />{' '}
                                            {saving ? 'Saving…' : 'Save Draft'}
                                        </Button>
                                        <Button
                                            size="sm"
                                            disabled={finalizing}
                                            onClick={() => save(true)}
                                            className="gap-1.5 bg-indigo-600 text-xs text-white hover:bg-indigo-700"
                                        >
                                            <CheckCircle className="h-3.5 w-3.5" />{' '}
                                            {finalizing
                                                ? 'Finalizing…'
                                                : 'Finalize'}
                                        </Button>
                                    </>
                                )}
                            </div>
                        </div>
                    </div>
                </div>

                <div className="mx-auto max-w-5xl space-y-4 px-4 py-4 pb-10 md:px-6">
                    {/* Finalized Banner */}
                    {isFinalized && (
                        <div className="flex items-center gap-3 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3">
                            <Lock className="h-5 w-5 text-emerald-600" />
                            <p className="text-sm font-medium text-emerald-800">
                                This record has been finalized and is read-only.
                            </p>
                        </div>
                    )}

                    {/* History Panel */}
                    {showHistory && (
                        <div className="rounded-2xl border border-slate-200 bg-white shadow-sm">
                            <div className="flex items-center justify-between border-b border-slate-100 px-4 py-3">
                                <h3 className="flex items-center gap-2 text-sm font-semibold text-slate-900">
                                    <History className="h-4 w-4 text-slate-500" />{' '}
                                    Previous Indoor Admissions
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
                                    No previous IND admissions.
                                </p>
                            ) : (
                                <div className="divide-y divide-slate-100">
                                    {previousVisits.map((v) => (
                                        <div
                                            key={v.id}
                                            className="flex items-start justify-between px-4 py-3"
                                        >
                                            <div>
                                                <p className="text-xs font-semibold text-slate-800">
                                                    {v.so_number}
                                                </p>
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

                    {/* ── SO + Bed Info Strip ─────────────────────────────── */}
                    <div className="grid grid-cols-2 gap-3 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:grid-cols-4 md:grid-cols-6">
                        <InfoCell
                            label="SO Number"
                            value={serviceOrder.so_number}
                        />
                        <InfoCell
                            label="Token"
                            value={
                                serviceOrder.token
                                    ? `#${serviceOrder.token}`
                                    : serviceOrder.so_short
                            }
                        />
                        <InfoCell
                            label="Doctor"
                            value={serviceOrder.doctor?.name ?? '—'}
                        />
                        <InfoCell
                            label="Admitted"
                            value={
                                currentAssignment
                                    ? formatDate(currentAssignment.admitted_at)
                                    : '—'
                            }
                        />
                        <div className="col-span-2">
                            <p className="text-xs text-slate-500">
                                Bed Assignment
                            </p>
                            {currentAssignment ? (
                                <div className="flex items-center gap-2">
                                    <span className="mt-0.5 inline-flex items-center gap-1 rounded-lg bg-indigo-50 px-2 py-1 text-xs font-semibold text-indigo-800 ring-1 ring-indigo-200">
                                        <BedDouble className="h-3 w-3" />
                                        {currentAssignment.ward?.name} /{' '}
                                        {currentAssignment.room?.name} / Bed{' '}
                                        {currentAssignment.bed?.bed_number}
                                    </span>
                                    {!isFinalized && (
                                        <button
                                            type="button"
                                            disabled={discharging}
                                            onClick={discharge}
                                            className="text-xs text-red-500 underline hover:text-red-700 disabled:opacity-50"
                                        >
                                            {discharging
                                                ? 'Discharging…'
                                                : 'Discharge'}
                                        </button>
                                    )}
                                </div>
                            ) : (
                                <div className="mt-0.5 flex items-center gap-2">
                                    <span className="rounded-lg bg-amber-50 px-2 py-1 text-xs font-medium text-amber-700 ring-1 ring-amber-200">
                                        Not Assigned
                                    </span>
                                    {!isFinalized && (
                                        <button
                                            type="button"
                                            onClick={() =>
                                                setShowBedModal(true)
                                            }
                                            className="text-xs text-indigo-600 underline hover:text-indigo-800"
                                        >
                                            Assign Bed
                                        </button>
                                    )}
                                </div>
                            )}
                        </div>
                    </div>

                    {/* ── IND-specific Details ────────────────────────────── */}
                    <FormSection
                        icon={
                            <ClipboardList className="h-4 w-4 text-indigo-500" />
                        }
                        title="Admission Details"
                    >
                        <div className="grid grid-cols-2 gap-3 sm:grid-cols-3">
                            {IND_SPECIFIC_FIELDS.map((field) => (
                                <div key={field}>
                                    <label className="mb-1 block text-xs font-medium text-slate-500">
                                        {field}
                                    </label>
                                    <input
                                        disabled={isFinalized}
                                        value={indSpecificData[field] ?? ''}
                                        onChange={(e) =>
                                            setIndSpecificData((d) => ({
                                                ...d,
                                                [field]: e.target.value,
                                            }))
                                        }
                                        placeholder={`Enter ${field.toLowerCase()}…`}
                                        className={inputClass(isFinalized)}
                                    />
                                </div>
                            ))}
                        </div>
                    </FormSection>

                    {/* Chief Complaint */}
                    <FormSection
                        icon={<AlertCircle className="h-4 w-4 text-red-500" />}
                        title="Chief Complaint / Presenting Problem"
                    >
                        <textarea
                            disabled={isFinalized}
                            value={chiefComplaint}
                            onChange={(e) => setChiefComplaint(e.target.value)}
                            rows={2}
                            placeholder="Primary reason for admission…"
                            className={textareaClass(isFinalized)}
                        />
                    </FormSection>

                    {/* HPI */}
                    <FormSection
                        icon={<FileText className="h-4 w-4 text-slate-500" />}
                        title="History of Present Illness"
                    >
                        <textarea
                            disabled={isFinalized}
                            value={hpi}
                            onChange={(e) => setHpi(e.target.value)}
                            rows={4}
                            placeholder="Detailed history, onset, progression, relevant past medical/surgical history…"
                            className={textareaClass(isFinalized)}
                        />
                    </FormSection>

                    {/* Vitals */}
                    <FormSection
                        icon={<Heart className="h-4 w-4 text-rose-500" />}
                        title="Vital Signs"
                    >
                        <div className="grid grid-cols-2 gap-3 sm:grid-cols-4">
                            <VitalInput
                                disabled={isFinalized}
                                label="Temp (°C)"
                                placeholder="37.0"
                                icon={<Thermometer className="h-3.5 w-3.5" />}
                                value={String(vitals.temperature)}
                                onChange={(v) =>
                                    setVitals((s) => ({ ...s, temperature: v }))
                                }
                            />
                            <VitalInput
                                disabled={isFinalized}
                                label="BP Systolic"
                                placeholder="120"
                                icon={<Heart className="h-3.5 w-3.5" />}
                                value={String(vitals.bp_systolic)}
                                onChange={(v) =>
                                    setVitals((s) => ({ ...s, bp_systolic: v }))
                                }
                            />
                            <VitalInput
                                disabled={isFinalized}
                                label="BP Diastolic"
                                placeholder="80"
                                icon={<Heart className="h-3.5 w-3.5" />}
                                value={String(vitals.bp_diastolic)}
                                onChange={(v) =>
                                    setVitals((s) => ({
                                        ...s,
                                        bp_diastolic: v,
                                    }))
                                }
                            />
                            <VitalInput
                                disabled={isFinalized}
                                label="Pulse (bpm)"
                                placeholder="72"
                                icon={<Activity className="h-3.5 w-3.5" />}
                                value={String(vitals.pulse_rate)}
                                onChange={(v) =>
                                    setVitals((s) => ({ ...s, pulse_rate: v }))
                                }
                            />
                            <VitalInput
                                disabled={isFinalized}
                                label="Resp. Rate"
                                placeholder="16"
                                icon={<Wind className="h-3.5 w-3.5" />}
                                value={String(vitals.respiratory_rate)}
                                onChange={(v) =>
                                    setVitals((s) => ({
                                        ...s,
                                        respiratory_rate: v,
                                    }))
                                }
                            />
                            <VitalInput
                                disabled={isFinalized}
                                label="SpO2 (%)"
                                placeholder="98"
                                icon={<Activity className="h-3.5 w-3.5" />}
                                value={String(vitals.oxygen_saturation)}
                                onChange={(v) =>
                                    setVitals((s) => ({
                                        ...s,
                                        oxygen_saturation: v,
                                    }))
                                }
                            />
                            <VitalInput
                                disabled={isFinalized}
                                label="Weight (kg)"
                                placeholder="70"
                                icon={<User className="h-3.5 w-3.5" />}
                                value={String(vitals.weight)}
                                onChange={(v) =>
                                    setVitals((s) => ({ ...s, weight: v }))
                                }
                            />
                            <VitalInput
                                disabled={isFinalized}
                                label="Height (cm)"
                                placeholder="170"
                                icon={<User className="h-3.5 w-3.5" />}
                                value={String(vitals.height)}
                                onChange={(v) =>
                                    setVitals((s) => ({ ...s, height: v }))
                                }
                            />
                        </div>
                    </FormSection>

                    {/* Examination Findings */}
                    <FormSection
                        icon={
                            <Stethoscope className="h-4 w-4 text-indigo-600" />
                        }
                        title="Examination Findings"
                    >
                        <div className="grid grid-cols-1 gap-3 sm:grid-cols-2">
                            {EXAM_SYSTEMS.map((sys) => (
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

                    {/* Diagnosis */}
                    <FormSection
                        icon={<FileText className="h-4 w-4 text-violet-500" />}
                        title="Diagnosis"
                    >
                        <div className="grid grid-cols-1 gap-3 sm:grid-cols-4">
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
                            <div className="sm:col-span-3">
                                <label className="mb-1 block text-xs font-medium text-slate-500">
                                    Diagnosis
                                </label>
                                <input
                                    disabled={isFinalized}
                                    value={diagnosisText}
                                    onChange={(e) =>
                                        setDiagnosisText(e.target.value)
                                    }
                                    placeholder="Auto-filled from ICD-10 selection or type manually"
                                    className={inputClass(isFinalized)}
                                />
                            </div>
                        </div>
                    </FormSection>

                    {/* Prescription */}
                    <FormSection
                        icon={<FileText className="h-4 w-4 text-emerald-600" />}
                        title="Medication Orders / Prescription"
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
                                                <input
                                                    disabled={isFinalized}
                                                    value={row.drug_name}
                                                    onChange={(e) =>
                                                        updatePrescription(
                                                            idx,
                                                            'drug_name',
                                                            e.target.value,
                                                        )
                                                    }
                                                    placeholder="Drug name"
                                                    className={tableInputClass(
                                                        isFinalized,
                                                    )}
                                                />
                                            </td>
                                            <td className="px-2 py-1.5">
                                                <input
                                                    disabled={isFinalized}
                                                    value={row.dose}
                                                    onChange={(e) =>
                                                        updatePrescription(
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
                                                    value={row.frequency}
                                                    onChange={(e) =>
                                                        updatePrescription(
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
                                                    value={row.duration}
                                                    onChange={(e) =>
                                                        updatePrescription(
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
                                                    value={row.route}
                                                    onChange={(e) =>
                                                        updatePrescription(
                                                            idx,
                                                            'route',
                                                            e.target.value,
                                                        )
                                                    }
                                                    placeholder="Oral/IV/IM"
                                                    className={tableInputClass(
                                                        isFinalized,
                                                    )}
                                                />
                                            </td>
                                            <td className="px-2 py-1.5">
                                                <input
                                                    disabled={isFinalized}
                                                    value={row.instructions}
                                                    onChange={(e) =>
                                                        updatePrescription(
                                                            idx,
                                                            'instructions',
                                                            e.target.value,
                                                        )
                                                    }
                                                    placeholder="After meals…"
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
                                                                removePrescription(
                                                                    idx,
                                                                )
                                                            }
                                                            className="text-slate-400 hover:text-red-500"
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
                                onClick={addPrescription}
                                className="mt-2 flex items-center gap-1.5 rounded-lg border border-dashed border-slate-300 px-3 py-2 text-xs font-medium text-slate-500 hover:border-indigo-400 hover:text-indigo-600"
                            >
                                <Plus className="h-3.5 w-3.5" /> Add Drug
                            </button>
                        )}
                    </FormSection>

                    {/* Treatment Plan */}
                    <FormSection
                        icon={
                            <ClipboardList className="h-4 w-4 text-orange-500" />
                        }
                        title="Treatment Plan / Management Notes"
                    >
                        <textarea
                            disabled={isFinalized}
                            value={treatmentPlan}
                            onChange={(e) => setTreatmentPlan(e.target.value)}
                            rows={4}
                            placeholder="Surgical plan, investigations ordered, consults, nursing orders, diet, activity restrictions…"
                            className={textareaClass(isFinalized)}
                        />
                    </FormSection>

                    {/* Outcome / Discharge Planning */}
                    <FormSection
                        icon={<Calendar className="h-4 w-4 text-blue-500" />}
                        title="Discharge Planning & Outcome"
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
                                    onChange={(e) => setOutcome(e.target.value)}
                                    className={inputClass(isFinalized)}
                                >
                                    <option value="">Select outcome</option>
                                    <option value="improved">Improved</option>
                                    <option value="unchanged">Unchanged</option>
                                    <option value="deteriorated">
                                        Deteriorated
                                    </option>
                                    <option value="referred">Referred</option>
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

                    {/* Bottom Save Bar */}
                    {!isFinalized && (
                        <div className="flex items-center justify-end gap-3 rounded-2xl border border-slate-200 bg-white px-4 py-3 shadow-sm">
                            <Button
                                variant="outline"
                                disabled={saving}
                                onClick={() => save(false)}
                                className="gap-1.5"
                            >
                                <Save className="h-4 w-4" />{' '}
                                {saving ? 'Saving…' : 'Save Draft'}
                            </Button>
                            <Button
                                disabled={finalizing}
                                onClick={() => save(true)}
                                className="gap-1.5 bg-indigo-600 text-white hover:bg-indigo-700"
                            >
                                <CheckCircle className="h-4 w-4" />{' '}
                                {finalizing
                                    ? 'Finalizing…'
                                    : 'Finalize & Discharge Plan'}
                            </Button>
                        </div>
                    )}
                </div>
            </div>

            {/* Bed Assignment Modal */}
            {showBedModal && (
                <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4">
                    <div className="w-full max-w-sm rounded-2xl bg-white shadow-2xl">
                        <div className="flex items-center justify-between border-b border-slate-100 px-5 py-4">
                            <h3 className="text-sm font-semibold text-slate-900">
                                Assign Bed
                            </h3>
                            <button
                                type="button"
                                onClick={() => {
                                    setShowBedModal(false);
                                    setSelectedBedId('');
                                }}
                                className="text-slate-400 hover:text-slate-600"
                            >
                                <X className="h-4 w-4" />
                            </button>
                        </div>
                        <div className="p-5">
                            <div className="mb-3 rounded-xl bg-slate-50 px-4 py-3">
                                <p className="text-sm font-semibold text-slate-900">
                                    {patient?.name}
                                </p>
                                <p className="text-xs text-slate-500">
                                    {serviceOrder.so_number}
                                </p>
                            </div>
                            <label className="mb-1 block text-xs font-medium text-slate-600">
                                Select Available Bed
                            </label>
                            <select
                                value={selectedBedId}
                                onChange={(e) =>
                                    setSelectedBedId(
                                        Number(e.target.value) || '',
                                    )
                                }
                                className="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-800 focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 focus:outline-none"
                            >
                                <option value="">— Choose a bed —</option>
                                {availableBedOptions.map((b) => (
                                    <option key={b.id} value={b.id}>
                                        {b.label}
                                    </option>
                                ))}
                            </select>
                            {availableBedOptions.length === 0 && (
                                <p className="mt-2 text-xs text-amber-600">
                                    No available beds right now.
                                </p>
                            )}
                        </div>
                        <div className="flex justify-end gap-2 border-t border-slate-100 px-5 py-3">
                            <button
                                type="button"
                                onClick={() => {
                                    setShowBedModal(false);
                                    setSelectedBedId('');
                                }}
                                className="rounded-lg border border-slate-200 px-4 py-2 text-xs font-medium text-slate-600 hover:bg-slate-50"
                            >
                                Cancel
                            </button>
                            <button
                                type="button"
                                disabled={!selectedBedId || assigning}
                                onClick={assignBed}
                                className="rounded-lg bg-indigo-600 px-4 py-2 text-xs font-semibold text-white hover:bg-indigo-700 disabled:opacity-50"
                            >
                                {assigning ? 'Assigning…' : 'Assign Bed'}
                            </button>
                        </div>
                    </div>
                </div>
            )}
        </AppLayout>
    );
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

function VitalInput({
    label,
    placeholder,
    icon,
    value,
    onChange,
    disabled,
}: {
    label: string;
    placeholder: string;
    icon: React.ReactNode;
    value: string;
    onChange: (v: string) => void;
    disabled: boolean;
}) {
    return (
        <div>
            <label className="mb-1 flex items-center gap-1 text-xs font-medium text-slate-500">
                {icon} {label}
            </label>
            <input
                disabled={disabled}
                type="number"
                step="any"
                value={value}
                onChange={(e) => onChange(e.target.value)}
                placeholder={placeholder}
                className={inputClass(disabled)}
            />
        </div>
    );
}

// ─── Style helpers ────────────────────────────────────────────────────────────

function textareaClass(disabled: boolean) {
    return clsx(
        'w-full resize-y rounded-xl border px-3 py-2.5 text-sm text-slate-800 placeholder:text-slate-400 focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 focus:outline-none',
        disabled
            ? 'cursor-not-allowed border-slate-100 bg-slate-50 text-slate-600'
            : 'border-slate-200 bg-white hover:border-slate-300',
    );
}

function inputClass(disabled: boolean) {
    return clsx(
        'w-full rounded-xl border px-3 py-2 text-sm text-slate-800 placeholder:text-slate-400 focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 focus:outline-none',
        disabled
            ? 'cursor-not-allowed border-slate-100 bg-slate-50 text-slate-600'
            : 'border-slate-200 bg-white hover:border-slate-300',
    );
}

function tableInputClass(disabled: boolean) {
    return clsx(
        'w-full rounded-lg border px-2 py-1.5 text-xs text-slate-800 placeholder:text-slate-400 focus:border-indigo-300 focus:ring-1 focus:ring-indigo-200 focus:outline-none',
        disabled
            ? 'cursor-not-allowed border-transparent bg-transparent text-slate-600'
            : 'border-slate-200 bg-white hover:border-slate-300',
    );
}
