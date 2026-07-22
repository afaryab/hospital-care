import { formatPatientAge, triageBadgeClass } from '@/lib/constants';
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
import { useCallback, useEffect, useRef, useState } from 'react';
import { toast } from 'sonner';

interface DeptOrder extends ServiceOrder {
    status: string;
    patient?: { id: number; name: string; ps_number: string; gender?: string; age_days?: number; age_dob?: string } | null;
    service?: { id: number; name: string } | null;
    treatment_record?: {
        id: number; is_finalized: boolean; diagnosis_text?: string;
        triage?: { id: number; name: string; color: string } | null;
    } | null;
    created_at?: string;
}

interface TodayStats { open: number; in_progress: number; treated: number; total: number }

interface Props {
    deptName: string;
    accentColor: string;        // Tailwind bg color for header icon, e.g. 'bg-red-600'
    accentClass: string;        // Tailwind text color, e.g. 'text-red-600'
    hasAccess: boolean;
    orders: DeptOrder[];
    stats: TodayStats;
    noAccessMessage: string;
    patientUrl: (id: number) => string;
    myQueueUrl: string;         // GET — /api/{dept}/my-queue?types[]=...
    searchApiUrl: string;       // POST — /api/{dept}/search
    searchTypes: string[];      // SO types to search, e.g. ['EMG']
    doctorScoped?: boolean;     // whether queue is filtered by doctor_id (default true)
    flashError?: string;
    icon: React.ReactNode;
}

function statusBadge(status: string) {
    const s = status.toLowerCase();
    if (s === 'in-progress') return (
        <span className="inline-flex items-center gap-1 rounded-full bg-blue-50 px-2.5 py-0.5 text-xs font-semibold text-blue-700 ring-1 ring-blue-200">
            <span className="h-1.5 w-1.5 animate-pulse rounded-full bg-blue-500" /> In Progress
        </span>
    );
    if (s === 'open') return (
        <span className="inline-flex items-center gap-1 rounded-full bg-amber-50 px-2.5 py-0.5 text-xs font-semibold text-amber-700 ring-1 ring-amber-200">
            <Clock className="h-3 w-3" /> Waiting
        </span>
    );
    if (s === 'treated' || s === 'closed') return (
        <span className="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2.5 py-0.5 text-xs font-semibold text-emerald-700 ring-1 ring-emerald-200">
            <UserCheck className="h-3 w-3" /> Treated
        </span>
    );
    return <span className="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-medium text-slate-600">{status}</span>;
}

function getCsrf() {
    return decodeURIComponent(document.cookie.split('XSRF-TOKEN=')[1]?.split(';')[0] ?? '');
}

