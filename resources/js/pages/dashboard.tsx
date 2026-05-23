import Currency from '@/components/currency';
import AppLayout from '@/layouts/app-layout';
import {
    counter as counterRoute,
    counterClose,
    counterOpen,
    counterView,
    home,
} from '@/routes';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, usePage } from '@inertiajs/react';
import { clsx } from 'clsx';
import {
    Activity,
    AlertCircle,
    CheckCircle2,
    ClipboardList,
    DoorClosed,
    DoorOpen,
    PiggyBank,
    Stethoscope,
    Wallet,
} from 'lucide-react';

const breadcrumbs: BreadcrumbItem[] = [{ title: 'Dashboard', href: home().url }];

type AuthUser = { name: string; email?: string };

type ReceptionistCounter = {
    id: number;
    ct_number: string;
    year: number | string;
    month: number | string;
    number: number | string;
    status: string;
    reception_name?: string | null;
    opening_amount: number;
    closing_amount: number | null;
    opened_at?: string | null;
    closed_at?: string | null;
};

type ReceptionistDashboard = {
    has_open_counter: boolean;
    counter: ReceptionistCounter | null;
    totals: {
        income: number;
        expense: number;
        balance: number;
        net_cash: number | null;
    };
};

type DoctorRecentSO = {
    id: number;
    so_number: string;
    token_short?: string | null;
    status: string;
    patient_name?: string | null;
    ps_number?: string | null;
    service_name?: string | null;
    created_at?: string | null;
};

type DoctorDashboard = {
    counts: {
        open: number;
        in_progress: number;
        treated: number;
        closed: number;
        refunded: number;
        cancelled: number;
        today: number;
    };
    recent: DoctorRecentSO[];
};

type DashboardProps = {
    auth: { user: AuthUser };
    roles: {
        isReceptionist: boolean;
        isDoctor: boolean;
        isAdmin: boolean;
        isAccountant: boolean;
    };
    receptionist: ReceptionistDashboard | null;
    doctor: DoctorDashboard | null;
    [key: string]: unknown;
};

export default function Dashboard() {
    const { auth, roles, receptionist, doctor } =
        usePage<DashboardProps>().props;
    const user = auth.user;

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Dashboard" />
            <div className="flex h-full flex-1 flex-col gap-4 overflow-x-auto p-4">
                {/* Row 1: greeting (2/3) + role widget (1/3) */}
                <div className="grid gap-4 md:grid-cols-3">
                    <div className="md:col-span-2">
                        <GreetingCard user={user} />
                    </div>
                    <div>
                        {roles.isReceptionist && receptionist ? (
                            <ReceptionistCard data={receptionist} />
                        ) : roles.isDoctor && doctor ? (
                            <DoctorCard data={doctor} />
                        ) : (
                            <IdleCard />
                        )}
                    </div>
                </div>

                {/* Row 2: quick links + recent activity */}
                <div className="grid gap-4 md:grid-cols-3">
                    <QuickLinksCard roles={roles} />
                    {roles.isDoctor && doctor ? (
                        <DoctorRecentCard data={doctor} />
                    ) : (
                        <TipCard />
                    )}
                    <ComplianceTipCard />
                </div>
            </div>
        </AppLayout>
    );
}

// ─── Cards ──────────────────────────────────────────────────────────────────

