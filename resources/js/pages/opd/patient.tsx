import { Button } from '@/components/ui/button';
import DrugPicker from '@/components/ui/drug-picker';
import Icd10Picker from '@/components/ui/icd10-picker';
import AppLayout from '@/layouts/app-layout';
import { opdDashboard, opdPatient, apiOpdSaveTreatment, apiOpdUpdateStatus } from '@/routes';
import { type BreadcrumbItem } from '@/types';
import { Head, usePage } from '@inertiajs/react';
import { clsx } from 'clsx';
import {
    Activity,
    AlertCircle,
    ArrowLeft,
    Calendar,
    CheckCircle,
    ChevronDown,
    ChevronUp,
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
    is_finalized?: boolean;
    treated_at?: string;
    vital_signs?: VitalSign[];
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

interface OpdPatientProps {
    serviceOrder: ServiceOrderData;
    previousVisits: PreviousVisit[];
    [key: string]: unknown;
}

// ─── Helpers ──────────────────────────────────────────────────────────────────

function ageDisplay(patient?: PatientData): string {
    if (!patient) return '—';
    if (patient.age_dob) {
        const years = Math.floor((Date.now() - new Date(patient.age_dob).getTime()) / 31557600000);
        return `${years} yrs`;
    }
    if (patient.age_days) {
        if (patient.age_days >= 365) return `${Math.floor(patient.age_days / 365)} yrs`;
        if (patient.age_days >= 30) return `${Math.floor(patient.age_days / 30)} mo`;
        return `${patient.age_days} days`;
    }
    return '—';
}

function genderLabel(g?: string) {
    return g === 'm' ? 'Male' : g === 'f' ? 'Female' : g === 't' ? 'Transgender' : 'Other';
}

function formatDate(dateStr?: string): string {
    if (!dateStr) return '—';
    return new Date(dateStr).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
}

function statusColor(status: string) {
    const s = status.toLowerCase();
    if (s === 'in-progress') return 'bg-blue-100 text-blue-700 ring-1 ring-blue-200';
    if (s === 'open') return 'bg-amber-100 text-amber-700 ring-1 ring-amber-200';
    if (s === 'treated' || s === 'closed') return 'bg-emerald-100 text-emerald-700 ring-1 ring-emerald-200';
    return 'bg-slate-100 text-slate-600';
}

function blankPrescription(): Prescription {
    return { drug_name: '', dose: '', frequency: '', duration: '', route: '', instructions: '' };
}

function getCsrfToken(): string {
    return decodeURIComponent(document.cookie.split('XSRF-TOKEN=')[1]?.split(';')[0] ?? '');
}

// ─── Component ────────────────────────────────────────────────────────────────

export default function OpdPatient() {
    const { serviceOrder, previousVisits } = usePage<OpdPatientProps>().props;
    const patient = serviceOrder.patient;
    const existingRecord = serviceOrder.treatment_record;
    const isFinalized = existingRecord?.is_finalized ?? false;

    // Form state — initialise from existing record
    const [chiefComplaint, setChiefComplaint] = useState(existingRecord?.chief_complaint ?? '');
    const [hpi, setHpi] = useState(existingRecord?.history_of_present_illness ?? '');
    const [diagnosisCode, setDiagnosisCode] = useState(existingRecord?.diagnosis_code ?? '');
    const [diagnosisText, setDiagnosisText] = useState(existingRecord?.diagnosis_text ?? '');
    const [icd10CodeId, setIcd10CodeId] = useState<number | null>(existingRecord?.icd10_code_id ?? null);
    const [treatmentPlan, setTreatmentPlan] = useState(existingRecord?.treatment_plan ?? '');
    const [followUpDate, setFollowUpDate] = useState(existingRecord?.follow_up_date ?? '');
    const [outcome, setOutcome] = useState(existingRecord?.outcome ?? '');
    const [referralTo, setReferralTo] = useState(existingRecord?.referral_to ?? '');

    // Vitals
    const lastVital = existingRecord?.vital_signs?.[existingRecord.vital_signs.length - 1] ?? {};
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

    // Examination findings
    const [examFindings, setExamFindings] = useState<Record<string, string>>(
        existingRecord?.examination_findings ?? {},
    );

    // Prescriptions
    const [prescriptions, setPrescriptions] = useState<Prescription[]>(
        existingRecord?.prescriptions && existingRecord.prescriptions.length > 0
            ? existingRecord.prescriptions
            : [blankPrescription()],
    );

    // UI state
    const [saving, setSaving] = useState(false);
    const [finalizing, setFinalizing] = useState(false);
    const [showPreviousVisits, setShowPreviousVisits] = useState(false);

    const breadcrumbs: BreadcrumbItem[] = [
        { title: 'Dashboard', href: '/' },
        { title: 'OPD', href: opdDashboard().url },
        { title: patient?.name ?? 'Patient', href: opdPatient({ id: serviceOrder.id }).url },
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
            vitals: Object.values(vitals).some((v) => v !== '') ? vitals : null,
            finalize,
        }),
        [chiefComplaint, hpi, examFindings, diagnosisCode, diagnosisText, treatmentPlan, prescriptions, followUpDate, outcome, referralTo, vitals],
    );

    const save = useCallback(
        async (finalize = false) => {
            if (finalize) {
                setFinalizing(true);
            } else {
                setSaving(true);
            }

            try {
                const res = await fetch(apiOpdSaveTreatment({ serviceOrder: serviceOrder.id }).url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        Accept: 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-XSRF-TOKEN': getCsrfToken(),
                    },
                    body: JSON.stringify(buildPayload(finalize)),
                });

                const json = await res.json();

                if (!res.ok) {
                    toast.error(json.message ?? 'Failed to save');
                    return;
                }

                toast.success(finalize ? 'Record finalized' : 'Draft saved');

                if (finalize) {
                    // Reload the page to reflect finalized state
                    window.location.reload();
                }
            } catch {
                toast.error('Network error — please try again');
            } finally {
                setSaving(false);
                setFinalizing(false);
            }
        },
        [buildPayload, serviceOrder.id],
    );

    const addPrescription = () => setPrescriptions((p) => [...p, blankPrescription()]);
    const removePrescription = (idx: number) => setPrescriptions((p) => p.filter((_, i) => i !== idx));
    const updatePrescription = (idx: number, field: keyof Prescription, value: string) => {
        setPrescriptions((p) => p.map((row, i) => (i === idx ? { ...row, [field]: value } : row)));
    };

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title={`OPD — ${patient?.name ?? 'Patient'}`} />

            <div className="min-h-full bg-gradient-to-br from-teal-50 via-white to-emerald-50">

                {/* ── Patient Banner ──────────────────────────────────────── */}
                <div className="sticky top-0 z-10 border-b border-teal-100 bg-white shadow-sm">
                    <div className="mx-auto max-w-5xl px-4 py-3 md:px-6">
                        <div className="flex flex-wrap items-center justify-between gap-3">
                            <div className="flex items-center gap-3">
                                <a href={opdDashboard().url} className="flex h-8 w-8 items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-600 transition-colors hover:bg-slate-50">
                                    <ArrowLeft className="h-4 w-4" />
                                </a>
                                <div className="flex h-10 w-10 items-center justify-center rounded-full bg-teal-600 text-base font-bold text-white shadow-sm">
                                    {patient?.name?.charAt(0)?.toUpperCase() ?? '?'}
                                </div>
                                <div>
                                    <div className="flex items-center gap-2">
                                        <h1 className="text-base font-bold text-slate-900 md:text-lg">{patient?.name}</h1>
                                        <span className={clsx('rounded-full px-2 py-0.5 text-xs font-semibold', statusColor(serviceOrder.status))}>
                                            {serviceOrder.status}
                                        </span>
                                        {isFinalized && (
                                            <span className="flex items-center gap-1 rounded-full bg-emerald-50 px-2 py-0.5 text-xs font-semibold text-emerald-700 ring-1 ring-emerald-200">
                                                <Lock className="h-3 w-3" /> Finalized
                                            </span>
                                        )}
                                    </div>
                                    <div className="flex flex-wrap items-center gap-x-2 text-xs text-slate-500">
                                        <span>{patient?.ps_number}</span>
                                        <span>&bull;</span>
                                        <span>{ageDisplay(patient)}</span>
                                        <span>&bull;</span>
                                        <span>{genderLabel(patient?.gender)}</span>
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
                                {/* Previous Visits toggle */}
                                <button
                                    type="button"
                                    onClick={() => setShowPreviousVisits((v) => !v)}
                                    className="flex items-center gap-1.5 rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-xs font-medium text-slate-600 transition-colors hover:bg-slate-50"
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
                                            <Save className="h-3.5 w-3.5" />
                                            {saving ? 'Saving…' : 'Save Draft'}
                                        </Button>
                                        <Button
                                            size="sm"
                                            disabled={finalizing}
                                            onClick={() => save(true)}
                                            className="gap-1.5 bg-teal-600 text-xs text-white hover:bg-teal-700"
                                        >
                                            <CheckCircle className="h-3.5 w-3.5" />
                                            {finalizing ? 'Finalizing…' : 'Finalize'}
                                        </Button>
                                    </>
                                )}
                            </div>
                        </div>
                    </div>
                </div>

                {/* ── Finalized Banner ────────────────────────────────────── */}
                {isFinalized && (
                    <div className="mx-auto max-w-5xl px-4 pt-4 md:px-6">
                        <div className="flex items-center gap-3 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3">
                            <Lock className="h-5 w-5 text-emerald-600" />
                            <p className="text-sm font-medium text-emerald-800">
                                This treatment record has been finalized and is read-only.
                            </p>
                        </div>
                    </div>
                )}

                {/* ── Previous Visits Panel ────────────────────────────────── */}
                {showPreviousVisits && (
                    <div className="mx-auto max-w-5xl px-4 pt-4 md:px-6">
                        <div className="rounded-2xl border border-slate-200 bg-white shadow-sm">
                            <div className="flex items-center justify-between border-b border-slate-100 px-4 py-3">
                                <h3 className="text-sm font-semibold text-slate-900 flex items-center gap-2">
                                    <History className="h-4 w-4 text-slate-500" /> Previous OPD Visits
                                </h3>
                                <button type="button" onClick={() => setShowPreviousVisits(false)} className="text-slate-400 hover:text-slate-600">
                                    <X className="h-4 w-4" />
                                </button>
                            </div>
                            {previousVisits.length === 0 ? (
                                <p className="px-4 py-6 text-center text-sm text-slate-500">No previous OPD visits.</p>
                            ) : (
                                <div className="divide-y divide-slate-100">
                                    {previousVisits.map((v) => (
                                        <div key={v.id} className="flex items-start justify-between px-4 py-3">
                                            <div>
                                                <p className="text-xs font-semibold text-slate-800">{v.so_number}</p>
                                                <p className="mt-0.5 text-xs text-slate-500">{formatDate(v.created_at)}</p>
                                                {v.treatment_record?.diagnosis_text && (
                                                    <p className="mt-1 text-xs text-slate-600">{v.treatment_record.diagnosis_text}</p>
                                                )}
                                                {v.treatment_record?.chief_complaint && (
                                                    <p className="mt-0.5 text-xs text-slate-400 italic">CC: {v.treatment_record.chief_complaint}</p>
                                                )}
                                            </div>
                                            <span className={clsx('rounded-full px-2 py-0.5 text-xs font-semibold', statusColor(v.status))}>
                                                {v.status}
                                            </span>
                                        </div>
                                    ))}
                                </div>
                            )}
                        </div>
                    </div>
                )}

                {/* ── Service Order Info ──────────────────────────────────── */}
                <div className="mx-auto max-w-5xl px-4 pt-4 md:px-6">
                    <div className="grid grid-cols-2 gap-3 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:grid-cols-4">
                        <InfoCell label="SO Number" value={serviceOrder.so_number} />
                        <InfoCell label="Token" value={serviceOrder.token ? `#${serviceOrder.token}` : serviceOrder.so_short} />
                        <InfoCell label="Doctor" value={serviceOrder.doctor?.name ?? '—'} />
                        <InfoCell label="Date" value={formatDate(serviceOrder.created_at)} />
                    </div>
                </div>

                {/* ── Form ────────────────────────────────────────────────── */}
                <div className="mx-auto max-w-5xl space-y-4 px-4 py-4 pb-10 md:px-6">

                    {/* Chief Complaint */}
                    <FormSection icon={<AlertCircle className="h-4 w-4 text-red-500" />} title="Chief Complaint">
                        <textarea
                            disabled={isFinalized}
                            value={chiefComplaint}
                            onChange={(e) => setChiefComplaint(e.target.value)}
                            rows={2}
                            placeholder="Patient's primary complaint..."
                            className={textareaClass(isFinalized)}
                        />
                    </FormSection>

                    {/* History of Present Illness */}
                    <FormSection icon={<FileText className="h-4 w-4 text-slate-500" />} title="History of Present Illness">
                        <textarea
                            disabled={isFinalized}
                            value={hpi}
                            onChange={(e) => setHpi(e.target.value)}
                            rows={3}
                            placeholder="Onset, duration, progression, associated symptoms..."
                            className={textareaClass(isFinalized)}
                        />
                    </FormSection>

                    {/* Vital Signs */}
                    <FormSection icon={<Heart className="h-4 w-4 text-rose-500" />} title="Vital Signs">
                        <div className="grid grid-cols-2 gap-3 sm:grid-cols-4">
                            <VitalInput disabled={isFinalized} label="Temp (°C)" placeholder="37.0" icon={<Thermometer className="h-3.5 w-3.5" />}
                                value={String(vitals.temperature)} onChange={(v) => setVitals((s) => ({ ...s, temperature: v }))} />
                            <VitalInput disabled={isFinalized} label="BP Systolic" placeholder="120" icon={<Heart className="h-3.5 w-3.5" />}
                                value={String(vitals.bp_systolic)} onChange={(v) => setVitals((s) => ({ ...s, bp_systolic: v }))} />
                            <VitalInput disabled={isFinalized} label="BP Diastolic" placeholder="80" icon={<Heart className="h-3.5 w-3.5" />}
                                value={String(vitals.bp_diastolic)} onChange={(v) => setVitals((s) => ({ ...s, bp_diastolic: v }))} />
                            <VitalInput disabled={isFinalized} label="Pulse (bpm)" placeholder="72" icon={<Activity className="h-3.5 w-3.5" />}
                                value={String(vitals.pulse_rate)} onChange={(v) => setVitals((s) => ({ ...s, pulse_rate: v }))} />
                            <VitalInput disabled={isFinalized} label="Resp. Rate" placeholder="16" icon={<Wind className="h-3.5 w-3.5" />}
                                value={String(vitals.respiratory_rate)} onChange={(v) => setVitals((s) => ({ ...s, respiratory_rate: v }))} />
                            <VitalInput disabled={isFinalized} label="SpO2 (%)" placeholder="98" icon={<Activity className="h-3.5 w-3.5" />}
                                value={String(vitals.oxygen_saturation)} onChange={(v) => setVitals((s) => ({ ...s, oxygen_saturation: v }))} />
                            <VitalInput disabled={isFinalized} label="Weight (kg)" placeholder="70" icon={<User className="h-3.5 w-3.5" />}
                                value={String(vitals.weight)} onChange={(v) => setVitals((s) => ({ ...s, weight: v }))} />
                            <VitalInput disabled={isFinalized} label="Height (cm)" placeholder="170" icon={<User className="h-3.5 w-3.5" />}
                                value={String(vitals.height)} onChange={(v) => setVitals((s) => ({ ...s, height: v }))} />
                        </div>
                    </FormSection>

                    {/* Examination Findings */}
                    <FormSection icon={<Stethoscope className="h-4 w-4 text-teal-600" />} title="Examination Findings">
                        <div className="grid grid-cols-1 gap-3 sm:grid-cols-2">
                            {['General', 'Cardiovascular', 'Respiratory', 'Abdomen', 'CNS', 'MSK', 'Other'].map((sys) => (
                                <div key={sys}>
                                    <label className="mb-1 block text-xs font-medium text-slate-500">{sys}</label>
                                    <input
                                        disabled={isFinalized}
                                        value={examFindings[sys] ?? ''}
                                        onChange={(e) => setExamFindings((f) => ({ ...f, [sys]: e.target.value }))}
                                        placeholder={`${sys} findings...`}
                                        className={inputClass(isFinalized)}
                                    />
                                </div>
                            ))}
                        </div>
                    </FormSection>

                    {/* Diagnosis */}
                    <FormSection icon={<FileText className="h-4 w-4 text-indigo-500" />} title="Diagnosis">
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
                                <label className="mb-1 block text-xs font-medium text-slate-500">Diagnosis</label>
                                <input
                                    disabled={isFinalized}
                                    value={diagnosisText}
                                    onChange={(e) => setDiagnosisText(e.target.value)}
                                    placeholder="Auto-filled from ICD-10 selection or type manually"
                                    className={inputClass(isFinalized)}
                                />
                            </div>
                        </div>
                    </FormSection>

                    {/* Prescriptions */}
                    <FormSection icon={<FileText className="h-4 w-4 text-emerald-600" />} title="Prescription">
                        <div className="overflow-x-auto rounded-xl border border-slate-200">
                            <table className="w-full min-w-[700px] text-sm">
                                <thead className="bg-slate-50">
                                    <tr>
                                        {['Drug Name', 'Dose', 'Frequency', 'Duration', 'Route', 'Instructions', ''].map((h) => (
                                            <th key={h} className="px-3 py-2 text-left text-xs font-semibold text-slate-600">
                                                {h}
                                            </th>
                                        ))}
                                    </tr>
                                </thead>
                                <tbody className="divide-y divide-slate-100">
                                    {prescriptions.map((row, idx) => (
                                        <tr key={idx} className="bg-white hover:bg-slate-50">
                                            <td className="px-2 py-1.5">
                                                {isFinalized ? (
                                                    <input disabled value={row.drug_name} className={tableInputClass(true)} />
                                                ) : (
                                                    <DrugPicker
                                                        value={row.drug_name}
                                                        onChange={(name) => updatePrescription(idx, 'drug_name', name)}
                                                        onSelect={(drug) => {
                                                            setPrescriptions((prev) => prev.map((r, i) => i !== idx ? r : {
                                                                ...r,
                                                                drug_name: drug.name,
                                                                dose: drug.default_dose ?? r.dose,
                                                                frequency: drug.default_frequency ?? r.frequency,
                                                                duration: drug.default_duration ?? r.duration,
                                                                route: drug.default_route ?? r.route,
                                                            }));
                                                        }}
                                                    />
                                                )}
                                            </td>
                                            <td className="px-2 py-1.5">
                                                <input disabled={isFinalized} value={row.dose} onChange={(e) => updatePrescription(idx, 'dose', e.target.value)}
                                                    placeholder="e.g. 500mg" className={tableInputClass(isFinalized)} />
                                            </td>
                                            <td className="px-2 py-1.5">
                                                <input disabled={isFinalized} value={row.frequency} onChange={(e) => updatePrescription(idx, 'frequency', e.target.value)}
                                                    placeholder="e.g. TDS" className={tableInputClass(isFinalized)} />
                                            </td>
                                            <td className="px-2 py-1.5">
                                                <input disabled={isFinalized} value={row.duration} onChange={(e) => updatePrescription(idx, 'duration', e.target.value)}
                                                    placeholder="e.g. 5 days" className={tableInputClass(isFinalized)} />
                                            </td>
                                            <td className="px-2 py-1.5">
                                                <input disabled={isFinalized} value={row.route} onChange={(e) => updatePrescription(idx, 'route', e.target.value)}
                                                    placeholder="Oral / IV / IM" className={tableInputClass(isFinalized)} />
                                            </td>
                                            <td className="px-2 py-1.5">
                                                <input disabled={isFinalized} value={row.instructions} onChange={(e) => updatePrescription(idx, 'instructions', e.target.value)}
                                                    placeholder="After meals..." className={tableInputClass(isFinalized)} />
                                            </td>
                                            <td className="px-2 py-1.5">
                                                {!isFinalized && prescriptions.length > 1 && (
                                                    <button type="button" onClick={() => removePrescription(idx)}
                                                        className="text-slate-400 hover:text-red-500 transition-colors">
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
                            <button type="button" onClick={addPrescription}
                                className="mt-2 flex items-center gap-1.5 rounded-lg border border-dashed border-slate-300 px-3 py-2 text-xs font-medium text-slate-500 transition-colors hover:border-teal-400 hover:text-teal-600">
                                <Plus className="h-3.5 w-3.5" /> Add Drug
                            </button>
                        )}
                    </FormSection>

                    {/* Treatment Plan */}
                    <FormSection icon={<FileText className="h-4 w-4 text-violet-500" />} title="Treatment Plan / Notes">
                        <textarea
                            disabled={isFinalized}
                            value={treatmentPlan}
                            onChange={(e) => setTreatmentPlan(e.target.value)}
                            rows={3}
                            placeholder="Management plan, investigations requested, advice given..."
                            className={textareaClass(isFinalized)}
                        />
                    </FormSection>

                    {/* Follow-up, Outcome, Referral */}
                    <FormSection icon={<Calendar className="h-4 w-4 text-orange-500" />} title="Follow-up & Outcome">
                        <div className="grid grid-cols-1 gap-3 sm:grid-cols-3">
                            <div>
                                <label className="mb-1 block text-xs font-medium text-slate-500">Follow-up Date</label>
                                <input
                                    disabled={isFinalized}
                                    type="date"
                                    value={followUpDate}
                                    onChange={(e) => setFollowUpDate(e.target.value)}
                                    className={inputClass(isFinalized)}
                                />
                            </div>
                            <div>
                                <label className="mb-1 block text-xs font-medium text-slate-500">Outcome</label>
                                <select
                                    disabled={isFinalized}
                                    value={outcome}
                                    onChange={(e) => setOutcome(e.target.value)}
                                    className={inputClass(isFinalized)}
                                >
                                    <option value="">Select outcome</option>
                                    <option value="improved">Improved</option>
                                    <option value="unchanged">Unchanged</option>
                                    <option value="deteriorated">Deteriorated</option>
                                    <option value="referred">Referred</option>
                                    <option value="expired">Expired</option>
                                </select>
                            </div>
                            <div>
                                <label className="mb-1 block text-xs font-medium text-slate-500">Referred To</label>
                                <input
                                    disabled={isFinalized}
                                    value={referralTo}
                                    onChange={(e) => setReferralTo(e.target.value)}
                                    placeholder="Department / Hospital"
                                    className={inputClass(isFinalized)}
                                />
                            </div>
                        </div>
                    </FormSection>

                    {/* Bottom Save Bar */}
                    {!isFinalized && (
                        <div className="flex items-center justify-end gap-3 rounded-2xl border border-slate-200 bg-white px-4 py-3 shadow-sm">
                            <Button variant="outline" disabled={saving} onClick={() => save(false)} className="gap-1.5">
                                <Save className="h-4 w-4" />
                                {saving ? 'Saving…' : 'Save Draft'}
                            </Button>
                            <Button disabled={finalizing} onClick={() => save(true)} className="gap-1.5 bg-teal-600 text-white hover:bg-teal-700">
                                <CheckCircle className="h-4 w-4" />
                                {finalizing ? 'Finalizing…' : 'Finalize Record'}
                            </Button>
                        </div>
                    )}
                </div>
            </div>
        </AppLayout>
    );
}

// ─── Sub-components ───────────────────────────────────────────────────────────

function FormSection({ icon, title, children }: { icon: React.ReactNode; title: string; children: React.ReactNode }) {
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
                    <span className="text-sm font-semibold text-slate-900">{title}</span>
                </div>
                {collapsed ? <ChevronDown className="h-4 w-4 text-slate-400" /> : <ChevronUp className="h-4 w-4 text-slate-400" />}
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
            <p className="mt-0.5 text-sm font-semibold text-slate-800">{value ?? '—'}</p>
        </div>
    );
}

