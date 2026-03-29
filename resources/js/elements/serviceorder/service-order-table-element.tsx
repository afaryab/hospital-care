import { Badge } from '@/components/ui/badge';
import { ServiceOrder } from '@/types';
import { cn } from '@/lib/utils';

type ServiceOrderTableElementProps = {
    serviceOrder: ServiceOrder;
    className?: string;
};

const ServiceOrderTableElement: React.FC<ServiceOrderTableElementProps> = ({ serviceOrder, className }) => (
    <div className={cn('flex items-center gap-2 min-w-0', className)}>
        <div className="min-w-0">
            <p className="text-xs font-mono text-muted-foreground">{serviceOrder.so_number}</p>
            <p className="text-sm font-medium truncate">{serviceOrder.type}</p>
        </div>
        <Badge variant="outline" className="text-xs shrink-0">
            {serviceOrder.so_short ?? serviceOrder.departmentKey}
        </Badge>
    </div>
);

export default ServiceOrderTableElement;
