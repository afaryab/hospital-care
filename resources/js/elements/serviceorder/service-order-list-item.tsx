import { Badge } from '@/components/ui/badge';
import { cn } from '@/lib/utils';
import { ServiceOrder } from '@/types';

type ServiceOrderListItemProps = {
    serviceOrder: ServiceOrder;
    className?: string;
    onClick?: () => void;
    selected?: boolean;
};

const ServiceOrderListItem: React.FC<ServiceOrderListItemProps> = ({
    serviceOrder,
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
                {serviceOrder.so_number}
            </p>
            <p className="text-sm font-medium">{serviceOrder.type}</p>
        </div>
        <Badge variant="outline" className="shrink-0 text-xs">
            {serviceOrder.so_short ?? serviceOrder.departmentKey}
        </Badge>
    </div>
);

export default ServiceOrderListItem;
