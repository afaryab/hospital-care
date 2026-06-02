import { Badge } from '@/components/ui/badge';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Separator } from '@/components/ui/separator';
import { cn } from '@/lib/utils';
import { ServiceOrder } from '@/types';

type ServiceOrderDetailedCardProps = {
    serviceOrder: ServiceOrder;
    className?: string;
};

const ServiceOrderDetailedCard: React.FC<ServiceOrderDetailedCardProps> = ({
    serviceOrder,
    className,
}) => (
    <Card className={cn('w-full', className)}>
        <CardHeader className="pb-2">
            <div className="flex items-start justify-between gap-2">
                <div>
                    <CardTitle className="font-mono text-base">
                        {serviceOrder.so_number}
                        {serviceOrder.so_short ? ` (${serviceOrder.so_short})` : ''}
                    </CardTitle>
                    <p className="mt-0.5 text-xs text-muted-foreground">
                        {serviceOrder.created_at}
                    </p>
                </div>
                <Badge variant="outline" className="shrink-0 text-xs">
                    {serviceOrder.type}
                </Badge>
            </div>
        </CardHeader>
        <Separator />
        <CardContent className="grid grid-cols-2 gap-x-4 gap-y-2 pt-3 text-sm">
            {serviceOrder.departmentKey && (
                <>
                    <span className="text-muted-foreground">Department</span>
                    <span className="font-medium">
                        {serviceOrder.departmentKey}
                    </span>
                </>
            )}
            {serviceOrder.notes && (
                <>
                    <span className="col-span-2 font-medium text-muted-foreground">
                        Notes
                    </span>
                    <span className="col-span-2 text-xs text-muted-foreground">
                        {serviceOrder.notes}
                    </span>
                </>
            )}
        </CardContent>
    </Card>
);

export default ServiceOrderDetailedCard;
