import { useEffect, useMemo, useState } from 'react';

import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';

export interface UserOption {
    id: number;
    name: string;
    username?: string | null;
    email?: string | null;
}

interface SearchResult {
    exact: UserOption[];
    possible: UserOption[];
}

interface SelectUserProps {
    value: string;
    onValueChange: (value: string) => void;
    onSelect?: (user: UserOption | null) => void;
    label?: string;
    placeholder?: string;
}

const parseOptions = (result: SearchResult | null): UserOption[] => {
    if (!result) {
        return [];
    }

    const merged = [...result.exact, ...result.possible];
    const byId = new Map<number, UserOption>();

    merged.forEach((item) => {
        byId.set(item.id, item);
    });

    return Array.from(byId.values());
};

export default function SelectUser({
    value,
    onValueChange,
    onSelect,
    label = 'User',
    placeholder = 'Select user',
}: SelectUserProps) {
    const [isLoading, setIsLoading] = useState(false);
    const [searchResult, setSearchResult] = useState<SearchResult | null>(null);

    useEffect(() => {
        const loadUsers = async () => {
            setIsLoading(true);

            try {
                const response = await fetch('/api/users/search', {
                    method: 'POST',
                    headers: {
                        Accept: 'application/json',
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ is_active: true, limit: 100 }),
                });

                if (!response.ok) {
                    throw new Error('Failed to load users');
                }

                const data = await response.json();
                setSearchResult(data?.data ?? { exact: [], possible: [] });
            } catch {
                setSearchResult({ exact: [], possible: [] });
            } finally {
                setIsLoading(false);
            }
        };

        loadUsers();
    }, []);

    const options = useMemo(() => parseOptions(searchResult), [searchResult]);

    useEffect(() => {
        if (!value || !onSelect) {
            return;
        }

        const selectedOption =
            options.find((item) => item.id.toString() === value) ?? null;
        onSelect(selectedOption);
    }, [onSelect, options, value]);

    const handleChange = (selectedValue: string) => {
        onValueChange(selectedValue);

        if (!onSelect) {
            return;
        }

        const selectedOption =
            options.find((item) => item.id.toString() === selectedValue) ??
            null;
        onSelect(selectedOption);
    };

    return (
        <div className="space-y-2">
            <Label>{label}</Label>
            <Select value={value} onValueChange={handleChange}>
                <SelectTrigger className="w-full">
                    <SelectValue
                        placeholder={isLoading ? 'Loading...' : placeholder}
                    />
                </SelectTrigger>
                <SelectContent searchable searchPlaceholder="Search user...">
                    {options.map((user) => (
                        <SelectItem
                            key={user.id}
                            value={user.id.toString()}
                            textValue={`${user.name} ${user.username ?? ''} ${user.email ?? ''}`}
                        >
                            {user.name}
                        </SelectItem>
                    ))}
                </SelectContent>
            </Select>
        </div>
    );
}
