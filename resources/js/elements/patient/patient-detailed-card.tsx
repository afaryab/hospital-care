import { Badge } from '@/components/ui/badge';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Separator } from '@/components/ui/separator';
import { Patient } from '@/types';
import { cn } from '@/lib/utils';

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

const PatientDetailedCard: React.FC<PatientDetailedCardProps> = ({ patient, className }) => (
    <Card className={cn('w-full', className)}>
        <CardHeader className="pb-2">
            <div className="flex items-start justify-between gap-2">
                <div>
                    <CardTitle className="text-base">{patient.name}</CardTitle>
                    <p className="text-xs text-muted-foreground font-mono mt-0.5">{patient.ps_number}</p>
                </div>
                <Badge variant="secondary">{genderLabel[patient.gender] ?? patient.gender}</Badge>
            </div>
        </CardHeader>
        <Separator />
        <CardContent className="pt-3 grid grid-cols-2 gap-x-4 gap-y-2 text-sm">
            {patient.age != null && (
                <>
                    <span className="text-muted-foreground">Age</span>
                    <span className="font-medium">{patient.age} yrs</span>
                </>
            )}
            {patient.contact && (
                <>
                    <span className="text-muted-foreground">Contact</span>
                    <span className="font-medium font-mono">{patient.contact}</span>
                </>
            )}
            {patient.cnic && (
                <>
                    <span className="text-muted-foreground">CNIC</span>
                    <span className="font-medium font-mono">{patient.cnic}</span>
                </>
            )}
        </CardContent>
    </Card>
);

export default PatientDetailedCard;
