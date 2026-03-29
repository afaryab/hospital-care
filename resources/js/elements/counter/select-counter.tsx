import { useEffect, useState } from 'react';

import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';

export interface ClosingOption {
    id: number;
    ct_number: string;
    status?: string;
}

interface SelectCounterProps {
    value: string;
    onValueChange: (value: string) => void;
    onSelect?: (closing: ClosingOption | null) => void;
    label?: string;
    placeholder?: string;
}

export default function SelectCounter({
    value,
    onValueChange,
    onSelect,
    label = 'Counter',
    placeholder = 'All counters',
}: SelectCounterProps) {
    const [isLoading, setIsLoading] = useState(false);
    const [options, setOptions] = useState<ClosingOption[]>([]);

    useEffect(() => {
        const load = async () => {
            setIsLoading(true);
            try {
                const response = await fetch('/api/closings/search', {
                    method: 'POST',
                    headers: {
                        Accept: 'application/json',
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ limit: 100 }),
                });
                if (!response.ok) throw new Error('Failed to load counters');
                const data = await response.json();
                setOptions(data?.data ?? []);
            } catch {
                setOptions([]);
            } finally {
                setIsLoading(false);
            }
        };
        load();
    }, []);

    const handleChange = (selectedValue: string) => {
        const normalized = selectedValue === '__all__' ? '' : selectedValue;
        onValueChange(normalized);
        if (!onSelect) return;
        if (normalized === '') {
            onSelect(null);
            return;
        }
        const selected =
            options.find((c) => c.id.toString() === normalized) ?? null;
        onSelect(selected);
    };

    return (
        <div className="space-y-2">
            <Label>{label}</Label>
            <Select value={value || '__all__'} onValueChange={handleChange}>
                <SelectTrigger className="w-full">
                    <SelectValue
                        placeholder={isLoading ? 'Loading...' : placeholder}
                    />
                </SelectTrigger>
                <SelectContent searchable searchPlaceholder="Search counter...">
                    <SelectItem value="__all__" textValue="All counters">
                        All counters
                    </SelectItem>
                    {options.map((c) => (
                        <SelectItem
                            key={c.id}
                            value={c.id.toString()}
                            textValue={c.ct_number}
                        >
                            {c.ct_number}
                        </SelectItem>
                    ))}
                </SelectContent>
            </Select>
        </div>
    );
}
