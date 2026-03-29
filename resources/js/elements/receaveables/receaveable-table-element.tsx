import { Badge } from '@/components/ui/badge';
import Currency from '@/components/currency';
import { Receaveable } from '@/types';
import { cn } from '@/lib/utils';

type ReceaveableTableElementProps = {
    receaveable: Receaveable;
    className?: string;
};

const ReceaveableTableElement: React.FC<ReceaveableTableElementProps> = ({ receaveable, className }) => (
    <div className={cn('flex items-center gap-2 min-w-0', className)}>
        <div className="min-w-0">
            <p className="text-xs font-mono text-muted-foreground">{receaveable.transaction?.tr_number}</p>
            <p className="text-sm font-semibold"><Currency value={receaveable.amount} /></p>
        </div>
        <Badge variant={receaveable.status === 'paid' ? 'default' : 'secondary'} className="text-xs shrink-0">
            {receaveable.status}
        </Badge>
    </div>
);

export default ReceaveableTableElement;
