import { useEffect, useMemo, useState } from 'react';

import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import FilterAndSelectPatient from '@/elements/patient/filter-and-select-patient';
import FilterAndSelectUser from '@/elements/user/filter-and-select-user';
import { LoaderCircle, Search } from 'lucide-react';

export interface ServiceOrderSearchItem {
    id: number;
    so_number: string;
    type?: string;
    created_at?: string;
    patient?: {
        id: number;
        name?: string;
    };
    doctor?: {
        id: number;
        name?: string;
    };
    service?: {
        id: number;
        name?: string;
    };
}

interface SearchResult {
    exact: ServiceOrderSearchItem[];
    possible: ServiceOrderSearchItem[];
}

interface ServiceOrderFilters {
    so_number: string;
    type: string;
    patient_id: string;
    doctor_id: string;
    service_id: string;
    created_from: string;
    created_to: string;
}

interface FilterAndSelectServiceOrderProps {
    value: string;
    onValueChange: (value: string) => void;
    onSelect?: (serviceOrder: ServiceOrderSearchItem | null) => void;
    placeholder?: string;
    label?: string;
}

const initialFilters: ServiceOrderFilters = {
    so_number: '',
    type: '',
    patient_id: '',
    doctor_id: '',
    service_id: '',
    created_from: '',
    created_to: '',
};

const serviceOrderTypeOptions = ['OPD', 'IND', 'EMG', 'DNT', 'ULT'];

const hasSearchResults = (result: SearchResult | null) => {
    if (!result) {
        return false;
    }

    return result.exact.length > 0 || result.possible.length > 0;
};

