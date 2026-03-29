import Currency from '@/components/currency';
import { Badge } from '@/components/ui/badge';
import { cn } from '@/lib/utils';
import { Transaction } from '@/types';

type TransactionTableElementProps = {
    transaction: Transaction;
    className?: string;
};

const TransactionTableElement: React.FC<TransactionTableElementProps> = ({
    transaction,
    className,
}) => (
    <div className={cn('flex min-w-0 items-center gap-2', className)}>
        <div className="min-w-0">
            <p className="font-mono text-xs text-muted-foreground">
                {transaction.tr_number}
            </p>
            <p className="text-sm font-semibold">
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
    </div>
);

export default TransactionTableElement;
