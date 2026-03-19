import { Badge } from '@/components/ui/badge';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Separator } from '@/components/ui/separator';
import Currency from '@/components/currency';
import { ExpenseVoucher } from '@/types';
import { cn } from '@/lib/utils';

type ExpenseVoucherDetailedCardProps = {
    voucher: ExpenseVoucher;
    className?: string;
};

const ExpenseVoucherDetailedCard: React.FC<ExpenseVoucherDetailedCardProps> = ({ voucher, className }) => (
    <Card className={cn('w-full', className)}>
        <CardHeader className="pb-2">
            <div className="flex items-start justify-between gap-2">
                <div>
                    <CardTitle className="text-base font-mono">{voucher.vc_number}</CardTitle>
                    <p className="text-xs text-muted-foreground mt-0.5">{voucher.created_at}</p>
                </div>
                <Badge
                    variant={voucher.status === 'payed' ? 'default' : 'secondary'}
                    className="text-xs shrink-0 capitalize"
                >
                    {voucher.status}
                </Badge>
            </div>
        </CardHeader>
        <Separator />
        <CardContent className="pt-3 grid grid-cols-2 gap-x-4 gap-y-2 text-sm">
            <span className="text-muted-foreground">Amount</span>
            <span className="font-semibold"><Currency value={voucher.amount} /></span>

            {voucher.payed_to_name && (
                <>
                    <span className="text-muted-foreground">Paid To</span>
                    <span className="font-medium">{voucher.payed_to_name}</span>
                </>
            )}

            {voucher.expenseCategory && (
                <>
                    <span className="text-muted-foreground">Category</span>
                    <span className="font-medium">{voucher.expenseCategory.name}</span>
                </>
            )}

            {voucher.notes && (
                <>
                    <span className="text-muted-foreground col-span-2 font-medium">Notes</span>
                    <span className="col-span-2 text-xs text-muted-foreground">{voucher.notes}</span>
                </>
            )}
        </CardContent>
    </Card>
);

export default ExpenseVoucherDetailedCard;
