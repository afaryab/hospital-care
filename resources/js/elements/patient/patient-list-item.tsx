import { Badge } from '@/components/ui/badge';
import { cn } from '@/lib/utils';
import { Patient } from '@/types';

const genderLabel: Record<string, string> = {
    m: 'Male',
    f: 'Female',
    t: 'Transgender',
    o: 'Other',
};

type PatientListItemProps = {
    patient: Patient;
    className?: string;
    onClick?: () => void;
    selected?: boolean;
};

const PatientListItem: React.FC<PatientListItemProps> = ({
    patient,
    className,
    onClick,
    selected,
}) => (
    <div
        className={cn(
            'flex items-center justify-between gap-3 rounded-md px-3 py-2 text-sm',
            'transition-colors hover:bg-accent',
            selected && 'bg-accent',
            onClick && 'cursor-pointer',
            className,
        )}
        onClick={onClick}
        role={onClick ? 'button' : undefined}
        tabIndex={onClick ? 0 : undefined}
        onKeyDown={onClick ? (e) => e.key === 'Enter' && onClick() : undefined}
    >
        <div className="min-w-0">
            <p className="truncate font-medium">{patient.name}</p>
            <p className="font-mono text-xs text-muted-foreground">
                {patient.ps_number}
            </p>
        </div>
        <div className="flex shrink-0 items-center gap-1.5">
            {patient.age != null && (
                <span className="text-xs text-muted-foreground">
                    {patient.age} yrs
                </span>
            )}
            <Badge variant="outline" className="text-xs">
                {genderLabel[patient.gender] ?? patient.gender}
            </Badge>
        </div>
    </div>
);

export default PatientListItem;
