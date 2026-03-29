import { Badge } from '@/components/ui/badge';
import { Card, CardContent } from '@/components/ui/card';
import Currency from '@/components/currency';
import { Transaction } from '@/types';
import { cn } from '@/lib/utils';

type TransactionCardProps = {
    transaction: Transaction;
    className?: string;
    onClick?: () => void;
};

const TransactionCard: React.FC<TransactionCardProps> = ({ transaction, className, onClick }) => (
    <Card
        className={cn('cursor-default', onClick && 'cursor-pointer hover:shadow-md transition-shadow', className)}
        onClick={onClick}
    >
        <CardContent className="p-4 flex items-center justify-between gap-3">
            <div className="min-w-0">
                <p className="text-xs font-mono text-muted-foreground">{transaction.tr_number}</p>
                <p className="font-semibold text-sm mt-0.5">
                    <Currency value={transaction.amount} />
                </p>
            </div>
            <Badge
                variant={transaction.income_or_expense === 'INCOME' ? 'default' : 'destructive'}
                className="text-xs shrink-0"
            >
                {transaction.income_or_expense}
            </Badge>
        </CardContent>
    </Card>
);

export default TransactionCard;
