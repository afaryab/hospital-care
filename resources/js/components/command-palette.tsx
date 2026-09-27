import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogTitle,
} from '@/components/ui/dialog';
import { useAppearance } from '@/hooks/use-appearance';
import { abilitiesFor, type Abilities } from '@/lib/abilities';
import {
    appointmentsCalendar,
    counter,
    counterClose,
    counterExpense,
    counterExpenseVouchersList,
    counterListAll,
    counterOpen,
    counterSelectPatient,
    dntDashboard,
    emgDashboard,
    home,
    hospitalDentalQueue,
    hospitalEmergencyQueue,
    hospitalIndoorQueue,
    hospitalLaboratoryQueue,
    hospitalOpdQueue,
    hospitalRadiologyQueue,
    hospitalUltrasoundQueue,
    indDashboard,
    labDashboard,
    logout,
    myCounterList,
    myPatients,
    myPayments,
    opdDashboard,
    patientsRegister,
    receaveables,
    serviceOrdersOverview,
    transactionSearch,
    ultDashboard,
    xrayDashboard,
} from '@/routes';
import { edit as editAppearance } from '@/routes/appearance';
import dms from '@/routes/dms';
import { edit as editProfile } from '@/routes/profile';
import { edit as editPassword } from '@/routes/user-password';
import { router } from '@inertiajs/react';
import { clsx } from 'clsx';
import {
    ArrowRight,
    Banknote,
    BookUser,
    CalendarDays,
    ClipboardList,
    FileText,
    FolderOpen,
    LayoutDashboard,
    Loader2,
    LogOut,
    MonitorPlay,
    Moon,
    Receipt,
    Search,
    Settings,
    Stethoscope,
    Sun,
    User,
    Wallet,
    type LucideIcon,
} from 'lucide-react';
import React, {
    useCallback,
    useEffect,
    useMemo,
    useRef,
    useState,
} from 'react';

type PaletteItem = {
    id: string;
    group: string;
    title: string;
    subtitle?: string | null;
    keywords?: string;
    icon: LucideIcon;
    run: () => void;
};

type StaticCommand = {
    group: string;
    title: string;
    keywords?: string;
    icon: LucideIcon;
    href?: string;
    external?: boolean;
    action?: () => void;
    visible: (can: Abilities) => boolean;
};

type SearchResult = {
    group: string;
    title: string;
    subtitle: string | null;
    url: string;
};

const RESULT_ICONS: Record<string, LucideIcon> = {
    Patients: BookUser,
    'Service orders': ClipboardList,
    Transactions: Receipt,
    Closings: Wallet,
    'Expense vouchers': Banknote,
    Appointments: CalendarDays,
};

const visit = (href: string, external = false) =>
    external ? window.location.assign(href) : router.visit(href);

