import { Badge } from '@/components/ui/badge';
import { Patient } from '@/types';
import { cn } from '@/lib/utils';

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
const PatientTableElement: React.FC<PatientTableElementProps> = ({ patient, className }) => (
    <div className={cn('flex items-center gap-2 min-w-0', className)}>
        <div className="min-w-0">
            <p className="text-sm font-medium truncate">{patient.name}</p>
            <p className="text-xs text-muted-foreground font-mono">{patient.ps_number}</p>
        </div>
        <Badge variant="outline" className="text-xs shrink-0">
            {genderLabel[patient.gender] ?? patient.gender}
        </Badge>
    </div>
);

export default PatientTableElement;
