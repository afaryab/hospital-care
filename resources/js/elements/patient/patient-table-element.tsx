import { Badge } from '@/components/ui/badge';
import { cn } from '@/lib/utils';
import { Patient } from '@/types';

const genderLabel: Record<string, string> = {
    m: 'Male',
    f: 'Female',
    t: 'Transgender',
    o: 'Other',
};

type PatientTableElementProps = {
    patient: Patient;
    className?: string;
};

/** Compact inline representation for use as a table cell value. */
const PatientTableElement: React.FC<PatientTableElementProps> = ({
    patient,
    className,
}) => (
    <div className={cn('flex min-w-0 items-center gap-2', className)}>
        <div className="min-w-0">
            <p className="truncate text-sm font-medium">{patient.name}</p>
            <p className="font-mono text-xs text-muted-foreground">
                {patient.ps_number}
            </p>
        </div>
        <Badge variant="outline" className="shrink-0 text-xs">
            {genderLabel[patient.gender] ?? patient.gender}
        </Badge>
    </div>
);

export default PatientTableElement;
