import { useEffect, useMemo, useState } from 'react';

import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';

export interface ExpenseCategoryOption {
    id: number;
    name: string;
    type?: string | null;
    pay_doc?: boolean;
    pay_others?: boolean;
    pay_users?: boolean;
}

interface SearchResult {
    exact: ExpenseCategoryOption[];
    possible: ExpenseCategoryOption[];
}

interface SelectExpenseCategoryProps {
    value: string;
    onValueChange: (value: string) => void;
    onSelect?: (category: ExpenseCategoryOption | null) => void;
    label?: string;
    placeholder?: string;
}

const parseOptions = (result: SearchResult | null): ExpenseCategoryOption[] => {
    if (!result) {
        return [];
    }

    const merged = [...result.exact, ...result.possible];
    const byId = new Map<number, ExpenseCategoryOption>();

    merged.forEach((item) => {
        byId.set(item.id, item);
    });

    return Array.from(byId.values());
};

export default function SelectExpenseCategory({
    value,
    onValueChange,
    onSelect,
    label = 'Expense Category',
    placeholder = 'Select expense category',
}: SelectExpenseCategoryProps) {
    const [isLoading, setIsLoading] = useState(false);
    const [searchResult, setSearchResult] = useState<SearchResult | null>(null);

    useEffect(() => {
        const loadCategories = async () => {
            setIsLoading(true);

            try {
                const response = await fetch('/api/expense-categories/search', {
                    method: 'POST',
                    headers: {
                        Accept: 'application/json',
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ limit: 100 }),
                });

                if (!response.ok) {
                    throw new Error('Failed to load expense categories');
                }

                const data = await response.json();
                setSearchResult(data?.data ?? { exact: [], possible: [] });
            } catch {
                setSearchResult({ exact: [], possible: [] });
            } finally {
                setIsLoading(false);
            }
        };

        loadCategories();
    }, []);

    const options = useMemo(() => parseOptions(searchResult), [searchResult]);

    useEffect(() => {
        if (!value || !onSelect) {
            return;
        }

        const selectedOption = options.find((item) => item.id.toString() === value) ?? null;
        onSelect(selectedOption);
    }, [onSelect, options, value]);

    const handleChange = (selectedValue: string) => {
        onValueChange(selectedValue);

        if (!onSelect) {
            return;
        }

        const selectedOption = options.find((item) => item.id.toString() === selectedValue) ?? null;
        onSelect(selectedOption);
    };

    return (
        <div className="space-y-2">
            <Label>{label}</Label>
            <Select value={value} onValueChange={handleChange}>
                <SelectTrigger className="w-full">
                    <SelectValue placeholder={isLoading ? 'Loading...' : placeholder} />
                </SelectTrigger>
                <SelectContent searchable searchPlaceholder="Search category...">
                    {options.map((category) => (
                        <SelectItem key={category.id} value={category.id.toString()} textValue={category.name}>
                            {category.name}
                        </SelectItem>
                    ))}
                </SelectContent>
            </Select>
        </div>
    );
}
