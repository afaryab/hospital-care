import { formatPatientAge } from '@/lib/constants';
import { type ServiceOrder } from '@/types';
import { router } from '@inertiajs/react';
import { clsx } from 'clsx';
import {
    Activity,
    AlertCircle,
    ChevronRight,
    Clock,
    Search,
    UserCheck,
    Users,
} from 'lucide-react';
import { useRef, useState } from 'react';

interface DeptOrder extends ServiceOrder {
    status: string;
    patient?: {
        id: number;
        name: string;
        ps_number: string;
        gender?: string;
        age_days?: number;
        age_dob?: string;
    } | null;
    service?: { id: number; name: string } | null;
    treatment_record?: {
        id: number;
        is_finalized: boolean;
        diagnosis_text?: string;
    } | null;
    created_at?: string;
}

interface TodayStats {
    open: number;
    in_progress: number;
    treated: number;
    total: number;
}

interface Props {
    deptName: string;
    accentClass: string; // e.g. 'text-red-600', used for heading
    hasAccess: boolean;
    orders: DeptOrder[];
    stats: TodayStats;
    noAccessMessage: string; // shown when user lacks the required profile
    searchUrl: string; // GET search route (server-side redirect)
    patientUrl: (id: number) => string;
    flashError?: string;
}

function statusBadge(status: string) {
    const s = status.toLowerCase();
    if (s === 'in-progress')
        return (
            <span className="inline-flex items-center gap-1 rounded-full bg-blue-50 px-2.5 py-0.5 text-xs font-semibold text-blue-700 ring-1 ring-blue-200">
                <span className="h-1.5 w-1.5 animate-pulse rounded-full bg-blue-500" />{' '}
                In Progress
            </span>
        );
    if (s === 'open')
        return (
            <span className="inline-flex items-center gap-1 rounded-full bg-amber-50 px-2.5 py-0.5 text-xs font-semibold text-amber-700 ring-1 ring-amber-200">
                <Clock className="h-3 w-3" /> Waiting
            </span>
        );
    if (s === 'treated' || s === 'closed')
        return (
            <span className="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2.5 py-0.5 text-xs font-semibold text-emerald-700 ring-1 ring-emerald-200">
                <UserCheck className="h-3 w-3" /> Treated
            </span>
        );
    return (
        <span className="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-medium text-slate-600">
            {status}
        </span>
    );
}

