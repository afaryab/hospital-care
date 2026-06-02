import { Badge } from '@/components/ui/badge';
import { cn } from '@/lib/utils';
import { ServiceOrder } from '@/types';

type ServiceOrderTableElementProps = {
    serviceOrder: ServiceOrder;
    className?: string;
};

const ServiceOrderTableElement: React.FC<ServiceOrderTableElementProps> = ({
    serviceOrder,
    className,
}) => (
    <div className={cn('flex min-w-0 items-center gap-2', className)}>
        <div className="min-w-0">
            <p className="font-mono text-xs text-muted-foreground">
                {serviceOrder.so_number}
                {serviceOrder.so_short ? ` (${serviceOrder.so_short})` : ''}
            </p>
            <p className="truncate text-sm font-medium">{serviceOrder.type}</p>
        </div>
        <Badge variant="outline" className="shrink-0 text-xs">
            {serviceOrder.so_short ?? serviceOrder.departmentKey}
        </Badge>
    </div>
);

export default ServiceOrderTableElement;
