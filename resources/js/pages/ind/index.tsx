import AppLayout from '@/layouts/app-layout';
import { indDashboard, indPatient, apiIndWardSnapshot, apiIndAssignBed } from '@/routes';
import { type BreadcrumbItem } from '@/types';
import { Head, router, usePage } from '@inertiajs/react';
import { clsx } from 'clsx';
import {
    Activity,
    AlertCircle,
    BedDouble,
    Building2,
    ChevronRight,
    Clock,
    DoorOpen,
    Layers,
    RefreshCw,
    Search,
    Stethoscope,
    UserCheck,
    Users,
    X,
} from 'lucide-react';
import { useCallback, useEffect, useRef, useState } from 'react';
import { toast } from 'sonner';

// ─── Types ────────────────────────────────────────────────────────────────────

interface PatientRef {
    id: number;
    name: string;
    ps_number: string;
    gender: 'm' | 'f' | 't' | 'o';
    age_days?: number;
    age_dob?: string;
}

interface ActiveAssignment {
    id: number;
    patient?: PatientRef;
    serviceOrder?: { id: number; so_number: string; status: string; created_at: string };
    admitted_at: string;
}

interface BedData {
    id: number;
    bed_number: string;
    status: string;
    is_active: boolean;
    active_assignment?: ActiveAssignment | null;
}

interface RoomData {
    id: number;
    name: string;
    room_number?: string;
    type: string;
    capacity: number;
    beds: BedData[];
}

interface WardData {
    id: number;
    name: string;
    type: string;
    floor?: string;
    building?: string;
    rooms: RoomData[];
}

interface IndServiceOrder {
    id: number;
    so_number: string;
    so_short: string;
    status: string;
    token?: string | number;
    created_at?: string;
    patient?: PatientRef;
    service?: { id: number; name: string };
}

interface IndDashboardProps {
    isIndDoctor: boolean;
    wards: WardData[];
    unassignedQueue: IndServiceOrder[];
    [key: string]: unknown;
}

// ─── Helpers ──────────────────────────────────────────────────────────────────

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/' },
    { title: 'Indoor', href: indDashboard().url },
];

function ageDisplay(p?: PatientRef): string {
    if (!p) return '—';
    if (p.age_dob) {
        const years = Math.floor((Date.now() - new Date(p.age_dob).getTime()) / 31557600000);
        return `${years} yrs`;
    }
    if (p.age_days) {
        if (p.age_days >= 365) return `${Math.floor(p.age_days / 365)} yrs`;
        if (p.age_days >= 30) return `${Math.floor(p.age_days / 30)} mo`;
        return `${p.age_days} d`;
    }
    return '—';
}

function genderLabel(g?: string) {
    return g === 'm' ? 'M' : g === 'f' ? 'F' : 'O';
}

function wardTypeColor(type: string) {
    return ({
        icu: 'bg-red-50 border-red-200 text-red-800',
        surgical: 'bg-orange-50 border-orange-200 text-orange-800',
        maternity: 'bg-pink-50 border-pink-200 text-pink-800',
        pediatric: 'bg-sky-50 border-sky-200 text-sky-800',
        isolation: 'bg-slate-50 border-slate-200 text-slate-700',
    } as Record<string, string>)[type] ?? 'bg-teal-50 border-teal-200 text-teal-800';
}

function bedStatusDot(status: string) {
    return ({
        available: 'bg-emerald-400',
        occupied: 'bg-red-400',
        reserved: 'bg-amber-400',
        maintenance: 'bg-slate-400',
        cleaning: 'bg-blue-400',
    } as Record<string, string>)[status] ?? 'bg-slate-300';
}

function getCsrfToken(): string {
    return decodeURIComponent(document.cookie.split('XSRF-TOKEN=')[1]?.split(';')[0] ?? '');
}

// ─── Main Component ───────────────────────────────────────────────────────────