function GreetingCard({ user }: { user: AuthUser }) {
    const hour = new Date().getHours();
    const greeting =
        hour < 12 ? 'Good morning' : hour < 17 ? 'Good afternoon' : 'Good evening';
    const today = new Date().toLocaleDateString(undefined, {
        weekday: 'long',
        day: 'numeric',
        month: 'long',
        year: 'numeric',
    });

    return (
        <div className="relative h-full overflow-hidden rounded-2xl border border-sidebar-border/70 bg-gradient-to-br from-teal-50 via-white to-sky-50 p-6 shadow-sm dark:border-sidebar-border dark:from-teal-950/40 dark:via-gray-900 dark:to-sky-950/40">
            <div className="relative z-10">
                <p className="text-xs font-semibold tracking-wide text-teal-700 uppercase dark:text-teal-300">
                    {today}
                </p>
                <h2 className="mt-2 text-2xl font-bold text-slate-900 md:text-3xl dark:text-slate-100">
                    {greeting}, {user.name}!
                </h2>
                <p className="mt-2 max-w-lg text-sm text-slate-600 dark:text-slate-300">
                    Welcome back to Hospital Care. Your work keeps every patient
                    safe — thank you.
                </p>
            </div>
            <div
                aria-hidden
                className="pointer-events-none absolute -right-12 -bottom-12 h-48 w-48 rounded-full bg-teal-200/40 blur-3xl dark:bg-teal-700/20"
            />
            <div
                aria-hidden
                className="pointer-events-none absolute -top-10 right-20 h-32 w-32 rounded-full bg-sky-200/40 blur-3xl dark:bg-sky-700/20"
            />
        </div>
    );
}

function ReceptionistCard({ data }: { data: ReceptionistDashboard }) {
    const { counter, totals, has_open_counter } = data;
    const balance = totals.balance;

    return (
        <div className="flex h-full flex-col rounded-2xl border border-sidebar-border/70 bg-white p-5 shadow-sm dark:border-sidebar-border dark:bg-gray-900">
            <div className="flex items-center justify-between">
                <div className="flex items-center gap-2">
                    <Wallet className="h-4 w-4 text-teal-600" />
                    <h3 className="text-sm font-semibold text-slate-700 dark:text-slate-200">
                        Counter
                    </h3>
                </div>
                <span
                    className={clsx(
                        'rounded-full px-2 py-0.5 text-xs font-semibold',
                        has_open_counter
                            ? 'bg-emerald-100 text-emerald-700'
                            : 'bg-slate-100 text-slate-600',
                    )}
                >
                    {has_open_counter ? 'OPEN' : counter ? 'LAST' : 'NONE'}
                </span>
            </div>

            {counter ? (
                <>
                    <p className="mt-3 text-xs text-slate-500">
                        {counter.reception_name ?? 'Reception'}
                    </p>
                    <p className="font-mono text-sm font-semibold text-slate-900 dark:text-slate-100">
                        {counter.ct_number}
                    </p>

                    <div className="mt-4 grid grid-cols-2 gap-2 text-xs">
                        <Stat
                            label="Income"
                            value={<Currency value={totals.income} />}
                            tone="positive"
                        />
                        <Stat
                            label="Expense"
                            value={<Currency value={totals.expense} />}
                            tone="negative"
                        />
                    </div>
                    <div className="mt-2 rounded-lg bg-slate-50 px-3 py-2 dark:bg-gray-800">
                        <p className="text-[10px] tracking-wide text-slate-500 uppercase">
                            {has_open_counter ? 'Running Balance' : 'Net Cash'}
                        </p>
                        <p className="text-lg font-bold text-slate-900 dark:text-slate-100">
                            <Currency
                                value={
                                    has_open_counter
                                        ? balance
                                        : (totals.net_cash ?? balance)
                                }
                            />
                        </p>
                    </div>
                </>
            ) : (
                <p className="mt-4 text-sm text-slate-500">
                    No counter found. Open one to start receiving transactions.
                </p>
            )}

            <div className="mt-auto pt-4">
                {has_open_counter && counter ? (
                    <div className="flex gap-2">
                        <Link
                            href={counterView({
                                ctYear: counter.year,
                                ctMonth: counter.month,
                                ctNumber: counter.number,
                            }).url}
                            className="flex-1 rounded-lg bg-teal-600 px-3 py-2 text-center text-xs font-semibold text-white hover:bg-teal-700"
                        >
                            View Counter
                        </Link>
                        <Link
                            href={counterClose().url}
                            className="flex items-center gap-1 rounded-lg border border-rose-200 bg-rose-50 px-3 py-2 text-xs font-semibold text-rose-700 hover:bg-rose-100"
                        >
                            <DoorClosed className="h-3.5 w-3.5" />
                            Close
                        </Link>
                    </div>
                ) : (
                    <div className="flex gap-2">
                        <Link
                            href={counterOpen().url}
                            className="flex flex-1 items-center justify-center gap-1 rounded-lg bg-emerald-600 px-3 py-2 text-xs font-semibold text-white hover:bg-emerald-700"
                        >
                            <DoorOpen className="h-3.5 w-3.5" />
                            Open Counter
                        </Link>
                        <Link
                            href={counterRoute().url}
                            className="rounded-lg border border-slate-200 bg-white px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50"
                        >
                            History
                        </Link>
                    </div>
                )}
            </div>
        </div>
    );
}