export default function DeptQueueDashboard({
    deptName,
    accentClass,
    hasAccess,
    orders,
    stats,
    noAccessMessage,
    searchUrl,
    patientUrl,
    flashError,
}: Props) {
    const [searchQuery, setSearchQuery] = useState('');
    const formRef = useRef<HTMLFormElement>(null);

    if (!hasAccess) {
        return (
            <div className="flex flex-1 items-center justify-center p-8">
                <div className="max-w-sm rounded-2xl border border-amber-200 bg-amber-50 p-8 text-center shadow-sm">
                    <AlertCircle className="mx-auto mb-3 h-10 w-10 text-amber-500" />
                    <h2 className="mb-2 text-lg font-semibold text-slate-800">
                        {deptName} Portal
                    </h2>
                    <p className="text-sm text-slate-500">{noAccessMessage}</p>
                </div>
            </div>
        );
    }

    return (
        <div className="flex flex-1 flex-col gap-4 p-4">
            {/* Header */}
            <div className="flex items-center justify-between">
                <h1 className={clsx('text-2xl font-bold', accentClass)}>
                    {deptName} Portal
                </h1>
                <p className="text-sm text-slate-400">Today&apos;s queue</p>
            </div>

            {/* Stats */}
            <div className="grid grid-cols-4 gap-3">
                {[
                    {
                        label: 'Waiting',
                        value: stats.open,
                        icon: <Clock className="h-4 w-4 text-amber-500" />,
                        color: 'bg-amber-50 text-amber-700',
                    },
                    {
                        label: 'In Progress',
                        value: stats.in_progress,
                        icon: <Activity className="h-4 w-4 text-blue-500" />,
                        color: 'bg-blue-50 text-blue-700',
                    },
                    {
                        label: 'Treated',
                        value: stats.treated,
                        icon: (
                            <UserCheck className="h-4 w-4 text-emerald-500" />
                        ),
                        color: 'bg-emerald-50 text-emerald-700',
                    },
                    {
                        label: 'Total',
                        value: stats.total,
                        icon: <Users className="h-4 w-4 text-slate-500" />,
                        color: 'bg-slate-100 text-slate-700',
                    },
                ].map((s) => (
                    <div
                        key={s.label}
                        className={clsx(
                            'flex items-center gap-3 rounded-xl px-4 py-3',
                            s.color,
                        )}
                    >
                        {s.icon}
                        <div>
                            <div className="text-xl font-bold">{s.value}</div>
                            <div className="text-xs opacity-75">{s.label}</div>
                        </div>
                    </div>
                ))}
            </div>

            {/* Search */}
            <form
                ref={formRef}
                action={searchUrl}
                method="GET"
                className="flex gap-2"
            >
                <div className="relative flex-1">
                    <Search className="absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 text-slate-400" />
                    <input
                        name="q"
                        value={searchQuery}
                        onChange={(e) => setSearchQuery(e.target.value)}
                        placeholder="Search by SO number or PS number…"
                        className="w-full rounded-lg border border-slate-200 bg-white py-2 pr-4 pl-9 text-sm focus:ring-2 focus:ring-indigo-300 focus:outline-none"
                    />
                </div>
                <button
                    type="submit"
                    className="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700"
                >
                    Search
                </button>
            </form>

            {flashError && (
                <div className="rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-700">
                    {flashError}
                </div>
            )}

            {/* Queue */}
            {orders.length === 0 ? (
                <div className="flex flex-1 flex-col items-center justify-center rounded-2xl border border-dashed border-slate-200 bg-slate-50 py-16 text-center">
                    <Users className="mx-auto mb-3 h-10 w-10 text-slate-300" />
                    <p className="text-sm font-medium text-slate-400">
                        No orders assigned to you today
                    </p>
                </div>
            ) : (
                <div className="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                    <table className="w-full text-sm">
                        <thead className="bg-slate-50 text-xs font-semibold tracking-wide text-slate-500 uppercase">
                            <tr>
                                <th className="px-4 py-3 text-left">Patient</th>
                                <th className="px-4 py-3 text-left">Service</th>
                                <th className="px-4 py-3 text-left">
                                    Age / Gender
                                </th>
                                <th className="px-4 py-3 text-left">Status</th>
                                <th className="px-4 py-3 text-left">SO#</th>
                                <th className="px-4 py-3" />
                            </tr>
                        </thead>
                        <tbody className="divide-y divide-slate-100">
                            {orders.map((order) => (
                                <tr
                                    key={order.id}
                                    onClick={() =>
                                        router.visit(patientUrl(order.id!))
                                    }
                                    className="cursor-pointer transition-colors hover:bg-indigo-50/50"
                                >
                                    <td className="px-4 py-3 font-medium text-slate-800">
                                        <div>{order.patient?.name ?? '—'}</div>
                                        <div className="text-xs text-slate-400">
                                            {order.patient?.ps_number}
                                        </div>
                                    </td>
                                    <td className="px-4 py-3 text-slate-600">
                                        {order.service?.name ?? '—'}
                                    </td>
                                    <td className="px-4 py-3 text-slate-500">
                                        {order.patient
                                            ? formatPatientAge(order.patient)
                                            : '—'}
                                        {order.patient?.gender && (
                                            <span className="ml-1 text-xs text-slate-400">
                                                /{' '}
                                                {order.patient.gender.toUpperCase()}
                                            </span>
                                        )}
                                    </td>
                                    <td className="px-4 py-3">
                                        {statusBadge(order.status)}
                                    </td>
                                    <td className="px-4 py-3 font-mono text-xs text-slate-400">
                                        {order.so_number}
                                    </td>
                                    <td className="px-4 py-3 text-right">
                                        <ChevronRight className="ml-auto h-4 w-4 text-slate-300" />
                                    </td>
                                </tr>
                            ))}
                        </tbody>
                    </table>
                </div>
            )}
        </div>
    );
}