export default function IndDashboard() {
    const { isIndDoctor, wards: initialWards, unassignedQueue: initialQueue } =
        usePage<IndDashboardProps>().props;

    const [wards, setWards] = useState<WardData[]>(initialWards ?? []);
    const [queue, setQueue] = useState<IndServiceOrder[]>(initialQueue ?? []);
    const [searchQuery, setSearchQuery] = useState('');
    const [searchResults, setSearchResults] = useState<{ exact: IndServiceOrder[]; possible: IndServiceOrder[] } | null>(null);
    const [searching, setSearching] = useState(false);
    const [refreshing, setRefreshing] = useState(false);

    // Bed assignment modal state
    const [assignModal, setAssignModal] = useState<{ order: IndServiceOrder } | null>(null);
    const [selectedBedId, setSelectedBedId] = useState<number | ''>('');
    const [assigning, setAssigning] = useState(false);

    const debounceRef = useRef<ReturnType<typeof setTimeout> | null>(null);

    // Compute stats from wards
    const allBeds = wards.flatMap((w) => w.rooms.flatMap((r) => r.beds));
    const totalBeds = allBeds.filter((b) => b.is_active).length;
    const occupiedBeds = allBeds.filter((b) => b.status === 'occupied').length;
    const availableBeds = allBeds.filter((b) => b.status === 'available').length;

    // Flat list of available beds for assignment
    const availableBedOptions = wards.flatMap((w) =>
        w.rooms.flatMap((r) =>
            r.beds
                .filter((b) => b.status === 'available' && b.is_active)
                .map((b) => ({ id: b.id, label: `${w.name} → ${r.name} → Bed ${b.bed_number}` })),
        ),
    );

    const refreshSnapshot = useCallback(async (silent = false) => {
        if (!silent) setRefreshing(true);
        try {
            const res = await fetch(apiIndWardSnapshot().url, {
                headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
            });
            if (!res.ok) return;
            const json = await res.json();
            setWards(json.data ?? []);
        } catch {
            // silently fail
        } finally {
            setRefreshing(false);
        }
    }, []);

    useEffect(() => {
        const interval = setInterval(() => refreshSnapshot(true), 45_000);
        return () => clearInterval(interval);
    }, [refreshSnapshot]);

    const handleSearch = useCallback(async (q: string) => {
        if (!q.trim()) { setSearchResults(null); return; }
        setSearching(true);
        try {
            const res = await fetch('/api/ind/search', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    Accept: 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-XSRF-TOKEN': getCsrfToken(),
                },
                body: JSON.stringify({ q }),
            });
            if (!res.ok) { setSearching(false); return; }
            const json = await res.json();
            setSearchResults(json.data);
        } catch {
            // ignore
        } finally {
            setSearching(false);
        }
    }, []);

    const onSearchInput = (e: React.ChangeEvent<HTMLInputElement>) => {
        const v = e.target.value;
        setSearchQuery(v);
        if (debounceRef.current) clearTimeout(debounceRef.current);
        debounceRef.current = setTimeout(() => handleSearch(v), 400);
    };

    const openOrder = (order: IndServiceOrder) => router.visit(indPatient({ id: order.id }).url);

    const assignBed = async () => {
        if (!assignModal || !selectedBedId) return;
        setAssigning(true);
        try {
            const res = await fetch(apiIndAssignBed({ serviceOrder: assignModal.order.id }).url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    Accept: 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-XSRF-TOKEN': getCsrfToken(),
                },
                body: JSON.stringify({ bed_id: selectedBedId }),
            });
            const json = await res.json();
            if (!res.ok) { toast.error(json.message ?? 'Failed to assign bed'); return; }
            toast.success(json.message ?? 'Bed assigned');
            setAssignModal(null);
            setSelectedBedId('');
            setQueue((q) => q.filter((o) => o.id !== assignModal.order.id));
            await refreshSnapshot();
        } catch {
            toast.error('Network error');
        } finally {
            setAssigning(false);
        }
    };

    const allSearchResults = searchResults ? [...searchResults.exact, ...searchResults.possible] : [];

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Indoor Department" />

            <div className="flex h-full flex-col bg-slate-50">

                {/* Header */}
                <div className="flex items-center justify-between border-b border-slate-200 bg-white px-4 py-3 md:px-6">
                    <div className="flex items-center gap-3">
                        <div className="flex h-10 w-10 items-center justify-center rounded-xl bg-indigo-600 shadow-sm">
                            <BedDouble className="h-5 w-5 text-white" />
                        </div>
                        <div>
                            <h1 className="text-base font-bold text-slate-900 md:text-lg">Indoor Department</h1>
                            <p className="text-xs text-slate-500">Ward & Bed Management</p>
                        </div>
                    </div>
                    <button
                        type="button"
                        onClick={() => refreshSnapshot()}
                        disabled={refreshing}
                        className="flex items-center gap-1.5 rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-xs font-medium text-slate-600 transition-colors hover:bg-slate-50 disabled:opacity-50"
                    >
                        <RefreshCw className={clsx('h-3.5 w-3.5', refreshing && 'animate-spin')} />
                        Refresh
                    </button>
                </div>

                {/* Access Denied */}
                {!isIndDoctor && (
                    <div className="flex flex-1 flex-col items-center justify-center gap-4 p-8 text-center">
                        <AlertCircle className="h-12 w-12 text-red-400" />
                        <div>
                            <h2 className="text-lg font-semibold text-red-800">Access Restricted</h2>
                            <p className="mt-1 text-sm text-red-600">You need an <strong>Indoor Doctor</strong> profile to access this dashboard.</p>
                        </div>
                    </div>
                )}

                {isIndDoctor && (
                    <div className="flex flex-1 overflow-hidden">
                        {/* ── LEFT: Ward/Bed Map ─────────────────────────────── */}
                        <div className="flex w-full flex-col overflow-y-auto border-r border-slate-200 bg-white md:w-3/5 lg:w-2/3">

                            {/* Bed stats */}
                            <div className="grid grid-cols-3 divide-x divide-slate-100 border-b border-slate-100">
                                <div className="px-4 py-3 text-center">
                                    <p className="text-2xl font-bold text-slate-900">{totalBeds}</p>
                                    <p className="text-xs text-slate-500">Total Beds</p>
                                </div>
                                <div className="px-4 py-3 text-center">
                                    <p className="text-2xl font-bold text-red-600">{occupiedBeds}</p>
                                    <p className="text-xs text-slate-500">Occupied</p>
                                </div>
                                <div className="px-4 py-3 text-center">
                                    <p className="text-2xl font-bold text-emerald-600">{availableBeds}</p>
                                    <p className="text-xs text-slate-500">Available</p>
                                </div>
                            </div>

                            {/* Legend */}
                            <div className="flex items-center gap-4 border-b border-slate-100 bg-slate-50 px-4 py-2">
                                {[
                                    { label: 'Available', dot: 'bg-emerald-400' },
                                    { label: 'Occupied', dot: 'bg-red-400' },
                                    { label: 'Reserved', dot: 'bg-amber-400' },
                                    { label: 'Maintenance', dot: 'bg-slate-400' },
                                ].map(({ label, dot }) => (
                                    <div key={label} className="flex items-center gap-1.5 text-xs text-slate-500">
                                        <span className={clsx('h-2.5 w-2.5 rounded-full', dot)} />
                                        {label}
                                    </div>
                                ))}
                            </div>

                            {/* Wards */}
                            <div className="space-y-4 p-4">
                                {wards.length === 0 && (
                                    <div className="flex flex-col items-center justify-center gap-3 py-16 text-center">
                                        <Building2 className="h-10 w-10 text-slate-300" />
                                        <p className="text-sm text-slate-500">No wards configured. Add them in Admin → Indoor.</p>
                                    </div>
                                )}
                                {wards.map((ward) => (
                                    <WardCard key={ward.id} ward={ward} onOpenPatient={(order) => openOrder(order)} />
                                ))}
                            </div>
                        </div>

                        {/* ── RIGHT: Queue + Search ──────────────────────────── */}
                        <div className="flex w-full flex-col overflow-y-auto md:w-2/5 lg:w-1/3">

                            {/* Search */}
                            <div className="border-b border-slate-200 bg-white p-4">
                                <label className="mb-2 block text-xs font-semibold text-slate-700">Find Service Order / Patient</label>
                                <div className="relative">
                                    <Search className="absolute left-3 top-1/2 h-3.5 w-3.5 -translate-y-1/2 text-slate-400" />
                                    <input
                                        value={searchQuery}
                                        onChange={onSearchInput}
                                        placeholder="SO number or Patient MR#..."
                                        className="w-full rounded-xl border border-slate-200 bg-slate-50 py-2.5 pl-9 pr-4 text-sm text-slate-800 placeholder:text-slate-400 focus:border-indigo-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-100"
                                    />
                                    {searching && (
                                        <span className="absolute right-3 top-1/2 -translate-y-1/2">
                                            <span className="inline-block h-3.5 w-3.5 animate-spin rounded-full border-2 border-indigo-400 border-t-transparent" />
                                        </span>
                                    )}
                                </div>
                                {searchQuery && allSearchResults.length > 0 && (
                                    <div className="mt-2 overflow-hidden rounded-xl border border-slate-200 bg-white shadow-lg">
                                        {allSearchResults.map((order) => (
                                            <SearchResultRow key={order.id} order={order}
                                                onOpen={() => { setSearchQuery(''); setSearchResults(null); openOrder(order); }}
                                                onAssign={() => { setSearchQuery(''); setSearchResults(null); setAssignModal({ order }); }}
                                                ageDisplay={ageDisplay(order.patient)}
                                            />
                                        ))}
                                    </div>
                                )}
                                {searchQuery && !searching && allSearchResults.length === 0 && (
                                    <p className="mt-2 text-xs text-slate-500">No results for "{searchQuery}"</p>
                                )}
                            </div>

                            {/* Unassigned Queue */}
                            <div className="flex-1 overflow-y-auto">
                                <div className="flex items-center justify-between border-b border-slate-100 bg-slate-50 px-4 py-2.5">
                                    <h3 className="text-xs font-semibold text-slate-700 flex items-center gap-1.5">
                                        <Clock className="h-3.5 w-3.5 text-amber-500" />
                                        Waiting for Bed ({queue.length})
                                    </h3>
                                </div>

                                {queue.length === 0 ? (
                                    <div className="flex flex-col items-center justify-center gap-2 py-12 text-center">
                                        <UserCheck className="h-8 w-8 text-emerald-300" />
                                        <p className="text-xs text-slate-400">All patients have been assigned beds.</p>
                                    </div>
                                ) : (
                                    <div className="divide-y divide-slate-100">
                                        {queue.map((order) => (
                                            <QueueRow
                                                key={order.id}
                                                order={order}
                                                ageDisplay={ageDisplay(order.patient)}
                                                onOpen={() => openOrder(order)}
                                                onAssign={() => setAssignModal({ order })}
                                            />
                                        ))}
                                    </div>
                                )}
                            </div>
                        </div>
                    </div>
                )}
            </div>

            {/* Bed Assignment Modal */}
            {assignModal && (
                <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4">
                    <div className="w-full max-w-sm rounded-2xl bg-white shadow-2xl">
                        <div className="flex items-center justify-between border-b border-slate-100 px-5 py-4">
                            <h3 className="text-sm font-semibold text-slate-900">Assign Bed</h3>
                            <button type="button" onClick={() => { setAssignModal(null); setSelectedBedId(''); }}
                                className="text-slate-400 hover:text-slate-600">
                                <X className="h-4 w-4" />
                            </button>
                        </div>
                        <div className="p-5">
                            <div className="mb-3 rounded-xl bg-slate-50 px-4 py-3">
                                <p className="text-sm font-semibold text-slate-900">{assignModal.order.patient?.name}</p>
                                <p className="text-xs text-slate-500">{assignModal.order.so_number} &bull; {assignModal.order.patient?.ps_number}</p>
                            </div>
                            <label className="mb-1 block text-xs font-medium text-slate-600">Select Available Bed</label>
                            <select
                                value={selectedBedId}
                                onChange={(e) => setSelectedBedId(Number(e.target.value) || '')}
                                className="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-800 focus:border-indigo-400 focus:outline-none focus:ring-2 focus:ring-indigo-100"
                            >
                                <option value="">— Choose a bed —</option>
                                {availableBedOptions.map((b) => (
                                    <option key={b.id} value={b.id}>{b.label}</option>
                                ))}
                            </select>
                            {availableBedOptions.length === 0 && (
                                <p className="mt-2 text-xs text-amber-600">No available beds. Check ward management.</p>
                            )}
                        </div>
                        <div className="flex justify-end gap-2 border-t border-slate-100 px-5 py-3">
                            <button type="button" onClick={() => { setAssignModal(null); setSelectedBedId(''); }}
                                className="rounded-lg border border-slate-200 px-4 py-2 text-xs font-medium text-slate-600 hover:bg-slate-50">
                                Cancel
                            </button>
                            <button type="button" disabled={!selectedBedId || assigning} onClick={assignBed}
                                className="rounded-lg bg-indigo-600 px-4 py-2 text-xs font-semibold text-white hover:bg-indigo-700 disabled:opacity-50">
                                {assigning ? 'Assigning…' : 'Assign Bed'}
                            </button>
                        </div>
                    </div>
                </div>
            )}
        </AppLayout>
    );
}