function DoctorCard({ data }: { data: DoctorDashboard }) {
    const c = data.counts;

    return (
        <div className="flex h-full flex-col rounded-2xl border border-sidebar-border/70 bg-white p-5 shadow-sm dark:border-sidebar-border dark:bg-gray-900">
            <div className="flex items-center justify-between">
                <div className="flex items-center gap-2">
                    <Stethoscope className="h-4 w-4 text-indigo-600" />
                    <h3 className="text-sm font-semibold text-slate-700 dark:text-slate-200">
                        My Service Orders
                    </h3>
                </div>
                <span className="rounded-full bg-indigo-50 px-2 py-0.5 text-xs font-semibold text-indigo-700">
                    Today: {c.today}
                </span>
            </div>

            <div className="mt-4 grid grid-cols-3 gap-2 text-xs">
                <Stat
                    label="Open"
                    value={c.open + c.in_progress}
                    tone="warning"
                />
                <Stat
                    label="Closed"
                    value={c.closed + c.treated}
                    tone="positive"
                />
                <Stat label="Refunded" value={c.refunded} tone="negative" />
            </div>

            <p className="mt-4 text-xs text-slate-500">
                Pending: {c.open + c.in_progress} · Treated: {c.treated} ·
                Cancelled: {c.cancelled}
            </p>
        </div>
    );
}

function IdleCard() {
    return (
        <div className="flex h-full flex-col items-center justify-center rounded-2xl border border-sidebar-border/70 bg-white p-5 text-center shadow-sm dark:border-sidebar-border dark:bg-gray-900">
            <AlertCircle className="h-6 w-6 text-slate-400" />
            <p className="mt-2 text-sm text-slate-500">
                No role-specific widget. Ask an admin to assign you a profile.
            </p>
        </div>
    );
}

function QuickLinksCard({ roles }: { roles: DashboardProps['roles'] }) {
    const links: { label: string; href: string; icon: typeof Activity }[] = [];
    if (roles.isReceptionist) {
        links.push({
            label: 'My Counter',
            href: counterRoute().url,
            icon: Wallet,
        });
    }
    if (roles.isDoctor) {
        links.push({
            label: 'My Patients',
            href: '/my-patients',
            icon: ClipboardList,
        });
        links.push({
            label: 'My Payments',
            href: '/my-payments',
            icon: PiggyBank,
        });
    }

    return (
        <div className="rounded-2xl border border-sidebar-border/70 bg-white p-5 shadow-sm dark:border-sidebar-border dark:bg-gray-900">
            <h3 className="mb-3 text-sm font-semibold text-slate-700 dark:text-slate-200">
                Quick Links
            </h3>
            {links.length === 0 ? (
                <p className="text-xs text-slate-500">
                    No quick actions for your profile.
                </p>
            ) : (
                <div className="space-y-2">
                    {links.map((l) => (
                        <Link
                            key={l.label}
                            href={l.href}
                            className="flex items-center justify-between rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-100 dark:border-gray-800 dark:bg-gray-800 dark:text-slate-200"
                        >
                            <span className="flex items-center gap-2">
                                <l.icon className="h-3.5 w-3.5" />
                                {l.label}
                            </span>
                            <span className="text-slate-400">›</span>
                        </Link>
                    ))}
                </div>
            )}
        </div>
    );
}

