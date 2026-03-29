import { Badge } from '@/components/ui/badge';
import { Card, CardContent } from '@/components/ui/card';
import { cn } from '@/lib/utils';
import { Patient } from '@/types';

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

const PatientCard: React.FC<PatientCardProps> = ({
    patient,
    className,
    onClick,
}) => (
    <Card
        className={cn(
            'cursor-default',
            onClick && 'cursor-pointer transition-shadow hover:shadow-md',
            className,
        )}
        onClick={onClick}
    >
        <CardContent className="flex items-center gap-3 p-4">
            <div className="flex min-w-0 flex-col gap-1">
                <p className="truncate text-sm font-semibold">{patient.name}</p>
                <p className="font-mono text-xs text-muted-foreground">
                    {patient.ps_number}
                </p>
                <div className="mt-1 flex flex-wrap gap-1">
                    <Badge variant="outline" className="text-xs">
                        {genderLabel[patient.gender] ?? patient.gender}
                    </Badge>
                    {patient.age != null && (
                        <Badge variant="outline" className="text-xs">
                            {patient.age} yrs
                        </Badge>
                    )}
                </div>
            </div>
        </CardContent>
    </Card>
);

export default PatientCard;
