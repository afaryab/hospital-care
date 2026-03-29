import Currency from '@/components/currency';
import { Badge } from '@/components/ui/badge';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Separator } from '@/components/ui/separator';
import { cn } from '@/lib/utils';
import { Closing } from '@/types';

type ClosingDetailedCardProps = {
    closing: Closing;
    className?: string;
};

const ClosingDetailedCard: React.FC<ClosingDetailedCardProps> = ({
    closing,
    className,
}) => (
    <Card className={cn('w-full', className)}>
        <CardHeader className="pb-2">
            <div className="flex items-start justify-between gap-2">
                <div>
                    <CardTitle className="font-mono text-base">
                        {closing.ct_number}
                    </CardTitle>
                    {closing.reception && (
                        <p className="mt-0.5 text-xs text-muted-foreground">
                            Reception: {closing.reception.name}
                        </p>
                    )}
                </div>
                <Badge
                    variant={
                        closing.status === 'OPEN' ? 'default' : 'secondary'
                    }
                    className="shrink-0 text-xs"
                >
                    {closing.status}
                </Badge>
            </div>
        </CardHeader>
        <Separator />
        <CardContent className="grid grid-cols-2 gap-x-4 gap-y-2 pt-3 text-sm">
            <span className="text-muted-foreground">Opening</span>
            <span className="font-semibold">
                <Currency value={closing.opening_amount} />
            </span>

            {closing.closing_amount != null && (
                <>
                    <span className="text-muted-foreground">Closing</span>
                    <span className="font-semibold">
                        <Currency value={closing.closing_amount} />
                    </span>
                </>
            )}

            {closing.amount_received != null && (
                <>
                    <span className="text-muted-foreground">Received</span>
                    <span className="font-medium">
                        <Currency value={closing.amount_received} />
                    </span>
                </>
            )}

            {closing.expense_payed != null && (
                <>
                    <span className="text-muted-foreground">Expenses</span>
                    <span className="font-medium">
                        <Currency value={closing.expense_payed} />
                    </span>
                </>
            )}

            {closing.closed_at && (
                <>
                    <span className="text-muted-foreground">Closed At</span>
                    <span className="font-medium">{closing.closed_at}</span>
                </>
            )}
        </CardContent>
    </Card>
);

export default ClosingDetailedCard;
