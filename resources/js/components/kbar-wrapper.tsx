import { router } from '@inertiajs/react';
import { motion } from 'framer-motion';
import { HelpCircle, Search } from 'lucide-react';
import React, { useEffect, useRef, useState } from 'react';

/**
 * Tailwind + KBar command palette that drops into any React or Inertia layout.
 * - Beautiful glassmorphism panel with subtle motion.
 * - Works with Inertia (pass `navigate` from @inertiajs/react router) or plain window.location fallback.
 * - Cmd/Ctrl+K to open. Also includes a floating trigger button.
 *
 * Usage (Inertia + React example):
 *
 *   import { router } from '@inertiajs/react'
 *   <CommandPaletteLayout navigate={(url) => router.visit(url)} routes={{
 *     dashboard: route('dashboard'),
 *     services: route('services.index'),
 *     departments: route('departments.index'),
 *     settings: route('settings'),
 *     createService: route('services.create'),
 *     help: '/help'
 *   }}>
 *     <YourApp />
 *   </CommandPaletteLayout>
 */

// Path prefixes for the unauthenticated auth flow (login, register, password
// reset, email verification, 2FA challenge) — the command palette's floating
// trigger is hidden there since its search requires an authenticated session
// and has nothing useful to offer before one exists.
const AUTH_PATH_PREFIXES = [
    '/login',
    '/register',
    '/forgot-password',
    '/reset-password',
    '/two-factor-challenge',
    '/email/verify',
    '/user/confirm-password',
];

