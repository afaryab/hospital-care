import Currency from '@/components/currency';
import { Badge } from '@/components/ui/badge';
import { Card, CardContent } from '@/components/ui/card';
import { cn } from '@/lib/utils';
import { Receaveable } from '@/types';

type ReceaveableCardProps = {
    receaveable: Receaveable;
    className?: string;
    onClick?: () => void;
};

const ReceaveableCard: React.FC<ReceaveableCardProps> = ({
    receaveable,
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
        <CardContent className="flex items-center justify-between gap-3 p-4">
            <div className="min-w-0">
                <p className="font-mono text-xs text-muted-foreground">
                    {receaveable.transaction?.tr_number}
                </p>
                <p className="mt-0.5 text-sm font-semibold">
                    <Currency value={receaveable.amount} />
                </p>
                {receaveable.patient?.name && (
                    <p className="truncate text-xs text-muted-foreground">
                        {receaveable.patient.name}
                    </p>
                )}
            </div>
            <Badge
                variant={
                    receaveable.status === 'paid' ? 'default' : 'secondary'
                }
                className="shrink-0 text-xs"
            >
                {receaveable.status}
            </Badge>
        </CardContent>
    </Card>
);

export default ReceaveableCard;