// ─── Ward Card ────────────────────────────────────────────────────────────────

function WardCard({ ward, onOpenPatient }: { ward: WardData; onOpenPatient: (order: IndServiceOrder) => void }) {
    const [collapsed, setCollapsed] = useState(false);
    const totalBeds = ward.rooms.flatMap((r) => r.beds).filter((b) => b.is_active).length;
    const occupiedBeds = ward.rooms.flatMap((r) => r.beds).filter((b) => b.status === 'occupied').length;

    return (
        <div className={clsx('rounded-2xl border', wardTypeColor(ward.type))}>
            <button
                type="button"
                onClick={() => setCollapsed((c) => !c)}
                className="flex w-full items-center justify-between px-4 py-3"
            >
                <div className="flex items-center gap-2.5">
                    <Building2 className="h-4 w-4 opacity-70" />
                    <div className="text-left">
                        <p className="text-sm font-bold">{ward.name}</p>
                        <p className="text-xs opacity-70">
                            {ward.floor && `${ward.floor} Floor`}{ward.building && ` · ${ward.building}`} &bull; {occupiedBeds}/{totalBeds} beds
                        </p>
                    </div>
                </div>
                <div className="flex items-center gap-2">
                    <span className={clsx('rounded-full px-2 py-0.5 text-xs font-semibold uppercase tracking-wide', wardTypeColor(ward.type))}>
                        {ward.type}
                    </span>
                    {collapsed ? <ChevronRight className="h-4 w-4 opacity-60 rotate-90" /> : <ChevronRight className="h-4 w-4 opacity-60 -rotate-90" />}
                </div>
            </button>

            {!collapsed && (
                <div className="divide-y divide-white/40 border-t border-white/40">
                    {ward.rooms.map((room) => (
                        <RoomRow key={room.id} room={room} onOpenPatient={onOpenPatient} />
                    ))}
                    {ward.rooms.length === 0 && (
                        <p className="px-4 py-3 text-xs opacity-60">No rooms in this ward.</p>
                    )}
                </div>
            )}
        </div>
    );
}

