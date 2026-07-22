import DentalChart, {
    type DentalChartValue,
} from '@/components/ui/dental-chart';
import { triageBadgeClass } from '@/lib/constants';
import { printServiceorder } from '@/routes';
import { clsx } from 'clsx';
import { FileText, Printer } from 'lucide-react';
import { useState } from 'react';

type TransactionElementItem = {
    id: number;
    amount?: number;
    income_or_expense?: string;
    type?: string;
    created_at?: string;
    transaction?: { tr_number?: string; created_at?: string; type?: string };
    expense_category?: { name?: string };
    service_recestation?: { name?: string };
    exp_voucher?: { vc_number?: string };
};

type ExpenseVoucherItem = {
    id: number;
    vc_number?: string;
    amount?: number;
    share_amount?: number;
    service_orders_count?: number;
    status?: string;
    created_at?: string;
    exp_category?: { name?: string };
    payed_to?: { name?: string };
};

type ReceivableItem = {
    id: number;
    amount?: number;
    orignal_amount?: number;
    status?: string;
    due_date?: string;
    patient?: { name?: string };
    panel?: { name?: string };
    transaction?: { tr_number?: string };
};

type VitalSignItem = {
    id: number;
    temperature?: number;
    blood_pressure_systolic?: number;
    blood_pressure_diastolic?: number;
    pulse_rate?: number;
    respiratory_rate?: number;
    oxygen_saturation?: number;
    recorded_at?: string;
};

type TriageHistoryEntry = {
    id: number;
    changed_at: string;
    old_triage?: { name: string; color: string } | null;
    new_triage?: { name: string; color: string } | null;
    changed_by?: { name: string } | null;
};

type AttachmentItem = {
    id: number;
    file_name: string;
    file_type: string;
    label?: string | null;
    url: string;
};

type TreatmentRecordItem = {
    id: number;
    chief_complaint?: string;
    history_of_present_illness?: string;
    diagnosis_code?: string;
    diagnosis_text?: string;
    treatment_plan?: string;
    prescriptions?: {
        drug_name: string;
        dose?: string;
        frequency?: string;
        duration?: string;
        route?: string;
        instructions?: string;
    }[];
    follow_up_date?: string;
    outcome?: string;
    referral_to?: string;
    treated_at?: string;
    is_finalized?: boolean;
    treating_doctor?: { name?: string };
    vital_signs?: VitalSignItem[];
    triage?: { id: number; name: string; color: string } | null;
    triage_histories?: TriageHistoryEntry[];
    dental_chart?: DentalChartValue | null;
    attachments?: AttachmentItem[];
};

export type ServiceOrderViewData = {
    id: number;
    so_number: string;
    so_short?: string;
    token?: string | number;
    status: string;
    created_at?: string;
    patient?: { name?: string; ps_number?: string };
    service?: { name?: string };
    doctor?: { name?: string };
    income_total?: number | null;
    expense_total?: number | null;
    voucher_expense_total?: number | null;
    transaction_elements?: TransactionElementItem[];
    expense_vouchers?: ExpenseVoucherItem[];
    receivables?: ReceivableItem[];
    treatment_record?: TreatmentRecordItem | null;
};

interface ServiceOrderViewProps {
    serviceOrder: ServiceOrderViewData;
    className?: string;
}

const currency = new Intl.NumberFormat('en-PK', {
    style: 'currency',
    currency: 'PKR',
    maximumFractionDigits: 2,
});

function formatMoney(value?: number | null): string {
    return currency.format(value ?? 0);
}

function formatDate(value?: string): string {
    if (!value) return '—';
    return new Date(value).toLocaleString();
}

function statusBadgeClass(status: string) {
    const s = status.toLowerCase();
    if (s === 'in-progress')
        return 'bg-blue-100 text-blue-700 ring-1 ring-blue-200';
    if (s === 'open')
        return 'bg-amber-100 text-amber-700 ring-1 ring-amber-200';
    if (s === 'treated' || s === 'closed')
        return 'bg-emerald-100 text-emerald-700 ring-1 ring-emerald-200';
    return 'bg-slate-100 text-slate-600';
}

