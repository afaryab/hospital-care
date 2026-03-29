import { Badge } from '@/components/ui/badge';
import { Card, CardContent } from '@/components/ui/card';
import { Patient } from '@/types';
import { cn } from '@/lib/utils';

const genderLabel: Record<string, string> = {
    m: 'Male',
    f: 'Female',
    t: 'Transgender',
    o: 'Other',
};

type PatientCardProps = {
    patient: Patient;
    className?: string;
    onClick?: () => void;
};

const PatientCard: React.FC<PatientCardProps> = ({ patient, className, onClick }) => (
    <Card className={cn('cursor-default', onClick && 'cursor-pointer hover:shadow-md transition-shadow', className)} onClick={onClick}>
        <CardContent className="p-4 flex items-center gap-3">
            <div className="flex flex-col gap-1 min-w-0">
                <p className="font-semibold text-sm truncate">{patient.name}</p>
                <p className="text-xs text-muted-foreground font-mono">{patient.ps_number}</p>
                <div className="flex gap-1 flex-wrap mt-1">
                    <Badge variant="outline" className="text-xs">{genderLabel[patient.gender] ?? patient.gender}</Badge>
                    {patient.age != null && <Badge variant="outline" className="text-xs">{patient.age} yrs</Badge>}
                </div>
            </div>
        </CardContent>
    </Card>
);

export default PatientCard;
