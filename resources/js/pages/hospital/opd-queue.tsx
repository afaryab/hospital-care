// @ts-nocheck
import AppLayout from '@/layouts/app-layout';
import { counter, home } from '@/routes';
import { type BreadcrumbItem } from '@/types';
import { Head, usePage } from '@inertiajs/react';
import { useEffect, useMemo, useRef, useState } from 'react';

interface opdQueuePageProps {
    serviceOrdersByService: any;
    services: any;
    [key: string]: any;
}

export default function ServiceOrdersList() {
    const breadcrumbs: BreadcrumbItem[] = [
        {
            title: 'Dashboard',
            href: home().url,
        },
        {
            title: 'Hospital OPD',
            href: counter().url,
        },
    ];

    const { serviceOrdersByService, services } =
        usePage<opdQueuePageProps>().props;

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="OPD Hospital Queue" />
            <div className="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl bg-[#06df72] p-1 dark:bg-[#262626]">
                <div className="flex flex-1 flex-col gap-4 overflow-x-auto rounded-xl bg-white p-0 p-6 text-[#1c398e] dark:bg-neutral-950">
                    {Object.keys(serviceOrdersByService).length === 0 ? (
                        <div className="flex h-full flex-col items-center justify-center gap-2">
                            <h2 className="text-lg font-semibold">
                                No active OPD queues
                            </h2>
                            <p className="text-sm text-slate-500">
                                All service orders are currently closed.
                            </p>
                        </div>
                    ) : (
                        Object.entries(serviceOrdersByService).map(
                            ([serviceId, orders]) => {
                                const service = services[serviceId];
                                return (
                                    <div key={serviceId}>
                                        <OPDQueueSlider
                                            serviceName={
                                                service
                                                    ? service.name
                                                    : `Service ${serviceId}`
                                            }
                                            nowServing={orders.find(
                                                (o) =>
                                                    o.status.toLowerCase() ===
                                                    'in-progress',
                                            )}
                                            waiting={orders.filter(
                                                (o) =>
                                                    o.status.toLowerCase() ===
                                                    'open',
                                            )}
                                            onViewAll={undefined}
                                        />
                                    </div>
                                );
                            },
                        )
                    )}
                </div>
            </div>
        </AppLayout>
    );
}

function cn(...classes) {
    return classes.filter(Boolean).join(' ');
}

function TokenCard({ item, variant = 'open', minify }) {
    const isNow = variant === 'in-progress';
    return (
        <div
            className={cn(
                'max-w-[260px] min-w-[260px] rounded-2xl border bg-white shadow-sm',
                'p-4 md:p-5',
                isNow
                    ? 'border-emerald-200 ring-1 ring-emerald-100'
                    : 'border-slate-200',
            )}
        >
            <div className="flex items-start justify-between gap-3">
                <div className="space-y-1">
                    <div className="text-xs font-semibold tracking-wide text-slate-500">
                        Token #
                        {item.token_short ? item.token_short : item.so_short}
                    </div>
                    <div className="text-lg leading-tight font-semibold text-slate-900">
                        {item.patient.name}
                    </div>
                </div>

                <span
                    className={cn(
                        'inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold',
                        isNow
                            ? 'bg-emerald-50 text-emerald-700'
                            : 'bg-slate-100 text-slate-700',
                    )}
                >
                    {isNow ? 'Now Serving' : 'Waiting'}
                </span>
            </div>

            {minify == false && (
                <>
                    {item.patient?.ps_number && minify == false ? (
                        <div className="text-xs text-slate-500">
                            MR #: {item.patient.ps_number}
                        </div>
                    ) : null}
                    {item.so_number && minify == false ? (
                        <div className="text-xs text-slate-500">
                            MRI #: {item.so_number}
                            {item.so_short ? ` (${item.so_short})` : ''}
                        </div>
                    ) : null}
                    <div className="mt-4 flex items-center justify-between text-sm">
                        <div className="text-slate-600">Estimated</div>
                        <div
                            className={cn(
                                'font-semibold',
                                isNow ? 'text-emerald-700' : 'text-slate-800',
                            )}
                        >
                            {item.eta ?? '—'}
                        </div>
                    </div>

                    <div className="mt-3 h-px bg-slate-100" />

                    <div className="mt-3 flex items-center justify-between text-xs text-slate-500">
                        <span>Queue status</span>
                        <span className="font-medium">
                            {item.status ?? (isNow ? 'Now Serving' : 'Waiting')}
                        </span>
                    </div>
                </>
            )}
        </div>
    );
}

function IconChevronLeft(props) {
    return (
        <svg viewBox="0 0 20 20" fill="currentColor" {...props}>
            <path
                fillRule="evenodd"
                d="M12.78 15.53a.75.75 0 0 1-1.06 0l-5-5a.75.75 0 0 1 0-1.06l5-5a.75.75 0 1 1 1.06 1.06L8.31 10l4.47 4.47a.75.75 0 0 1 0 1.06Z"
                clipRule="evenodd"
            />
        </svg>
    );
}

function IconChevronRight(props) {
    return (
        <svg viewBox="0 0 20 20" fill="currentColor" {...props}>
            <path
                fillRule="evenodd"
                d="M7.22 4.47a.75.75 0 0 1 1.06 0l5 5a.75.75 0 0 1 0 1.06l-5 5a.75.75 0 1 1-1.06-1.06L11.69 10 7.22 5.53a.75.75 0 0 1 0-1.06Z"
                clipRule="evenodd"
            />
        </svg>
    );
}

