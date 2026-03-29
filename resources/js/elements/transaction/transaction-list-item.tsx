import { Badge } from '@/components/ui/badge';
import Currency from '@/components/currency';
import { Transaction } from '@/types';
import { cn } from '@/lib/utils';

type TransactionListItemProps = {
    transaction: Transaction;
    className?: string;
    onClick?: () => void;
    selected?: boolean;
};

const TransactionListItem: React.FC<TransactionListItemProps> = ({ transaction, className, onClick, selected }) => (
    <div
        className={cn(
            'flex items-center justify-between gap-3 px-3 py-2 rounded-md',
            'hover:bg-accent transition-colors',
            selected && 'bg-accent',
            onClick && 'cursor-pointer',
            className,
        )}
        onClick={onClick}
        role={onClick ? 'button' : undefined}
        tabIndex={onClick ? 0 : undefined}
        onKeyDown={onClick ? (e) => e.key === 'Enter' && onClick() : undefined}
    >
        <div className="min-w-0">
            <p className="text-xs font-mono text-muted-foreground">{transaction.tr_number}</p>
            <p className="text-sm font-semibold"><Currency value={transaction.amount} /></p>
        </div>
        <div className="flex items-center gap-1.5 shrink-0">
            <span className="text-xs text-muted-foreground">{transaction.created_at?.split('T')[0]}</span>
            <Badge
                variant={transaction.income_or_expense === 'INCOME' ? 'default' : 'destructive'}
                className="text-xs"
            >
                {transaction.income_or_expense}
            </Badge>
        </div>
    </div>
);

export default TransactionListItem;
