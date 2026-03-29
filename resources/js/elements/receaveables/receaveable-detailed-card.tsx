import { Badge } from '@/components/ui/badge';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Separator } from '@/components/ui/separator';
import Currency from '@/components/currency';
import { Receaveable } from '@/types';
import { cn } from '@/lib/utils';

type ReceaveableDetailedCardProps = {
    receaveable: Receaveable;
    className?: string;
};

const ReceaveableDetailedCard: React.FC<ReceaveableDetailedCardProps> = ({ receaveable, className }) => (
    <Card className={cn('w-full', className)}>
        <CardHeader className="pb-2">
            <div className="flex items-start justify-between gap-2">
                <div>
                    <CardTitle className="text-base font-mono">{receaveable.transaction?.tr_number}</CardTitle>
                    <p className="text-xs text-muted-foreground mt-0.5">{receaveable.created_at}</p>
                </div>
                <Badge variant={receaveable.status === 'paid' ? 'default' : 'secondary'} className="text-xs shrink-0">
                    {receaveable.status}
                </Badge>
            </div>
        </CardHeader>
        <Separator />
        <CardContent className="pt-3 grid grid-cols-2 gap-x-4 gap-y-2 text-sm">
            <span className="text-muted-foreground">Original Amount</span>
            <span className="font-semibold"><Currency value={receaveable.orignal_amount} /></span>

            <span className="text-muted-foreground">Remaining</span>
            <span className="font-semibold"><Currency value={receaveable.amount} /></span>

            {receaveable.patient?.name && (
                <>
                    <span className="text-muted-foreground">Patient</span>
                    <span className="font-medium">{receaveable.patient.name}</span>
                </>
            )}

            {receaveable.due_date && (
                <>
                    <span className="text-muted-foreground">Due Date</span>
                    <span className="font-medium">{receaveable.due_date}</span>
                </>
            )}
        </CardContent>
    </Card>
);

export default ReceaveableDetailedCard;
