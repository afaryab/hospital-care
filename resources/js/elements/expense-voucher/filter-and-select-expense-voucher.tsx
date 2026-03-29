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
import SelectExpenseCategory from '@/elements/expense-category/select-expense-category';
import FilterAndSelectServiceOrder from '@/elements/serviceorder/filter-and-select-serviceorder';
import SelectUser from '@/elements/user/select-user';
import { LoaderCircle, Search } from 'lucide-react';

export interface ExpenseVoucherSearchItem {
    id: number;
    vc_number: string;
    amount?: number;
    payed_to?: number;
    payed_to_name?: string;
    created_at?: string;
    exp_category_id?: number;
    service_order_id?: number;
    payed_to_user?: {
        id: number;
        name?: string;
    };
    payedTo?: {
        id: number;
        name?: string;
    };
}

interface SearchResult {
    exact: ExpenseVoucherSearchItem[];
    possible: ExpenseVoucherSearchItem[];
}

interface ExpenseVoucherFilters {
    vc_number: string;
    exp_category_id: string;
    service_order_id: string;
    payed_to: string;
    payed_to_name: string;
    amount_min: string;
    amount_max: string;
    created_from: string;
    created_to: string;
}

interface FilterAndSelectExpenseVoucherProps {
    value: string;
    onValueChange: (value: string) => void;
    onSelect?: (voucher: ExpenseVoucherSearchItem | null) => void;
    placeholder?: string;
    label?: string;
}

const initialFilters: ExpenseVoucherFilters = {
    vc_number: '',
    exp_category_id: '',
    service_order_id: '',
    payed_to: '',
    payed_to_name: '',
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

const resolvePayedTo = (voucher: ExpenseVoucherSearchItem) => {
    return (
        voucher.payedTo?.name ??
        voucher.payed_to_user?.name ??
        voucher.payed_to_name ??
        'N/A'
    );
};

export default function FilterAndSelectExpenseVoucher({
    value,
    onValueChange,
    onSelect,
    placeholder = 'Enter voucher number (VC/...)',
    label = 'Expense Voucher',
}: FilterAndSelectExpenseVoucherProps) {
    const [query, setQuery] = useState('');
    const [isLoading, setIsLoading] = useState(false);
    const [result, setResult] = useState<SearchResult | null>(null);
    const [selected, setSelected] = useState<ExpenseVoucherSearchItem | null>(
        null,
    );

    const [isAdvancedOpen, setIsAdvancedOpen] = useState(false);
    const [filters, setFilters] =
        useState<ExpenseVoucherFilters>(initialFilters);
    const [advancedResult, setAdvancedResult] = useState<SearchResult | null>(
        null,
    );
    const [isAdvancedLoading, setIsAdvancedLoading] = useState(false);

    const allQuickResults = useMemo(() => {
        if (!result) {
            return [] as ExpenseVoucherSearchItem[];
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
            const response = await fetch('/api/expense-vouchers/search', {
                method: 'POST',
                headers: {
                    Accept: 'application/json',
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(payload),
            });

            if (!response.ok) {
                throw new Error('Failed to search expense vouchers');
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
            search({ vc_number: query.trim(), limit: 8 });
        }, 300);

        return () => clearTimeout(timer);
    }, [query]);

    const selectVoucher = (voucher: ExpenseVoucherSearchItem) => {
        setSelected(voucher);
        setQuery(voucher.vc_number);
        onValueChange(voucher.id.toString());
        onSelect?.(voucher);
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
                vc_number: filters.vc_number || undefined,
                exp_category_id: filters.exp_category_id || undefined,
                service_order_id: filters.service_order_id || undefined,
                payed_to: filters.payed_to || undefined,
                payed_to_name: filters.payed_to_name || undefined,
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
                            onClick={() => selectVoucher(item)}
                            className="w-full border-b px-3 py-2 text-left last:border-b-0 hover:bg-muted/40"
                        >
                            <div className="font-medium">{item.vc_number}</div>
                            <div className="text-xs text-muted-foreground">
                                Amount: {item.amount ?? 0}
                            </div>
                            <div className="text-xs text-muted-foreground">
                                Paid To: {resolvePayedTo(item)}
                            </div>
                        </button>
                    ))}
                </div>
            ) : null}

            {selected ? (
                <div className="flex items-center justify-between rounded-md border bg-muted/20 p-2 text-sm">
                    <div>
                        <div className="font-medium">{selected.vc_number}</div>
                        <div className="text-xs text-muted-foreground">
                            Amount: {selected.amount ?? 0}
                        </div>
                        <div className="text-xs text-muted-foreground">
                            Paid To: {resolvePayedTo(selected)}
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
                        <DialogTitle>
                            Advanced Expense Voucher Search
                        </DialogTitle>
                        <DialogDescription>
                            Filter expense vouchers and select one from results.
                        </DialogDescription>
                    </DialogHeader>

                    <div className="grid grid-cols-1 gap-3 md:grid-cols-2">
                        <div className="space-y-1">
                            <Label>Voucher Number</Label>
                            <Input
                                value={filters.vc_number}
                                onChange={(event) =>
                                    setFilters((prev) => ({
                                        ...prev,
                                        vc_number: event.target.value,
                                    }))
                                }
                            />
                        </div>
                        <div className="space-y-1">
                            <SelectExpenseCategory
                                value={filters.exp_category_id}
                                onValueChange={(value) =>
                                    setFilters((prev) => ({
                                        ...prev,
                                        exp_category_id: value,
                                    }))
                                }
                                label="Expense Category"
                                placeholder="Select expense category"
                            />
                        </div>
                        <div className="space-y-1">
                            <FilterAndSelectServiceOrder
                                value={filters.service_order_id}
                                onValueChange={(value) =>
                                    setFilters((prev) => ({
                                        ...prev,
                                        service_order_id: value,
                                    }))
                                }
                                label="Service Order"
                                placeholder="Find service order"
                            />
                        </div>
                        <div className="space-y-1">
                            <SelectUser
                                value={filters.payed_to}
                                onValueChange={(value) =>
                                    setFilters((prev) => ({
                                        ...prev,
                                        payed_to: value,
                                    }))
                                }
                                label="Paid To"
                                placeholder="Select paid-to user"
                            />
                        </div>
                        <div className="space-y-1">
                            <Label>Paid To Name</Label>
                            <Input
                                value={filters.payed_to_name}
                                onChange={(event) =>
                                    setFilters((prev) => ({
                                        ...prev,
                                        payed_to_name: event.target.value,
                                    }))
                                }
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
                                    onClick={() => selectVoucher(item)}
                                    className="w-full border-b px-3 py-2 text-left last:border-b-0 hover:bg-muted/40"
                                >
                                    <div className="font-medium">
                                        {item.vc_number}
                                    </div>
                                    <div className="text-xs text-muted-foreground">
                                        Amount: {item.amount ?? 0}
                                    </div>
                                    <div className="text-xs text-muted-foreground">
                                        Paid To: {resolvePayedTo(item)}
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
