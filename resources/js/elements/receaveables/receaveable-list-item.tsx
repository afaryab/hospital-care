import { Badge } from '@/components/ui/badge';
import Currency from '@/components/currency';
import { Receaveable } from '@/types';
import { cn } from '@/lib/utils';

type ReceaveableListItemProps = {
    receaveable: Receaveable;
    className?: string;
    onClick?: () => void;
    selected?: boolean;
};

const ReceaveableListItem: React.FC<ReceaveableListItemProps> = ({ receaveable, className, onClick, selected }) => (
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
            <p className="text-xs font-mono text-muted-foreground">{receaveable.transaction?.tr_number}</p>
            <div className="flex items-baseline gap-1.5">
                <span className="text-sm font-semibold"><Currency value={receaveable.amount} /></span>
                {receaveable.patient?.name && (
                    <span className="text-xs text-muted-foreground truncate">{'-> '}{receaveable.patient.name}</span>
                )}
            </div>
        </div>
        <Badge variant={receaveable.status === 'paid' ? 'default' : 'secondary'} className="text-xs shrink-0">
            {receaveable.status}
        </Badge>
    </div>
);

export default ReceaveableListItem;
