import AppLayout from '@/layouts/app-layout';
import { type OutpatientDepartment, withId } from '@/lib/outpatient-department';
import { type BreadcrumbItem, type ServiceOrder } from '@/types';
import { Head, router, usePage } from '@inertiajs/react';
import { clsx } from 'clsx';
import {
    Activity,
    AlertCircle,
    ChevronRight,
    Clock,
    Search,
    Stethoscope,
    UserCheck,
    Users,
} from 'lucide-react';
import { useCallback, useEffect, useRef, useState } from 'react';
import { toast } from 'sonner';

interface OpdServiceOrder extends ServiceOrder {
    status: string;
    token?: string | number;
    token_short?: string | null;
    patient?: {
        id: number;
        name: string;
        ps_number: string;
        gender: 'm' | 'f' | 't' | 'o';
        age_days?: number;
        age_dob?: string;
    };
    service?: { id: number; name: string };
    treatment_record?: {
        id: number;
        is_finalized: boolean;
        diagnosis_text?: string;
    } | null;
    created_at?: string;
}

interface OpdDashboardProps {
    isOpdDoctor: boolean;
    recentOrders: OpdServiceOrder[];
    todayStats: {
        open: number;
        in_progress: number;
        treated: number;
        total: number;
    };
    searchPrefill: string;
    department: OutpatientDepartment;
    [key: string]: unknown;
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

function ageDisplay(order: OpdServiceOrder) {
    const p = order.patient;
    if (!p) return '—';
    if (p.age_dob) {
        const years = Math.floor(
            (Date.now() - new Date(p.age_dob).getTime()) / 31557600000,
        );
        return `${years} yrs`;
    }
    if (p.age_days) {
        if (p.age_days >= 365) return `${Math.floor(p.age_days / 365)} yrs`;
        return `${p.age_days} days`;
    }
    return '—';
}

function statusBadge(status: string) {
    const s = status.toLowerCase();
    if (s === 'in-progress') {
        return (
            <span className="inline-flex items-center gap-1 rounded-full bg-blue-50 px-2.5 py-0.5 text-xs font-semibold text-blue-700 ring-1 ring-blue-200 dark:bg-blue-950/40 dark:text-blue-300 dark:ring-blue-800">
                <span className="h-1.5 w-1.5 animate-pulse rounded-full bg-blue-500" />
                In Progress
            </span>
        );
    }
    if (s === 'open') {
        return (
            <span className="inline-flex items-center gap-1 rounded-full bg-amber-50 px-2.5 py-0.5 text-xs font-semibold text-amber-700 ring-1 ring-amber-200 dark:bg-amber-950/40 dark:text-amber-300 dark:ring-amber-800">
                <Clock className="h-3 w-3" />
                Waiting
            </span>
        );
    }
    if (s === 'treated' || s === 'closed') {
        return (
            <span className="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2.5 py-0.5 text-xs font-semibold text-emerald-700 ring-1 ring-emerald-200 dark:bg-emerald-950/40 dark:text-emerald-300 dark:ring-emerald-800">
                <UserCheck className="h-3 w-3" />
                Treated
            </span>
        );
    }
    return (
        <span className="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-medium text-slate-600 dark:bg-neutral-800 dark:text-neutral-300">
            {status}
        </span>
    );
}

export default function OpdDashboard() {
    const {
        isOpdDoctor,
        recentOrders: initialOrders,
        todayStats: initialStats,
        searchPrefill,
        flash,
        department,
    } = usePage<OpdDashboardProps>().props;

    const breadcrumbs: BreadcrumbItem[] = [
        { title: 'Dashboard', href: '/' },
        { title: department.label, href: department.dashboardUrl },
    ];

    const [orders, setOrders] = useState<OpdServiceOrder[]>(
        initialOrders ?? [],
    );
    const [stats, setStats] = useState(initialStats);
    const [searchQuery, setSearchQuery] = useState(searchPrefill ?? '');
    const [searchResults, setSearchResults] = useState<{
        exact: OpdServiceOrder[];
        possible: OpdServiceOrder[];
    } | null>(null);
    const [searching, setSearching] = useState(false);
    const [callingPatient, setCallingPatient] = useState<number | null>(null);
    const searchRef = useRef<HTMLInputElement>(null);
    const debounceRef = useRef<ReturnType<typeof setTimeout> | null>(null);

    // Flash message from redirect
    const searchError = (flash as any)?.searchError;

    const refreshQueue = useCallback(async () => {
        try {
            const res = await fetch(department.apiMyQueueUrl, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    Accept: 'application/json',
                },
            });
            if (!res.ok) return;
            const json = await res.json();
            setOrders(json.data ?? []);
            setStats(json.stats ?? stats);
        } catch {
            toast.error('Queue refresh failed');
        }
        // eslint-disable-next-line react-hooks/exhaustive-deps
    }, [department.apiMyQueueUrl]);

    useEffect(() => {
        const interval = setInterval(refreshQueue, 30_000);
        return () => clearInterval(interval);
    }, [refreshQueue]);

    const handleSearch = useCallback(
        async (q: string) => {
            if (!q.trim()) {
                setSearchResults(null);
                return;
            }
            setSearching(true);
            try {
                const res = await fetch(department.apiSearchUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        Accept: 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-XSRF-TOKEN': decodeURIComponent(
                            document.cookie
                                .split('XSRF-TOKEN=')[1]
                                ?.split(';')[0] ?? '',
                        ),
                    },
                    body: JSON.stringify({ q }),
                });
                if (!res.ok) {
                    setSearching(false);
                    toast.error('Search failed');
                    return;
                }
                const json = await res.json();
                setSearchResults(json.data);
            } catch {
                toast.error('Network error — search unavailable');
            } finally {
                setSearching(false);
            }
        },
        [department.apiSearchUrl],
    );

    // Run the pre-filled search immediately on load so staff see the latest
    // matching service orders straight away instead of an empty list.
    useEffect(() => {
        if (searchPrefill) handleSearch(searchPrefill);
        // eslint-disable-next-line react-hooks/exhaustive-deps
    }, []);

    const onSearchInput = (e: React.ChangeEvent<HTMLInputElement>) => {
        const v = e.target.value;
        setSearchQuery(v);
        if (debounceRef.current) clearTimeout(debounceRef.current);
        debounceRef.current = setTimeout(() => handleSearch(v), 400);
    };

    const openOrder = (order: OpdServiceOrder) => {
        router.visit(withId(department.patientUrlTemplate, order.id!));
    };

    const callPatient = async (order: OpdServiceOrder, e: React.MouseEvent) => {
        e.stopPropagation();
        setCallingPatient(order.id!);
        try {
            const res = await fetch(
                withId(department.apiStatusUrlTemplate, order.id!),
                {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        Accept: 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-XSRF-TOKEN': decodeURIComponent(
                            document.cookie
                                .split('XSRF-TOKEN=')[1]
                                ?.split(';')[0] ?? '',
                        ),
                    },
                    body: JSON.stringify({ status: 'in-progress' }),
                },
            );
            if (!res.ok) throw new Error();
            toast.success(`${order.patient?.name ?? 'Patient'} called`);
            await refreshQueue();
        } catch {
            toast.error('Failed to update status');
        } finally {
            setCallingPatient(null);
        }
    };

    const allResults = searchResults
        ? [...searchResults.exact, ...searchResults.possible]
        : [];

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title={`${department.label} Doctor Dashboard`} />

            <div className="min-h-full bg-gradient-to-br from-teal-50 via-white to-emerald-50 p-4 md:p-6 dark:from-teal-950/40 dark:via-neutral-950 dark:to-emerald-950/40">
                {/* Header */}
                <div className="mb-6 flex items-center justify-between">
                    <div className="flex items-center gap-3">
                        <div className="flex h-12 w-12 items-center justify-center rounded-2xl bg-teal-600 shadow-md">
                            <Stethoscope className="h-6 w-6 text-white" />
                        </div>
                        <div>
                            <h1 className="text-xl font-bold text-slate-900 md:text-2xl dark:text-neutral-100">
                                {department.label} Dashboard
                            </h1>
                            <p className="text-sm text-slate-500 dark:text-neutral-400">
                                Outpatient Department
                            </p>
                        </div>
                    </div>
                    <div className="hidden items-center gap-2 rounded-xl bg-white px-3 py-2 shadow-sm ring-1 ring-slate-200 sm:flex dark:bg-neutral-900 dark:ring-neutral-700">
                        <span className="h-2 w-2 animate-pulse rounded-full bg-emerald-500" />
                        <span className="text-sm font-medium text-slate-700 dark:text-neutral-200">
                            Live Queue
                        </span>
                    </div>
                </div>

                {/* Access Denied */}
                {!isOpdDoctor && (
                    <div className="flex flex-col items-center justify-center gap-4 rounded-2xl border border-red-200 bg-red-50 py-16 text-center dark:border-red-900 dark:bg-red-950/40">
                        <AlertCircle className="h-12 w-12 text-red-400" />
                        <div>
                            <h2 className="text-lg font-semibold text-red-800 dark:text-red-300">
                                Access Restricted
                            </h2>
                            <p className="mt-1 text-sm text-red-600 dark:text-red-400">
                                You need an{' '}
                                <strong>{department.profileLabel}</strong>{' '}
                                profile to access this dashboard.
                            </p>
                        </div>
                    </div>
                )}

                {isOpdDoctor && (
                    <>
                        {/* Stats Row */}
                        <div className="mb-6 grid grid-cols-2 gap-3 sm:grid-cols-4">
                            <StatCard
                                label="Total Today"
                                value={stats.total}
                                icon={
                                    <Users className="h-5 w-5 text-slate-600 dark:text-neutral-300" />
                                }
                                color="bg-white dark:bg-neutral-900"
                            />
                            <StatCard
                                label="Waiting"
                                value={stats.open}
                                icon={
                                    <Clock className="h-5 w-5 text-amber-500" />
                                }
                                color="bg-amber-50 dark:bg-amber-950/40"
                                valueColor="text-amber-700 dark:text-amber-300"
                            />
                            <StatCard
                                label="In Progress"
                                value={stats.in_progress}
                                icon={
                                    <Activity className="h-5 w-5 text-blue-500" />
                                }
                                color="bg-blue-50 dark:bg-blue-950/40"
                                valueColor="text-blue-700 dark:text-blue-300"
                            />
                            <StatCard
                                label="Treated"
                                value={stats.treated}
                                icon={
                                    <UserCheck className="h-5 w-5 text-emerald-600 dark:text-emerald-400" />
                                }
                                color="bg-emerald-50 dark:bg-emerald-950/40"
                                valueColor="text-emerald-700 dark:text-emerald-300"
                            />
                        </div>

                        {/* Search */}
                        <div className="mb-6 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm md:p-5 dark:border-neutral-800 dark:bg-neutral-900">
                            <label className="mb-2 block text-sm font-semibold text-slate-700 dark:text-neutral-200">
                                Find Patient or Service Order
                            </label>
                            <div className="relative">
                                <Search className="absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 text-slate-400" />
                                <input
                                    ref={searchRef}
                                    value={searchQuery}
                                    onChange={onSearchInput}
                                    placeholder={`Enter SO number (e.g. PS/2026/04/0001/${department.type}/01) or Patient MR# (e.g. PS/2026/04/0001)`}
                                    className="w-full rounded-xl border border-slate-200 bg-slate-50 py-3 pr-4 pl-10 text-sm text-slate-800 placeholder:text-slate-400 focus:border-teal-400 focus:bg-white focus:ring-2 focus:ring-teal-100 focus:outline-none dark:border-neutral-800 dark:bg-neutral-800/50 dark:text-neutral-100 dark:focus:bg-neutral-900 dark:focus:ring-teal-800"
                                />
                                {searching && (
                                    <span className="absolute top-1/2 right-3 -translate-y-1/2">
                                        <span className="inline-block h-4 w-4 animate-spin rounded-full border-2 border-teal-400 border-t-transparent" />
                                    </span>
                                )}
                            </div>

                            {searchError && (
                                <p className="mt-2 text-sm text-red-600 dark:text-red-400">
                                    {searchError}
                                </p>
                            )}

                            {/* Search Results Dropdown */}
                            {searchQuery && allResults.length > 0 && (
                                <div className="mt-2 divide-y divide-slate-100 overflow-hidden rounded-xl border border-slate-200 bg-white shadow-lg dark:divide-neutral-800 dark:border-neutral-800 dark:bg-neutral-900">
                                    {allResults.map((order) => (
                                        <button
                                            key={order.id}
                                            type="button"
                                            onClick={() => openOrder(order)}
                                            className="flex w-full items-center justify-between px-4 py-3 text-left transition-colors hover:bg-teal-50 dark:hover:bg-teal-950/40"
                                        >
                                            <div>
                                                <p className="text-sm font-semibold text-slate-900 dark:text-neutral-100">
                                                    {order.patient?.name}
                                                </p>
                                                <p className="text-xs text-slate-500 dark:text-neutral-400">
                                                    {order.so_number} &bull;{' '}
                                                    {order.patient?.ps_number}
                                                </p>
                                            </div>
                                            <div className="flex items-center gap-2">
                                                {statusBadge(order.status)}
                                                <ChevronRight className="h-4 w-4 text-slate-400" />
                                            </div>
                                        </button>
                                    ))}
                                </div>
                            )}
                            {searchQuery &&
                                !searching &&
                                allResults.length === 0 && (
                                    <p className="mt-2 text-sm text-slate-500 dark:text-neutral-400">
                                        No results found for "{searchQuery}"
                                    </p>
                                )}
                        </div>

                        {/* Today's Queue */}
                        <div className="rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-neutral-800 dark:bg-neutral-900">
                            <div className="flex items-center justify-between border-b border-slate-100 px-4 py-4 md:px-5 dark:border-neutral-800">
                                <h2 className="text-base font-semibold text-slate-900 dark:text-neutral-100">
                                    My Queue Today
                                </h2>
                                <button
                                    type="button"
                                    onClick={refreshQueue}
                                    className="rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-xs font-medium text-slate-600 transition-colors hover:bg-slate-50 dark:border-neutral-800 dark:bg-neutral-900 dark:text-neutral-300 dark:hover:bg-neutral-800/50"
                                >
                                    Refresh
                                </button>
                            </div>

                            {orders.length === 0 ? (
                                <div className="flex flex-col items-center justify-center gap-3 py-16 text-center">
                                    <Stethoscope className="h-10 w-10 text-slate-300" />
                                    <p className="text-sm text-slate-500 dark:text-neutral-400">
                                        No patients in your queue today.
                                    </p>
                                </div>
                            ) : (
                                <div className="divide-y divide-slate-100 dark:divide-neutral-800">
                                    {orders.map((order, idx) => (
                                        <QueueRow
                                            key={order.id}
                                            order={order}
                                            position={idx + 1}
                                            onOpen={() => openOrder(order)}
                                            onCall={(e) =>
                                                callPatient(order, e)
                                            }
                                            calling={
                                                callingPatient === order.id
                                            }
                                            ageDisplay={ageDisplay(order)}
                                        />
                                    ))}
                                </div>
                            )}
                        </div>
                    </>
                )}
            </div>
        </AppLayout>
    );
}

