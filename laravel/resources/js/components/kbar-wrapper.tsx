import React, { useMemo } from "react";
import {
  KBarProvider,
  KBarPortal,
  KBarPositioner,
  KBarAnimator,
  KBarSearch,
  KBarResults,
  useMatches,
  useKBar,
} from "kbar";
import { motion } from "framer-motion";
import {
  Home,
  FileText,
  Settings,
  HelpCircle,
  Plus,
  Search,
  LayoutGrid,
  Building2,
} from "lucide-react";
import { router } from "@inertiajs/react";

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

export default function CommandPaletteLayout({
  children,
  navigate,
  routes = {},
}: {
  children: React.ReactNode;
  navigate?: (url: string) => void;
  routes?: Record<string, string>;
}) {
const go = (url: string) => {
    console.log('Navigating to:', url);
    return navigate ? navigate(url) : router.visit(url);
};

  const actions = useMemo(
    () => [
      {
        id: "dashboard",
        name: "Go to Dashboard",
        shortcut: ["g", "d"],
        keywords: "home main overview",
        section: "Pages",
        icon: <Home className="w-4 h-4" />,
        perform: () => go('dashboard'),
      },
      {
        id: "services",
        name: "Services",
        shortcut: ["g", "s"],
        keywords: "products offerings",
        section: "Pages",
        icon: <LayoutGrid className="w-4 h-4" />,
        perform: () => go('services'),
      },
      {
        id: "departments",
        name: "Departments",
        shortcut: ["g", "p"],
        keywords: "service departments",
        section: "Pages",
        icon: <Building2 className="w-4 h-4" />,
        perform: () => routes.departments && go(routes.departments),
      },
      {
        id: "createService",
        name: "Create New Service",
        shortcut: ["c", "s"],
        keywords: "new add service",
        section: "Actions",
        icon: <Plus className="w-4 h-4" />,
        perform: () => routes.createService && go(routes.createService),
      },
      {
        id: "docs",
        name: "Documentation",
        shortcut: ["?"],
        keywords: "help guide manual",
        section: "Help",
        icon: <FileText className="w-4 h-4" />,
        perform: () => routes.help && go(routes.help),
      },
      {
        id: "settings",
        name: "Settings",
        shortcut: ["g", "t"],
        keywords: "preferences account",
        section: "Pages",
        icon: <Settings className="w-4 h-4" />,
        perform: () => routes.settings && go(routes.settings),
      },
    ], [routes]);

  return (
    <KBarProvider actions={actions} options={{ animations: {
      enterMs: 160,
      exitMs: 120,
    }}}>
      <div className="min-h-dvh bg-blue-800 text-white antialiased dark:bg-neutral-950 dark:text-neutral-100">
        {children}
        <KBarUI />
        <CommandHintButton />
      </div>
    </KBarProvider>
  );
}

function KBarUI() {
  return (
    <KBarPortal>
      <KBarPositioner className="backdrop-blur-md bg-black/20 fixed inset-0 z-[100] flex items-start justify-center pt-24 p-4">
        <KBarAnimator>
          <motion.div
            initial={{ opacity: 0, y: 8, scale: 0.98 }}
            animate={{ opacity: 1, y: 0, scale: 1 }}
            exit={{ opacity: 0, y: 8, scale: 0.98 }}
            transition={{ type: "spring", stiffness: 320, damping: 26 }}
            className="w-full max-w-2xl overflow-hidden rounded-2xl border border-white/10 shadow-2xl ring-1 ring-black/10 dark:ring-white/10 bg-white/70 dark:bg-neutral-900/70 backdrop-blur-xl"
          >
            <div className="relative">
              <div className="pointer-events-none absolute inset-px rounded-[calc(theme(borderRadius.2xl)-2px)] bg-gradient-to-b from-white/70 to-white/5 dark:from-white/10 dark:to-white/5" />
              <div className="relative p-3">
                <div className="flex items-center gap-2 rounded-xl border border-neutral-200/60 dark:border-neutral-700/60 bg-white/80 dark:bg-neutral-800/60 px-3 py-2 shadow-sm">
                  <Search className="h-4 w-4 opacity-60" />
                  <KBarSearch
                    className="w-full bg-transparent text-sm outline-none placeholder:text-neutral-400"
                    placeholder="Search commands… (⌘K / Ctrl K)"
                    autoFocus
                  />
                  <kbd className="hidden sm:flex items-center gap-0.5 rounded-md bg-neutral-100 dark:bg-neutral-700 px-2 py-1 text-[10px] tracking-wider text-neutral-500 dark:text-neutral-200">⌘K</kbd>
                </div>
              </div>
            </div>

            <CommandResults />
          </motion.div>
        </KBarAnimator>
      </KBarPositioner>
    </KBarPortal>
  );
}

function CommandResults() {
  const { results } = useMatches();

  return (
    <div className="max-h-[60vh] overflow-y-auto p-2">
      <KBarResults
        items={results}
        onRender={({ item, active }) => {
          if (typeof item === "string") {
            return (
              <div className="px-3 py-3 text-xs uppercase tracking-wider text-neutral-500">
                {item}
              </div>
            );
          }

          return <ResultItem action={item} active={active} />;
        }}
      />
    </div>
  );
}

function ResultItem({ action, active }: { action: any; active: boolean }) {
  return (
    <motion.div
      layout
      className={[
        "mx-1 my-1 flex items-center justify-between rounded-xl border px-3 py-2",
        active
          ? "border-transparent bg-gradient-to-r from-indigo-500/10 via-fuchsia-500/10 to-cyan-500/10 ring-1 ring-indigo-500/30 dark:ring-indigo-400/30"
          : "border-transparent hover:border-neutral-200/70 dark:hover:border-neutral-700/70",
      ].join(" ")}
      initial={false}
      animate={{ scale: active ? 1.01 : 1, opacity: 1 }}
      transition={{ duration: 0.08 }}
    >
      <div className="flex items-center gap-3">
        <div className="grid h-7 w-7 place-content-center rounded-lg bg-neutral-100 dark:bg-neutral-800">
          {action.icon ?? <HelpCircle className="h-4 w-4" />}
        </div>
        <div className="flex flex-col">
          <div className="text-sm font-medium leading-none">{action.name}</div>
          {action.subtitle && (
            <div className="text-[11px] text-neutral-500 dark:text-neutral-400">{action.subtitle}</div>
          )}
        </div>
      </div>

      {action.shortcut?.length ? (
        <div className="hidden sm:flex items-center gap-1">
          {action.shortcut.map((sc: string, i: number) => (
            <kbd
              key={i}
              className="rounded-md bg-neutral-100 dark:bg-neutral-800 px-1.5 py-1 text-[10px] tracking-wider text-neutral-600 dark:text-neutral-300 shadow"
            >
              {sc}
            </kbd>
          ))}
        </div>
      ) : null}
    </motion.div>
  );
}

function CommandHintButton() {
  const { query } = useKBar();
  return (
    <button
      onClick={() => query.toggle()}
      className="fixed bottom-5 right-5 z-[99] inline-flex items-center gap-2 rounded-full border border-white/20 bg-white/80 px-4 py-2 shadow-lg backdrop-blur-md transition hover:shadow-xl dark:bg-neutral-900/70 dark:text-neutral-100"
      aria-label="Open command palette"
    >
      <kbd className="rounded-md bg-neutral-100 dark:bg-neutral-800 px-1.5 py-0.5 text-[10px]">⌘K</kbd>
      <span className="text-sm">Command</span>
    </button>
  );
}
