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
    transaction?: { tr_number?: string; created_at?: string };
    expense_category?: { name?: string };
};

type ExpenseVoucherItem = {
    id: number;
    vc_number?: string;
    amount?: number;
    created_at?: string;
    exp_category?: { name?: string };
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

export default function ServiceOrdersOverview() {
    const { serviceOrders, selectedServiceOrder, filters } =
        usePage<PageProps>().props;

    const [search, setSearch] = useState(filters.search ?? '');
    const [status, setStatus] = useState(filters.status ?? '');
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
        router.get('/service-orders', {}, { preserveState: false, replace: true });
    };

    const openServiceOrder = (serviceOrderId: number) => {
        setNewStatus('');
        router.get(
            '/service-orders',
            {
                search: search || undefined,
                status: status || undefined,
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
                <div className="grid gap-4 rounded-xl bg-white p-4 text-[#1c398e] dark:bg-neutral-950 lg:grid-cols-3">
                    <div className="space-y-2 lg:col-span-2">
                        <Label htmlFor="service-order-search">Search</Label>
                        <Input
                            id="service-order-search"
                            value={search}
                            onChange={(event) => setSearch(event.target.value)}
                            placeholder="SO number, patient, service, doctor"
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

                    <aside className="space-y-4 rounded-lg border border-gray-200 p-4">
                        {!selectedServiceOrder && (
                            <p className="text-sm text-gray-500">
                                Click a service order row to view profile, treatment, and expense details.
                            </p>
                        )}

                        {selectedServiceOrder && (
                            <>
                                <div className="space-y-1 border-b pb-3">
                                    <h2 className="text-lg font-bold text-gray-900">
                                        {selectedServiceOrder.so_number}
                                    </h2>
                                    <p className="text-sm text-gray-600">
                                        {selectedServiceOrder.patient?.name ?? '-'} · {selectedServiceOrder.patient?.ps_number ?? '-'}
                                    </p>
                                    <p className="text-sm text-gray-600">
                                        Doctor: {selectedServiceOrder.doctor?.name ?? '-'}
                                    </p>
                                    <span className="inline-block rounded-full bg-gray-100 px-2 py-0.5 text-xs font-semibold uppercase text-gray-700">
                                        {selectedServiceOrder.status}
                                    </span>
                                </div>

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
                                    <Button
                                        className="w-full"
                                        disabled={!newStatus || updatingStatus}
                                        onClick={changeStatus}
                                    >
                                        {updatingStatus ? 'Updating…' : 'Update Status'}
                                    </Button>
                                </div>

                                <div className="grid grid-cols-1 gap-2 text-sm">
                                    <div className="rounded-md bg-green-50 p-2 text-green-800">
                                        Income: {formatMoney(selectedServiceOrder.income_total)}
                                    </div>
                                    <div className="rounded-md bg-red-50 p-2 text-red-800">
                                        Expense: {formatMoney(selectedServiceOrder.expense_total)}
                                    </div>
                                    <div className="rounded-md bg-blue-50 p-2 text-blue-800">
                                        Voucher Expense: {formatMoney(selectedServiceOrder.voucher_expense_total)}
                                    </div>
                                    <div className="rounded-md bg-gray-100 p-2 font-semibold text-gray-900">
                                        Net: {formatMoney(selectedNet)}
                                    </div>
                                </div>

                                <div className="space-y-2">
                                    <h3 className="text-sm font-semibold text-gray-900">Treatment</h3>
                                    {selectedServiceOrder.treatment_record ? (
                                        <div className="rounded-md border p-2 text-sm">
                                            <p>
                                                Diagnosis: {selectedServiceOrder.treatment_record.diagnosis_text ?? '-'}
                                            </p>
                                            <p>
                                                Outcome: {selectedServiceOrder.treatment_record.outcome ?? '-'}
                                            </p>
                                            <p>
                                                Treated At: {formatDate(selectedServiceOrder.treatment_record.treated_at)}
                                            </p>
                                            <p>
                                                Finalized: {selectedServiceOrder.treatment_record.is_finalized ? 'Yes' : 'No'}
                                            </p>
                                        </div>
                                    ) : (
                                        <p className="text-sm text-gray-500">No treatment record.</p>
                                    )}
                                </div>

                                <div className="space-y-2">
                                    <h3 className="text-sm font-semibold text-gray-900">Expense Vouchers</h3>
                                    {selectedServiceOrder.expense_vouchers && selectedServiceOrder.expense_vouchers.length > 0 ? (
                                        <div className="space-y-2">
                                            {selectedServiceOrder.expense_vouchers.map((voucher) => (
                                                <div key={voucher.id} className="rounded-md border p-2 text-sm">
                                                    <p className="font-medium">{voucher.vc_number}</p>
                                                    <p>{voucher.exp_category?.name ?? '-'}</p>
                                                    <p>{formatMoney(voucher.amount)}</p>
                                                </div>
                                            ))}
                                        </div>
                                    ) : (
                                        <p className="text-sm text-gray-500">No expense vouchers linked.</p>
                                    )}
                                </div>

                                <div className="space-y-2">
                                    <h3 className="text-sm font-semibold text-gray-900">Transaction Elements</h3>
                                    {selectedServiceOrder.transaction_elements && selectedServiceOrder.transaction_elements.length > 0 ? (
                                        <div className="space-y-2">
                                            {selectedServiceOrder.transaction_elements.map((element) => (
                                                <div key={element.id} className="rounded-md border p-2 text-sm">
                                                    <p className="font-medium">
                                                        {element.transaction?.tr_number ?? 'Transaction'}
                                                    </p>
                                                    <p>
                                                        {element.income_or_expense} · {element.type ?? '-'}
                                                    </p>
                                                    <p>{formatMoney(element.amount)}</p>
                                                </div>
                                            ))}
                                        </div>
                                    ) : (
                                        <p className="text-sm text-gray-500">No transaction elements linked.</p>
                                    )}
                                </div>
                            </>
                        )}
                    </aside>
                </div>
            </div>
        </AppLayout>
    );
}
