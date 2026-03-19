import { Badge } from '@/components/ui/badge';
import Currency from '@/components/currency';
import { Closing } from '@/types';
import { cn } from '@/lib/utils';

type ClosingTableElementProps = {
    closing: Closing;
    className?: string;
};

const ClosingTableElement: React.FC<ClosingTableElementProps> = ({ closing, className }) => (
    <div className={cn('flex items-center gap-2 min-w-0', className)}>
        <div className="min-w-0">
            <p className="text-xs font-mono text-muted-foreground">{closing.ct_number}</p>
            <p className="text-sm font-semibold"><Currency value={closing.opening_amount} /></p>
        </div>
        <Badge variant={closing.status === 'OPEN' ? 'default' : 'secondary'} className="text-xs shrink-0">
            {closing.status}
        </Badge>
    </div>
);

export default ClosingTableElement;
