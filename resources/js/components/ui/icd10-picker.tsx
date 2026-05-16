import { useEffect, useRef, useState } from 'react';

interface Icd10Code {
    id: number;
    code: string;
    description: string;
    category: string;
}

interface Icd10PickerProps {
    value: string;
    onSelect: (code: string, description: string) => void;
    disabled?: boolean;
    placeholder?: string;
    className?: string;
}

export default function Icd10Picker({
    value,
    onSelect,
    disabled = false,
    placeholder = 'Search ICD-10 code or diagnosis…',
    className = '',
}: Icd10PickerProps) {
    const [query, setQuery] = useState(value);
    const [results, setResults] = useState<Icd10Code[]>([]);
    const [open, setOpen] = useState(false);
    const [loading, setLoading] = useState(false);
    const debounceRef = useRef<ReturnType<typeof setTimeout> | null>(null);
    const containerRef = useRef<HTMLDivElement>(null);

    // Sync external value changes (e.g. form reset)
    useEffect(() => { setQuery(value); }, [value]);

    // Close dropdown when clicking outside
    useEffect(() => {
        const handler = (e: MouseEvent) => {
            if (containerRef.current && !containerRef.current.contains(e.target as Node)) {
                setOpen(false);
            }
        };
        document.addEventListener('mousedown', handler);
        return () => document.removeEventListener('mousedown', handler);
    }, []);

    const search = (q: string) => {
        if (debounceRef.current) clearTimeout(debounceRef.current);
        if (!q.trim()) { setResults([]); setOpen(false); return; }

        debounceRef.current = setTimeout(async () => {
            setLoading(true);
            try {
                const res = await fetch(`/api/icd10-codes?q=${encodeURIComponent(q)}`, {
                    headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                });
                if (res.ok) {
                    const json = await res.json();
                    setResults(json.data ?? []);
                    setOpen((json.data ?? []).length > 0);
                }
            } finally {
                setLoading(false);
            }
        }, 250);
    };

    return (
        <div ref={containerRef} className="relative">
            <input
                type="text"
                value={query}
                disabled={disabled}
                placeholder={placeholder}
                className={className}
                onChange={(e) => {
                    setQuery(e.target.value);
                    search(e.target.value);
                }}
                onFocus={() => { if (results.length > 0) setOpen(true); }}
                aria-autocomplete="list"
                aria-expanded={open}
            />
            {loading && (
                <span className="absolute top-1/2 right-2 -translate-y-1/2 text-xs text-slate-400">…</span>
            )}
            {open && results.length > 0 && (
                <ul className="absolute z-50 mt-1 max-h-60 w-full overflow-auto rounded-md border border-slate-200 bg-white shadow-lg text-sm">
                    {results.map((r) => (
                        <li
                            key={r.id}
                            className="flex cursor-pointer flex-col gap-0.5 px-3 py-2 hover:bg-indigo-50"
                            onMouseDown={(e) => {
                                e.preventDefault();
                                setQuery(r.code);
                                setOpen(false);
                                onSelect(r.code, r.description);
                            }}
                        >
                            <span className="font-mono font-semibold text-indigo-700">{r.code}</span>
                            <span className="text-slate-600 text-xs">{r.description}</span>
                            {r.category && (
                                <span className="text-slate-400 text-xs">{r.category}</span>
                            )}
                        </li>
                    ))}
                </ul>
            )}
        </div>
    );
}