function VitalInput({
    label, placeholder, icon, value, onChange, disabled,
}: {
    label: string; placeholder: string; icon: React.ReactNode; value: string;
    onChange: (v: string) => void; disabled: boolean;
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
        'w-full rounded-xl border px-3 py-2.5 text-sm text-slate-800 placeholder:text-slate-400',
        'focus:outline-none focus:ring-2 focus:ring-teal-100 focus:border-teal-400',
        'resize-y',
        disabled
            ? 'border-slate-100 bg-slate-50 text-slate-600 cursor-not-allowed'
            : 'border-slate-200 bg-white hover:border-slate-300',
    );
}

function inputClass(disabled: boolean) {
    return clsx(
        'w-full rounded-xl border px-3 py-2 text-sm text-slate-800 placeholder:text-slate-400',
        'focus:outline-none focus:ring-2 focus:ring-teal-100 focus:border-teal-400',
        disabled
            ? 'border-slate-100 bg-slate-50 text-slate-600 cursor-not-allowed'
            : 'border-slate-200 bg-white hover:border-slate-300',
    );
}

function tableInputClass(disabled: boolean) {
    return clsx(
        'w-full rounded-lg border px-2 py-1.5 text-xs text-slate-800 placeholder:text-slate-400',
        'focus:outline-none focus:ring-1 focus:ring-teal-200 focus:border-teal-300',
        disabled
            ? 'border-transparent bg-transparent text-slate-600 cursor-not-allowed'
            : 'border-slate-200 bg-white hover:border-slate-300',
    );
}

