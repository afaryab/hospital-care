import { Badge } from '@/components/ui/badge';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Separator } from '@/components/ui/separator';
import { ServiceOrder } from '@/types';
import { cn } from '@/lib/utils';

type ServiceOrderDetailedCardProps = {
    serviceOrder: ServiceOrder;
    className?: string;
};

const ServiceOrderDetailedCard: React.FC<ServiceOrderDetailedCardProps> = ({ serviceOrder, className }) => (
    <Card className={cn('w-full', className)}>
        <CardHeader className="pb-2">
            <div className="flex items-start justify-between gap-2">
                <div>
                    <CardTitle className="text-base font-mono">{serviceOrder.so_number}</CardTitle>
                    <p className="text-xs text-muted-foreground mt-0.5">{serviceOrder.created_at}</p>
                </div>
                <Badge variant="outline" className="text-xs shrink-0">{serviceOrder.type}</Badge>
            </div>
        </CardHeader>
        <Separator />
        <CardContent className="pt-3 grid grid-cols-2 gap-x-4 gap-y-2 text-sm">
            {serviceOrder.departmentKey && (
                <>
                    <span className="text-muted-foreground">Department</span>
                    <span className="font-medium">{serviceOrder.departmentKey}</span>
                </>
            )}
            {serviceOrder.notes && (
                <>
                    <span className="text-muted-foreground col-span-2 font-medium">Notes</span>
                    <span className="col-span-2 text-muted-foreground text-xs">{serviceOrder.notes}</span>
                </>
            )}
        </CardContent>
    </Card>
);

export default ServiceOrderDetailedCard;
