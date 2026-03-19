import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { cn } from '@/lib/utils';

export type PatientSearchFilters = {
    name?: string;
    ps_number?: string;
    cnic?: string;
    contact?: string;
};

type PatientAdvancedSearchProps = {
    filters: PatientSearchFilters;
    onChange: (filters: PatientSearchFilters) => void;
    onSearch?: () => void;
    onReset?: () => void;
    className?: string;
};

const PatientAdvancedSearch: React.FC<PatientAdvancedSearchProps> = ({
    filters,
    onChange,
    onSearch,
    onReset,
    className,
}) => (
    <div className={cn('grid gap-4', className)}>
        <div className="grid gap-3 md:grid-cols-2">
            <label className="grid gap-1.5">
                <Label>Name</Label>
                <Input
                    value={filters.name ?? ''}
                    onChange={(event) => onChange({ ...filters, name: event.target.value })}
                    placeholder="Patient name"
                />
            </label>
            <label className="grid gap-1.5">
                <Label>PS Number</Label>
                <Input
                    value={filters.ps_number ?? ''}
                    onChange={(event) => onChange({ ...filters, ps_number: event.target.value })}
                    placeholder="PS/2026/03/0001"
                />
            </label>
            <label className="grid gap-1.5">
                <Label>CNIC</Label>
                <Input
                    value={filters.cnic ?? ''}
                    onChange={(event) => onChange({ ...filters, cnic: event.target.value })}
                    placeholder="XXXXX-XXXXXXX-X"
                />
            </label>
            <label className="grid gap-1.5">
                <Label>Contact</Label>
                <Input
                    value={filters.contact ?? ''}
                    onChange={(event) => onChange({ ...filters, contact: event.target.value })}
                    placeholder="03XX-XXXXXXX"
                />
            </label>
        </div>
        <div className="flex flex-wrap items-center gap-2">
            {onSearch && (
                <Button type="button" onClick={onSearch}>
                    Search
                </Button>
            )}
            {onReset && (
                <Button type="button" variant="outline" onClick={onReset}>
                    Reset
                </Button>
            )}
        </div>
    </div>
);

export default PatientAdvancedSearch;
