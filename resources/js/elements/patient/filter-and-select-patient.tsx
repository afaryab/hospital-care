import { useEffect, useMemo, useState } from 'react';

import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { LoaderCircle } from 'lucide-react';

export interface PatientSearchItem {
    id: number;
    ps_number?: string;
    name?: string;
    cnic?: string;
    contact?: string;
}

interface SearchResult {
    exact: Array<PatientSearchItem | null>;
    possible: PatientSearchItem[];
}

interface FilterAndSelectPatientProps {
    value: string;
    onValueChange: (value: string) => void;
    onSelect?: (patient: PatientSearchItem | null) => void;
    label?: string;
    placeholder?: string;
}

const hasSearchResults = (result: SearchResult | null) => {
    if (!result) {
        return false;
    }

    const exact = result.exact.filter(Boolean);
    return exact.length > 0 || result.possible.length > 0;
};

export default function FilterAndSelectPatient({
    value,
    onValueChange,
    onSelect,
    label = 'Patient',
    placeholder = 'Find patient by PS or name',
}: FilterAndSelectPatientProps) {
    const [query, setQuery] = useState('');
    const [isLoading, setIsLoading] = useState(false);
    const [result, setResult] = useState<SearchResult | null>(null);
    const [selected, setSelected] = useState<PatientSearchItem | null>(null);

    const allResults = useMemo(() => {
        if (!result) {
            return [] as PatientSearchItem[];
        }

        return [...result.exact.filter(Boolean) as PatientSearchItem[], ...result.possible];
    }, [result]);

    const search = async (searchQuery: string) => {
        setIsLoading(true);

        try {
            const response = await fetch('/api/patients', {
                method: 'POST',
                headers: {
                    Accept: 'application/json',
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    mr_number: searchQuery,
                    patient_name: searchQuery,
                }),
            });

            if (!response.ok) {
                throw new Error('Failed to search patients');
            }

            const data = await response.json();
            setResult(data?.data ?? { exact: [], possible: [] });
        } catch {
            setResult({ exact: [], possible: [] });
        } finally {
            setIsLoading(false);
        }
    };

    useEffect(() => {
        if (!query || query.trim().length < 2) {
            setResult(null);
            return;
        }

        const timer = setTimeout(() => {
            search(query.trim());
        }, 300);

        return () => clearTimeout(timer);
    }, [query]);

    const selectPatient = (patient: PatientSearchItem) => {
        setSelected(patient);
        setQuery(patient.ps_number ?? patient.name ?? '');
        onValueChange(patient.id.toString());
        onSelect?.(patient);
        setResult(null);
    };

    return (
        <div className="space-y-2">
            <Label>{label}</Label>
            <div className="relative">
                <Input value={query} onChange={(event) => setQuery(event.target.value)} placeholder={placeholder} />
                {isLoading ? <LoaderCircle className="absolute top-2.5 right-2 h-4 w-4 animate-spin text-muted-foreground" /> : null}
            </div>

            {hasSearchResults(result) ? (
                <div className="max-h-44 overflow-auto rounded-md border bg-white dark:bg-neutral-900">
                    {allResults.map((patient) => (
                        <button
                            key={patient.id}
                            type="button"
                            onClick={() => selectPatient(patient)}
                            className="w-full border-b px-3 py-2 text-left last:border-b-0 hover:bg-muted/40"
                        >
                            <div className="font-medium">{patient.ps_number ?? 'N/A'}</div>
                            <div className="text-xs text-muted-foreground">{patient.name ?? 'Unknown patient'}</div>
                        </button>
                    ))}
                </div>
            ) : null}

            {selected ? (
                <div className="rounded-md border bg-muted/20 p-2 text-sm">
                    <div className="font-medium">{selected.ps_number ?? 'N/A'}</div>
                    <div className="text-xs text-muted-foreground">{selected.name ?? 'Unknown patient'}</div>
                </div>
            ) : null}

            <input type="hidden" value={value} readOnly />
        </div>
    );
}
