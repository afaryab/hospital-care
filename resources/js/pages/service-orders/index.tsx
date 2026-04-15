import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import AppLayout from '@/layouts/app-layout';
import { home } from '@/routes';
import { type BreadcrumbItem } from '@/types';
import { Head, router, usePage } from '@inertiajs/react';
import { useMemo, useState } from 'react';

type ServiceOrderListItem = {
    id: number;
    so_number: string;
    status: string;
    created_at: string;
    patient?: { name?: string; ps_number?: string };
    service?: { name?: string; icon?: string | null };
    doctor?: { name?: string };
    income_total?: number | null;
    expense_total?: number | null;
    voucher_expense_total?: number | null;
};

type TransactionElementItem = {
    id: number;
    amount?: number;
    income_or_expense?: string;
    type?: string;
    transaction?: { tr_number?: string; created_at?: string; type?: string };
    expense_category?: { name?: string };
    service_recestation?: { name?: string };
    exp_voucher?: { vc_number?: string };
};

type ExpenseVoucherItem = {
    id: number;
    vc_number?: string;
    amount?: number;
    status?: string;
    created_at?: string;
    exp_category?: { name?: string };
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

type TreatmentRecordItem = {
    id: number;
    diagnosis_text?: string;
    treatment_plan?: string;
    outcome?: string;
    treated_at?: string;
    is_finalized?: boolean;
    treating_doctor?: { name?: string };
    vital_signs?: VitalSignItem[];
};

type ServiceOrderDetail = ServiceOrderListItem & {
    patient?: { name?: string; ps_number?: string; contact?: string; cnic?: string };
    transaction_elements?: TransactionElementItem[];
    expense_vouchers?: ExpenseVoucherItem[];
    receivables?: ReceivableItem[];
    treatment_record?: TreatmentRecordItem | null;
};

type PageProps = {
    serviceOrders: {
        data: ServiceOrderListItem[];
        current_page: number;
        last_page: number;
    };
    selectedServiceOrder?: ServiceOrderDetail | null;
    filters: {
        search?: string;
        status?: string;
        type?: string;
    };
};

const currency = new Intl.NumberFormat('en-PK', {
    style: 'currency',
    currency: 'PKR',
    maximumFractionDigits: 2,
});

function formatMoney(value?: number | null): string {
    return currency.format(value ?? 0);
}

function formatDate(value?: string): string {
    if (!value) {
        return '-';
    }

    return new Date(value).toLocaleString();
}

function healthIconUrl(icon?: string | null): string | null {
    if (!icon) {
        return null;
    }

    return `/vendor/blade-health-icons/${icon}.svg`;
}

function ServiceOrderDetailPanel({
    so,
    selectedNet,
    newStatus,
    setNewStatus,
    updatingStatus,
    changeStatus,
}: {
    so: ServiceOrderDetail;
    selectedNet: number;
    newStatus: string;
    setNewStatus: (v: string) => void;
    updatingStatus: boolean;
    changeStatus: () => void;
}) {
    const incomeElements = useMemo(
        () => (so.transaction_elements ?? []).filter((e) => e.income_or_expense === 'INCOME' && e.type !== 'RECES-IND'),
        [so.transaction_elements],
    );
    const recesElements = useMemo(
        () => (so.transaction_elements ?? []).filter((e) => e.type === 'RECES-IND'),
        [so.transaction_elements],
    );
    const expenseElements = useMemo(
        () => (so.transaction_elements ?? []).filter((e) => e.income_or_expense === 'EXPENSE'),
        [so.transaction_elements],
    );

    return (
        <>
            {/* Header */}
            <div className="space-y-1 border-b pb-3">
                <h2 className="text-lg font-bold text-gray-900">{so.so_number}</h2>
                <p className="text-sm text-gray-600">
                    {so.patient?.name ?? '-'} · {so.patient?.ps_number ?? '-'}
                </p>
                <p className="text-sm text-gray-600">Doctor: {so.doctor?.name ?? '-'}</p>
                <span className="inline-block rounded-full bg-gray-100 px-2 py-0.5 text-xs font-semibold uppercase text-gray-700">
                    {so.status}
                </span>
            </div>

            {/* Change Status */}
            <div className="space-y-2 border-b pb-3">
                <h3 className="text-sm font-semibold text-gray-900">Change Status</h3>
                <Select value={newStatus || 'none'} onValueChange={(v) => setNewStatus(v === 'none' ? '' : v)}>
                    <SelectTrigger className="w-full">
                        <SelectValue placeholder="Select new status" />
                    </SelectTrigger>
                    <SelectContent>
                        <SelectItem value="none" disabled>
                            Select new status
                        </SelectItem>
                        <SelectItem value="OPEN">OPEN</SelectItem>
                        <SelectItem value="IN-PROGRESS">IN-PROGRESS</SelectItem>
                        <SelectItem value="CLOSED">CLOSED</SelectItem>
                    </SelectContent>
                </Select>
                <Button className="w-full" disabled={!newStatus || updatingStatus} onClick={changeStatus}>
                    {updatingStatus ? 'Updating\u2026' : 'Update Status'}
                </Button>
            </div>

            {/* Financial Summary */}
            <div className="grid grid-cols-2 gap-2 text-sm">
                <div className="rounded-md bg-green-50 p-2 text-green-800">
                    <span className="text-xs font-medium">Income</span>
                    <p className="font-semibold">{formatMoney(so.income_total)}</p>
                </div>
                <div className="rounded-md bg-red-50 p-2 text-red-800">
                    <span className="text-xs font-medium">Expense</span>
                    <p className="font-semibold">{formatMoney(so.expense_total)}</p>
                </div>
                <div className="rounded-md bg-blue-50 p-2 text-blue-800">
                    <span className="text-xs font-medium">Voucher Exp.</span>
                    <p className="font-semibold">{formatMoney(so.voucher_expense_total)}</p>
                </div>
                <div className="rounded-md bg-gray-100 p-2 text-gray-900">
                    <span className="text-xs font-medium">Net</span>
                    <p className="font-semibold">{formatMoney(selectedNet)}</p>
                </div>
            </div>

            {/* Income Transactions */}
            <DetailSection title="Income Transactions" count={incomeElements.length} color="green">
                {incomeElements.map((el) => (
                    <div key={el.id} className="flex items-center justify-between rounded-md border p-2 text-sm">
                        <div>
                            <p className="font-mono text-xs text-gray-600">{el.transaction?.tr_number ?? '-'}</p>
                            <span className="text-xs text-gray-500">{el.type}</span>
                        </div>
                        <span className="font-mono font-medium text-green-700">{formatMoney(el.amount)}</span>
                    </div>
                ))}
            </DetailSection>

            {/* Recestation Charges */}
            <DetailSection title="Recestation Charges" count={recesElements.length} color="indigo">
                {recesElements.map((el) => (
                    <div key={el.id} className="flex items-center justify-between rounded-md border p-2 text-sm">
                        <div>
                            <p className="font-mono text-xs text-gray-600">{el.transaction?.tr_number ?? '-'}</p>
                            <p className="text-xs text-gray-500">{el.service_recestation?.name ?? 'Recestation'}</p>
                        </div>
                        <span className="font-mono font-medium text-indigo-700">{formatMoney(el.amount)}</span>
                    </div>
                ))}
            </DetailSection>

            {/* Expense Transactions */}
            <DetailSection title="Expense Transactions" count={expenseElements.length} color="red">
                {expenseElements.map((el) => (
                    <div key={el.id} className="flex items-center justify-between rounded-md border p-2 text-sm">
                        <div>
                            <p className="font-mono text-xs text-gray-600">{el.transaction?.tr_number ?? '-'}</p>
                            <p className="text-xs text-gray-500">
                                {el.type} {el.expense_category?.name ? `· ${el.expense_category.name}` : ''}
                                {el.exp_voucher?.vc_number ? ` · ${el.exp_voucher.vc_number}` : ''}
                            </p>
                        </div>
                        <span className="font-mono font-medium text-red-700">{formatMoney(el.amount)}</span>
                    </div>
                ))}
            </DetailSection>

            {/* Receivables */}
            <DetailSection title="Receivables" count={so.receivables?.length ?? 0} color="orange">
                {(so.receivables ?? []).map((rec) => (
                    <div key={rec.id} className="rounded-md border p-2 text-sm">
                        <div className="flex items-center justify-between">
                            <span className="font-mono text-xs text-gray-600">{rec.transaction?.tr_number ?? '-'}</span>
                            <span
                                className={`rounded-full px-2 py-0.5 text-xs font-medium ${
                                    rec.status === 'paid' || rec.status === 'payed'
                                        ? 'bg-green-100 text-green-800'
                                        : 'bg-orange-100 text-orange-800'
                                }`}
                            >
                                {(rec.status ?? 'pending').toUpperCase()}
                            </span>
                        </div>
                        <div className="mt-1 flex items-center justify-between text-xs text-gray-500">
                            <span>{rec.panel?.name ?? '-'}</span>
                            <span>
                                {formatMoney(rec.amount)} / {formatMoney(rec.orignal_amount)}
                            </span>
                        </div>
                    </div>
                ))}
            </DetailSection>

            {/* Expense Vouchers */}
            <DetailSection title="Expense Vouchers" count={so.expense_vouchers?.length ?? 0} color="purple">
                {(so.expense_vouchers ?? []).map((voucher) => (
                    <div key={voucher.id} className="flex items-center justify-between rounded-md border p-2 text-sm">
                        <div>
                            <p className="font-mono text-xs font-medium">{voucher.vc_number}</p>
                            <p className="text-xs text-gray-500">{voucher.exp_category?.name ?? '-'}</p>
                            {voucher.status && (
                                <span
                                    className={`mt-0.5 inline-block rounded-full px-2 py-0.5 text-xs font-medium ${
                                        voucher.status === 'paid' || voucher.status === 'payed'
                                            ? 'bg-green-100 text-green-800'
                                            : 'bg-orange-100 text-orange-800'
                                    }`}
                                >
                                    {voucher.status.toUpperCase()}
                                </span>
                            )}
                        </div>
                        <span className="font-mono font-medium text-purple-700">{formatMoney(voucher.amount)}</span>
                    </div>
                ))}
            </DetailSection>

            {/* Treatment */}
            <div className="space-y-2">
                <h3 className="text-sm font-semibold text-gray-900">Treatment</h3>
                {so.treatment_record ? (
                    <div className="rounded-md border p-2 text-sm">
                        <p>Diagnosis: {so.treatment_record.diagnosis_text ?? '-'}</p>
                        <p>Outcome: {so.treatment_record.outcome ?? '-'}</p>
                        <p>Treated At: {formatDate(so.treatment_record.treated_at)}</p>
                        <p>Finalized: {so.treatment_record.is_finalized ? 'Yes' : 'No'}</p>
                    </div>
                ) : (
                    <p className="text-sm text-gray-500">No treatment record.</p>
                )}
            </div>
        </>
    );
}

function DetailSection({
    title,
    count,
    color,
    children,
}: {
    title: string;
    count: number;
    color: string;
    children: React.ReactNode;
}) {
    const colorMap: Record<string, string> = {
        green: 'bg-green-100 text-green-800',
        red: 'bg-red-100 text-red-800',
        indigo: 'bg-indigo-100 text-indigo-800',
        orange: 'bg-orange-100 text-orange-800',
        purple: 'bg-purple-100 text-purple-800',
    };

    return (
        <div className="space-y-2">
            <div className="flex items-center gap-2">
                <h3 className="text-sm font-semibold text-gray-900">{title}</h3>
                <span className={`rounded-full px-1.5 py-0.5 text-xs font-medium ${colorMap[color] ?? 'bg-gray-100 text-gray-800'}`}>
                    {count}
                </span>
            </div>
            {count > 0 ? <div className="space-y-2">{children}</div> : <p className="text-sm text-gray-500">None.</p>}
        </div>
    );
}

export default function ServiceOrdersOverview() {
    const { serviceOrders, selectedServiceOrder, filters } =
        usePage<PageProps>().props;

    const [search, setSearch] = useState(filters.search ?? '');
    const [status, setStatus] = useState(filters.status ?? '');
    const [activeTab, setActiveTab] = useState(filters.type ?? '');
    const [newStatus, setNewStatus] = useState<string>('');
    const [updatingStatus, setUpdatingStatus] = useState(false);

    const breadcrumbs: BreadcrumbItem[] = [
        {
            title: 'Dashboard',
            href: home().url,
        },
        {
            title: 'Service Orders',
            href: '/service-orders',
        },
    ];

    const selectedNet = useMemo(() => {
        if (!selectedServiceOrder) {
            return 0;
        }

        const income = selectedServiceOrder.income_total ?? 0;
        const expense = selectedServiceOrder.expense_total ?? 0;

        return income - expense;
    }, [selectedServiceOrder]);

    const applyFilters = () => {
        router.get(
            '/service-orders',
            {
                search: search || undefined,
                status: status || undefined,
                type: activeTab || undefined,
                service_order_id: selectedServiceOrder?.id,
            },
            {
                preserveState: true,
                replace: true,
            },
        );
    };

    const clearFilters = () => {
        setSearch('');
        setStatus('');
        setActiveTab('');
        router.get('/service-orders', {}, { preserveState: false, replace: true });
    };

    const switchTab = (type: string) => {
        setActiveTab(type);
        setNewStatus('');
        router.get(
            '/service-orders',
            {
                type: type || undefined,
                search: search || undefined,
                status: status || undefined,
            },
            { preserveState: true, replace: true },
        );
    };

    const openServiceOrder = (serviceOrderId: number) => {
        setNewStatus('');
        router.get(
            '/service-orders',
            {
                search: search || undefined,
                status: status || undefined,
                type: activeTab || undefined,
                service_order_id: serviceOrderId,
            },
            {
                preserveState: true,
                replace: true,
            },
        );
    };

    const changeStatus = () => {
        if (!selectedServiceOrder || !newStatus) {
            return;
        }

        setUpdatingStatus(true);

        router.patch(
            `/service-orders/${selectedServiceOrder.id}/status`,
            { status: newStatus },
            {
                preserveState: false,
                onFinish: () => {
                    setUpdatingStatus(false);
                    setNewStatus('');
                },
            },
        );
    };

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Service Orders" />

            <div className="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl bg-[#06df72] p-1 dark:bg-[#262626]">
                {/* Department tabs */}
                <div className="flex flex-wrap gap-1 rounded-xl bg-white px-4 pt-3 pb-1 dark:bg-neutral-950">
                    {[
                        { label: 'General', value: '' },
                        { label: 'OPD', value: 'OPD' },
                        { label: 'Indoor', value: 'IND' },
                        { label: 'Emergency', value: 'EMG' },
                        { label: 'Dental', value: 'DNT' },
                        { label: 'Laboratory', value: 'LAB' },
                        { label: 'Ultrasound', value: 'ULT' },
                        { label: 'Radiology', value: 'RAD' },
                    ].map((tab) => (
                        <button
                            key={tab.value}
                            onClick={() => switchTab(tab.value)}
                            className={`rounded-md px-4 py-1.5 text-sm font-medium transition-colors ${
                                activeTab === tab.value
                                    ? 'bg-[#1c398e] text-white'
                                    : 'text-[#1c398e] hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-neutral-800'
                            }`}
                        >
                            {tab.label}
                        </button>
                    ))}
                </div>
                <div className="grid gap-4 rounded-xl bg-white p-4 text-[#1c398e] dark:bg-neutral-950 lg:grid-cols-3">
                    <div className="space-y-2 lg:col-span-2">
                        <Label htmlFor="service-order-search">Search</Label>
                        <Input
                            id="service-order-search"
                            value={search}
                            onChange={(event) => setSearch(event.target.value)}
                            placeholder="SO number, SO short, PS number, doctor, reception, TR number"
                        />
                    </div>
                    <div className="space-y-2">
                        <Label htmlFor="service-order-status">Status</Label>
                        <Select value={status || 'all'} onValueChange={(value) => setStatus(value === 'all' ? '' : value)}>
                            <SelectTrigger id="service-order-status">
                                <SelectValue placeholder="All statuses" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem value="all">All statuses</SelectItem>
                                <SelectItem value="OPEN">OPEN</SelectItem>
                                <SelectItem value="CLOSED">CLOSED</SelectItem>
                                <SelectItem value="IN-PROGRESS">IN-PROGRESS</SelectItem>
                            </SelectContent>
                        </Select>
                    </div>
                    <div className="flex gap-3 lg:col-span-3">
                        <Button onClick={applyFilters}>Apply Filters</Button>
                        <Button variant="outline" onClick={clearFilters}>
                            Clear
                        </Button>
                    </div>
                </div>

                <div className="grid flex-1 gap-4 rounded-xl bg-white p-4 text-[#1c398e] dark:bg-neutral-950 xl:grid-cols-[2fr_1fr]">
                    <div className="overflow-hidden rounded-lg border border-gray-200">
                        <table className="w-full text-left text-sm">
                            <thead className="bg-gray-50 text-xs uppercase">
                                <tr>
                                    <th className="px-4 py-3">Service Order</th>
                                    <th className="px-4 py-3">Patient</th>
                                    <th className="px-4 py-3">Income</th>
                                    <th className="px-4 py-3">Expense</th>
                                    <th className="px-4 py-3">Net</th>
                                </tr>
                            </thead>
                            <tbody>
                                {serviceOrders.data.length === 0 && (
                                    <tr>
                                        <td colSpan={5} className="px-4 py-10 text-center text-gray-500">
                                            No service orders found.
                                        </td>
                                    </tr>
                                )}

                                {serviceOrders.data.map((serviceOrder) => {
                                    const income = serviceOrder.income_total ?? 0;
                                    const expense = serviceOrder.expense_total ?? 0;
                                    const net = income - expense;
                                    const iconUrl = healthIconUrl(serviceOrder.service?.icon);

                                    return (
                                        <tr
                                            key={serviceOrder.id}
                                            className="cursor-pointer border-t hover:bg-gray-50"
                                            onClick={() => openServiceOrder(serviceOrder.id)}
                                        >
                                            <td className="px-4 py-3 align-top">
                                                <div className="flex items-start gap-3">
                                                    {iconUrl ? (
                                                        <img
                                                            src={iconUrl}
                                                            alt={serviceOrder.service?.name ?? 'Service icon'}
                                                            className="mt-0.5 h-5 w-5"
                                                        />
                                                    ) : (
                                                        <span className="mt-0.5 h-5 w-5 rounded-full bg-gray-200" />
                                                    )}
                                                    <div>
                                                        <p className="font-semibold text-gray-900">
                                                            {serviceOrder.so_number}
                                                        </p>
                                                        <p className="text-xs text-gray-500">
                                                            {serviceOrder.service?.name ?? 'Service'} · {serviceOrder.status}
                                                        </p>
                                                        <p className="text-xs text-gray-500">
                                                            {formatDate(serviceOrder.created_at)}
                                                        </p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td className="px-4 py-3 align-top">
                                                <p className="font-medium text-gray-900">
                                                    {serviceOrder.patient?.name ?? '-'}
                                                </p>
                                                <p className="text-xs text-gray-500">
                                                    {serviceOrder.patient?.ps_number ?? '-'}
                                                </p>
                                                <p className="text-xs text-gray-500">
                                                    {serviceOrder.doctor?.name ?? '-'}
                                                </p>
                                            </td>
                                            <td className="px-4 py-3 align-top text-green-700">
                                                {formatMoney(income)}
                                            </td>
                                            <td className="px-4 py-3 align-top text-red-700">
                                                {formatMoney(expense)}
                                            </td>
                                            <td
                                                className={`px-4 py-3 align-top font-semibold ${
                                                    net >= 0 ? 'text-emerald-700' : 'text-rose-700'
                                                }`}
                                            >
                                                {formatMoney(net)}
                                            </td>
                                        </tr>
                                    );
                                })}
                            </tbody>
                        </table>
                    </div>

                    <aside className="space-y-4 overflow-y-auto rounded-lg border border-gray-200 p-4" style={{ maxHeight: '80vh' }}>
                        {!selectedServiceOrder && (
                            <p className="text-sm text-gray-500">
                                Click a service order row to view profile, treatment, and expense details.
                            </p>
                        )}

                        {selectedServiceOrder && (
                            <ServiceOrderDetailPanel
                                so={selectedServiceOrder}
                                selectedNet={selectedNet}
                                newStatus={newStatus}
                                setNewStatus={setNewStatus}
                                updatingStatus={updatingStatus}
                                changeStatus={changeStatus}
                            />
                        )}
                    </aside>
                </div>
            </div>
        </AppLayout>
    );
}
