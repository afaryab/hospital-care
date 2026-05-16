import Icd10Picker from '@/components/ui/icd10-picker';
import { formatPatientAge } from '@/lib/constants';
import { router } from '@inertiajs/react';
import { clsx } from 'clsx';
import { ArrowLeft, CheckCircle, Save } from 'lucide-react';
import { useCallback, useState } from 'react';
import { toast } from 'sonner';

interface Patient {
    id: number; name: string; ps_number: string; gender?: string;
    age_days?: number; age_dob?: string; contact?: string;
}

interface TreatmentRecord {
    id?: number;
    chief_complaint?: string;
    history_of_present_illness?: string;
    diagnosis_code?: string;
    icd10_code_id?: number | null;
    diagnosis_text?: string;
    treatment_plan?: string;
    prescriptions?: Prescription[];
    is_finalized?: boolean;
    treated_at?: string;
}

interface Prescription { drug_name: string; dose?: string; frequency?: string; duration?: string; route?: string; instructions?: string }
interface PreviousVisit { id: number; so_number: string; status: string; created_at: string; treatment_record?: { diagnosis_text?: string; chief_complaint?: string } | null }

interface Props {
    deptName: string;
    dashboardUrl: string;
    saveApiUrl: string;       // POST URL for saving treatment record
    updateStatusUrl: string;  // PATCH URL for status update
    serviceOrder: {
        id: number; so_number: string; status: string;
        patient?: Patient | null;
        service?: { id: number; name: string } | null;
        doctor?: { id: number; name: string } | null;
        treatment_record?: TreatmentRecord | null;
    };
    previousVisits: PreviousVisit[];
}

function blank(): Prescription { return { drug_name: '', dose: '', frequency: '', duration: '', route: '', instructions: '' }; }

function getCsrf() {
    return decodeURIComponent(document.cookie.split('XSRF-TOKEN=')[1]?.split(';')[0] ?? '');
}

async function patchStatus(url: string, status: string) {
    return fetch(url, {
        method: 'PATCH',
        headers: { 'Content-Type': 'application/json', Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'X-XSRF-TOKEN': getCsrf() },
        body: JSON.stringify({ status }),
    });
}

