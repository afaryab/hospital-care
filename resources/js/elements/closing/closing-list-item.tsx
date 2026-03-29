import { Badge } from '@/components/ui/badge';
import Currency from '@/components/currency';
import { Closing } from '@/types';
import { cn } from '@/lib/utils';

type ClosingListItemProps = {
    closing: Closing;
    className?: string;
    onClick?: () => void;
    selected?: boolean;
};

const ClosingListItem: React.FC<ClosingListItemProps> = ({ closing, className, onClick, selected }) => (
    <div
        className={cn(
            'flex items-center justify-between gap-3 px-3 py-2 rounded-md',
            'hover:bg-accent transition-colors',
            selected && 'bg-accent',
            onClick && 'cursor-pointer',
            className,
        )}
        onClick={onClick}
        role={onClick ? 'button' : undefined}
        tabIndex={onClick ? 0 : undefined}
        onKeyDown={onClick ? (e) => e.key === 'Enter' && onClick() : undefined}
    >
        <div className="min-w-0">
            <p className="text-xs font-mono text-muted-foreground">{closing.ct_number}</p>
            <p className="text-sm font-semibold"><Currency value={closing.opening_amount} /></p>
        </div>
        <Badge variant={closing.status === 'OPEN' ? 'default' : 'secondary'} className="text-xs shrink-0">
            {closing.status}
        </Badge>
    </div>
);

export default ClosingListItem;
