import Currency from '@/components/currency';
import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router, usePage } from '@inertiajs/react';
import { clsx } from 'clsx';
import { Filter, PiggyBank, Search } from 'lucide-react';
import { useState } from 'react';

type VoucherRow = {
    id: number;
    vc_number: string;
    amount: number;
    notes?: string | null;
    status: 'payed' | 'pending';
    created_at: string;
    exp_category?: { id: number; name: string };
    service_order?: {
        id: number;
        so_number: string;
        patient?: { id: number; name: string; ps_number: string };
    };
};

type Paginated<T> = {
    data: T[];
    current_page: number;
    last_page: number;
    total: number;
    links: { url: string | null; label: string; active: boolean }[];
};

type Props = {
    vouchers: Paginated<VoucherRow>;
    filters: { q?: string; from?: string; until?: string };
    totals: { paid: number; pending: number; total: number };
    [key: string]: unknown;
};

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/' },
    { title: 'My Payments', href: '/my-payments' },
];

export default function MyPayments() {
    const { vouchers, filters, totals } = usePage<Props>().props;
    const [form, setForm] = useState(filters);

    const apply = () => {
        router.get('/my-payments', form, { preserveState: true, replace: true });
    };

    const clearAll = () => {
        setForm({});
        router.get('/my-payments', {}, { preserveState: true, replace: true });
    };

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="My Payments" />
            <div className="flex flex-col gap-4 p-4">
                <header className="flex items-center justify-between">
                    <div className="flex items-center gap-3">
                        <div className="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-600">
                            <PiggyBank className="h-5 w-5 text-white" />
                        </div>
                        <div>
                            <h1 className="text-lg font-bold text-slate-900 dark:text-slate-100">
                                My Payments
                            </h1>
                            <p className="text-xs text-slate-500">
                                Expense vouchers issued to you ({vouchers.total}{' '}
                                total).
                            </p>
                        </div>
                    </div>
                </header>

                {/* Totals */}
                <div className="grid grid-cols-3 gap-2">
                    <TotalTile
                        label="Paid"
                        value={totals.paid}
                        tone="positive"
                    />
                    <TotalTile
                        label="Pending"
                        value={totals.pending}
                        tone="warning"
                    />
                    <TotalTile
                        label="Total"
                        value={totals.total}
                        tone="neutral"
                    />
                </div>

                {/* Filters */}
                <div className="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm dark:border-gray-800 dark:bg-gray-900">
                    <div className="grid gap-3 md:grid-cols-4">
                        <div className="md:col-span-2">
                            <label className="mb-1 block text-xs font-medium text-slate-600 dark:text-slate-400">
                                Search
                            </label>
                            <div className="relative">
                                <Search className="absolute top-1/2 left-3 h-3.5 w-3.5 -translate-y-1/2 text-slate-400" />
                                <input
                                    type="text"
                                    value={form.q ?? ''}
                                    onChange={(e) =>
                                        setForm((f) => ({
                                            ...f,
                                            q: e.target.value,
                                        }))
                                    }
                                    onKeyDown={(e) => e.key === 'Enter' && apply()}
                                    placeholder="Voucher #, notes…"
                                    className="w-full rounded-lg border border-slate-200 bg-slate-50 py-2 pr-3 pl-9 text-sm focus:bg-white focus:ring-2 focus:ring-emerald-100 focus:outline-none dark:border-gray-700 dark:bg-gray-800"
                                />
                            </div>
                        </div>
                        <div>
                            <label className="mb-1 block text-xs font-medium text-slate-600 dark:text-slate-400">
                                From
                            </label>
                            <input
                                type="date"
                                value={form.from ?? ''}
                                onChange={(e) =>
                                    setForm((f) => ({
                                        ...f,
                                        from: e.target.value,
                                    }))
                                }
                                className="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800"
                            />
                        </div>
                        <div>
                            <label className="mb-1 block text-xs font-medium text-slate-600 dark:text-slate-400">
                                Until
                            </label>
                            <input
                                type="date"
                                value={form.until ?? ''}
                                onChange={(e) =>
                                    setForm((f) => ({
                                        ...f,
                                        until: e.target.value,
                                    }))
                                }
                                className="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800"
                            />
                        </div>
                    </div>
                    <div className="mt-3 flex gap-2">
                        <button
                            type="button"
                            onClick={apply}
                            className="flex items-center gap-1 rounded-lg bg-emerald-600 px-4 py-2 text-xs font-semibold text-white hover:bg-emerald-700"
                        >
                            <Filter className="h-3.5 w-3.5" /> Apply
                        </button>
                        <button
                            type="button"
                            onClick={clearAll}
                            className="rounded-lg border border-slate-200 bg-white px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-50 dark:border-gray-700 dark:bg-gray-800"
                        >
                            Clear
                        </button>
                    </div>
                </div>

                {/* Table */}
                <div className="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
                    <div className="overflow-x-auto">
                        <table className="w-full text-sm">
                            <thead className="bg-slate-50 text-xs font-semibold tracking-wide text-slate-600 uppercase dark:bg-gray-800 dark:text-slate-300">
                                <tr>
                                    <th className="px-4 py-2 text-left">Date</th>
                                    <th className="px-4 py-2 text-left">Voucher #</th>
                                    <th className="px-4 py-2 text-left">
                                        Service Order
                                    </th>
                                    <th className="px-4 py-2 text-left">Category</th>
                                    <th className="px-4 py-2 text-right">Amount</th>
                                    <th className="px-4 py-2 text-left">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                {vouchers.data.length === 0 && (
                                    <tr>
                                        <td
                                            colSpan={6}
                                            className="px-4 py-12 text-center text-sm text-slate-500"
                                        >
                                            No payment vouchers yet.
                                        </td>
                                    </tr>
                                )}
                                {vouchers.data.map((v) => (
                                    <tr
                                        key={v.id}
                                        className="border-t border-slate-100 hover:bg-slate-50 dark:border-gray-800 dark:hover:bg-gray-800"
                                    >
                                        <td className="px-4 py-2 text-xs text-slate-500">
                                            {new Date(
                                                v.created_at,
                                            ).toLocaleDateString()}
                                        </td>
                                        <td className="px-4 py-2 font-mono text-xs">
                                            {v.vc_number}
                                        </td>
                                        <td className="px-4 py-2 text-xs">
                                            {v.service_order ? (
                                                <>
                                                    <div className="font-mono">
                                                        {v.service_order.so_number}
                                                    </div>
                                                    <div className="text-slate-500">
                                                        {
                                                            v.service_order.patient
                                                                ?.name
                                                        }
                                                    </div>
                                                </>
                                            ) : (
                                                <span className="text-slate-400">—</span>
                                            )}
                                        </td>
                                        <td className="px-4 py-2 text-xs text-slate-600">
                                            {v.exp_category?.name ?? '—'}
                                        </td>
                                        <td className="px-4 py-2 text-right font-semibold text-slate-800 dark:text-slate-100">
                                            <Currency value={Number(v.amount)} />
                                        </td>
                                        <td className="px-4 py-2">
                                            <span
                                                className={clsx(
                                                    'rounded-full px-2 py-0.5 text-[10px] font-semibold tracking-wide uppercase',
                                                    v.status === 'payed'
                                                        ? 'bg-emerald-100 text-emerald-700'
                                                        : 'bg-amber-100 text-amber-700',
                                                )}
                                            >
                                                {v.status === 'payed'
                                                    ? 'PAID'
                                                    : 'PENDING'}
                                            </span>
                                        </td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    </div>

                    {vouchers.last_page > 1 && (
                        <div className="flex items-center justify-between border-t border-slate-100 px-4 py-2 text-xs dark:border-gray-800">
                            <p className="text-slate-500">
                                Page {vouchers.current_page} of {vouchers.last_page}{' '}
                                · {vouchers.total} total
                            </p>
                            <div className="flex gap-1">
                                {vouchers.links.map((l, i) => (
                                    <Link
                                        key={i}
                                        href={l.url ?? '#'}
                                        preserveScroll
                                        preserveState
                                        className={clsx(
                                            'rounded px-2 py-1 text-xs',
                                            l.active
                                                ? 'bg-emerald-600 text-white'
                                                : l.url
                                                  ? 'bg-slate-100 text-slate-600 hover:bg-slate-200 dark:bg-gray-800 dark:text-slate-200'
                                                  : 'cursor-not-allowed text-slate-300',
                                        )}
                                        dangerouslySetInnerHTML={{
                                            __html: l.label,
                                        }}
                                    />
                                ))}
                            </div>
                        </div>
                    )}
                </div>
            </div>
        </AppLayout>
    );
}

function TotalTile({
    label,
    value,
    tone,
}: {
    label: string;
    value: number;
    tone: 'positive' | 'warning' | 'neutral';
}) {
    const tones: Record<string, string> = {
        positive: 'bg-emerald-50 text-emerald-700',
        warning: 'bg-amber-50 text-amber-700',
        neutral: 'bg-slate-50 text-slate-700',
    };
    return (
        <div
            className={clsx(
                'rounded-xl border border-slate-100 p-3 dark:border-gray-800',
                tones[tone],
            )}
        >
            <p className="text-[10px] tracking-wide uppercase opacity-80">
                {label}
            </p>
            <p className="text-xl font-bold">
                <Currency value={value} />
            </p>
        </div>
    );
}