// ─── Room Row ─────────────────────────────────────────────────────────────────

function RoomRow({ room, onOpenPatient }: { room: RoomData; onOpenPatient: (order: IndServiceOrder) => void }) {
    return (
        <div className="px-4 py-3">
            <div className="mb-2 flex items-center gap-2">
                <DoorOpen className="h-3.5 w-3.5 opacity-60" />
                <p className="text-xs font-semibold opacity-80">
                    {room.name}{room.room_number ? ` (#${room.room_number})` : ''} &bull; Cap. {room.capacity}
                </p>
            </div>
            <div className="flex flex-wrap gap-2">
                {room.beds.filter((b) => b.is_active).map((bed) => (
                    <BedBlock key={bed.id} bed={bed} onOpenPatient={onOpenPatient} />
                ))}
                {room.beds.filter((b) => b.is_active).length === 0 && (
                    <p className="text-xs opacity-50">No active beds</p>
                )}
            </div>
        </div>
    );
}

// ─── Bed Block ────────────────────────────────────────────────────────────────

function BedBlock({ bed, onOpenPatient }: { bed: BedData; onOpenPatient: (order: IndServiceOrder) => void }) {
    const assignment = bed.active_assignment;
    const isOccupied = bed.status === 'occupied' && assignment;

    return (
        <div
            className={clsx(
                'min-w-[120px] max-w-[150px] flex-1 rounded-xl border p-2.5 transition-all',
                isOccupied
                    ? 'cursor-pointer border-red-200 bg-red-50 hover:bg-red-100'
                    : bed.status === 'available'
                        ? 'border-emerald-100 bg-emerald-50/50'
                        : 'border-slate-200 bg-slate-100',
            )}
            onClick={() => {
                if (isOccupied && assignment?.serviceOrder) {
                    onOpenPatient(assignment.serviceOrder as any);
                }
            }}
            title={isOccupied ? `Open ${assignment?.patient?.name}'s file` : bed.status}
        >
            <div className="flex items-center gap-1.5">
                <span className={clsx('h-2 w-2 rounded-full shrink-0', bedStatusDot(bed.status))} />
                <span className="truncate text-xs font-bold text-slate-700">Bed {bed.bed_number}</span>
            </div>
            {isOccupied && assignment ? (
                <>
                    <p className="mt-1 truncate text-xs font-semibold text-slate-800">{assignment.patient?.name}</p>
                    <p className="truncate text-[10px] text-slate-500">
                        {assignment.patient?.ps_number} &bull; {ageDisplay(assignment.patient)} &bull; {genderLabel(assignment.patient?.gender)}
                    </p>
                    <p className="mt-0.5 text-[10px] text-slate-400">
                        Admitted {new Date(assignment.admitted_at).toLocaleDateString('en-US', { month: 'short', day: 'numeric' })}
                    </p>
                </>
            ) : (
                <p className="mt-1 text-[10px] capitalize text-slate-400">{bed.status}</p>
            )}
        </div>
    );
}

