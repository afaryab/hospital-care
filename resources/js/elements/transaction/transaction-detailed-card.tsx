import { Badge } from '@/components/ui/badge';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Separator } from '@/components/ui/separator';
import Currency from '@/components/currency';
import { Transaction } from '@/types';
import { cn } from '@/lib/utils';

type TransactionDetailedCardProps = {
    transaction: Transaction;
    className?: string;
};

const TransactionDetailedCard: React.FC<TransactionDetailedCardProps> = ({ transaction, className }) => (
    <Card className={cn('w-full', className)}>
        <CardHeader className="pb-2">
            <div className="flex items-start justify-between gap-2">
                <div>
                    <CardTitle className="text-base font-mono">{transaction.tr_number}</CardTitle>
                    <p className="text-xs text-muted-foreground mt-0.5">{transaction.created_at}</p>
                </div>
                <Badge
                    variant={transaction.income_or_expense === 'INCOME' ? 'default' : 'destructive'}
                    className="text-xs shrink-0"
                >
                    {transaction.income_or_expense}
                </Badge>
            </div>
        </CardHeader>
        <Separator />
        <CardContent className="pt-3 grid grid-cols-2 gap-x-4 gap-y-2 text-sm">
            <span className="text-muted-foreground">Amount</span>
            <span className="font-semibold"><Currency value={transaction.amount} /></span>

            {transaction.customer_payed != null && (
                <>
                    <span className="text-muted-foreground">Paid</span>
                    <span className="font-medium"><Currency value={transaction.customer_payed} /></span>
                </>
            )}

            {transaction.change != null && transaction.change > 0 && (
                <>
                    <span className="text-muted-foreground">Change</span>
                    <span className="font-medium"><Currency value={transaction.change} /></span>
                </>
            )}

            {transaction.type && (
                <>
                    <span className="text-muted-foreground">Type</span>
                    <span className="font-medium">{transaction.type}</span>
                </>
            )}

            {transaction.patient && (
                <>
                    <span className="text-muted-foreground">Patient</span>
                    <span className="font-medium">{transaction.patient.name}</span>
                </>
            )}
        </CardContent>
    </Card>
);

export default TransactionDetailedCard;
