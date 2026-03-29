import Currency from '@/components/currency';
import { Badge } from '@/components/ui/badge';
import { cn } from '@/lib/utils';
import { Receaveable } from '@/types';

type ReceaveableListItemProps = {
    receaveable: Receaveable;
    className?: string;
    onClick?: () => void;
    selected?: boolean;
};

const ReceaveableListItem: React.FC<ReceaveableListItemProps> = ({
    receaveable,
    className,
    onClick,
    selected,
}) => (
    <div
        className={cn(
            'flex items-center justify-between gap-3 rounded-md px-3 py-2',
            'transition-colors hover:bg-accent',
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
            <p className="font-mono text-xs text-muted-foreground">
                {receaveable.transaction?.tr_number}
            </p>
            <div className="flex items-baseline gap-1.5">
                <span className="text-sm font-semibold">
                    <Currency value={receaveable.amount} />
                </span>
                {receaveable.patient?.name && (
                    <span className="truncate text-xs text-muted-foreground">
                        {'-> '}
                        {receaveable.patient.name}
                    </span>
                )}
            </div>
        </div>
        <Badge
            variant={receaveable.status === 'paid' ? 'default' : 'secondary'}
            className="shrink-0 text-xs"
        >
            {receaveable.status}
        </Badge>
    </div>
);

export default ReceaveableListItem;
