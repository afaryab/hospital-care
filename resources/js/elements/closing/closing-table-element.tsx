import Currency from '@/components/currency';
import { Badge } from '@/components/ui/badge';
import { cn } from '@/lib/utils';
import { Closing } from '@/types';

type ClosingTableElementProps = {
    closing: Closing;
    className?: string;
};

const ClosingTableElement: React.FC<ClosingTableElementProps> = ({
    closing,
    className,
}) => (
    <div className={cn('flex min-w-0 items-center gap-2', className)}>
        <div className="min-w-0">
            <p className="font-mono text-xs text-muted-foreground">
                {closing.ct_number}
            </p>
            <p className="text-sm font-semibold">
                <Currency value={closing.opening_amount} />
            </p>
        </div>
        <Badge
            variant={closing.status === 'OPEN' ? 'default' : 'secondary'}
            className="shrink-0 text-xs"
        >
            {closing.status}
        </Badge>
    </div>
);

export default ClosingTableElement;
