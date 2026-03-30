import { apiFetch } from '@/lib/api-fetch';
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
import { LoaderCircle, Search } from 'lucide-react';

export interface TransactionSearchItem {
    id: number;
    tr_number: string;
    type?: string;
    income_or_expense?: string;
    amount?: number;
    created_at?: string;
    patient?: {
        id: number;
        name?: string;
    };
}

interface SearchResult {
    exact: TransactionSearchItem[];
    possible: TransactionSearchItem[];
}

interface TransactionFilters {
    tr_number: string;
    income_or_expense: string;
    type: string;
    amount_min: string;
    amount_max: string;
    created_from: string;
    created_to: string;
}

interface FilterAndSelectTransactionProps {
    value: string;
    onValueChange: (value: string) => void;
    onSelect?: (transaction: TransactionSearchItem | null) => void;
    placeholder?: string;
    label?: string;
}

const initialFilters: TransactionFilters = {
    tr_number: '',
    income_or_expense: '',
    type: '',
    amount_min: '',
    amount_max: '',
    created_from: '',
    created_to: '',
};

const hasSearchResults = (result: SearchResult | null) => {
    if (!result) {
        return false;
    }

    return result.exact.length > 0 || result.possible.length > 0;
};

export default function FilterAndSelectTransaction({
    value,
    onValueChange,
    onSelect,
    placeholder = 'Enter transaction number (TR/...)',
    label = 'Transaction',
}: FilterAndSelectTransactionProps) {
    const [query, setQuery] = useState('');
    const [isLoading, setIsLoading] = useState(false);
    const [result, setResult] = useState<SearchResult | null>(null);
    const [selected, setSelected] = useState<TransactionSearchItem | null>(
        null,
    );

    const [isAdvancedOpen, setIsAdvancedOpen] = useState(false);
    const [filters, setFilters] = useState<TransactionFilters>(initialFilters);
    const [advancedResult, setAdvancedResult] = useState<SearchResult | null>(
        null,
    );
    const [isAdvancedLoading, setIsAdvancedLoading] = useState(false);

    const allQuickResults = useMemo(() => {
        if (!result) {
            return [] as TransactionSearchItem[];
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
            const response = await apiFetch('/api/transactions/search', {
                method: 'POST',
                body: JSON.stringify(payload),
            });

            if (!response.ok) {
                throw new Error('Failed to search transactions');
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
            search({ tr_number: query.trim(), limit: 8 });
        }, 300);

        return () => clearTimeout(timer);
    }, [query]);

    const selectTransaction = (transaction: TransactionSearchItem) => {
        setSelected(transaction);
        setQuery(transaction.tr_number);
        onValueChange(transaction.id.toString());
        onSelect?.(transaction);
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
                tr_number: filters.tr_number || undefined,
                income_or_expense: filters.income_or_expense || undefined,
                type: filters.type || undefined,
                amount_min: filters.amount_min || undefined,
                amount_max: filters.amount_max || undefined,
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
                            onClick={() => selectTransaction(item)}
                            className="w-full border-b px-3 py-2 text-left last:border-b-0 hover:bg-muted/40"
                        >
                            <div className="font-medium">{item.tr_number}</div>
                            <div className="text-xs text-muted-foreground">
                                {item.income_or_expense ?? 'N/A'} |{' '}
                                {item.type ?? 'N/A'} | {item.amount ?? 0}
                            </div>
                        </button>
                    ))}
                </div>
            ) : null}

            {selected ? (
                <div className="flex items-center justify-between rounded-md border bg-muted/20 p-2 text-sm">
                    <div>
                        <div className="font-medium">{selected.tr_number}</div>
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
                        <DialogTitle>Advanced Transaction Search</DialogTitle>
                        <DialogDescription>
                            Filter transactions and select one from results.
                        </DialogDescription>
                    </DialogHeader>

                    <div className="grid grid-cols-1 gap-3 md:grid-cols-2">
                        <div className="space-y-1">
                            <Label>Transaction Number</Label>
                            <Input
                                value={filters.tr_number}
                                onChange={(event) =>
                                    setFilters((prev) => ({
                                        ...prev,
                                        tr_number: event.target.value,
                                    }))
                                }
                            />
                        </div>
                        <div className="space-y-1">
                            <Label>Type</Label>
                            <Input
                                value={filters.type}
                                onChange={(event) =>
                                    setFilters((prev) => ({
                                        ...prev,
                                        type: event.target.value,
                                    }))
                                }
                            />
                        </div>
                        <div className="space-y-1">
                            <Label>Income/Expense</Label>
                            <Input
                                value={filters.income_or_expense}
                                onChange={(event) =>
                                    setFilters((prev) => ({
                                        ...prev,
                                        income_or_expense:
                                            event.target.value.toUpperCase(),
                                    }))
                                }
                                placeholder="INCOME or EXPENSE"
                            />
                        </div>
                        <div className="space-y-1">
                            <Label>Amount Min</Label>
                            <Input
                                type="number"
                                step="0.01"
                                value={filters.amount_min}
                                onChange={(event) =>
                                    setFilters((prev) => ({
                                        ...prev,
                                        amount_min: event.target.value,
                                    }))
                                }
                            />
                        </div>
                        <div className="space-y-1">
                            <Label>Amount Max</Label>
                            <Input
                                type="number"
                                step="0.01"
                                value={filters.amount_max}
                                onChange={(event) =>
                                    setFilters((prev) => ({
                                        ...prev,
                                        amount_max: event.target.value,
                                    }))
                                }
                            />
                        </div>
                        <div className="space-y-1">
                            <Label>Created From</Label>
                            <Input
                                type="date"
                                value={filters.created_from}
                                onChange={(event) =>
                                    setFilters((prev) => ({
                                        ...prev,
                                        created_from: event.target.value,
                                    }))
                                }
                            />
                        </div>
                        <div className="space-y-1">
                            <Label>Created To</Label>
                            <Input
                                type="date"
                                value={filters.created_to}
                                onChange={(event) =>
                                    setFilters((prev) => ({
                                        ...prev,
                                        created_to: event.target.value,
                                    }))
                                }
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
                                    onClick={() => selectTransaction(item)}
                                    className="w-full border-b px-3 py-2 text-left last:border-b-0 hover:bg-muted/40"
                                >
                                    <div className="font-medium">
                                        {item.tr_number}
                                    </div>
                                    <div className="text-xs text-muted-foreground">
                                        {item.income_or_expense ?? 'N/A'} |{' '}
                                        {item.type ?? 'N/A'} |{' '}
                                        {item.amount ?? 0}
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
