import { Badge } from '@/components/ui/badge';
import { Card, CardContent } from '@/components/ui/card';
import { cn } from '@/lib/utils';
import { ServiceOrder } from '@/types';

type ServiceOrderCardProps = {
    serviceOrder: ServiceOrder;
    className?: string;
    onClick?: () => void;
};

const ServiceOrderCard: React.FC<ServiceOrderCardProps> = ({
    serviceOrder,
    className,
    onClick,
}) => (
    <Card
        className={cn(
            'cursor-default',
            onClick && 'cursor-pointer transition-shadow hover:shadow-md',
            className,
        )}
        onClick={onClick}
    >
        <CardContent className="flex items-center justify-between gap-3 p-4">
            <div className="min-w-0">
                <p className="font-mono text-xs text-muted-foreground">
                    {serviceOrder.so_number}
                </p>
                <p className="mt-0.5 text-sm font-semibold">
                    {serviceOrder.type}
                </p>
            </div>
            <Badge variant="outline" className="shrink-0 text-xs">
                {serviceOrder.so_short ?? serviceOrder.departmentKey}
            </Badge>
        </CardContent>
    </Card>
);

export default ServiceOrderCard;