function staticCommands(
    toggleTheme: () => void,
    isDark: boolean,
): StaticCommand[] {
    const queue = (
        title: string,
        href: string,
        lcd: string,
    ): StaticCommand => ({
        group: 'Queues',
        title: `${title} queue`,
        keywords: 'que display lcd token',
        icon: MonitorPlay,
        href,
        visible: (can) => can.staff || can.lcd(lcd),
    });

    return [
        {
            group: 'Go to',
            title: 'Home',
            keywords: 'dashboard start',
            icon: LayoutDashboard,
            href: home().url,
            visible: (can) => can.staff,
        },
        {
            group: 'Go to',
            title: 'Patient register',
            keywords: 'patients ps list',
            icon: BookUser,
            href: patientsRegister().url,
            visible: (can) =>
                can.receptionist ||
                can.accountant ||
                can.nursing ||
                can.anyDoctor,
        },
        {
            group: 'Go to',
            title: 'Service orders',
            keywords: 'so orders overview',
            icon: ClipboardList,
            href: serviceOrdersOverview().url,
            visible: (can) => can.receptionist,
        },
        {
            group: 'Go to',
            title: 'Receivables',
            keywords: 'receaveables outstanding credit',
            icon: Wallet,
            href: receaveables().url,
            visible: (can) => can.receptionist,
        },
        {
            group: 'Go to',
            title: 'Appointments',
            keywords: 'calendar booking apt',
            icon: CalendarDays,
            href: appointmentsCalendar().url,
            visible: (can) => can.receptionist,
        },
        {
            group: 'Go to',
            title: 'Expense vouchers',
            keywords: 'vc vouchers',
            icon: Banknote,
            href: counterExpenseVouchersList().url,
            visible: (can) => can.receptionist,
        },
        {
            group: 'Go to',
            title: 'My closings',
            keywords: 'ct counters history',
            icon: Wallet,
            href: myCounterList().url,
            visible: (can) => can.receptionist,
        },
        {
            group: 'Go to',
            title: 'All closings',
            keywords: 'ct counters accounts',
            icon: Wallet,
            href: counterListAll().url,
            visible: (can) => can.accountant || can.admin,
        },
        {
            group: 'Go to',
            title: 'My patients',
            keywords: 'doctor treated',
            icon: Stethoscope,
            href: myPatients().url,
            visible: (can) => can.anyDoctor,
        },
        {
            group: 'Go to',
            title: 'My payments',
            keywords: 'salary share earnings',
            icon: Banknote,
            href: myPayments().url,
            visible: (can) => can.authenticated,
        },
        {
            group: 'Go to',
            title: 'Documents',
            keywords: 'dms files folders',
            icon: FolderOpen,
            href: dms.index().url,
            visible: (can) => can.admin,
        },
        {
            group: 'Go to',
            title: 'Administration panel',
            keywords: 'admin filament settings',
            icon: Settings,
            href: '/admin',
            external: true,
            visible: (can) => can.admin,
        },
        {
            group: 'Go to',
            title: 'Accounts panel',
            keywords: 'accounting ledger finance',
            icon: FileText,
            href: '/accounts',
            external: true,
            visible: (can) => can.accountant,
        },

        {
            group: 'Counter',
            title: 'Counter',
            keywords: 'ct current open',
            icon: Wallet,
            href: counter().url,
            visible: (can) => can.receptionist,
        },
        {
            group: 'Counter',
            title: 'Open counter',
            keywords: 'ct new start shift',
            icon: Wallet,
            href: counterOpen().url,
            visible: (can) => can.receptionist,
        },
        {
            group: 'Counter',
            title: 'Close counter',
            keywords: 'ct end shift',
            icon: Wallet,
            href: counterClose().url,
            visible: (can) => can.receptionist,
        },
        {
            group: 'Counter',
            title: 'New patient / receive payment',
            keywords: 'register patient income billing ps',
            icon: BookUser,
            href: counterSelectPatient().url,
            visible: (can) => can.receptionist,
        },
        {
            group: 'Counter',
            title: 'Counter expense',
            keywords: 'expense voucher pay out',
            icon: Banknote,
            href: counterExpense().url,
            visible: (can) => can.receptionist,
        },
        {
            group: 'Counter',
            title: 'Find transaction',
            keywords: 'tr receipt search',
            icon: Receipt,
            href: transactionSearch().url,
            visible: (can) => can.receptionist || can.accountant,
        },

        {
            group: 'Departments',
            title: 'OPD dashboard',
            keywords: 'outpatient doctor',
            icon: Stethoscope,
            href: opdDashboard().url,
            visible: (can) => can.opdDoctor || can.nursing,
        },
        {
            group: 'Departments',
            title: 'Indoor dashboard',
            keywords: 'ind inpatient ward',
            icon: Stethoscope,
            href: indDashboard().url,
            visible: (can) => can.indDoctor || can.nursing,
        },
        {
            group: 'Departments',
            title: 'Emergency dashboard',
            keywords: 'emg er triage',
            icon: Stethoscope,
            href: emgDashboard().url,
            visible: (can) => can.emergencyDoctor || can.nursing,
        },
        {
            group: 'Departments',
            title: 'Dental dashboard',
            keywords: 'dnt dentist',
            icon: Stethoscope,
            href: dntDashboard().url,
            visible: (can) => can.dentist || can.nursing,
        },
        {
            group: 'Departments',
            title: 'Laboratory dashboard',
            keywords: 'lab pth tests',
            icon: Stethoscope,
            href: labDashboard().url,
            visible: (can) => can.nursing || can.receptionist || can.admin,
        },
        {
            group: 'Departments',
            title: 'Ultrasound dashboard',
            keywords: 'ult imaging',
            icon: Stethoscope,
            href: ultDashboard().url,
            visible: (can) => can.ultrasoundDoctor || can.nursing,
        },
        {
            group: 'Departments',
            title: 'X-Ray dashboard',
            keywords: 'xray radiology rad',
            icon: Stethoscope,
            href: xrayDashboard().url,
            visible: (can) => can.xrayTechnician || can.nursing,
        },

        queue('OPD', hospitalOpdQueue().url, 'opd'),
        queue('Indoor', hospitalIndoorQueue().url, 'ind'),
        queue('Emergency', hospitalEmergencyQueue().url, 'emergency'),
        queue('Dental', hospitalDentalQueue().url, 'dental'),
        queue('Laboratory', hospitalLaboratoryQueue().url, 'laboratory'),
        queue('Ultrasound', hospitalUltrasoundQueue().url, 'ultrasound'),
        queue('Radiology', hospitalRadiologyQueue().url, 'xray'),

        {
            group: 'Settings',
            title: 'Profile settings',
            keywords: 'account name email',
            icon: User,
            href: editProfile().url,
            visible: (can) => can.authenticated,
        },
        {
            group: 'Settings',
            title: 'Change password',
            keywords: 'security',
            icon: Settings,
            href: editPassword().url,
            visible: (can) => can.authenticated,
        },
        {
            group: 'Settings',
            title: 'Appearance',
            keywords: 'theme dark light',
            icon: Settings,
            href: editAppearance().url,
            visible: (can) => can.authenticated,
        },
        {
            group: 'Settings',
            title: isDark ? 'Switch to light mode' : 'Switch to dark mode',
            keywords: 'theme toggle appearance',
            icon: isDark ? Sun : Moon,
            action: toggleTheme,
            visible: (can) => can.authenticated,
        },
        {
            group: 'Settings',
            title: 'Log out',
            keywords: 'sign out exit',
            icon: LogOut,
            action: () => router.post(logout().url),
            visible: (can) => can.authenticated,
        },
    ];
}