function OPDQueueSlider({
    serviceName,
    nowServing,
    waiting,
    countersOpen,
    onViewAll,
}: {
    serviceName: string;
    nowServing: {
        token: string | number;
        token_short?: string | null;
        name: string;
        mrn?: string;
    }[];
    waiting: {
        token: string | number;
        token_short?: string | null;
        name: string;
        mrn?: string;
    }[];
    countersOpen: number;
    onViewAll?: () => void;
}) {
    const scrollerRef = useRef(null);
    const nowCardRef = useRef(null);

    const items = useMemo(() => {
        const list = [];
        if (nowServing) list.push({ ...nowServing, _variant: 'now' });
        for (const w of waiting || []) list.push({ ...w, _variant: 'waiting' });
        return list;
    }, [nowServing, waiting]);

    const [canLeft, setCanLeft] = useState(false);
    const [canRight, setCanRight] = useState(true);

    const updateEdges = () => {
        const el = scrollerRef.current;
        if (!el) return;
        const max = el.scrollWidth - el.clientWidth;
        setCanLeft(el.scrollLeft > 4);
        setCanRight(el.scrollLeft < max - 4);
    };

    const scrollByCards = (dir = 1) => {
        const el = scrollerRef.current;
        if (!el) return;
        // Scroll roughly 2 cards
        const amount = Math.round(el.clientWidth * 0.7) * dir;
        el.scrollBy({ left: amount, behavior: 'smooth' });
    };

    useEffect(() => {
        const el = scrollerRef.current;
        if (!el) return;

        updateEdges();
        const onScroll = () => updateEdges();
        el.addEventListener('scroll', onScroll, { passive: true });

        const ro = new ResizeObserver(() => updateEdges());
        ro.observe(el);

        return () => {
            el.removeEventListener('scroll', onScroll);
            ro.disconnect();
        };
    }, []);

    useEffect(() => {
        // Keep "Now Serving" visible on mount / when token changes
        if (!nowCardRef.current) return;
        nowCardRef.current.scrollIntoView({
            behavior: 'smooth',
            inline: 'start',
            block: 'nearest',
        });
    }, [nowServing?.token]);

    const [minify, setMinify] = useState(true);

    return (
        <div className="w-full">
            <div className="rounded-2xl border border-slate-200 bg-white shadow-sm">
                {/* Title row (no global header) */}
                <div className="flex items-center justify-between gap-4 p-4 md:p-5">
                    <div className="min-w-0">
                        <div className="flex items-center gap-3">
                            <div className="flex h-10 w-10 items-center justify-center rounded-xl bg-slate-100">
                                <span className="text-sm font-bold text-slate-700">
                                    OPD
                                </span>
                            </div>
                            <div className="min-w-0">
                                <h2 className="truncate text-base font-semibold text-slate-900 md:text-lg">
                                    {serviceName}
                                </h2>
                                <p className="text-xs text-slate-600 md:text-sm">
                                    {items.length} in queue • {countersOpen}{' '}
                                    counter(s) open
                                </p>
                            </div>
                        </div>
                    </div>

                    <div className="flex items-center gap-2">
                        <button
                            type="button"
                            onClick={() => (onViewAll ? onViewAll() : null)}
                            className="hidden items-center rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50 sm:inline-flex"
                        >
                            View All
                        </button>

                        <div className="flex items-center gap-2">
                            <button
                                type="button"
                                disabled={!canLeft}
                                onClick={() => scrollByCards(-1)}
                                className={cn(
                                    'inline-flex h-10 w-10 items-center justify-center rounded-xl border',
                                    canLeft
                                        ? 'border-slate-200 bg-white text-slate-700 hover:bg-slate-50'
                                        : 'cursor-not-allowed border-slate-100 bg-slate-50 text-slate-300',
                                )}
                                aria-label="Scroll left"
                            >
                                <IconChevronLeft className="h-5 w-5" />
                            </button>

                            <button
                                type="button"
                                disabled={!canRight}
                                onClick={() => scrollByCards(1)}
                                className={cn(
                                    'inline-flex h-10 w-10 items-center justify-center rounded-xl border',
                                    canRight
                                        ? 'border-slate-200 bg-white text-slate-700 hover:bg-slate-50'
                                        : 'cursor-not-allowed border-slate-100 bg-slate-50 text-slate-300',
                                )}
                                aria-label="Scroll right"
                            >
                                <IconChevronRight className="h-5 w-5" />
                            </button>
                        </div>
                    </div>
                </div>

                <div className="h-px bg-slate-100" />

                {/* Slider */}
                <div className="relative p-4 md:p-5">
                    <div
                        ref={scrollerRef}
                        className={cn(
                            'flex gap-4 overflow-x-auto scroll-smooth',
                            'pb-2',
                            '[scrollbar-color:#cbd5e1_transparent] [scrollbar-width:thin]',
                        )}
                    >
                        {items.map((item, idx) => (
                            <div
                                key={`${item.token}-${idx}`}
                                ref={
                                    item._variant === 'now' ? nowCardRef : null
                                }
                            >
                                <TokenCard
                                    item={item}
                                    variant={item.status}
                                    minify={minify}
                                />
                            </div>
                        ))}

                        {/* End spacer */}
                        <div className="min-w-[1px]" />
                    </div>

                    {/* small hint */}
                    <div className="mt-3 flex items-center justify-between text-xs text-slate-500">
                        <span>Tip: swipe/scroll horizontally to see more</span>
                        <span className="tabular-nums">
                            Now:{' '}
                            {nowServing
                                ? `#${nowServing.token_short ?? nowServing.token}`
                                : '—'}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    );
}