export default function FilterAndSelectServiceOrder({
    value,
    onValueChange,
    onSelect,
    placeholder = 'Enter service order number (SO/...)',
    label = 'Service Order',
}: FilterAndSelectServiceOrderProps) {
    const [query, setQuery] = useState('');
    const [isLoading, setIsLoading] = useState(false);
    const [result, setResult] = useState<SearchResult | null>(null);
    const [selected, setSelected] = useState<ServiceOrderSearchItem | null>(
        null,
    );

    const [isAdvancedOpen, setIsAdvancedOpen] = useState(false);
    const [filters, setFilters] = useState<ServiceOrderFilters>(initialFilters);
    const [advancedResult, setAdvancedResult] = useState<SearchResult | null>(
        null,
    );
    const [isAdvancedLoading, setIsAdvancedLoading] = useState(false);

    const allQuickResults = useMemo(() => {
        if (!result) {
            return [] as ServiceOrderSearchItem[];
        }

        return [...result.exact, ...result.possible];
    }, [result]);

    const search = async (
        payload: Record<string, unknown>,
        advanced = false,
    ) => {
        if (advanced) {
            setIsAdvancedLoading(true);
        } else {
            setIsLoading(true);
        }

        try {
            const response = await fetch('/api/service-orders/search', {
                method: 'POST',
                headers: {
                    Accept: 'application/json',
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(payload),
            });

            if (!response.ok) {
                throw new Error('Failed to search service orders');
            }

            const data = await response.json();
            const parsedResult: SearchResult = data?.data ?? {
                exact: [],
                possible: [],
            };

            if (advanced) {
                setAdvancedResult(parsedResult);
            } else {
                setResult(parsedResult);
            }
        } catch {
            if (advanced) {
                setAdvancedResult({ exact: [], possible: [] });
            } else {
                setResult({ exact: [], possible: [] });
            }
        } finally {
            if (advanced) {
                setIsAdvancedLoading(false);
            } else {
                setIsLoading(false);
            }
        }
    };

    useEffect(() => {
        if (!query || query.trim().length < 2) {
            setResult(null);
            return;
        }

        const timer = setTimeout(() => {
            search({ so_number: query.trim(), limit: 8 });
        }, 300);

        return () => clearTimeout(timer);
    }, [query]);

    const selectServiceOrder = (serviceOrder: ServiceOrderSearchItem) => {
        setSelected(serviceOrder);
        setQuery(serviceOrder.so_number);
        onValueChange(serviceOrder.id.toString());
        onSelect?.(serviceOrder);
        setResult(null);
        setIsAdvancedOpen(false);
    };

    const clearSelection = () => {
        setSelected(null);
        setQuery('');
        onValueChange('');
        onSelect?.(null);
        setResult(null);
    };

    const runAdvancedSearch = async () => {
        await search(
            {
                so_number: filters.so_number || undefined,
                type: filters.type || undefined,
                patient_id: filters.patient_id || undefined,
                doctor_id: filters.doctor_id || undefined,
                service_id: filters.service_id || undefined,
                created_from: filters.created_from || undefined,
                created_to: filters.created_to || undefined,
                limit: 25,
            },
            true,
        );
    };

    return (
        <div className="space-y-2">
            <Label>{label}</Label>
            <div className="flex">
                <div className="relative flex-1">
                    <Input
                        className="rounded-r-none"
                        value={query}
                        onChange={(event) => setQuery(event.target.value)}
                        placeholder={placeholder}
                    />
                    {isLoading ? (
                        <LoaderCircle className="absolute top-2.5 right-2 h-4 w-4 animate-spin text-muted-foreground" />
                    ) : null}
                </div>
                <Button
                    type="button"
                    variant="outline"
                    className="rounded-l-none border-l-0 px-3"
                    onClick={() => setIsAdvancedOpen(true)}
                >
                    <Search className="h-4 w-4" />
                </Button>
            </div>

            {hasSearchResults(result) ? (
                <div className="max-h-56 overflow-auto rounded-md border bg-white dark:bg-neutral-900">
                    {allQuickResults.map((item) => (
                        <button
                            key={item.id}
                            type="button"
                            onClick={() => selectServiceOrder(item)}
                            className="w-full border-b px-3 py-2 text-left last:border-b-0 hover:bg-muted/40"
                        >
                            <div className="font-medium">{item.so_number}</div>
                            <div className="text-xs text-muted-foreground">
                                {item.patient?.name ?? 'N/A'} |{' '}
                                {item.service?.name ?? 'N/A'}
                            </div>
                        </button>
                    ))}
                </div>
            ) : null}

            {selected ? (
                <div className="flex items-center justify-between rounded-md border bg-muted/20 p-2 text-sm">
                    <div>
                        <div className="font-medium">{selected.so_number}</div>
                        <div className="text-xs text-muted-foreground">
                            {selected.patient?.name ?? 'No patient linked'}
                        </div>
                    </div>
                    <Button
                        type="button"
                        variant="ghost"
                        size="sm"
                        onClick={clearSelection}
                    >
                        Clear
                    </Button>
                </div>
            ) : null}

            <input type="hidden" value={value} readOnly />

            <Dialog open={isAdvancedOpen} onOpenChange={setIsAdvancedOpen}>
                <DialogContent className="max-w-5xl">
                    <DialogHeader>
                        <DialogTitle>Advanced Service Order Search</DialogTitle>
                        <DialogDescription>
                            Filter service orders and select one from results.
                        </DialogDescription>
                    </DialogHeader>

                    <div className="grid grid-cols-1 gap-3 md:grid-cols-2">
                        <div className="space-y-1">
                            <Label>Service Order Number</Label>
                            <Input
                                value={filters.so_number}
                                onChange={(event) =>
                                    setFilters((prev) => ({
                                        ...prev,
                                        so_number: event.target.value,
                                    }))
                                }
                            />
                        </div>
                        <div className="space-y-1">
                            <Label>Type</Label>
                            <Select
                                value={filters.type}
                                onValueChange={(value) =>
                                    setFilters((prev) => ({
                                        ...prev,
                                        type: value,
                                    }))
                                }
                            >
                                <SelectTrigger>
                                    <SelectValue placeholder="Select type" />
                                </SelectTrigger>
                                <SelectContent>
                                    {serviceOrderTypeOptions.map((type) => (
                                        <SelectItem key={type} value={type}>
                                            {type}
                                        </SelectItem>
                                    ))}
                                </SelectContent>
                            </Select>
                        </div>
                        <div className="space-y-1">
                            <FilterAndSelectPatient
                                value={filters.patient_id}
                                onValueChange={(value) =>
                                    setFilters((prev) => ({
                                        ...prev,
                                        patient_id: value,
                                    }))
                                }
                                label="Patient"
                                placeholder="Find patient by PS or name"
                            />
                        </div>
                        <div className="space-y-1">
                            <FilterAndSelectUser
                                value={filters.doctor_id}
                                onValueChange={(value) =>
                                    setFilters((prev) => ({
                                        ...prev,
                                        doctor_id: value,
                                    }))
                                }
                                doctorOnly
                                label="Doctor"
                                placeholder="Find doctor"
                            />
                        </div>
                    </div>

                    <div className="flex justify-end gap-2">
                        <Button
                            type="button"
                            variant="outline"
                            onClick={() => setFilters(initialFilters)}
                        >
                            Reset
                        </Button>
                        <Button
                            type="button"
                            onClick={runAdvancedSearch}
                            disabled={isAdvancedLoading}
                        >
                            {isAdvancedLoading ? (
                                <LoaderCircle className="h-4 w-4 animate-spin" />
                            ) : null}
                            Search
                        </Button>
                    </div>

                    <div className="max-h-72 overflow-auto rounded-md border">
                        {hasSearchResults(advancedResult) ? (
                            [
                                ...(advancedResult?.exact ?? []),
                                ...(advancedResult?.possible ?? []),
                            ].map((item) => (
                                <button
                                    key={`advanced-${item.id}`}
                                    type="button"
                                    onClick={() => selectServiceOrder(item)}
                                    className="w-full border-b px-3 py-2 text-left last:border-b-0 hover:bg-muted/40"
                                >
                                    <div className="font-medium">
                                        {item.so_number}
                                    </div>
                                    <div className="text-xs text-muted-foreground">
                                        {item.patient?.name ?? 'N/A'} |{' '}
                                        {item.service?.name ?? 'N/A'}
                                    </div>
                                </button>
                            ))
                        ) : (
                            <div className="p-4 text-sm text-muted-foreground">
                                No results yet. Apply filters and click Search.
                            </div>
                        )}
                    </div>
                </DialogContent>
            </Dialog>
        </div>
    );
}
