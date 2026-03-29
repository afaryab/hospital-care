import Currency from '@/components/currency';
import { Badge } from '@/components/ui/badge';
import { cn } from '@/lib/utils';
import { Receaveable } from '@/types';

type ReceaveableTableElementProps = {
    receaveable: Receaveable;
    className?: string;
};

const ReceaveableTableElement: React.FC<ReceaveableTableElementProps> = ({
    receaveable,
    className,
}) => (
    <div className={cn('flex min-w-0 items-center gap-2', className)}>
        <div className="min-w-0">
            <p className="font-mono text-xs text-muted-foreground">
                {receaveable.transaction?.tr_number}
            </p>
            <p className="text-sm font-semibold">
                <Currency value={receaveable.amount} />
            </p>
        </div>
        <Badge
            variant={receaveable.status === 'paid' ? 'default' : 'secondary'}
            className="shrink-0 text-xs"
        >
            {receaveable.status}
        </Badge>
    </div>
);

export default ReceaveableTableElement;