function StatCard({
    label,
    value,
    icon,
    color = 'bg-white dark:bg-neutral-900',
    valueColor = 'text-slate-900 dark:text-neutral-100',
}: {
    label: string;
    value: number;
    icon: React.ReactNode;
    color?: string;
    valueColor?: string;
}) {
    return (
        <div
            className={clsx(
                'rounded-2xl border border-slate-200 p-4 shadow-sm dark:border-neutral-800',
                color,
            )}
        >
            <div className="flex items-center justify-between">
                <span className="text-xs font-medium text-slate-500 dark:text-neutral-400">
                    {label}
                </span>
                {icon}
            </div>
            <p
                className={clsx(
                    'mt-2 text-3xl font-bold tabular-nums',
                    valueColor,
                )}
            >
                {value}
            </p>
        </div>
    );
}

function QueueRow({
    order,
    position,
    onOpen,
    onCall,
    calling,
    ageDisplay,
}: {
    order: OpdServiceOrder;
    position: number;
    onOpen: () => void;
    onCall: (e: React.MouseEvent) => void;
    calling: boolean;
    ageDisplay: string;
}) {
    const status = order.status?.toLowerCase();
    const isInProgress = status === 'in-progress';
    const isTreated = status === 'treated' || status === 'closed';

    return (
        <div
            className={clsx(
                'flex cursor-pointer items-center gap-4 px-4 py-3 transition-colors hover:bg-slate-50 md:px-5 dark:hover:bg-neutral-800/50',
                isInProgress &&
                    'bg-blue-50/50 hover:bg-blue-50 dark:bg-blue-950/40 dark:hover:bg-blue-950/40',
            )}
            onClick={onOpen}
        >
            {/* Position */}
            <div
                className={clsx(
                    'flex h-8 w-8 shrink-0 items-center justify-center rounded-full text-sm font-bold',
                    isInProgress
                        ? 'bg-blue-600 text-white'
                        : isTreated
                          ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300'
                          : 'bg-slate-100 text-slate-600 dark:bg-neutral-800 dark:text-neutral-300',
                )}
            >
                {isInProgress ? <Activity className="h-4 w-4" /> : position}
            </div>

            {/* Patient Info */}
            <div className="min-w-0 flex-1">
                <div className="flex flex-wrap items-center gap-2">
                    <span className="truncate text-sm font-semibold text-slate-900 dark:text-neutral-100">
                        {order.patient?.name}
                    </span>
                    {statusBadge(order.status)}
                </div>
                <div className="mt-0.5 flex flex-wrap items-center gap-x-2 gap-y-0.5 text-xs text-slate-500 dark:text-neutral-400">
                    <span>{order.patient?.ps_number}</span>
                    <span>&bull;</span>
                    <span>{ageDisplay}</span>
                    <span>&bull;</span>
                    <span>{genderLabel(order.patient?.gender)}</span>
                    {order.treatment_record?.diagnosis_text && (
                        <>
                            <span>&bull;</span>
                            <span className="max-w-[160px] truncate">
                                {order.treatment_record.diagnosis_text}
                            </span>
                        </>
                    )}
                </div>
                <p className="mt-0.5 text-xs text-slate-400">
                    {order.so_number}
                </p>
            </div>

            {/* Actions */}
            <div className="flex shrink-0 items-center gap-2">
                {!isTreated && !isInProgress && (
                    <button
                        type="button"
                        disabled={calling}
                        onClick={onCall}
                        className="hidden rounded-lg bg-teal-600 px-3 py-1.5 text-xs font-semibold text-white transition-colors hover:bg-teal-700 disabled:opacity-50 sm:block"
                    >
                        {calling ? 'Calling…' : 'Call'}
                    </button>
                )}
                <button
                    type="button"
                    onClick={onOpen}
                    className="flex items-center gap-1 rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-xs font-semibold text-slate-700 transition-colors hover:bg-slate-50 dark:border-neutral-800 dark:bg-neutral-900 dark:text-neutral-200 dark:hover:bg-neutral-800/50"
                >
                    Open
                    <ChevronRight className="h-3.5 w-3.5" />
                </button>
            </div>
        </div>
    );
}
