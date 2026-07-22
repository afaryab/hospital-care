// @ts-nocheck
import { Checkbox as UICheckbox } from '@/components/ui/checkbox';
import { Input as UIInput } from '@/components/ui/input';
import { Label as UILabel } from '@/components/ui/label';
import { useEffect, useMemo, useState } from 'react';
import { toast } from 'sonner';

const uid = () => `${Date.now()}_${Math.random().toString(16).slice(2)}`;

const Input = ({
    label,
    value,
    onChange,
    placeholder = '',
    className = '',
    type = 'text',
}) => (
    <label className={`block ${className}`}>
        {label ? (
            <UILabel className="mb-1 text-xs font-semibold text-slate-600">
                {label}
            </UILabel>
        ) : null}
        <UIInput
            type={type}
            value={value ?? ''}
            onChange={(e) => onChange(e.target.value)}
            placeholder={placeholder}
            className="w-full"
        />
    </label>
);

const TextArea = ({
    label,
    value,
    onChange,
    placeholder = '',
    rows = 4,
    className = '',
}) => (
    <label className={`block ${className}`}>
        {label ? (
            <div className="mb-1 text-xs font-semibold text-slate-600">
                {label}
            </div>
        ) : null}
        <textarea
            value={value ?? ''}
            onChange={(e) => onChange(e.target.value)}
            placeholder={placeholder}
            rows={rows}
            className="w-full resize-y rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm text-slate-800 shadow-sm transition outline-none focus:border-slate-400 focus:ring-2 focus:ring-slate-200"
        />
    </label>
);

const Checkbox = ({ label, checked, onChange, className = '' }) => (
    <div className={`inline-flex items-center gap-2 ${className}`}>
        <UICheckbox
            checked={!!checked}
            onCheckedChange={(v) => onChange(!!v)}
        />
        <UILabel className="text-sm text-slate-700 select-none">
            {label}
        </UILabel>
    </div>
);

// Removed decorative Pill component to simplify the view

function EditableTable({
    title,
    columns,
    rows,
    setRows,
    addLabel = 'Add row',
    compact = false,
}) {
    const addRow = () => {
        const empty = columns.reduce((acc, c) => ({ ...acc, [c.key]: '' }), {});
        setRows((prev) => [...prev, { id: uid(), ...empty }]);
    };

    const removeRow = (id) =>
        setRows((prev) => prev.filter((r) => r.id !== id));

    const updateCell = (id, key, value) =>
        setRows((prev) =>
            prev.map((r) => (r.id === id ? { ...r, [key]: value } : r)),
        );

    return (
        <div className="rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div className="flex flex-wrap items-center justify-between gap-3 border-b border-slate-200 px-4 py-3">
                <div className="flex items-center gap-2">
                    <div className="h-2.5 w-2.5 rounded-full bg-slate-900" />
                    <h3 className="text-sm font-bold tracking-wide text-slate-900">
                        {title}
                    </h3>
                </div>

                <button
                    type="button"
                    onClick={addRow}
                    className="inline-flex items-center gap-2 rounded-xl bg-slate-900 px-3 py-2 text-xs font-semibold text-white shadow-sm transition hover:bg-slate-800 active:scale-[0.99]"
                >
                    <span className="text-sm leading-none">＋</span>
                    {addLabel}
                </button>
            </div>

            <div className="overflow-x-auto">
                <table className="min-w-full text-left text-sm">
                    <thead className="bg-slate-50">
                        <tr>
                            {columns.map((c) => (
                                <th
                                    key={c.key}
                                    className={`px-3 py-3 text-xs font-bold tracking-wider whitespace-nowrap text-slate-600 uppercase ${
                                        c.className ?? ''
                                    }`}
                                >
                                    {c.label}
                                </th>
                            ))}
                            <th className="px-3 py-3 text-xs font-bold tracking-wider text-slate-600 uppercase">
                                Actions
                            </th>
                        </tr>
                    </thead>

                    <tbody className="divide-y divide-slate-100">
                        {rows.length === 0 ? (
                            <tr>
                                <td
                                    colSpan={columns.length + 1}
                                    className="px-4 py-10 text-center text-sm text-slate-500"
                                >
                                    No rows yet. Click <b>Add row</b>.
                                </td>
                            </tr>
                        ) : (
                            rows.map((r) => (
                                <tr key={r.id} className="hover:bg-slate-50/60">
                                    {columns.map((c) => (
                                        <td
                                            key={c.key}
                                            className="px-3 py-2 align-top"
                                        >
                                            {c.type === 'textarea' ? (
                                                <TextArea
                                                    label=""
                                                    value={r[c.key] ?? ''}
                                                    onChange={(v) =>
                                                        updateCell(
                                                            r.id,
                                                            c.key,
                                                            v,
                                                        )
                                                    }
                                                    rows={compact ? 2 : 3}
                                                    placeholder={
                                                        c.placeholder ?? ''
                                                    }
                                                    className=""
                                                />
                                            ) : (
                                                <Input
                                                    label=""
                                                    value={r[c.key] ?? ''}
                                                    onChange={(v) =>
                                                        updateCell(
                                                            r.id,
                                                            c.key,
                                                            v,
                                                        )
                                                    }
                                                    placeholder={
                                                        c.placeholder ?? ''
                                                    }
                                                />
                                            )}
                                        </td>
                                    ))}
                                    <td className="px-3 py-2 align-top">
                                        <button
                                            type="button"
                                            onClick={() => removeRow(r.id)}
                                            className="rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50 active:scale-[0.99]"
                                        >
                                            Remove
                                        </button>
                                    </td>
                                </tr>
                            ))
                        )}
                    </tbody>
                </table>
            </div>
        </div>
    );
}

