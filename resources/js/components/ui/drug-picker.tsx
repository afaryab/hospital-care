import { apiDrugsSearch } from '@/routes';
import { clsx } from 'clsx';
import { useCallback, useEffect, useRef, useState } from 'react';

interface DrugResult {
    id: number;
    name: string;
    generic_name?: string;
    type?: string;
    strength?: string;
    default_dose?: string;
    default_frequency?: string;
    default_duration?: string;
    default_route?: string;
    category?: { id: number; name: string };
}

interface DrugPickerProps {
    value: string;
    onChange: (name: string) => void;
    onSelect: (drug: DrugResult) => void;
    disabled?: boolean;
    placeholder?: string;
    className?: string;
}

export default function DrugPicker({
    value,
    onChange,
    onSelect,
    disabled = false,
    placeholder = 'Drug name…',
    className,
}: DrugPickerProps) {
    const [results, setResults] = useState<DrugResult[]>([]);
    const [open, setOpen] = useState(false);
    const [loading, setLoading] = useState(false);
    const debounceRef = useRef<ReturnType<typeof setTimeout> | null>(null);
    const wrapperRef = useRef<HTMLDivElement>(null);

    const search = useCallback(async (q: string) => {
        if (!q.trim()) { setResults([]); setOpen(false); return; }
        setLoading(true);
        try {
            const url = `${apiDrugsSearch().url}?q=${encodeURIComponent(q)}&limit=12`;
            const res = await fetch(url, {
                headers: { 'X-Requested-With': 'XMLHttpRequest', Accept: 'application/json' },
            });
            if (res.ok) {
                const json = await res.json();
                setResults(json.data ?? []);
                setOpen(true);
            }
        } catch {
            // silently ignore
        } finally {
            setLoading(false);
        }
    }, []);

    const handleInput = (e: React.ChangeEvent<HTMLInputElement>) => {
        const v = e.target.value;
        onChange(v);
        if (debounceRef.current) clearTimeout(debounceRef.current);
        debounceRef.current = setTimeout(() => search(v), 250);
    };

    const pick = (drug: DrugResult) => {
        onChange(drug.name);
        onSelect(drug);
        setOpen(false);
        setResults([]);
    };

    // Close on outside click
    useEffect(() => {
        const handler = (e: MouseEvent) => {
            if (wrapperRef.current && !wrapperRef.current.contains(e.target as Node)) {
                setOpen(false);
            }
        };
        document.addEventListener('mousedown', handler);
        return () => document.removeEventListener('mousedown', handler);
    }, []);

    return (
        <div ref={wrapperRef} className="relative">
            <input
                type="text"
                value={value}
                onChange={handleInput}
                disabled={disabled}
                placeholder={placeholder}
                className={clsx(
                    'w-full rounded-lg border px-2 py-1.5 text-xs text-slate-800 placeholder:text-slate-400',
                    'focus:outline-none focus:ring-1 focus:ring-teal-300 focus:border-teal-400',
                    disabled
                        ? 'border-transparent bg-transparent text-slate-600 cursor-not-allowed'
                        : 'border-slate-200 bg-white hover:border-slate-300',
                    className,
                )}
                autoComplete="off"
            />
            {loading && (
                <span className="absolute right-2 top-1/2 -translate-y-1/2">
                    <span className="inline-block h-3 w-3 animate-spin rounded-full border-2 border-teal-400 border-t-transparent" />
                </span>
            )}
            {open && results.length > 0 && (
                <div className="absolute left-0 top-full z-50 mt-0.5 w-72 overflow-hidden rounded-xl border border-slate-200 bg-white shadow-xl">
                    {results.map((drug) => (
                        <button
                            key={drug.id}
                            type="button"
                            onMouseDown={() => pick(drug)}
                            className="flex w-full flex-col gap-0.5 px-3 py-2 text-left transition-colors hover:bg-teal-50"
                        >
                            <div className="flex items-center gap-2">
                                <span className="text-xs font-semibold text-slate-900">{drug.name}</span>
                                {drug.type && (
                                    <span className="rounded-full bg-slate-100 px-1.5 py-0.5 text-[10px] text-slate-500">{drug.type}</span>
                                )}
                                {drug.strength && (
                                    <span className="rounded-full bg-teal-50 px-1.5 py-0.5 text-[10px] text-teal-700">{drug.strength}</span>
                                )}
                            </div>
                            {drug.generic_name && (
                                <span className="text-[10px] text-slate-400 italic">{drug.generic_name}</span>
                            )}
                            {(drug.default_dose || drug.default_frequency) && (
                                <span className="text-[10px] text-slate-500">
                                    {[drug.default_dose, drug.default_frequency, drug.default_duration, drug.default_route]
                                        .filter(Boolean).join(' · ')}
                                </span>
                            )}
                        </button>
                    ))}
                </div>
            )}
        </div>
    );
}
