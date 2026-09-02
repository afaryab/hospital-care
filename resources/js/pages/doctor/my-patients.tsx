import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router, usePage } from '@inertiajs/react';
import { clsx } from 'clsx';
import { Filter, Search, Users } from 'lucide-react';
import { useState } from 'react';

type ServiceOrderRow = {
    id: number;
    so_number: string;
    so_short: string;
    token_short?: string | null;
    type: string;
    status: string;
    created_at: string;
    patient?: { id: number; name: string; ps_number: string };
    service?: { id: number; name: string };
};

type Paginated<T> = {
    data: T[];
    current_page: number;
    last_page: number;
    total: number;
    links: { url: string | null; label: string; active: boolean }[];
};

type Props = {
    orders: Paginated<ServiceOrderRow>;
    filters: {
        status?: string;
        q?: string;
        from?: string;
        until?: string;
    };
    stats: {
        open: number;
        in_progress: number;
        treated: number;
        closed: number;
        refunded: number;
        total: number;
    };
    [key: string]: unknown;
};

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/' },
    { title: 'My Patients', href: '/my-patients' },
];

const STATUS_OPTIONS = [
    { value: '', label: 'All Statuses' },
    { value: 'open', label: 'Open' },
    { value: 'in-progress', label: 'In Progress' },
    { value: 'treated', label: 'Treated' },
    { value: 'closed', label: 'Closed' },
    { value: 'refunded', label: 'Refunded' },
    { value: 'cancelled', label: 'Cancelled' },
];