function Card({
    title,
    children,
}: {
    title: string;
    children: React.ReactNode;
}) {
    return (
        <div className="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm md:p-5">
            <h3 className="mb-3 text-sm font-semibold text-slate-900">
                {title}
            </h3>
            {children}
        </div>
    );
}

function Field({ label, value }: { label: string; value?: string | null }) {
    if (!value) return null;
    return (
        <div className="mb-3 last:mb-0">
            <p className="text-xs font-medium text-slate-500">{label}</p>
            <p className="mt-0.5 text-sm whitespace-pre-line text-slate-800">
                {value}
            </p>
        </div>
    );
}

export default function ServiceOrderView({
    serviceOrder,
    className,
}: ServiceOrderViewProps) {
    const [tab, setTab] = useState<'transactions' | 'vouchers'>('transactions');
    const tr = serviceOrder.treatment_record;
    const lastVital = tr?.vital_signs?.[tr.vital_signs.length - 1];
    const dentalTeeth = tr?.dental_chart ? Object.entries(tr.dental_chart) : [];

    return (
        <div className={clsx('flex flex-col gap-4', className)}>
            {/* Header */}
            <div className="flex flex-wrap items-center justify-between gap-3 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                <div>
                    <div className="flex items-center gap-2">
                        <h2 className="font-mono text-sm font-semibold text-slate-900">
                            {serviceOrder.so_number}
                        </h2>
                        <span
                            className={clsx(
                                'rounded-full px-2 py-0.5 text-xs font-semibold',
                                statusBadgeClass(serviceOrder.status),
                            )}
                        >
                            {serviceOrder.status}
                        </span>
                    </div>
                    <p className="mt-1 text-xs text-slate-500">
                        {serviceOrder.service?.name} &bull; Dr.{' '}
                        {serviceOrder.doctor?.name ?? '—'} &bull;{' '}
                        {formatDate(serviceOrder.created_at)}
                    </p>
                </div>
                <a
                    href={printServiceorder({ id: serviceOrder.id }).url}
                    target="_blank"
                    rel="noopener noreferrer"
                    className="flex items-center gap-1.5 rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-50"
                >
                    <Printer className="h-3.5 w-3.5" /> Print
                </a>
            </div>

            <div className="grid grid-cols-1 gap-4 xl:grid-cols-3">
                {/* Main section */}
                <div className="flex flex-col gap-4 xl:col-span-2">
                    {tr ? (
                        <>
                            <Card title="Clinical Summary">
                                <Field
                                    label="Chief Complaint"
                                    value={tr.chief_complaint}
                                />
                                <Field
                                    label="History of Present Illness"
                                    value={tr.history_of_present_illness}
                                />
                                <Field
                                    label="Diagnosis"
                                    value={
                                        [tr.diagnosis_code, tr.diagnosis_text]
                                            .filter(Boolean)
                                            .join(' — ') || undefined
                                    }
                                />
                                <Field
                                    label="Treatment Plan / Notes"
                                    value={tr.treatment_plan}
                                />
                                <Field label="Outcome" value={tr.outcome} />
                                <Field
                                    label="Follow-up Date"
                                    value={tr.follow_up_date}
                                />
                                <Field
                                    label="Referral To"
                                    value={tr.referral_to}
                                />
                                <Field
                                    label="Time of Treatment"
                                    value={formatDate(tr.treated_at)}
                                />
                                {tr.triage && (
                                    <div className="mb-3">
                                        <p className="text-xs font-medium text-slate-500">
                                            Triage
                                        </p>
                                        <span
                                            className={clsx(
                                                'mt-1 inline-block rounded-full px-2 py-0.5 text-xs font-semibold ring-1',
                                                triageBadgeClass(
                                                    tr.triage.color,
                                                ),
                                            )}
                                        >
                                            {tr.triage.name}
                                        </span>
                                    </div>
                                )}
                            </Card>

                            {!!tr.triage_histories?.length && (
                                <Card title="Triage History">
                                    <div className="space-y-2">
                                        {tr.triage_histories.map((h) => (
                                            <div
                                                key={h.id}
                                                className="flex items-center justify-between text-xs"
                                            >
                                                <span className="text-slate-600">
                                                    {h.old_triage?.name ?? '—'}{' '}
                                                    &rarr;{' '}
                                                    <strong>
                                                        {h.new_triage?.name ??
                                                            '—'}
                                                    </strong>{' '}
                                                    by{' '}
                                                    {h.changed_by?.name ??
                                                        'System'}
                                                </span>
                                                <span className="text-slate-400">
                                                    {formatDate(h.changed_at)}
                                                </span>
                                            </div>
                                        ))}
                                    </div>
                                </Card>
                            )}

                            {!!tr.prescriptions?.length && (
                                <Card title="Prescriptions">
                                    <div className="overflow-x-auto">
                                        <table className="w-full text-sm">
                                            <thead>
                                                <tr className="text-left text-xs text-slate-500">
                                                    <th className="pb-2">
                                                        Drug
                                                    </th>
                                                    <th className="pb-2">
                                                        Dose
                                                    </th>
                                                    <th className="pb-2">
                                                        Frequency
                                                    </th>
                                                    <th className="pb-2">
                                                        Duration
                                                    </th>
                                                    <th className="pb-2">
                                                        Route
                                                    </th>
                                                    <th className="pb-2">
                                                        Instructions
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody className="divide-y divide-slate-100">
                                                {tr.prescriptions.map(
                                                    (rx, i) => (
                                                        <tr key={i}>
                                                            <td className="py-1.5 font-medium text-slate-800">
                                                                {rx.drug_name}
                                                            </td>
                                                            <td className="py-1.5 text-slate-600">
                                                                {rx.dose ?? '—'}
                                                            </td>
                                                            <td className="py-1.5 text-slate-600">
                                                                {rx.frequency ??
                                                                    '—'}
                                                            </td>
                                                            <td className="py-1.5 text-slate-600">
                                                                {rx.duration ??
                                                                    '—'}
                                                            </td>
                                                            <td className="py-1.5 text-slate-600">
                                                                {rx.route ??
                                                                    '—'}
                                                            </td>
                                                            <td className="py-1.5 text-slate-600">
                                                                {rx.instructions ??
                                                                    '—'}
                                                            </td>
                                                        </tr>
                                                    ),
                                                )}
                                            </tbody>
                                        </table>
                                    </div>
                                </Card>
                            )}

                            {lastVital && (
                                <Card title="Vitals">
                                    <div className="grid grid-cols-2 gap-3 sm:grid-cols-4">
                                        {[
                                            [
                                                'Temp',
                                                lastVital.temperature,
                                                '°C',
                                            ],
                                            [
                                                'BP',
                                                lastVital.blood_pressure_systolic &&
                                                lastVital.blood_pressure_diastolic
                                                    ? `${lastVital.blood_pressure_systolic}/${lastVital.blood_pressure_diastolic}`
                                                    : undefined,
                                                '',
                                            ],
                                            [
                                                'Pulse',
                                                lastVital.pulse_rate,
                                                'bpm',
                                            ],
                                            [
                                                'Resp. Rate',
                                                lastVital.respiratory_rate,
                                                '/min',
                                            ],
                                            [
                                                'SpO2',
                                                lastVital.oxygen_saturation,
                                                '%',
                                            ],
                                        ].map(([label, value, unit]) =>
                                            value ? (
                                                <div key={label as string}>
                                                    <p className="text-xs text-slate-500">
                                                        {label}
                                                    </p>
                                                    <p className="text-sm font-semibold text-slate-800">
                                                        {value}
                                                        {unit}
                                                    </p>
                                                </div>
                                            ) : null,
                                        )}
                                    </div>
                                </Card>
                            )}

                            {!!dentalTeeth.length && (
                                <Card title="Dental Chart">
                                    <DentalChart
                                        value={tr.dental_chart ?? {}}
                                        onChange={() => {}}
                                        disabled
                                    />
                                </Card>
                            )}

                            {!!tr.attachments?.length && (
                                <Card title="Images & Reports">
                                    <div className="grid grid-cols-2 gap-3 sm:grid-cols-4">
                                        {tr.attachments.map((a) => (
                                            <a
                                                key={a.id}
                                                href={a.url}
                                                target="_blank"
                                                rel="noopener noreferrer"
                                                className="flex flex-col items-center gap-1.5 rounded-xl border border-slate-200 p-2 text-center hover:bg-slate-50"
                                            >
                                                {a.file_type?.startsWith(
                                                    'image',
                                                ) ? (
                                                    <img
                                                        src={a.url}
                                                        alt={
                                                            a.label ??
                                                            a.file_name
                                                        }
                                                        className="h-16 w-16 rounded-lg object-cover"
                                                    />
                                                ) : (
                                                    <FileText className="h-8 w-8 text-slate-400" />
                                                )}
                                                <span className="truncate text-[11px] text-slate-600">
                                                    {a.label ?? a.file_name}
                                                </span>
                                            </a>
                                        ))}
                                    </div>
                                </Card>
                            )}
                        </>
                    ) : (
                        <Card title="Clinical Summary">
                            <p className="text-sm text-slate-500">
                                No treatment record for this service order yet.
                            </p>
                        </Card>
                    )}
                </div>

                {/* Sidebar */}
                <div className="flex flex-col gap-4">
                    <div className="grid grid-cols-3 gap-2">
                        <div className="rounded-xl border border-slate-200 bg-white p-3 text-center shadow-sm">
                            <p className="text-[11px] text-slate-500">Income</p>
                            <p className="text-sm font-bold text-emerald-700">
                                {formatMoney(serviceOrder.income_total)}
                            </p>
                        </div>
                        <div className="rounded-xl border border-slate-200 bg-white p-3 text-center shadow-sm">
                            <p className="text-[11px] text-slate-500">
                                Expense
                            </p>
                            <p className="text-sm font-bold text-red-700">
                                {formatMoney(serviceOrder.expense_total)}
                            </p>
                        </div>
                        <div className="rounded-xl border border-slate-200 bg-white p-3 text-center shadow-sm">
                            <p className="text-[11px] text-slate-500">
                                Vouchers
                            </p>
                            <p className="text-sm font-bold text-slate-800">
                                {formatMoney(
                                    serviceOrder.voucher_expense_total,
                                )}
                            </p>
                        </div>
                    </div>

                    <div className="rounded-2xl border border-slate-200 bg-white shadow-sm">
                        <div className="flex border-b border-slate-100">
                            {(
                                [
                                    ['transactions', 'Transactions'],
                                    ['vouchers', 'Vouchers'],
                                ] as const
                            ).map(([value, label]) => (
                                <button
                                    key={value}
                                    type="button"
                                    onClick={() => setTab(value)}
                                    className={clsx(
                                        'flex-1 border-b-2 px-3 py-2.5 text-xs font-semibold transition-colors',
                                        tab === value
                                            ? 'border-slate-800 text-slate-900'
                                            : 'border-transparent text-slate-400 hover:text-slate-600',
                                    )}
                                >
                                    {label}
                                </button>
                            ))}
                        </div>

                        <div className="max-h-[70vh] overflow-y-auto p-4">
                            {tab === 'transactions' && (
                                <div className="space-y-4">
                                    <div>
                                        <h4 className="mb-2 text-xs font-semibold tracking-wide text-slate-500 uppercase">
                                            Transaction Elements
                                        </h4>
                                        {!serviceOrder.transaction_elements
                                            ?.length ? (
                                            <p className="text-xs text-slate-400">
                                                No transactions.
                                            </p>
                                        ) : (
                                            <div className="space-y-2">
                                                {serviceOrder.transaction_elements.map(
                                                    (el) => (
                                                        <div
                                                            key={el.id}
                                                            className="flex items-center justify-between text-xs"
                                                        >
                                                            <span className="text-slate-600">
                                                                {el.transaction
                                                                    ?.tr_number ??
                                                                    '—'}{' '}
                                                                (
                                                                {
                                                                    el.income_or_expense
                                                                }
                                                                )
                                                            </span>
                                                            <span
                                                                className={clsx(
                                                                    'font-semibold',
                                                                    el.income_or_expense ===
                                                                        'INCOME'
                                                                        ? 'text-emerald-700'
                                                                        : 'text-red-700',
                                                                )}
                                                            >
                                                                {formatMoney(
                                                                    el.amount,
                                                                )}
                                                            </span>
                                                        </div>
                                                    ),
                                                )}
                                            </div>
                                        )}
                                    </div>
                                    <div>
                                        <h4 className="mb-2 text-xs font-semibold tracking-wide text-slate-500 uppercase">
                                            Receivables
                                        </h4>
                                        {!serviceOrder.receivables?.length ? (
                                            <p className="text-xs text-slate-400">
                                                No receivables.
                                            </p>
                                        ) : (
                                            <div className="space-y-2">
                                                {serviceOrder.receivables.map(
                                                    (r) => (
                                                        <div
                                                            key={r.id}
                                                            className="flex items-center justify-between text-xs"
                                                        >
                                                            <span className="text-slate-600">
                                                                {r.panel
                                                                    ?.name ??
                                                                    'Patient'}{' '}
                                                                &bull;{' '}
                                                                {r.status}
                                                            </span>
                                                            <span className="font-semibold text-slate-800">
                                                                {formatMoney(
                                                                    r.amount,
                                                                )}
                                                            </span>
                                                        </div>
                                                    ),
                                                )}
                                            </div>
                                        )}
                                    </div>
                                </div>
                            )}

                            {tab === 'vouchers' && (
                                <div>
                                    {!serviceOrder.expense_vouchers?.length ? (
                                        <p className="text-xs text-slate-400">
                                            No expense vouchers.
                                        </p>
                                    ) : (
                                        <div className="space-y-3">
                                            {serviceOrder.expense_vouchers.map(
                                                (v) => (
                                                    <div
                                                        key={v.id}
                                                        className="text-xs"
                                                    >
                                                        <div className="flex items-center justify-between">
                                                            <span className="font-mono text-slate-600">
                                                                {v.vc_number}
                                                            </span>
                                                            <span className="font-semibold text-slate-800">
                                                                {formatMoney(
                                                                    v.share_amount ??
                                                                        v.amount,
                                                                )}
                                                            </span>
                                                        </div>
                                                        {!!v.service_orders_count &&
                                                            v.service_orders_count >
                                                                1 && (
                                                                <p className="text-[11px] text-slate-400">
                                                                    of{' '}
                                                                    {formatMoney(
                                                                        v.amount,
                                                                    )}{' '}
                                                                    total,
                                                                    shared
                                                                    across{' '}
                                                                    {
                                                                        v.service_orders_count
                                                                    }{' '}
                                                                    orders
                                                                </p>
                                                            )}
                                                        <p className="mt-0.5 text-slate-400">
                                                            {v.exp_category
                                                                ?.name ??
                                                                '—'}{' '}
                                                            &bull;{' '}
                                                            {v.payed_to?.name ??
                                                                '—'}{' '}
                                                            &bull; {v.status}
                                                        </p>
                                                    </div>
                                                ),
                                            )}
                                        </div>
                                    )}
                                </div>
                            )}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    );
}
