import Currency from '@/components/currency';
import { Badge } from '@/components/ui/badge';
import { Card, CardContent } from '@/components/ui/card';
import { cn } from '@/lib/utils';
import { Transaction } from '@/types';

type TransactionCardProps = {
    transaction: Transaction;
    className?: string;
    onClick?: () => void;
};

const TransactionCard: React.FC<TransactionCardProps> = ({
    transaction,
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
                    {transaction.tr_number}
                </p>
                <p className="mt-0.5 text-sm font-semibold">
                    <Currency value={transaction.amount} />
                </p>
            </div>
            <Badge
                variant={
                    transaction.income_or_expense === 'INCOME'
                        ? 'default'
                        : 'destructive'
                }
                className="shrink-0 text-xs"
            >
                {transaction.income_or_expense}
            </Badge>
        </CardContent>
    </Card>
);

export default TransactionCard;
