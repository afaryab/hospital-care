import { Badge } from '@/components/ui/badge';
import Currency from '@/components/currency';
import { Transaction } from '@/types';
import { cn } from '@/lib/utils';

type TransactionTableElementProps = {
    transaction: Transaction;
    className?: string;
};

const TransactionTableElement: React.FC<TransactionTableElementProps> = ({ transaction, className }) => (
    <div className={cn('flex items-center gap-2 min-w-0', className)}>
        <div className="min-w-0">
            <p className="text-xs font-mono text-muted-foreground">{transaction.tr_number}</p>
            <p className="text-sm font-semibold"><Currency value={transaction.amount} /></p>
        </div>
        <Badge
            variant={transaction.income_or_expense === 'INCOME' ? 'default' : 'destructive'}
            className="text-xs shrink-0"
        >
            {transaction.income_or_expense}
        </Badge>
    </div>
);

export default TransactionTableElement;
