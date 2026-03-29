import Currency from '@/components/currency';
import { Badge } from '@/components/ui/badge';
import { Card, CardContent } from '@/components/ui/card';
import { cn } from '@/lib/utils';
import { Closing } from '@/types';

type ClosingCardProps = {
    closing: Closing;
    className?: string;
    onClick?: () => void;
};

const ClosingCard: React.FC<ClosingCardProps> = ({
    closing,
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
                    {closing.ct_number}
                </p>
                <p className="mt-0.5 text-sm font-semibold">
                    Opening: <Currency value={closing.opening_amount} />
                </p>
            </div>
            <Badge
                variant={closing.status === 'OPEN' ? 'default' : 'secondary'}
                className="shrink-0 text-xs"
            >
                {closing.status}
            </Badge>
        </CardContent>
    </Card>
);

export default ClosingCard;
