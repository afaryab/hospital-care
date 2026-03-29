import { Badge } from '@/components/ui/badge';
import { Card, CardContent } from '@/components/ui/card';
import { ServiceOrder } from '@/types';
import { cn } from '@/lib/utils';

type ServiceOrderCardProps = {
    serviceOrder: ServiceOrder;
    className?: string;
    onClick?: () => void;
};

const ServiceOrderCard: React.FC<ServiceOrderCardProps> = ({ serviceOrder, className, onClick }) => (
    <Card
        className={cn('cursor-default', onClick && 'cursor-pointer hover:shadow-md transition-shadow', className)}
        onClick={onClick}
    >
        <CardContent className="p-4 flex items-center justify-between gap-3">
            <div className="min-w-0">
                <p className="text-xs font-mono text-muted-foreground">{serviceOrder.so_number}</p>
                <p className="font-semibold text-sm mt-0.5">{serviceOrder.type}</p>
            </div>
            <Badge variant="outline" className="text-xs shrink-0">
                {serviceOrder.so_short ?? serviceOrder.departmentKey}
            </Badge>
        </CardContent>
    </Card>
);

export default ServiceOrderCard;