export default function DeptQueueDashboard({
    deptName, accentColor, accentClass, hasAccess, orders: initialOrders, stats: initialStats,
    noAccessMessage, patientUrl, myQueueUrl, searchApiUrl, searchTypes,
    doctorScoped = true, flashError, icon,
}: Props) {
    const [orders, setOrders] = useState<DeptOrder[]>(initialOrders ?? []);
    const [stats, setStats] = useState(initialStats);
    const [searchQuery, setSearchQuery] = useState('');
    const [searchResults, setSearchResults] = useState<DeptOrder[] | null>(null);
    const [searching, setSearching] = useState(false);
    const [callingPatient, setCallingPatient] = useState<number | null>(null);
    const debounceRef = useRef<ReturnType<typeof setTimeout> | null>(null);

    const refreshQueue = useCallback(async () => {
        try {
            const params = new URLSearchParams();
            searchTypes.forEach((t) => params.append('types[]', t));
            if (!doctorScoped) params.set('doctor_scoped', '0');
            const res = await fetch(`${myQueueUrl}?${params}`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest', Accept: 'application/json' },
            });
            if (!res.ok) return;
            const json = await res.json();
            setOrders(json.data ?? []);
            setStats(json.stats ?? stats);
        } catch {
            toast.error('Queue refresh failed');
        }
    }, [myQueueUrl, searchTypes, doctorScoped]);

    useEffect(() => {
        const interval = setInterval(refreshQueue, 30_000);
        return () => clearInterval(interval);
    }, [refreshQueue]);

    const handleSearch = useCallback(async (q: string) => {
        if (!q.trim()) { setSearchResults(null); return; }
        setSearching(true);
        try {
            const res = await fetch(searchApiUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json', Accept: 'application/json',
                    'X-Requested-With': 'XMLHttpRequest', 'X-XSRF-TOKEN': getCsrf(),
                },
                body: JSON.stringify({ q, types: searchTypes }),
            });
            if (!res.ok) { toast.error('Search failed'); return; }
            const json = await res.json();
            setSearchResults(json.data ?? []);
        } catch {
            toast.error('Network error');
        } finally {
            setSearching(false);
        }
    }, [searchApiUrl, searchTypes]);

    const onSearchInput = (e: React.ChangeEvent<HTMLInputElement>) => {
        const v = e.target.value;
        setSearchQuery(v);
        if (debounceRef.current) clearTimeout(debounceRef.current);
        debounceRef.current = setTimeout(() => handleSearch(v), 350);
    };

    const callPatient = async (order: DeptOrder, e: React.MouseEvent) => {
        e.stopPropagation();
        setCallingPatient(order.id!);
        try {
            const statusUrl = order.id
                ? myQueueUrl.replace('/my-queue', `/service-orders/${order.id}/status`)
                : null;
            if (!statusUrl) return;
            await fetch(statusUrl, {
                method: 'PATCH',
                headers: { 'Content-Type': 'application/json', Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'X-XSRF-TOKEN': getCsrf() },
                body: JSON.stringify({ status: 'in-progress' }),
            });
            toast.success(`${order.patient?.name ?? 'Patient'} called`);
            await refreshQueue();
        } catch {
            toast.error('Failed to update status');
        } finally {
            setCallingPatient(null);
        }
    };

    if (!hasAccess) {
        return (
            <div className="flex flex-1 items-center justify-center p-8">
                <div className="max-w-sm rounded-2xl border border-amber-200 bg-amber-50 p-8 text-center shadow-sm">
                    <AlertCircle className="mx-auto mb-3 h-10 w-10 text-amber-500" />
                    <h2 className="mb-2 text-lg font-semibold text-slate-800">{deptName} Portal</h2>
                    <p className="text-sm text-slate-500">{noAccessMessage}</p>
                </div>
            </div>
        );
    }

    const displayOrders = searchResults ?? orders;

    return (
        <div className="min-h-full bg-gradient-to-br from-slate-50 via-white to-slate-50 p-4 md:p-6">

            {/* Header */}
            <div className="mb-6 flex items-center justify-between">
                <div className="flex items-center gap-3">
                    <div className={clsx('flex h-12 w-12 items-center justify-center rounded-2xl shadow-md text-white', accentColor)}>
                        {icon}
                    </div>
                    <div>
                        <h1 className="text-xl font-bold text-slate-900 md:text-2xl">{deptName} Dashboard</h1>
                        <p className="text-sm text-slate-500">Today's queue</p>
                    </div>
                </div>
                <div className="hidden items-center gap-2 rounded-xl bg-white px-3 py-2 shadow-sm ring-1 ring-slate-200 sm:flex">
                    <span className="h-2 w-2 animate-pulse rounded-full bg-emerald-500" />
                    <span className="text-sm font-medium text-slate-700">Live Queue</span>
                </div>
            </div>

            {/* Stats */}
            <div className="mb-6 grid grid-cols-2 gap-3 sm:grid-cols-4">
                {[
                    { label: 'Total Today', value: stats.total, icon: <Users className="h-5 w-5 text-slate-500" />, bg: 'bg-white', text: 'text-slate-900' },
                    { label: 'Waiting', value: stats.open, icon: <Clock className="h-5 w-5 text-amber-500" />, bg: 'bg-amber-50', text: 'text-amber-700' },
                    { label: 'In Progress', value: stats.in_progress, icon: <Activity className="h-5 w-5 text-blue-500" />, bg: 'bg-blue-50', text: 'text-blue-700' },
                    { label: 'Treated', value: stats.treated, icon: <UserCheck className="h-5 w-5 text-emerald-600" />, bg: 'bg-emerald-50', text: 'text-emerald-700' },
                ].map((s) => (
                    <div key={s.label} className={clsx('rounded-2xl border border-slate-200 p-4 shadow-sm', s.bg)}>
                        <div className="flex items-center justify-between">
                            <span className="text-xs font-medium text-slate-500">{s.label}</span>
                            {s.icon}
                        </div>
                        <p className={clsx('mt-2 text-3xl font-bold tabular-nums', s.text)}>{s.value}</p>
                    </div>
                ))}
            </div>

            {/* Search */}
            <div className="mb-6 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm md:p-5">
                <label className="mb-2 block text-sm font-semibold text-slate-700">Find Patient or Service Order</label>
                <div className="relative">
                    <Search className="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" />
                    <input
                        value={searchQuery}
                        onChange={onSearchInput}
                        placeholder="Enter SO number or patient name / MR#…"
                        className="w-full rounded-xl border border-slate-200 bg-slate-50 py-3 pl-10 pr-4 text-sm text-slate-800 placeholder:text-slate-400 focus:border-slate-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-slate-100"
                    />
                    {searching && (
                        <span className="absolute right-3 top-1/2 -translate-y-1/2">
                            <span className="inline-block h-4 w-4 animate-spin rounded-full border-2 border-slate-400 border-t-transparent" />
                        </span>
                    )}
                </div>
                {flashError && <p className="mt-2 text-sm text-red-600">{flashError}</p>}
                {searchQuery && searchResults !== null && searchResults.length === 0 && !searching && (
                    <p className="mt-2 text-sm text-slate-500">No results for "{searchQuery}"</p>
                )}
                {searchQuery && searchResults !== null && searchResults.length > 0 && (
                    <div className="mt-2 divide-y divide-slate-100 overflow-hidden rounded-xl border border-slate-200 bg-white shadow-lg">
                        {searchResults.map((order) => (
                            <button
                                key={order.id}
                                type="button"
                                onClick={() => router.visit(patientUrl(order.id!))}
                                className="flex w-full items-center justify-between px-4 py-3 text-left transition-colors hover:bg-slate-50"
                            >
                                <div>
                                    <p className="text-sm font-semibold text-slate-900">{order.patient?.name}</p>
                                    <p className="text-xs text-slate-500">{order.so_number} &bull; {order.patient?.ps_number}</p>
                                </div>
                                <div className="flex items-center gap-2">
                                    {order.treatment_record?.triage && (
                                        <span className={clsx('rounded-full px-2 py-0.5 text-[10px] font-semibold ring-1', triageBadgeClass(order.treatment_record.triage.color))}>
                                            {order.treatment_record.triage.name}
                                        </span>
                                    )}
                                    {statusBadge(order.status)}
                                    <ChevronRight className="h-4 w-4 text-slate-400" />
                                </div>
                            </button>
                        ))}
                    </div>
                )}
            </div>

            {/* Queue */}
            <div className="rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div className="flex items-center justify-between border-b border-slate-100 px-4 py-4 md:px-5">
                    <h2 className="text-base font-semibold text-slate-900">
                        {searchResults ? `Search Results (${searchResults.length})` : 'My Queue Today'}
                    </h2>
                    <button
                        type="button"
                        onClick={() => { setSearchQuery(''); setSearchResults(null); refreshQueue(); }}
                        className="rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-xs font-medium text-slate-600 transition-colors hover:bg-slate-50"
                    >
                        Refresh
                    </button>
                </div>

                {displayOrders.length === 0 ? (
                    <div className="flex flex-col items-center justify-center gap-3 py-16 text-center">
                        <Users className="h-10 w-10 text-slate-300" />
                        <p className="text-sm text-slate-500">
                            {searchResults ? 'No matching orders found.' : 'No patients in your queue today.'}
                        </p>
                    </div>
                ) : (
                    <div className="divide-y divide-slate-100">
                        {displayOrders.map((order, idx) => {
                            const status = order.status?.toLowerCase();
                            const isInProgress = status === 'in-progress';
                            const isTreated = status === 'treated' || status === 'closed';
                            return (
                                <div
                                    key={order.id}
                                    className={clsx(
                                        'flex cursor-pointer items-center gap-4 px-4 py-3 transition-colors hover:bg-slate-50 md:px-5',
                                        isInProgress && 'bg-blue-50/50 hover:bg-blue-50',
                                    )}
                                    onClick={() => router.visit(patientUrl(order.id!))}
                                >
                                    <div className={clsx(
                                        'flex h-8 w-8 shrink-0 items-center justify-center rounded-full text-sm font-bold',
                                        isInProgress ? 'bg-blue-600 text-white' : isTreated ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-600',
                                    )}>
                                        {isInProgress ? <Activity className="h-4 w-4" /> : idx + 1}
                                    </div>

                                    <div className="min-w-0 flex-1">
                                        <div className="flex flex-wrap items-center gap-2">
                                            <span className="truncate text-sm font-semibold text-slate-900">{order.patient?.name}</span>
                                            {order.treatment_record?.triage && (
                                                <span className={clsx('rounded-full px-2 py-0.5 text-[10px] font-semibold ring-1', triageBadgeClass(order.treatment_record.triage.color))}>
                                                    {order.treatment_record.triage.name}
                                                </span>
                                            )}
                                            {statusBadge(order.status)}
                                        </div>
                                        <div className="mt-0.5 flex flex-wrap items-center gap-x-2 gap-y-0.5 text-xs text-slate-500">
                                            <span>{order.patient?.ps_number}</span>
                                            {order.patient && <><span>&bull;</span><span>{formatPatientAge(order.patient)}</span></>}
                                            {order.service?.name && <><span>&bull;</span><span>{order.service.name}</span></>}
                                            {order.treatment_record?.diagnosis_text && (
                                                <><span>&bull;</span><span className="max-w-[160px] truncate">{order.treatment_record.diagnosis_text}</span></>
                                            )}
                                        </div>
                                        <p className="mt-0.5 text-xs text-slate-400">{order.so_number}</p>
                                    </div>

                                    <div className="flex shrink-0 items-center gap-2">
                                        {!isTreated && !isInProgress && (
                                            <button
                                                type="button"
                                                disabled={callingPatient === order.id}
                                                onClick={(e) => callPatient(order, e)}
                                                className={clsx(
                                                    'hidden rounded-lg px-3 py-1.5 text-xs font-semibold text-white transition-colors disabled:opacity-50 sm:block',
                                                    accentColor, 'hover:opacity-90',
                                                )}
                                            >
                                                {callingPatient === order.id ? 'Calling…' : 'Call'}
                                            </button>
                                        )}
                                        <button
                                            type="button"
                                            onClick={() => router.visit(patientUrl(order.id!))}
                                            className="flex items-center gap-1 rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-xs font-semibold text-slate-700 transition-colors hover:bg-slate-50"
                                        >
                                            Open <ChevronRight className="h-3.5 w-3.5" />
                                        </button>
                                    </div>
                                </div>
                            );
                        })}
                    </div>
                )}
            </div>
        </div>
    );
}