/**
 * Scores how well `query` matches `text`: contiguous substring matches rank
 * highest, then in-order subsequence (fuzzy) matches. Returns 0 on no match.
 */
function matchScore(query: string, text: string): number {
    const q = query.toLowerCase().trim();
    const t = text.toLowerCase();
    if (!q) {
        return 1;
    }
    const index = t.indexOf(q);
    if (index === 0) {
        return 100;
    }
    if (index > 0) {
        return 80 - Math.min(index, 40);
    }
    let position = 0;
    for (const char of q) {
        if (char === ' ') {
            continue;
        }
        position = t.indexOf(char, position);
        if (position === -1) {
            return 0;
        }
        position++;
    }
    return 20;
}

export default function CommandPaletteLayout({
    children,
    initialPage,
}: {
    children: React.ReactNode;
    initialPage?: { props: Record<string, unknown> };
}) {
    // The palette wraps Inertia's <App> at the React root (see app.tsx), so it
    // can't use usePage(); the navigate event carries each new page's props.
    const [pageProps, setPageProps] = useState(initialPage?.props);
    useEffect(
        () =>
            router.on('navigate', (event) => {
                setPageProps(event.detail.page.props);
                setOpen(false);
            }),
        [],
    );

    const user = (pageProps as { auth?: { user?: unknown } } | undefined)?.auth
        ?.user as Parameters<typeof abilitiesFor>[0];
    const can = useMemo(() => abilitiesFor(user), [user]);

    const { appearance, updateAppearance } = useAppearance();
    const isDark =
        appearance === 'dark' ||
        (appearance === 'system' &&
            typeof window !== 'undefined' &&
            window.matchMedia?.('(prefers-color-scheme: dark)').matches);

    const [open, setOpen] = useState(false);
    const [query, setQuery] = useState('');
    const [results, setResults] = useState<SearchResult[]>([]);
    const [loading, setLoading] = useState(false);
    const [activeIndex, setActiveIndex] = useState(0);
    const listRef = useRef<HTMLDivElement | null>(null);

    const handleOpenChange = useCallback((next: boolean) => {
        setOpen(next);
        if (!next) {
            setQuery('');
            setResults([]);
            setActiveIndex(0);
        }
    }, []);

    useEffect(() => {
        const onKeyDown = (e: KeyboardEvent) => {
            if ((e.metaKey || e.ctrlKey) && e.key.toLowerCase() === 'k') {
                e.preventDefault();
                setOpen((isOpen) => {
                    if (isOpen) {
                        setQuery('');
                        setResults([]);
                    }
                    return !isOpen;
                });
            }
        };
        window.addEventListener('keydown', onKeyDown);
        return () => window.removeEventListener('keydown', onKeyDown);
    }, []);

    useEffect(() => {
        const q = query.trim();
        if (!open || q.length < 2 || !can.authenticated) {
            return;
        }

        const controller = new AbortController();
        const timer = window.setTimeout(async () => {
            setLoading(true);
            try {
                const response = await fetch(
                    `/api/lookup?q=${encodeURIComponent(q)}`,
                    {
                        headers: { Accept: 'application/json' },
                        credentials: 'same-origin',
                        signal: controller.signal,
                    },
                );
                if (response.ok) {
                    const json = await response.json();
                    setResults(Array.isArray(json?.data) ? json.data : []);
                    setActiveIndex(0);
                }
            } catch (error) {
                if ((error as Error).name !== 'AbortError') {
                    setResults([]);
                }
            } finally {
                if (!controller.signal.aborted) {
                    setLoading(false);
                }
            }
        }, 200);

        return () => {
            controller.abort();
            window.clearTimeout(timer);
        };
    }, [query, open, can.authenticated]);

    const items = useMemo<PaletteItem[]>(() => {
        const trimmed = query.trim();
        const toggleTheme = () => updateAppearance(isDark ? 'light' : 'dark');

        const commands = staticCommands(toggleTheme, isDark)
            .filter((command) => command.visible(can))
            .map((command) => ({
                command,
                score: Math.max(
                    matchScore(trimmed, command.title),
                    matchScore(trimmed, command.keywords ?? '') * 0.6,
                    matchScore(trimmed, command.group) * 0.4,
                ),
            }))
            .filter(({ score }) => score > 0)
            .sort((a, b) => (trimmed ? b.score - a.score : 0))
            .slice(0, trimmed ? 8 : undefined)
            .map(({ command }, index) => ({
                id: `cmd-${command.group}-${command.title}-${index}`,
                group: trimmed ? 'Pages & actions' : command.group,
                title: command.title,
                icon: command.icon,
                keywords: command.keywords,
                run: () =>
                    command.action
                        ? command.action()
                        : visit(command.href!, command.external),
            }));

        const records =
            trimmed.length >= 2
                ? results.map((result, index) => ({
                      id: `rec-${index}-${result.url}`,
                      group: result.group,
                      title: result.title,
                      subtitle: result.subtitle,
                      icon: RESULT_ICONS[result.group] ?? ArrowRight,
                      run: () => visit(result.url),
                  }))
                : [];

        return [...records, ...commands];
    }, [query, results, can, isDark, updateAppearance]);

    const groups = useMemo(() => {
        const map = new Map<string, PaletteItem[]>();
        items.forEach((item) => {
            map.set(item.group, [...(map.get(item.group) ?? []), item]);
        });
        return Array.from(map.entries());
    }, [items]);

    const runItem = (item: PaletteItem | undefined) => {
        if (!item) {
            return;
        }
        handleOpenChange(false);
        item.run();
    };

    const onInputKeyDown = (e: React.KeyboardEvent<HTMLInputElement>) => {
        if (e.key === 'ArrowDown') {
            e.preventDefault();
            setActiveIndex((i) => Math.min(i + 1, items.length - 1));
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            setActiveIndex((i) => Math.max(i - 1, 0));
        } else if (e.key === 'Enter') {
            e.preventDefault();
            runItem(items[activeIndex]);
        }
    };

    useEffect(() => {
        listRef.current
            ?.querySelector<HTMLElement>(`[data-index="${activeIndex}"]`)
            ?.scrollIntoView({ block: 'nearest' });
    }, [activeIndex]);

    let flatIndex = -1;

    return (
        <div className="min-h-dvh bg-blue-800 text-gray-800 antialiased dark:bg-neutral-950 dark:text-neutral-100">
            {children}

            {can.authenticated && (
                <button
                    type="button"
                    onClick={() => handleOpenChange(true)}
                    className="fixed right-48 bottom-5 z-[49] inline-flex items-center gap-2 rounded-full border border-white/20 bg-white/80 px-4 py-2 shadow-lg backdrop-blur-md transition hover:shadow-xl dark:bg-neutral-900/70 dark:text-neutral-100"
                    aria-label="Open command palette"
                >
                    <kbd className="rounded-md bg-neutral-100 px-1.5 py-0.5 text-[10px] dark:bg-neutral-800">
                        Ctrl K
                    </kbd>
                    <span className="text-sm">Command</span>
                </button>
            )}

            <Dialog
                open={open && can.authenticated}
                onOpenChange={handleOpenChange}
            >
                <DialogContent className="top-24 max-w-2xl translate-y-0 gap-0 overflow-hidden p-0 sm:top-[15vh] [&>button]:hidden">
                    <DialogTitle className="sr-only">
                        Command palette
                    </DialogTitle>
                    <DialogDescription className="sr-only">
                        Search patients, records and pages, or run an action.
                    </DialogDescription>
                    <div className="flex items-center gap-3 border-b border-neutral-200 px-4 dark:border-neutral-800">
                        <Search className="h-5 w-5 shrink-0 text-neutral-400" />
                        <input
                            autoFocus
                            value={query}
                            onChange={(e) => {
                                setQuery(e.target.value);
                                setActiveIndex(0);
                                if (e.target.value.trim().length < 2) {
                                    setResults([]);
                                }
                            }}
                            onKeyDown={onInputKeyDown}
                            placeholder="Search name, PS/TR/CT/VC/APT number, CNIC, phone, or a page…"
                            className="h-14 w-full bg-transparent text-base outline-none placeholder:text-neutral-400"
                            aria-label="Search"
                            role="combobox"
                            aria-expanded="true"
                            aria-controls="command-palette-list"
                        />
                        {loading && (
                            <Loader2 className="h-4 w-4 shrink-0 animate-spin text-neutral-400" />
                        )}
                        <kbd className="hidden rounded border border-neutral-200 px-1.5 py-0.5 text-[10px] text-neutral-500 sm:block dark:border-neutral-700">
                            Esc
                        </kbd>
                    </div>

                    <div
                        ref={listRef}
                        id="command-palette-list"
                        role="listbox"
                        className="max-h-[60vh] overflow-y-auto p-2"
                    >
                        {items.length === 0 && (
                            <div className="px-3 py-10 text-center text-sm text-neutral-500">
                                {loading ? 'Searching…' : 'No results found.'}
                            </div>
                        )}
                        {groups.map(([group, groupItems]) => (
                            <div key={group} className="mb-2">
                                <div className="px-3 py-1.5 text-xs font-medium tracking-wide text-neutral-500 uppercase">
                                    {group}
                                </div>
                                {groupItems.map((item) => {
                                    flatIndex++;
                                    const index = flatIndex;
                                    const Icon = item.icon;
                                    return (
                                        <div
                                            key={item.id}
                                            role="option"
                                            aria-selected={
                                                index === activeIndex
                                            }
                                            data-index={index}
                                            onMouseMove={() =>
                                                setActiveIndex(index)
                                            }
                                            onClick={() => runItem(item)}
                                            className={clsx(
                                                'flex cursor-pointer items-center gap-3 rounded-md px-3 py-2 text-sm',
                                                index === activeIndex
                                                    ? 'bg-blue-600 text-white'
                                                    : 'text-neutral-800 dark:text-neutral-200',
                                            )}
                                        >
                                            <Icon className="h-4 w-4 shrink-0 opacity-80" />
                                            <div className="min-w-0 flex-1">
                                                <div className="truncate">
                                                    {item.title}
                                                </div>
                                                {item.subtitle && (
                                                    <div
                                                        className={clsx(
                                                            'truncate text-xs',
                                                            index ===
                                                                activeIndex
                                                                ? 'text-blue-100'
                                                                : 'text-neutral-500',
                                                        )}
                                                    >
                                                        {item.subtitle}
                                                    </div>
                                                )}
                                            </div>
                                            {index === activeIndex && (
                                                <ArrowRight className="h-4 w-4 shrink-0" />
                                            )}
                                        </div>
                                    );
                                })}
                            </div>
                        ))}
                    </div>

                    <div className="flex items-center justify-between border-t border-neutral-200 px-4 py-2 text-xs text-neutral-500 dark:border-neutral-800">
                        <span>↑↓ to move · Enter to open · Esc to close</span>
                        <span>Ctrl/⌘ K toggles</span>
                    </div>
                </DialogContent>
            </Dialog>
        </div>
    );
}