export default function MyPatients() {
    const { orders, filters, stats } = usePage<Props>().props;
    const [form, setForm] = useState(filters);

    const apply = () => {
        router.get('/my-patients', form, {
            preserveState: true,
            replace: true,
        });
    };

    const clearAll = () => {
        setForm({});
        router.get('/my-patients', {}, { preserveState: true, replace: true });
    };

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="My Patients" />
            <div className="flex flex-col gap-4 p-4 bg-white dark:bg-neutral-900 rounded-lg">
                <header className="flex items-center justify-between">
                    <div className="flex items-center gap-3">
                        <div className="flex h-10 w-10 items-center justify-center rounded-xl bg-indigo-600">
                            <Users className="h-5 w-5 text-white" />
                        </div>
                        <div>
                            <h1 className="text-lg font-bold text-slate-900 dark:text-slate-100">
                                My Patients
                            </h1>
                            <p className="text-xs text-slate-500">
                                Service orders assigned to you ({stats.total}{' '}
                                total).
                            </p>
                        </div>
                    </div>
                </header>

                {/* Stats */}
                <div className="grid grid-cols-2 gap-2 md:grid-cols-5">
                    <StatTile label="Open" value={stats.open} tone="warning" />
                    <StatTile
                        label="In-Progress"
                        value={stats.in_progress}
                        tone="info"
                    />
                    <StatTile
                        label="Treated"
                        value={stats.treated}
                        tone="positive"
                    />
                    <StatTile
                        label="Closed"
                        value={stats.closed}
                        tone="positive"
                    />
                    <StatTile
                        label="Refunded"
                        value={stats.refunded}
                        tone="negative"
                    />
                </div>

                {/* Filters */}
                <div className="rounded-2xl border border-sidebar-border/70 bg-gradient-to-br from-teal-50 via-white to-sky-50 p-4 shadow-sm dark:border-sidebar-border dark:from-teal-950/40 dark:via-gray-900 dark:to-sky-950/40">
                    <div className="grid gap-3 md:grid-cols-5">
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
                                    onKeyDown={(e) =>
                                        e.key === 'Enter' && apply()
                                    }
                                    placeholder="SO#, name, MR#…"
                                    className="w-full rounded-lg border border-slate-200 bg-slate-50 py-2 pr-3 pl-9 text-sm focus:bg-white focus:ring-2 focus:ring-indigo-100 focus:outline-none dark:border-gray-700 dark:bg-gray-800"
                                />
                            </div>
                        </div>
                        <div>
                            <label className="mb-1 block text-xs font-medium text-slate-600 dark:text-slate-400">
                                Status
                            </label>
                            <select
                                value={form.status ?? ''}
                                onChange={(e) =>
                                    setForm((f) => ({
                                        ...f,
                                        status: e.target.value,
                                    }))
                                }
                                className="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800"
                            >
                                {STATUS_OPTIONS.map((o) => (
                                    <option key={o.value} value={o.value}>
                                        {o.label}
                                    </option>
                                ))}
                            </select>
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
                            className="flex items-center gap-1 rounded-lg bg-indigo-600 px-4 py-2 text-xs font-semibold text-white hover:bg-indigo-700"
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
                        <table className="w-full bg-gray-50 text-left text-xs text-gray-700 uppercase dark:bg-neutral-950 dark:text-gray-400">
                            <thead className="">
                                <tr>
                                    <th className="px-4 py-2 text-left">
                                        Date
                                    </th>
                                    <th className="px-4 py-2 text-left">
                                        SO #
                                    </th>
                                    <th className="px-4 py-2 text-left">
                                        Patient
                                    </th>
                                    <th className="px-4 py-2 text-left">
                                        Service
                                    </th>
                                    <th className="px-4 py-2 text-left">
                                        Token
                                    </th>
                                    <th className="px-4 py-2 text-left">
                                        Status
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                {orders.data.length === 0 && (
                                    <tr>
                                        <td
                                            colSpan={6}
                                            className="px-4 py-12 text-center text-sm text-slate-500"
                                        >
                                            No service orders match these
                                            filters.
                                        </td>
                                    </tr>
                                )}
                                {orders.data.map((o) => (
                                    <tr
                                        key={o.id}
                                        className="border-t border-slate-100 hover:bg-slate-50 dark:border-gray-800 dark:hover:bg-gray-800"
                                    >
                                        <td className="px-4 py-2 text-xs text-slate-500">
                                            {new Date(
                                                o.created_at,
                                            ).toLocaleDateString()}
                                        </td>
                                        <td className="px-4 py-2 font-mono text-xs">
                                            {o.so_number}
                                        </td>
                                        <td className="px-4 py-2">
                                            <div className="font-medium text-slate-800 dark:text-slate-100">
                                                {o.patient?.name ?? '—'}
                                            </div>
                                            <div className="text-xs text-slate-500">
                                                {o.patient?.ps_number}
                                            </div>
                                        </td>
                                        <td className="px-4 py-2 text-xs text-slate-600">
                                            {o.service?.name ?? '—'}
                                        </td>
                                        <td className="px-4 py-2">
                                            {o.token_short ? (
                                                <span className="rounded bg-slate-100 px-2 py-0.5 font-mono text-xs font-semibold text-slate-700 dark:bg-gray-800 dark:text-slate-200">
                                                    #{o.token_short}
                                                </span>
                                            ) : (
                                                <span className="text-xs text-slate-400">
                                                    —
                                                </span>
                                            )}
                                        </td>
                                        <td className="px-4 py-2">
                                            <StatusBadge status={o.status} />
                                        </td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    </div>

                    {/* Pagination */}
                    {orders.last_page > 1 && (
                        <div className="flex items-center justify-between border-t border-slate-100 px-4 py-2 text-xs dark:border-gray-800">
                            <p className="text-slate-500">
                                Page {orders.current_page} of {orders.last_page}{' '}
                                · {orders.total} total
                            </p>
                            <div className="flex gap-1">
                                {orders.links.map((l, i) => (
                                    <Link
                                        key={i}
                                        href={l.url ?? '#'}
                                        preserveScroll
                                        preserveState
                                        className={clsx(
                                            'rounded px-2 py-1 text-xs',
                                            l.active
                                                ? 'bg-indigo-600 text-white'
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

function StatTile({
    label,
    value,
    tone,
}: {
    label: string;
    value: number;
    tone: 'positive' | 'negative' | 'warning' | 'info';
}) {
    const tones: Record<string, string> = {
        positive: 'bg-emerald-50 text-emerald-700',
        negative: 'bg-rose-50 text-rose-700',
        warning: 'bg-amber-50 text-amber-700',
        info: 'bg-sky-50 text-sky-700',
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
            <p className="text-xl font-bold">{value}</p>
        </div>
    );
}

function StatusBadge({ status }: { status: string }) {
    const s = status?.toLowerCase();
    const map: Record<string, string> = {
        open: 'bg-amber-100 text-amber-700',
        'in-progress': 'bg-blue-100 text-blue-700',
        treated: 'bg-sky-100 text-sky-700',
        closed: 'bg-emerald-100 text-emerald-700',
        refunded: 'bg-rose-100 text-rose-700',
        cancelled: 'bg-slate-100 text-slate-600',
    };
    return (
        <span
            className={clsx(
                'rounded-full px-2 py-0.5 text-[10px] font-semibold tracking-wide uppercase',
                map[s] ?? 'bg-slate-100 text-slate-600',
            )}
        >
            {s ?? '—'}
        </span>
    );
}
