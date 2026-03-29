import { Badge } from '@/components/ui/badge';
import { Card, CardContent } from '@/components/ui/card';
import Currency from '@/components/currency';
import { Closing } from '@/types';
import { cn } from '@/lib/utils';

type ClosingCardProps = {
    closing: Closing;
    className?: string;
    onClick?: () => void;
};

const ClosingCard: React.FC<ClosingCardProps> = ({ closing, className, onClick }) => (
    <Card
        className={cn('cursor-default', onClick && 'cursor-pointer hover:shadow-md transition-shadow', className)}
        onClick={onClick}
    >
        <CardContent className="p-4 flex items-center justify-between gap-3">
            <div className="min-w-0">
                <p className="text-xs font-mono text-muted-foreground">{closing.ct_number}</p>
                <p className="font-semibold text-sm mt-0.5">
                    Opening: <Currency value={closing.opening_amount} />
                </p>
            </div>
            <Badge variant={closing.status === 'OPEN' ? 'default' : 'secondary'} className="text-xs shrink-0">
                {closing.status}
            </Badge>
        </CardContent>
    </Card>
);

export default ClosingCard;