// ─── Queue Row ────────────────────────────────────────────────────────────────

function QueueRow({ order, ageDisplay, onOpen, onAssign }: {
    order: IndServiceOrder;
    ageDisplay: string;
    onOpen: () => void;
    onAssign: () => void;
}) {
    return (
        <div className="flex items-start gap-3 bg-white px-4 py-3 transition-colors hover:bg-slate-50">
            <div className="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-amber-100 text-amber-700">
                <Clock className="h-3.5 w-3.5" />
            </div>
            <div className="min-w-0 flex-1">
                <p className="truncate text-sm font-semibold text-slate-900">{order.patient?.name}</p>
                <p className="text-xs text-slate-500">{order.patient?.ps_number} &bull; {ageDisplay}</p>
                <p className="text-xs text-slate-400">{order.so_number}</p>
            </div>
            <div className="flex shrink-0 flex-col items-end gap-1">
                <button type="button" onClick={onAssign}
                    className="rounded-lg bg-indigo-600 px-2.5 py-1 text-xs font-semibold text-white hover:bg-indigo-700">
                    Assign Bed
                </button>
                <button type="button" onClick={onOpen}
                    className="flex items-center gap-1 text-xs text-slate-400 hover:text-slate-600">
                    Open file <ChevronRight className="h-3 w-3" />
                </button>
            </div>
        </div>
    );
}

// ─── Search Result Row ────────────────────────────────────────────────────────

function SearchResultRow({ order, ageDisplay, onOpen, onAssign }: {
    order: IndServiceOrder;
    ageDisplay: string;
    onOpen: () => void;
    onAssign: () => void;
}) {
    return (
        <div className="flex items-center justify-between px-4 py-3 hover:bg-slate-50">
            <div className="min-w-0 flex-1">
                <p className="truncate text-sm font-semibold text-slate-900">{order.patient?.name}</p>
                <p className="text-xs text-slate-500">{order.so_number} &bull; {ageDisplay}</p>
            </div>
            <div className="flex shrink-0 items-center gap-1.5">
                <button type="button" onClick={onAssign}
                    className="rounded-lg bg-indigo-600 px-2 py-1 text-xs font-semibold text-white hover:bg-indigo-700">
                    Assign
                </button>
                <button type="button" onClick={onOpen}
                    className="rounded-lg border border-slate-200 bg-white px-2 py-1 text-xs font-medium text-slate-600 hover:bg-slate-50">
                    File
                </button>
            </div>
        </div>
    );
}