function DoctorRecentCard({ data }: { data: DoctorDashboard }) {
    return (
        <div className="rounded-2xl border border-sidebar-border/70 bg-white p-5 shadow-sm dark:border-sidebar-border dark:bg-gray-900">
            <h3 className="mb-3 flex items-center gap-2 text-sm font-semibold text-slate-700 dark:text-slate-200">
                <Activity className="h-4 w-4 text-indigo-500" /> Recent Service
                Orders
            </h3>
            {data.recent.length === 0 ? (
                <p className="text-xs text-slate-500">
                    No service orders yet. They'll appear here as patients book.
                </p>
            ) : (
                <ul className="space-y-2">
                    {data.recent.map((so) => (
                        <li
                            key={so.id}
                            className="flex items-center justify-between rounded-lg border border-slate-100 px-3 py-2 text-xs dark:border-gray-800"
                        >
                            <div className="min-w-0">
                                <p className="truncate font-semibold text-slate-800 dark:text-slate-100">
                                    {so.patient_name ?? '—'}
                                </p>
                                <p className="truncate font-mono text-[10px] text-slate-500">
                                    {so.so_number}
                                </p>
                            </div>
                            <div className="flex shrink-0 items-center gap-1.5">
                                {so.token_short && (
                                    <span className="rounded bg-slate-100 px-1.5 py-0.5 font-mono text-[10px] font-semibold text-slate-700 dark:bg-gray-800 dark:text-slate-200">
                                        #{so.token_short}
                                    </span>
                                )}
                                <StatusBadge status={so.status} />
                            </div>
                        </li>
                    ))}
                </ul>
            )}
        </div>
    );
}

function TipCard() {
    return (
        <div className="rounded-2xl border border-sidebar-border/70 bg-gradient-to-br from-amber-50 to-orange-50 p-5 shadow-sm dark:border-sidebar-border dark:from-amber-950/30 dark:to-orange-950/30">
            <h3 className="mb-2 flex items-center gap-2 text-sm font-semibold text-amber-900 dark:text-amber-200">
                <CheckCircle2 className="h-4 w-4" /> Tip of the day
            </h3>
            <p className="text-xs text-amber-900/80 dark:text-amber-200/80">
                Confirm patient CNIC and contact at every visit — keeps records
                clean and PHC-audit ready.
            </p>
        </div>
    );
}

function ComplianceTipCard() {
    return (
        <div className="rounded-2xl border border-sidebar-border/70 bg-white p-5 shadow-sm dark:border-sidebar-border dark:bg-gray-900">
            <h3 className="mb-2 text-sm font-semibold text-slate-700 dark:text-slate-200">
                Privacy reminder
            </h3>
            <p className="text-xs text-slate-500 dark:text-slate-400">
                Never share login credentials. Lock your workstation when
                stepping away. Every action is logged with your name (HIPAA &
                PHC).
            </p>
        </div>
    );
}

// ─── Helpers ────────────────────────────────────────────────────────────────

function Stat({
    label,
    value,
    tone = 'neutral',
}: {
    label: string;
    value: React.ReactNode;
    tone?: 'neutral' | 'positive' | 'negative' | 'warning';
}) {
    const tones: Record<string, string> = {
        neutral: 'text-slate-700',
        positive: 'text-emerald-700',
        negative: 'text-rose-700',
        warning: 'text-amber-700',
    };
    return (
        <div className="rounded-lg bg-slate-50 px-3 py-2 dark:bg-gray-800">
            <p className="text-[10px] tracking-wide text-slate-500 uppercase">
                {label}
            </p>
            <p className={clsx('text-sm font-bold', tones[tone])}>{value}</p>
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
                'rounded-full px-1.5 py-0.5 text-[10px] font-semibold tracking-wide uppercase',
                map[s] ?? 'bg-slate-100 text-slate-600',
            )}
        >
            {s ?? '—'}
        </span>
    );
}
