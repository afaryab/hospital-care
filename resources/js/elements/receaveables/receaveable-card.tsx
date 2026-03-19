import { Badge } from '@/components/ui/badge';
import { Card, CardContent } from '@/components/ui/card';
import Currency from '@/components/currency';
import { Receaveable } from '@/types';
import { cn } from '@/lib/utils';

type ReceaveableCardProps = {
    receaveable: Receaveable;
    className?: string;
    onClick?: () => void;
};

const ReceaveableCard: React.FC<ReceaveableCardProps> = ({ receaveable, className, onClick }) => (
    <Card
        className={cn('cursor-default', onClick && 'cursor-pointer hover:shadow-md transition-shadow', className)}
        onClick={onClick}
    >
        <CardContent className="p-4 flex items-center justify-between gap-3">
            <div className="min-w-0">
                <p className="text-xs font-mono text-muted-foreground">{receaveable.transaction?.tr_number}</p>
                <p className="font-semibold text-sm mt-0.5">
                    <Currency value={receaveable.amount} />
                </p>
                {receaveable.patient?.name && (
                    <p className="text-xs text-muted-foreground truncate">{receaveable.patient.name}</p>
                )}
            </div>
            <Badge variant={receaveable.status === 'paid' ? 'default' : 'secondary'} className="text-xs shrink-0">
                {receaveable.status}
            </Badge>
        </CardContent>
    </Card>
);

export default ReceaveableCard;