export default function CommandPaletteLayout({
    children,
    navigate,
}: {
    children: React.ReactNode;
    navigate?: (url: string) => void;
}) {
    const [open, setOpen] = useState(false);
    const [queryText, setQueryText] = useState('');
    const [items, setItems] = useState<
        Array<{ name: string; url?: string; type?: string }>
    >([]);
    const [loading, setLoading] = useState(false);
    const [activeIndex, setActiveIndex] = useState<number>(-1);
    const inputRef = useRef<HTMLInputElement | null>(null);

    // CommandPaletteLayout wraps Inertia's <App> at the React root (see
    // app.tsx), so it sits outside Inertia's page context and can't use
    // usePage() — router.on('navigate') is Inertia's context-free event feed
    // for exactly this kind of "track the current URL from outside" need.
    const [pathname, setPathname] = useState(() => window.location.pathname);
    useEffect(() => {
        return router.on('navigate', (event) => {
            // page.url is typically a relative path (e.g. "/login") — an
            // explicit base makes this safe whether it's relative or absolute.
            setPathname(
                new URL(event.detail.page.url, window.location.origin).pathname,
            );
        });
    }, []);
    const isAuthScreen = AUTH_PATH_PREFIXES.some((prefix) =>
        pathname.startsWith(prefix),
    );

    const go = (url: string) => {
        return navigate ? navigate(url) : router.visit(url);
    };

    // Global keyboard shortcut: Cmd/Ctrl + K
    useEffect(() => {
        const onKeyDown = (e: KeyboardEvent) => {
            const isTrigger =
                (e.metaKey || e.ctrlKey) && e.key.toLowerCase() === 'k';
            if (isTrigger) {
                e.preventDefault();
                setOpen(true);
                setTimeout(() => inputRef.current?.focus(), 0);
            }
            if (open && e.key === 'Escape') {
                setOpen(false);
            }
        };
        window.addEventListener('keydown', onKeyDown);
        return () => window.removeEventListener('keydown', onKeyDown);
    }, [open]);

    // Debounced API lookup based on queryText
    useEffect(() => {
        const controller = new AbortController();
        const q = queryText.trim();
        const timer = setTimeout(async () => {
            if (!q) {
                setItems([]);
                setLoading(false);
                return;
            }
            try {
                setLoading(true);
                const res = await fetch(
                    `/api/lookup?q=${encodeURIComponent(q)}`,
                    {
                        signal: controller.signal,
                        headers: { Accept: 'application/json' },
                    },
                );
                if (!res.ok) throw new Error(`Lookup failed: ${res.status}`);
                const data = await res.json();
                const mapped: Array<{
                    name: string;
                    url?: string;
                    type?: string;
                }> = Array.isArray(data.data)
                    ? data.data.map((item: any) => ({
                          name: item?.name ?? 'Untitled',
                          url: item?.url,
                          type: item?.type,
                      }))
                    : [];
                setItems(mapped);
            } catch (err) {
                console.error(err);
                setItems([]);
            } finally {
                setLoading(false);
            }
        }, 250);
        return () => {
            clearTimeout(timer);
            controller.abort();
        };
    }, [queryText]);

    // Keyboard navigation inside the list
    const onInputKeyDown = (e: React.KeyboardEvent<HTMLInputElement>) => {
        if (e.key === 'ArrowDown') {
            e.preventDefault();
            setActiveIndex((i) => Math.min(i + 1, items.length - 1));
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            setActiveIndex((i) => Math.max(i - 1, 0));
        } else if (e.key === 'Enter') {
            e.preventDefault();
            const item = items[activeIndex];
            if (item && item.type === 'link' && item.url) {
                setOpen(false);
                go(item.url);
            }
        }
    };

    return (
        <div className="min-h-dvh bg-blue-800 text-gray-800 antialiased dark:bg-neutral-950 dark:text-neutral-100">
            {children}

            {/* Floating trigger button — hidden pre-login, see isAuthScreen above */}
            {!isAuthScreen && (
                <button
                    onClick={() => {
                        setOpen(true);
                        setTimeout(() => inputRef.current?.focus(), 0);
                    }}
                    className="fixed right-5 bottom-5 z-[99] inline-flex items-center gap-2 rounded-full border border-white/20 bg-white/80 px-4 py-2 shadow-lg backdrop-blur-md transition hover:shadow-xl dark:bg-neutral-900/70 dark:text-neutral-100"
                    aria-label="Open command palette"
                >
                    <kbd className="rounded-md bg-neutral-100 px-1.5 py-0.5 text-[10px] dark:bg-neutral-800">
                        ⌘K
                    </kbd>
                    <span className="text-sm">Command</span>
                </button>
            )}

            {/* Overlay */}
            {open && (
                <div className="fixed inset-0 z-[100] flex items-start justify-center bg-black/20 p-4 pt-24 backdrop-blur-md">
                    <motion.div
                        initial={{ opacity: 0, y: 8, scale: 0.98 }}
                        animate={{ opacity: 1, y: 0, scale: 1 }}
                        exit={{ opacity: 0, y: 8, scale: 0.98 }}
                        transition={{
                            type: 'spring',
                            stiffness: 320,
                            damping: 26,
                        }}
                        className="w-full max-w-2xl overflow-hidden rounded-2xl border border-white/10 bg-white/70 shadow-2xl ring-1 ring-black/10 backdrop-blur-xl dark:bg-neutral-900/70 dark:ring-white/10"
                    >
                        <div className="relative">
                            <div className="pointer-events-none absolute inset-px rounded-[calc(theme(borderRadius.2xl)-2px)] bg-gradient-to-b from-white/70 to-white/5 dark:from-white/10 dark:to-white/5" />
                            <div className="relative p-3">
                                <div className="flex items-center gap-2 rounded-xl border border-neutral-200/60 bg-white/80 px-3 py-2 shadow-sm dark:border-neutral-700/60 dark:bg-neutral-800/60">
                                    <Search className="h-4 w-4 text-gray-800 opacity-60" />
                                    <input
                                        ref={inputRef}
                                        className="w-full bg-transparent text-sm text-gray-800 outline-none placeholder:text-neutral-400"
                                        placeholder="Search… (PS… / TR…)"
                                        value={queryText}
                                        onChange={(e) => {
                                            setQueryText(e.target.value);
                                            setActiveIndex(-1);
                                        }}
                                        onKeyDown={onInputKeyDown}
                                        autoFocus
                                    />
                                    <kbd className="hidden items-center gap-0.5 rounded-md bg-neutral-100 px-2 py-1 text-[10px] tracking-wider text-neutral-500 sm:flex dark:bg-neutral-700 dark:text-neutral-200">
                                        ⌘K
                                    </kbd>
                                </div>
                            </div>
                        </div>

                        <div className="max-h-[60vh] min-h-[20vh] overflow-y-auto p-2">
                            {loading && (
                                <div className="px-3 py-3 text-xs tracking-wider text-neutral-500 uppercase">
                                    Searching…
                                </div>
                            )}
                            {!loading &&
                                items.length === 0 &&
                                queryText.trim() && (
                                    <div className="px-3 py-3 text-xs tracking-wider text-neutral-500 uppercase">
                                        No results
                                    </div>
                                )}

                            {!loading && items.length > 0 && (
                                <div>
                                    <div className="px-3 py-2 text-xs tracking-wider text-neutral-500 uppercase">
                                        Lookup
                                    </div>
                                    {items.map((item, idx) => (
                                        <motion.div
                                            key={`${item.name}-${item.url ?? idx}`}
                                            layout
                                            onClick={() => {
                                                if (
                                                    item.type === 'link' &&
                                                    item.url
                                                ) {
                                                    setOpen(false);
                                                    go(item.url);
                                                }
                                            }}
                                            className={[
                                                'mx-1 my-1 flex cursor-pointer items-center justify-between rounded-xl border px-3 py-2 text-gray-800 dark:text-neutral-100',
                                                idx === activeIndex
                                                    ? 'border-transparent bg-gradient-to-r from-indigo-500/10 via-fuchsia-500/10 to-cyan-500/10 ring-1 ring-indigo-500/30 dark:ring-indigo-400/30'
                                                    : 'border-transparent hover:border-neutral-200/70 dark:hover:border-neutral-700/70',
                                            ].join(' ')}
                                            initial={false}
                                            animate={{
                                                scale:
                                                    idx === activeIndex
                                                        ? 1.01
                                                        : 1,
                                                opacity: 1,
                                            }}
                                            transition={{ duration: 0.08 }}
                                        >
                                            <div className="flex items-center gap-3">
                                                <div className="grid h-7 w-7 place-content-center rounded-lg bg-neutral-100 dark:bg-neutral-800">
                                                    <HelpCircle className="h-4 w-4" />
                                                </div>
                                                <div className="flex flex-col">
                                                    <div className="text-sm leading-none font-medium">
                                                        {item.name}
                                                    </div>
                                                    {item.type === 'static' && (
                                                        <div className="text-[11px] text-neutral-500 dark:text-neutral-400">
                                                            Info
                                                        </div>
                                                    )}
                                                </div>
                                            </div>
                                        </motion.div>
                                    ))}
                                </div>
                            )}
                        </div>
                    </motion.div>
                </div>
            )}
        </div>
    );
}
