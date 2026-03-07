import { useEffect, useMemo, useState } from 'react';

import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { LoaderCircle } from 'lucide-react';

export interface UserSearchItem {
    id: number;
    name?: string;
    username?: string;
    email?: string;
}

interface SearchResult {
    exact: UserSearchItem[];
    possible: UserSearchItem[];
}

interface FilterAndSelectUserProps {
    value: string;
    onValueChange: (value: string) => void;
    onSelect?: (user: UserSearchItem | null) => void;
    label?: string;
    placeholder?: string;
    doctorOnly?: boolean;
}

const hasSearchResults = (result: SearchResult | null) => {
    if (!result) {
        return false;
    }

    return result.exact.length > 0 || result.possible.length > 0;
};

export default function FilterAndSelectUser({
    value,
    onValueChange,
    onSelect,
    label = 'User',
    placeholder = 'Find user by name',
    doctorOnly = false,
}: FilterAndSelectUserProps) {
    const [query, setQuery] = useState('');
    const [isLoading, setIsLoading] = useState(false);
    const [result, setResult] = useState<SearchResult | null>(null);
    const [selected, setSelected] = useState<UserSearchItem | null>(null);

    const allResults = useMemo(() => {
        if (!result) {
            return [] as UserSearchItem[];
        }

        return [...result.exact, ...result.possible];
    }, [result]);

    const search = async (searchQuery: string) => {
        setIsLoading(true);

        try {
            const response = await fetch('/api/users/search', {
                method: 'POST',
                headers: {
                    Accept: 'application/json',
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    name: searchQuery,
                    is_active: true,
                    doctor_only: doctorOnly,
                    limit: 20,
                }),
            });

            if (!response.ok) {
                throw new Error('Failed to search users');
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
    }, [doctorOnly, query]);

    const selectUser = (user: UserSearchItem) => {
        setSelected(user);
        setQuery(user.name ?? user.username ?? user.email ?? '');
        onValueChange(user.id.toString());
        onSelect?.(user);
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
                    {allResults.map((user) => (
                        <button
                            key={user.id}
                            type="button"
                            onClick={() => selectUser(user)}
                            className="w-full border-b px-3 py-2 text-left last:border-b-0 hover:bg-muted/40"
                        >
                            <div className="font-medium">{user.name ?? 'Unknown user'}</div>
                            <div className="text-xs text-muted-foreground">{user.username ?? user.email ?? 'N/A'}</div>
                        </button>
                    ))}
                </div>
            ) : null}

            {selected ? (
                <div className="rounded-md border bg-muted/20 p-2 text-sm">
                    <div className="font-medium">{selected.name ?? 'Unknown user'}</div>
                    <div className="text-xs text-muted-foreground">{selected.username ?? selected.email ?? 'N/A'}</div>
                </div>
            ) : null}

            <input type="hidden" value={value} readOnly />
        </div>
    );
}