export default function DeptPatientForm({ deptName, dashboardUrl, saveApiUrl, updateStatusUrl, serviceOrder, previousVisits }: Props) {
    const existing = serviceOrder.treatment_record;
    const isFinalized = existing?.is_finalized ?? false;
    const patient = serviceOrder.patient;

    const [chiefComplaint, setChiefComplaint] = useState(existing?.chief_complaint ?? '');
    const [hpi, setHpi] = useState(existing?.history_of_present_illness ?? '');
    const [diagnosisCode, setDiagnosisCode] = useState(existing?.diagnosis_code ?? '');
    const [icd10CodeId, setIcd10CodeId] = useState<number | null>(existing?.icd10_code_id ?? null);
    const [diagnosisText, setDiagnosisText] = useState(existing?.diagnosis_text ?? '');
    const [treatmentPlan, setTreatmentPlan] = useState(existing?.treatment_plan ?? '');
    const [prescriptions, setPrescriptions] = useState<Prescription[]>(
        existing?.prescriptions?.length ? existing.prescriptions : [blank()],
    );
    const [saving, setSaving] = useState(false);
    const [finalizing, setFinalizing] = useState(false);

    const input = 'w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-300 focus:outline-none';
    const disabled = `${input} bg-slate-50 text-slate-500 cursor-not-allowed`;

    const buildPayload = useCallback((finalize = false) => ({
        chief_complaint: chiefComplaint,
        history_of_present_illness: hpi,
        diagnosis_code: diagnosisCode,
        icd10_code_id: icd10CodeId,
        diagnosis_text: diagnosisText,
        treatment_plan: treatmentPlan,
        prescriptions: prescriptions.filter((p) => p.drug_name.trim()),
        finalize,
    }), [chiefComplaint, hpi, diagnosisCode, icd10CodeId, diagnosisText, treatmentPlan, prescriptions]);

    const submit = async (finalize: boolean) => {
        if (finalize) setFinalizing(true); else setSaving(true);
        try {
            const res = await fetch(saveApiUrl, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'X-XSRF-TOKEN': getCsrf() },
                body: JSON.stringify(buildPayload(finalize)),
            });
            const json = await res.json();
            if (!res.ok) { toast.error(json.message ?? 'Save failed'); return; }
            toast.success(json.message ?? (finalize ? 'Finalized.' : 'Saved.'));
            if (finalize) router.visit(dashboardUrl);
        } catch { toast.error('Network error'); }
        finally { setSaving(false); setFinalizing(false); }
    };

    const callPatient = async () => {
        await patchStatus(updateStatusUrl, 'in-progress');
        toast.success('Patient called');
    };

    return (
        <div className="flex flex-1 flex-col gap-4 p-4">
            {/* Back nav */}
            <div className="flex items-center gap-3">
                <button onClick={() => router.visit(dashboardUrl)} className="flex items-center gap-1.5 text-sm text-slate-500 hover:text-slate-700">
                    <ArrowLeft className="h-4 w-4" /> {deptName} Queue
                </button>
            </div>

            {/* Patient header */}
            <div className="flex items-start justify-between rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <div>
                    <h2 className="text-lg font-bold text-slate-800">{patient?.name ?? 'Patient'}</h2>
                    <div className="mt-1 flex flex-wrap gap-3 text-sm text-slate-500">
                        <span>{patient?.ps_number}</span>
                        {patient && <span>{formatPatientAge(patient)}</span>}
                        {patient?.gender && <span>{patient.gender.toUpperCase()}</span>}
                        {patient?.contact && <span>{patient.contact}</span>}
                    </div>
                    <div className="mt-1 text-xs text-slate-400">{serviceOrder.so_number} · {serviceOrder.service?.name}</div>
                </div>
                <div className="flex gap-2">
                    {!isFinalized && strtolower(serviceOrder.status) === 'open' && (
                        <button onClick={callPatient} className="rounded-lg border border-indigo-200 bg-indigo-50 px-3 py-1.5 text-xs font-medium text-indigo-700 hover:bg-indigo-100">
                            Call Patient
                        </button>
                    )}
                    {isFinalized && (
                        <span className="flex items-center gap-1 rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700">
                            <CheckCircle className="h-3.5 w-3.5" /> Finalized
                        </span>
                    )}
                </div>
            </div>

            <div className="grid grid-cols-1 gap-4 lg:grid-cols-3">
                {/* Treatment form */}
                <div className="lg:col-span-2 space-y-4">
                    <div className="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm space-y-4">
                        <h3 className="font-semibold text-slate-700">Clinical Notes</h3>

                        <div>
                            <label className="mb-1 block text-xs font-medium text-slate-500">Chief Complaint</label>
                            <textarea disabled={isFinalized} value={chiefComplaint} onChange={(e) => setChiefComplaint(e.target.value)}
                                rows={2} className={clsx(isFinalized ? disabled : input, 'resize-none')} placeholder="Patient's main complaint…" />
                        </div>

                        <div>
                            <label className="mb-1 block text-xs font-medium text-slate-500">History of Present Illness</label>
                            <textarea disabled={isFinalized} value={hpi} onChange={(e) => setHpi(e.target.value)}
                                rows={3} className={clsx(isFinalized ? disabled : input, 'resize-none')} placeholder="HPI…" />
                        </div>

                        <div className="grid grid-cols-1 gap-3 sm:grid-cols-3">
                            <div>
                                <label className="mb-1 block text-xs font-medium text-slate-500">ICD-10 Code</label>
                                <Icd10Picker value={diagnosisCode} disabled={isFinalized} className={isFinalized ? disabled : input}
                                    onSelect={(code, desc) => { setDiagnosisCode(code); setDiagnosisText(desc); }} />
                            </div>
                            <div className="sm:col-span-2">
                                <label className="mb-1 block text-xs font-medium text-slate-500">Diagnosis</label>
                                <input disabled={isFinalized} value={diagnosisText} onChange={(e) => setDiagnosisText(e.target.value)}
                                    className={isFinalized ? disabled : input} placeholder="Auto-filled or type manually" />
                            </div>
                        </div>

                        <div>
                            <label className="mb-1 block text-xs font-medium text-slate-500">Treatment Plan / Findings</label>
                            <textarea disabled={isFinalized} value={treatmentPlan} onChange={(e) => setTreatmentPlan(e.target.value)}
                                rows={4} className={clsx(isFinalized ? disabled : input, 'resize-none')} placeholder="Treatment plan, imaging findings, lab results…" />
                        </div>
                    </div>

                    {/* Prescriptions */}
                    <div className="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                        <div className="mb-3 flex items-center justify-between">
                            <h3 className="font-semibold text-slate-700">Prescriptions</h3>
                            {!isFinalized && (
                                <button onClick={() => setPrescriptions([...prescriptions, blank()])}
                                    className="text-xs font-medium text-indigo-600 hover:text-indigo-800">+ Add row</button>
                            )}
                        </div>
                        <div className="space-y-2">
                            {prescriptions.map((p, i) => (
                                <div key={i} className="grid grid-cols-6 gap-2 text-xs">
                                    <input disabled={isFinalized} value={p.drug_name} onChange={(e) => { const n = [...prescriptions]; n[i].drug_name = e.target.value; setPrescriptions(n); }}
                                        placeholder="Drug name *" className={clsx('col-span-2', isFinalized ? disabled : input)} />
                                    <input disabled={isFinalized} value={p.dose ?? ''} onChange={(e) => { const n = [...prescriptions]; n[i].dose = e.target.value; setPrescriptions(n); }}
                                        placeholder="Dose" className={isFinalized ? disabled : input} />
                                    <input disabled={isFinalized} value={p.frequency ?? ''} onChange={(e) => { const n = [...prescriptions]; n[i].frequency = e.target.value; setPrescriptions(n); }}
                                        placeholder="Frequency" className={isFinalized ? disabled : input} />
                                    <input disabled={isFinalized} value={p.duration ?? ''} onChange={(e) => { const n = [...prescriptions]; n[i].duration = e.target.value; setPrescriptions(n); }}
                                        placeholder="Duration" className={isFinalized ? disabled : input} />
                                    {!isFinalized && prescriptions.length > 1 && (
                                        <button onClick={() => setPrescriptions(prescriptions.filter((_, j) => j !== i))} className="text-red-400 hover:text-red-600">✕</button>
                                    )}
                                </div>
                            ))}
                        </div>
                    </div>

                    {/* Actions */}
                    {!isFinalized && (
                        <div className="flex justify-end gap-3">
                            <button onClick={() => submit(false)} disabled={saving || finalizing}
                                className="flex items-center gap-2 rounded-lg border border-slate-200 bg-white px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50 disabled:opacity-50">
                                <Save className="h-4 w-4" /> {saving ? 'Saving…' : 'Save Draft'}
                            </button>
                            <button onClick={() => submit(true)} disabled={saving || finalizing}
                                className="flex items-center gap-2 rounded-lg bg-emerald-600 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-700 disabled:opacity-50">
                                <CheckCircle className="h-4 w-4" /> {finalizing ? 'Finalizing…' : 'Finalize Record'}
                            </button>
                        </div>
                    )}
                </div>

                {/* Previous visits */}
                <div className="space-y-3">
                    <div className="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                        <h3 className="mb-3 font-semibold text-slate-700">Previous Visits</h3>
                        {previousVisits.length === 0 ? (
                            <p className="text-xs text-slate-400">No previous visits.</p>
                        ) : (
                            <ul className="space-y-3">
                                {previousVisits.map((v) => (
                                    <li key={v.id} className="border-b border-slate-100 pb-3 last:border-0">
                                        <div className="text-xs font-mono text-slate-400">{v.so_number}</div>
                                        <div className="text-xs text-slate-500">{new Date(v.created_at).toLocaleDateString()}</div>
                                        {v.treatment_record?.diagnosis_text && (
                                            <div className="mt-1 text-xs text-slate-600">{v.treatment_record.diagnosis_text}</div>
                                        )}
                                    </li>
                                ))}
                            </ul>
                        )}
                    </div>
                </div>
            </div>
        </div>
    );
}

function strtolower(s: string) { return s.toLowerCase(); }
