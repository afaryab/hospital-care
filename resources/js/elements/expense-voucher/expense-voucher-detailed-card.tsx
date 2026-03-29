import Currency from '@/components/currency';
import { Badge } from '@/components/ui/badge';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Separator } from '@/components/ui/separator';
import { cn } from '@/lib/utils';
import { ExpenseVoucher } from '@/types';

type ExpenseVoucherDetailedCardProps = {
    voucher: ExpenseVoucher;
    className?: string;
};

const ExpenseVoucherDetailedCard: React.FC<ExpenseVoucherDetailedCardProps> = ({
    voucher,
    className,
}) => (
    <Card className={cn('w-full', className)}>
        <CardHeader className="pb-2">
            <div className="flex items-start justify-between gap-2">
                <div>
                    <CardTitle className="font-mono text-base">
                        {voucher.vc_number}
                    </CardTitle>
                    <p className="mt-0.5 text-xs text-muted-foreground">
                        {voucher.created_at}
                    </p>
                </div>
                <Badge
                    variant={
                        voucher.status === 'payed' ? 'default' : 'secondary'
                    }
                    className="shrink-0 text-xs capitalize"
                >
                    {voucher.status}
                </Badge>
            </div>
        </CardHeader>
        <Separator />
        <CardContent className="grid grid-cols-2 gap-x-4 gap-y-2 pt-3 text-sm">
            <span className="text-muted-foreground">Amount</span>
            <span className="font-semibold">
                <Currency value={voucher.amount} />
            </span>

            {voucher.payed_to_name && (
                <>
                    <span className="text-muted-foreground">Paid To</span>
                    <span className="font-medium">{voucher.payed_to_name}</span>
                </>
            )}

            {voucher.expenseCategory && (
                <>
                    <span className="text-muted-foreground">Category</span>
                    <span className="font-medium">
                        {voucher.expenseCategory.name}
                    </span>
                </>
            )}

            {voucher.notes && (
                <>
                    <span className="col-span-2 font-medium text-muted-foreground">
                        Notes
                    </span>
                    <span className="col-span-2 text-xs text-muted-foreground">
                        {voucher.notes}
                    </span>
                </>
            )}
        </CardContent>
    </Card>
);

export default ExpenseVoucherDetailedCard;
