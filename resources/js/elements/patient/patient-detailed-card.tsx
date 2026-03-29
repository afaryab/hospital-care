import { Badge } from '@/components/ui/badge';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Separator } from '@/components/ui/separator';
import { cn } from '@/lib/utils';
import { Patient } from '@/types';

const genderLabel: Record<string, string> = {
    m: 'Male',
    f: 'Female',
    t: 'Transgender',
    o: 'Other',
};

type PatientDetailedCardProps = {
    patient: Patient;
    className?: string;
};

const PatientDetailedCard: React.FC<PatientDetailedCardProps> = ({
    patient,
    className,
}) => (
    <Card className={cn('w-full', className)}>
        <CardHeader className="pb-2">
            <div className="flex items-start justify-between gap-2">
                <div>
                    <CardTitle className="text-base">{patient.name}</CardTitle>
                    <p className="mt-0.5 font-mono text-xs text-muted-foreground">
                        {patient.ps_number}
                    </p>
                </div>
                <Badge variant="secondary">
                    {genderLabel[patient.gender] ?? patient.gender}
                </Badge>
            </div>
        </CardHeader>
        <Separator />
        <CardContent className="grid grid-cols-2 gap-x-4 gap-y-2 pt-3 text-sm">
            {patient.age != null && (
                <>
                    <span className="text-muted-foreground">Age</span>
                    <span className="font-medium">{patient.age} yrs</span>
                </>
            )}
            {patient.contact && (
                <>
                    <span className="text-muted-foreground">Contact</span>
                    <span className="font-mono font-medium">
                        {patient.contact}
                    </span>
                </>
            )}
            {patient.cnic && (
                <>
                    <span className="text-muted-foreground">CNIC</span>
                    <span className="font-mono font-medium">
                        {patient.cnic}
                    </span>
                </>
            )}
        </CardContent>
    </Card>
);

export default PatientDetailedCard;