export default function EmergencyClinicalPerforma({
    patient,
    serviceOrder,
    className,
}) {
    const fmtDate = (d) => {
        if (!d) return '';
        const dt = new Date(d);
        if (isNaN(dt.getTime())) return '';
        return dt.toISOString().slice(0, 10);
    };
    const fmtTime = (d) => {
        if (!d) return '';
        const dt = new Date(d);
        if (isNaN(dt.getTime())) return '';
        return dt.toTimeString().slice(0, 5);
    };

    const [header, setHeader] = useState({
        mrNo: '',
        date: '',
        time: '',
        patientName: '',
        sdoWo: '',
        ageSex: '',
        address: '',
    });

    const [triage, setTriage] = useState({
        date: '',
        time: '',
        doctorName: '',
        nurseName: '',
        complaint: '',
        triageCategory: '',
    });

    const [doctorBlock, setDoctorBlock] = useState({
        doctorsName: '',
        timeSeenByDoctor: '',
        history: '',
        pastHistory: '',
    });

    const [exam, setExam] = useState({
        airwayClear: 'Y',
        breathingSpont: 'Y',
        rr: '',
        gcs: '',
        circulation: 'Y',
        pulse: '',
        bp: '',
        tempF: '',
        bsl: '',
        medicationsYN: 'N',
        allergiesYN: 'N',
        immunizationYN: 'N',
        smokerYN: 'N',
    });

    // PAGE 2
    const [treatmentGiven, setTreatmentGiven] = useState('');
    const [discharge, setDischarge] = useState({
        diagnosis: '',
        dischargePlan: false,
        referredTo: '',
        admittedTo: false,
        admittedDate: '',
        admittedTime: '',
        transferredTo: false,
        transferredDate: '',
        transferredTime: '',
        dischargedHome: false,
        dischargedDate: '',
        dischargedTime: '',
        reviewByGP: false,
        reviewInED: false,
        reviewClinic: false,
        reviewOPD: false,
        reviewDate: '',
        diedInED: false,
        diedTime: '',
        leftAgainstMedicalAdvice: false,
        didNotWantToBeSeen: false,
        dischargingDoctorName: '',
        doctorSignature: '',
        dischargingNurseSign: '',
    });

    const [medRows, setMedRows] = useState([
        {
            id: uid(),
            date: '',
            drug: '',
            dose: '',
            route: '',
            prescribedBy: '',
            givenBySign: '',
            time: '',
        },
    ]);

    const [investigationRows, setInvestigationRows] = useState([
        { id: uid(), item: '' },
    ]);

    const [nursingRows, setNursingRows] = useState([{ id: uid(), note: '' }]);

    const medColumns = useMemo(
        () => [
            { key: 'date', label: 'Date', placeholder: 'YYYY-MM-DD' },
            { key: 'drug', label: 'Drug', placeholder: 'e.g., Ceftriaxone' },
            { key: 'dose', label: 'Dose', placeholder: 'e.g., 1g' },
            { key: 'route', label: 'Route', placeholder: 'IV / IM / PO' },
            {
                key: 'prescribedBy',
                label: 'Prescribed By',
                placeholder: 'Dr. Name',
            },
            {
                key: 'givenBySign',
                label: 'Given By (Sign)',
                placeholder: 'Nurse sign',
            },
            { key: 'time', label: 'Time', placeholder: 'HH:MM' },
        ],
        [],
    );

    const invColumns = useMemo(
        () => [
            {
                key: 'item',
                label: 'Investigation Done',
                type: 'textarea',
                placeholder: 'Write test / imaging / labs...',
            },
        ],
        [],
    );

    const nursingColumns = useMemo(
        () => [
            {
                key: 'note',
                label: 'Nursing Interventions',
                type: 'textarea',
                placeholder: 'Vitals monitoring, IV access, oxygen...',
            },
        ],
        [],
    );

    const onPrint = () => {
        const id = serviceOrder?.id;
        if (!id) {
            toast.error('Missing Service Order ID');
            return;
        }
        const url = `/PRINT/SO/${id}`;
        window.open(url, '_blank', 'noopener,noreferrer');
    };

    const onReset = () => {
        if (!confirm('Clear the whole form?')) return;
        setHeader({
            mrNo: '',
            date: '',
            time: '',
            patientName: '',
            sdoWo: '',
            ageSex: '',
            address: '',
        });
        setTriage({
            date: '',
            time: '',
            doctorName: '',
            nurseName: '',
            complaint: '',
            triageCategory: '',
        });
        setDoctorBlock({
            doctorsName: '',
            timeSeenByDoctor: '',
            history: '',
            pastHistory: '',
        });
        setExam({
            airwayClear: 'Y',
            breathingSpont: 'Y',
            rr: '',
            gcs: '',
            circulation: 'Y',
            pulse: '',
            bp: '',
            tempF: '',
            bsl: '',
            medicationsYN: 'N',
            allergiesYN: 'N',
            immunizationYN: 'N',
            smokerYN: 'N',
        });
        setTreatmentGiven('');
        setDischarge({
            diagnosis: '',
            dischargePlan: false,
            referredTo: '',
            admittedTo: false,
            admittedDate: '',
            admittedTime: '',
            transferredTo: false,
            transferredDate: '',
            transferredTime: '',
            dischargedHome: false,
            dischargedDate: '',
            dischargedTime: '',
            reviewByGP: false,
            reviewInED: false,
            reviewClinic: false,
            reviewOPD: false,
            reviewDate: '',
            diedInED: false,
            diedTime: '',
            leftAgainstMedicalAdvice: false,
            didNotWantToBeSeen: false,
            dischargingDoctorName: '',
            doctorSignature: '',
            dischargingNurseSign: '',
        });
        setMedRows([
            {
                id: uid(),
                date: '',
                drug: '',
                dose: '',
                route: '',
                prescribedBy: '',
                givenBySign: '',
                time: '',
            },
        ]);
        setInvestigationRows([{ id: uid(), item: '' }]);
        setNursingRows([{ id: uid(), note: '' }]);
    };

    useEffect(() => {
        setHeader((s) => ({
            ...s,
            mrNo:
                (patient && (patient.ps_number || patient.mr_no)) ||
                s.mrNo ||
                '',
            date:
                (serviceOrder &&
                    serviceOrder.created_at &&
                    fmtDate(serviceOrder.created_at)) ||
                s.date ||
                '',
            time:
                (serviceOrder &&
                    serviceOrder.created_at &&
                    fmtTime(serviceOrder.created_at)) ||
                s.time ||
                '',
            patientName:
                (patient && (patient.name || patient.full_name)) ||
                s.patientName ||
                '',
            sdoWo:
                (patient && (patient.relation || patient.guardian_name)) ||
                s.sdoWo ||
                '',
            ageSex:
                [
                    patient && patient.age,
                    patient && (patient.gender || patient.sex),
                ]
                    .filter(Boolean)
                    .join(' / ') ||
                s.ageSex ||
                '',
            address:
                (patient && (patient.address || patient.city)) ||
                s.address ||
                '',
        }));

        setTriage((t) => ({
            ...t,
            date:
                (serviceOrder &&
                    serviceOrder.created_at &&
                    fmtDate(serviceOrder.created_at)) ||
                t.date ||
                '',
            time:
                (serviceOrder &&
                    serviceOrder.created_at &&
                    fmtTime(serviceOrder.created_at)) ||
                t.time ||
                '',
            doctorName:
                (serviceOrder &&
                    serviceOrder.doctor &&
                    (serviceOrder.doctor.name ||
                        serviceOrder.doctor.full_name)) ||
                t.doctorName ||
                '',
            nurseName:
                (serviceOrder &&
                    serviceOrder.nurse &&
                    (serviceOrder.nurse.name ||
                        serviceOrder.nurse.full_name)) ||
                t.nurseName ||
                '',
            triageCategory:
                (serviceOrder &&
                    (serviceOrder.triage_category || serviceOrder.triage)) ||
                t.triageCategory ||
                '',
        }));

        setDoctorBlock((d) => ({
            ...d,
            doctorsName:
                (serviceOrder &&
                    serviceOrder.doctor &&
                    (serviceOrder.doctor.name ||
                        serviceOrder.doctor.full_name)) ||
                d.doctorsName ||
                '',
            timeSeenByDoctor:
                (serviceOrder &&
                    serviceOrder.time_seen_by_doctor &&
                    fmtTime(serviceOrder.time_seen_by_doctor)) ||
                d.timeSeenByDoctor ||
                '',
            history: (serviceOrder && serviceOrder.history) || d.history || '',
            pastHistory:
                (serviceOrder && serviceOrder.past_history) ||
                d.pastHistory ||
                '',
        }));

        setExam((e) => ({
            ...e,
            rr:
                (serviceOrder &&
                    serviceOrder.vitals &&
                    serviceOrder.vitals.rr) ||
                e.rr ||
                '',
            gcs:
                (serviceOrder &&
                    serviceOrder.vitals &&
                    serviceOrder.vitals.gcs) ||
                e.gcs ||
                '',
            circulation: e.circulation,
            pulse:
                (serviceOrder &&
                    serviceOrder.vitals &&
                    serviceOrder.vitals.pulse) ||
                e.pulse ||
                '',
            bp:
                (serviceOrder &&
                    serviceOrder.vitals &&
                    serviceOrder.vitals.bp) ||
                e.bp ||
                '',
            tempF:
                (serviceOrder &&
                    serviceOrder.vitals &&
                    (serviceOrder.vitals.temp_f ||
                        serviceOrder.vitals.tempF)) ||
                e.tempF ||
                '',
            bsl:
                (serviceOrder &&
                    serviceOrder.vitals &&
                    serviceOrder.vitals.bsl) ||
                e.bsl ||
                '',
            medicationsYN:
                serviceOrder &&
                serviceOrder.flags &&
                serviceOrder.flags.medications
                    ? 'Y'
                    : e.medicationsYN,
            allergiesYN:
                serviceOrder &&
                serviceOrder.flags &&
                serviceOrder.flags.allergies
                    ? 'Y'
                    : e.allergiesYN,
            immunizationYN:
                serviceOrder &&
                serviceOrder.flags &&
                serviceOrder.flags.immunization
                    ? 'Y'
                    : e.immunizationYN,
            smokerYN:
                serviceOrder && serviceOrder.flags && serviceOrder.flags.smoker
                    ? 'Y'
                    : e.smokerYN,
        }));

        if (
            serviceOrder &&
            Array.isArray(serviceOrder.medications) &&
            serviceOrder.medications.length
        ) {
            setMedRows(
                serviceOrder.medications.map((m) => ({
                    id: uid(),
                    date: fmtDate(m.date || m.datetime),
                    drug: m.drug || m.name || '',
                    dose: m.dose || '',
                    route: m.route || '',
                    prescribedBy:
                        (m.prescribed_by &&
                            (m.prescribed_by.name || m.prescribed_by)) ||
                        '',
                    givenBySign: m.given_by_sign || '',
                    time: fmtTime(m.time || m.datetime),
                })),
            );
        }

        if (
            serviceOrder &&
            Array.isArray(serviceOrder.investigations) &&
            serviceOrder.investigations.length
        ) {
            setInvestigationRows(
                serviceOrder.investigations.map((i) => ({
                    id: uid(),
                    item: i.item || i.name || '',
                })),
            );
        }

        if (
            serviceOrder &&
            Array.isArray(serviceOrder.nursing_interventions) &&
            serviceOrder.nursing_interventions.length
        ) {
            setNursingRows(
                serviceOrder.nursing_interventions.map((n) => ({
                    id: uid(),
                    note: n.note || '',
                })),
            );
        }

        if (
            serviceOrder &&
            (serviceOrder.treatment_given || serviceOrder.treatment)
        ) {
            setTreatmentGiven(
                serviceOrder.treatment_given || serviceOrder.treatment || '',
            );
        }
    }, [patient, serviceOrder]);

    return (
        <div className="min-h-screen bg-slate-50">
            <div className="sticky top-0 z-10 border-b border-slate-200 bg-white/85 backdrop-blur">
                <div className="mx-auto flex max-w-6xl flex-wrap items-center justify-between gap-3 px-4 py-3">
                    <div className="flex items-center gap-3">
                        <div className="grid h-10 w-10 place-items-center rounded-2xl bg-slate-900 text-white shadow-sm">
                            ED
                        </div>
                        <div>
                            <div className="text-sm font-extrabold tracking-wide text-slate-900">
                                Emergency Department
                            </div>
                            <div className="text-xs font-semibold text-slate-500">
                                Clinical Performa (Editable)
                            </div>
                        </div>
                    </div>

                    <div className="flex flex-wrap items-center gap-2">
                        <button
                            type="button"
                            onClick={onPrint}
                            className="rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50 active:scale-[0.99]"
                        >
                            Print
                        </button>
                        <button
                            type="button"
                            onClick={onReset}
                            className="rounded-xl bg-rose-600 px-3 py-2 text-xs font-semibold text-white shadow-sm transition hover:bg-rose-500 active:scale-[0.99]"
                        >
                            Reset
                        </button>
                    </div>
                </div>
            </div>

            <div className="mx-auto max-w-6xl space-y-6 px-4 py-6">
                <div className="grid grid-cols-1 gap-6 lg:grid-cols-5">
                    <div className="lg:col-span-3">
                        <div className="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                            <div className="mb-3 flex items-center justify-between">
                                <h2 className="text-sm font-extrabold tracking-wide text-slate-900">
                                    Patient Details
                                </h2>
                                <span className="text-xs font-semibold text-slate-500">
                                    Page 1
                                </span>
                            </div>

                            <div className="grid grid-cols-1 gap-3 sm:grid-cols-2">
                                <Input
                                    label="MR #"
                                    value={header.mrNo}
                                    onChange={(v) =>
                                        setHeader((s) => ({ ...s, mrNo: v }))
                                    }
                                    placeholder="MR-000123"
                                />
                                <div className="grid grid-cols-2 gap-3">
                                    <Input
                                        label="Date"
                                        value={header.date}
                                        onChange={(v) =>
                                            setHeader((s) => ({
                                                ...s,
                                                date: v,
                                            }))
                                        }
                                        placeholder="YYYY-MM-DD"
                                    />
                                    <Input
                                        label="Time"
                                        value={header.time}
                                        onChange={(v) =>
                                            setHeader((s) => ({
                                                ...s,
                                                time: v,
                                            }))
                                        }
                                        placeholder="HH:MM"
                                    />
                                </div>

                                <Input
                                    label="Pt. Name"
                                    value={header.patientName}
                                    onChange={(v) =>
                                        setHeader((s) => ({
                                            ...s,
                                            patientName: v,
                                        }))
                                    }
                                    placeholder="Patient full name"
                                    className="sm:col-span-2"
                                />

                                <Input
                                    label="S/o, D/o, W/o"
                                    value={header.sdoWo}
                                    onChange={(v) =>
                                        setHeader((s) => ({ ...s, sdoWo: v }))
                                    }
                                    placeholder="Relation name"
                                />
                                <Input
                                    label="Age & Sex"
                                    value={header.ageSex}
                                    onChange={(v) =>
                                        setHeader((s) => ({ ...s, ageSex: v }))
                                    }
                                    placeholder="e.g., 32 / M"
                                />

                                <Input
                                    label="Address"
                                    value={header.address}
                                    onChange={(v) =>
                                        setHeader((s) => ({ ...s, address: v }))
                                    }
                                    placeholder="City / Area"
                                    className="sm:col-span-2"
                                />
                            </div>
                        </div>
                    </div>

                    <div className="lg:col-span-2">
                        <div className="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                            <div className="mb-3 flex items-center justify-between">
                                <h2 className="text-sm font-extrabold tracking-wide text-slate-900">
                                    Triage Notes
                                </h2>
                                <span className="text-xs font-semibold text-slate-500">
                                    ED
                                </span>
                            </div>

                            <div className="grid grid-cols-1 gap-3">
                                <div className="grid grid-cols-2 gap-3">
                                    <Input
                                        label="Date"
                                        value={triage.date}
                                        onChange={(v) =>
                                            setTriage((s) => ({
                                                ...s,
                                                date: v,
                                            }))
                                        }
                                        placeholder="YYYY-MM-DD"
                                    />
                                    <Input
                                        label="Time"
                                        value={triage.time}
                                        onChange={(v) =>
                                            setTriage((s) => ({
                                                ...s,
                                                time: v,
                                            }))
                                        }
                                        placeholder="HH:MM"
                                    />
                                </div>

                                <Input
                                    label="Name of Doctor"
                                    value={triage.doctorName}
                                    onChange={(v) =>
                                        setTriage((s) => ({
                                            ...s,
                                            doctorName: v,
                                        }))
                                    }
                                    placeholder="Dr. ..."
                                />
                                <Input
                                    label="Name of Nurse"
                                    value={triage.nurseName}
                                    onChange={(v) =>
                                        setTriage((s) => ({
                                            ...s,
                                            nurseName: v,
                                        }))
                                    }
                                    placeholder="Nurse ..."
                                />

                                <TextArea
                                    label="Complaint"
                                    value={triage.complaint}
                                    onChange={(v) =>
                                        setTriage((s) => ({
                                            ...s,
                                            complaint: v,
                                        }))
                                    }
                                    placeholder="Chief complaint..."
                                    rows={3}
                                />

                                <Input
                                    label="Triage Category"
                                    value={triage.triageCategory}
                                    onChange={(v) =>
                                        setTriage((s) => ({
                                            ...s,
                                            triageCategory: v,
                                        }))
                                    }
                                    placeholder="e.g., Red / Yellow / Green"
                                />
                            </div>
                        </div>
                    </div>
                </div>

                <div className="grid grid-cols-1 gap-6 lg:grid-cols-3">
                    <div className="lg:col-span-2">
                        <div className="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                            <div className="mb-3 grid grid-cols-1 gap-3 sm:grid-cols-3">
                                <Input
                                    label="Doctor's Name"
                                    value={doctorBlock.doctorsName}
                                    onChange={(v) =>
                                        setDoctorBlock((s) => ({
                                            ...s,
                                            doctorsName: v,
                                        }))
                                    }
                                    placeholder="Dr. ..."
                                    className="sm:col-span-2"
                                />
                                <Input
                                    label="Time seen by Doctor"
                                    value={doctorBlock.timeSeenByDoctor}
                                    onChange={(v) =>
                                        setDoctorBlock((s) => ({
                                            ...s,
                                            timeSeenByDoctor: v,
                                        }))
                                    }
                                    placeholder="HH:MM"
                                />
                            </div>

                            <div className="grid grid-cols-1 gap-3 sm:grid-cols-3">
                                <TextArea
                                    label="History"
                                    value={doctorBlock.history}
                                    onChange={(v) =>
                                        setDoctorBlock((s) => ({
                                            ...s,
                                            history: v,
                                        }))
                                    }
                                    placeholder="Present history..."
                                    rows={6}
                                    className="sm:col-span-2"
                                />
                                <TextArea
                                    label="Past History"
                                    value={doctorBlock.pastHistory}
                                    onChange={(v) =>
                                        setDoctorBlock((s) => ({
                                            ...s,
                                            pastHistory: v,
                                        }))
                                    }
                                    placeholder="PMH / surgeries..."
                                    rows={6}
                                />
                            </div>
                        </div>
                    </div>

                    <div>
                        <div className="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                            <div className="mb-3 flex items-center justify-between">
                                <h2 className="text-sm font-extrabold tracking-wide text-slate-900">
                                    Examination
                                </h2>
                                <span className="text-xs font-semibold text-slate-500">
                                    Vitals
                                </span>
                            </div>

                            <div className="space-y-3">
                                <div className="grid grid-cols-2 gap-3">
                                    <Input
                                        label="Airway (Y/N)"
                                        value={exam.airwayClear}
                                        onChange={(v) =>
                                            setExam((s) => ({
                                                ...s,
                                                airwayClear: v,
                                            }))
                                        }
                                        placeholder="Y / N"
                                    />
                                    <Input
                                        label="Breathing (Y/N)"
                                        value={exam.breathingSpont}
                                        onChange={(v) =>
                                            setExam((s) => ({
                                                ...s,
                                                breathingSpont: v,
                                            }))
                                        }
                                        placeholder="Y / N"
                                    />
                                </div>

                                <div className="grid grid-cols-2 gap-3">
                                    <Input
                                        label="R/R (per min)"
                                        value={exam.rr}
                                        onChange={(v) =>
                                            setExam((s) => ({ ...s, rr: v }))
                                        }
                                        placeholder="e.g., 18"
                                    />
                                    <Input
                                        label="GCS"
                                        value={exam.gcs}
                                        onChange={(v) =>
                                            setExam((s) => ({ ...s, gcs: v }))
                                        }
                                        placeholder="e.g., 15"
                                    />
                                </div>

                                <div className="grid grid-cols-2 gap-3">
                                    <Input
                                        label="Circulation (Y/N)"
                                        value={exam.circulation}
                                        onChange={(v) =>
                                            setExam((s) => ({
                                                ...s,
                                                circulation: v,
                                            }))
                                        }
                                        placeholder="Y / N"
                                    />
                                    <Input
                                        label="Pulse (per min)"
                                        value={exam.pulse}
                                        onChange={(v) =>
                                            setExam((s) => ({ ...s, pulse: v }))
                                        }
                                        placeholder="e.g., 92"
                                    />
                                </div>

                                <Input
                                    label="BP"
                                    value={exam.bp}
                                    onChange={(v) =>
                                        setExam((s) => ({ ...s, bp: v }))
                                    }
                                    placeholder="e.g., 120/80"
                                />

                                <div className="grid grid-cols-2 gap-3">
                                    <Input
                                        label="Temp (F)"
                                        value={exam.tempF}
                                        onChange={(v) =>
                                            setExam((s) => ({ ...s, tempF: v }))
                                        }
                                        placeholder="e.g., 98.6"
                                    />
                                    <Input
                                        label="BSL"
                                        value={exam.bsl}
                                        onChange={(v) =>
                                            setExam((s) => ({ ...s, bsl: v }))
                                        }
                                        placeholder="e.g., 110"
                                    />
                                </div>

                                <div className="grid grid-cols-2 gap-3">
                                    <Input
                                        label="Medications (Y/N)"
                                        value={exam.medicationsYN}
                                        onChange={(v) =>
                                            setExam((s) => ({
                                                ...s,
                                                medicationsYN: v,
                                            }))
                                        }
                                        placeholder="Y / N"
                                    />
                                    <Input
                                        label="Allergies (Y/N)"
                                        value={exam.allergiesYN}
                                        onChange={(v) =>
                                            setExam((s) => ({
                                                ...s,
                                                allergiesYN: v,
                                            }))
                                        }
                                        placeholder="Y / N"
                                    />
                                </div>

                                <div className="grid grid-cols-2 gap-3">
                                    <Input
                                        label="Immunization (Y/N)"
                                        value={exam.immunizationYN}
                                        onChange={(v) =>
                                            setExam((s) => ({
                                                ...s,
                                                immunizationYN: v,
                                            }))
                                        }
                                        placeholder="Y / N"
                                    />
                                    <Input
                                        label="Smoker (Y/N)"
                                        value={exam.smokerYN}
                                        onChange={(v) =>
                                            setExam((s) => ({
                                                ...s,
                                                smokerYN: v,
                                            }))
                                        }
                                        placeholder="Y / N"
                                    />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div className="grid grid-cols-1 gap-6 lg:grid-cols-3">
                    <div className="space-y-6 lg:col-span-2">
                        <div className="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                            <div className="mb-3 flex items-center justify-between">
                                <h2 className="text-sm font-extrabold tracking-wide text-slate-900">
                                    Treatment Given
                                </h2>
                                <span className="text-xs font-semibold text-slate-500">
                                    Page 2
                                </span>
                            </div>
                            <TextArea
                                label=""
                                value={treatmentGiven}
                                onChange={setTreatmentGiven}
                                placeholder="Write treatment details..."
                                rows={8}
                            />
                        </div>

                        <EditableTable
                            title="Medications (Date / Drug / Dose / Route / Prescribed / Given / Time)"
                            columns={medColumns}
                            rows={medRows}
                            setRows={setMedRows}
                            addLabel="Add medication"
                            compact
                        />
                    </div>

                    <div className="space-y-6">
                        <EditableTable
                            title="Investigation Done"
                            columns={invColumns}
                            rows={investigationRows}
                            setRows={setInvestigationRows}
                            addLabel="Add investigation"
                        />

                        <EditableTable
                            title="Nursing Interventions"
                            columns={nursingColumns}
                            rows={nursingRows}
                            setRows={setNursingRows}
                            addLabel="Add nursing note"
                        />
                    </div>
                </div>

                <div className="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                    <div className="mb-3 flex items-center justify-between">
                        <h2 className="text-sm font-extrabold tracking-wide text-slate-900">
                            Discharge / Referral
                        </h2>
                        <span className="text-xs font-semibold text-slate-500">
                            Diagnosis + Plan
                        </span>
                    </div>

                    <div className="grid grid-cols-1 gap-4 lg:grid-cols-3">
                        <div className="lg:col-span-2">
                            <TextArea
                                label="Discharge Diagnosis"
                                value={discharge.diagnosis}
                                onChange={(v) =>
                                    setDischarge((s) => ({
                                        ...s,
                                        diagnosis: v,
                                    }))
                                }
                                placeholder="Write discharge diagnosis..."
                                rows={5}
                            />

                            <div className="mt-3 flex flex-wrap gap-2">
                                <Checkbox
                                    label="Discharge Plan"
                                    checked={discharge.dischargePlan}
                                    onChange={(v) =>
                                        setDischarge((s) => ({
                                            ...s,
                                            dischargePlan: v,
                                        }))
                                    }
                                />
                                <Checkbox
                                    label="Admitted"
                                    checked={discharge.admittedTo}
                                    onChange={(v) =>
                                        setDischarge((s) => ({
                                            ...s,
                                            admittedTo: v,
                                        }))
                                    }
                                />
                                <Checkbox
                                    label="Transferred"
                                    checked={discharge.transferredTo}
                                    onChange={(v) =>
                                        setDischarge((s) => ({
                                            ...s,
                                            transferredTo: v,
                                        }))
                                    }
                                />
                                <Checkbox
                                    label="Discharged Home"
                                    checked={discharge.dischargedHome}
                                    onChange={(v) =>
                                        setDischarge((s) => ({
                                            ...s,
                                            dischargedHome: v,
                                        }))
                                    }
                                />
                            </div>

                            <div className="mt-3 grid grid-cols-1 gap-3 sm:grid-cols-2">
                                <Input
                                    label="Referred To (Surgical / Medical / O&G / Paeds / Other)"
                                    value={discharge.referredTo}
                                    onChange={(v) =>
                                        setDischarge((s) => ({
                                            ...s,
                                            referredTo: v,
                                        }))
                                    }
                                    placeholder="e.g., Medical"
                                    className="sm:col-span-2"
                                />

                                <Input
                                    label="Admitted Date"
                                    value={discharge.admittedDate}
                                    onChange={(v) =>
                                        setDischarge((s) => ({
                                            ...s,
                                            admittedDate: v,
                                        }))
                                    }
                                    placeholder="YYYY-MM-DD"
                                />
                                <Input
                                    label="Admitted Time"
                                    value={discharge.admittedTime}
                                    onChange={(v) =>
                                        setDischarge((s) => ({
                                            ...s,
                                            admittedTime: v,
                                        }))
                                    }
                                    placeholder="HH:MM"
                                />

                                <Input
                                    label="Transferred Date"
                                    value={discharge.transferredDate}
                                    onChange={(v) =>
                                        setDischarge((s) => ({
                                            ...s,
                                            transferredDate: v,
                                        }))
                                    }
                                    placeholder="YYYY-MM-DD"
                                />
                                <Input
                                    label="Transferred Time"
                                    value={discharge.transferredTime}
                                    onChange={(v) =>
                                        setDischarge((s) => ({
                                            ...s,
                                            transferredTime: v,
                                        }))
                                    }
                                    placeholder="HH:MM"
                                />

                                <Input
                                    label="Discharged Date"
                                    value={discharge.dischargedDate}
                                    onChange={(v) =>
                                        setDischarge((s) => ({
                                            ...s,
                                            dischargedDate: v,
                                        }))
                                    }
                                    placeholder="YYYY-MM-DD"
                                />
                                <Input
                                    label="Discharged Time"
                                    value={discharge.dischargedTime}
                                    onChange={(v) =>
                                        setDischarge((s) => ({
                                            ...s,
                                            dischargedTime: v,
                                        }))
                                    }
                                    placeholder="HH:MM"
                                />
                            </div>
                        </div>

                        <div className="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                            <div className="text-xs font-bold tracking-wider text-slate-600 uppercase">
                                Review / Outcome
                            </div>

                            <div className="mt-3 flex flex-wrap gap-2">
                                <Checkbox
                                    label="Review by GP"
                                    checked={discharge.reviewByGP}
                                    onChange={(v) =>
                                        setDischarge((s) => ({
                                            ...s,
                                            reviewByGP: v,
                                        }))
                                    }
                                />
                                <Checkbox
                                    label="In ED"
                                    checked={discharge.reviewInED}
                                    onChange={(v) =>
                                        setDischarge((s) => ({
                                            ...s,
                                            reviewInED: v,
                                        }))
                                    }
                                />
                                <Checkbox
                                    label="# Clinic"
                                    checked={discharge.reviewClinic}
                                    onChange={(v) =>
                                        setDischarge((s) => ({
                                            ...s,
                                            reviewClinic: v,
                                        }))
                                    }
                                />
                                <Checkbox
                                    label="OPD"
                                    checked={discharge.reviewOPD}
                                    onChange={(v) =>
                                        setDischarge((s) => ({
                                            ...s,
                                            reviewOPD: v,
                                        }))
                                    }
                                />
                            </div>

                            <div className="mt-3">
                                <Input
                                    label="Review Date"
                                    value={discharge.reviewDate}
                                    onChange={(v) =>
                                        setDischarge((s) => ({
                                            ...s,
                                            reviewDate: v,
                                        }))
                                    }
                                    placeholder="YYYY-MM-DD"
                                />
                            </div>

                            <div className="mt-4 space-y-2">
                                <Checkbox
                                    label="Died in ED"
                                    checked={discharge.diedInED}
                                    onChange={(v) =>
                                        setDischarge((s) => ({
                                            ...s,
                                            diedInED: v,
                                        }))
                                    }
                                />
                                <Input
                                    label="Died Time"
                                    value={discharge.diedTime}
                                    onChange={(v) =>
                                        setDischarge((s) => ({
                                            ...s,
                                            diedTime: v,
                                        }))
                                    }
                                    placeholder="HH:MM"
                                />
                                <Checkbox
                                    label="Left Against Medical Advice"
                                    checked={discharge.leftAgainstMedicalAdvice}
                                    onChange={(v) =>
                                        setDischarge((s) => ({
                                            ...s,
                                            leftAgainstMedicalAdvice: v,
                                        }))
                                    }
                                />
                                <Checkbox
                                    label="Did not want to be seen"
                                    checked={discharge.didNotWantToBeSeen}
                                    onChange={(v) =>
                                        setDischarge((s) => ({
                                            ...s,
                                            didNotWantToBeSeen: v,
                                        }))
                                    }
                                />
                            </div>

                            <div className="mt-4 grid grid-cols-1 gap-3">
                                <Input
                                    label="Discharging Doctor's Name"
                                    value={discharge.dischargingDoctorName}
                                    onChange={(v) =>
                                        setDischarge((s) => ({
                                            ...s,
                                            dischargingDoctorName: v,
                                        }))
                                    }
                                    placeholder="Dr. ..."
                                />
                                <Input
                                    label="Doctor Signature"
                                    value={discharge.doctorSignature}
                                    onChange={(v) =>
                                        setDischarge((s) => ({
                                            ...s,
                                            doctorSignature: v,
                                        }))
                                    }
                                    placeholder="Signature / initials"
                                />
                                <Input
                                    label="Discharging Nurse / Disp. Sign"
                                    value={discharge.dischargingNurseSign}
                                    onChange={(v) =>
                                        setDischarge((s) => ({
                                            ...s,
                                            dischargingNurseSign: v,
                                        }))
                                    }
                                    placeholder="Signature / initials"
                                />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    );
}
