import Currency from '@/components/currency';
import { Badge } from '@/components/ui/badge';
import { cn } from '@/lib/utils';
import { ExpenseVoucher } from '@/types';

type ExpenseVoucherTableElementProps = {
    voucher: ExpenseVoucher;
    className?: string;
};

const ExpenseVoucherTableElement: React.FC<ExpenseVoucherTableElementProps> = ({
    voucher,
    className,
}) => (
    <div className={cn('flex min-w-0 items-center gap-2', className)}>
        <div className="min-w-0">
            <p className="font-mono text-xs text-muted-foreground">
                {voucher.vc_number}
            </p>
            <p className="text-sm font-semibold">
                <Currency value={voucher.amount} />
            </p>
        </div>
        <Badge
            variant={voucher.status === 'payed' ? 'default' : 'secondary'}
            className="shrink-0 text-xs capitalize"
        >
            {voucher.status}
        </Badge>
    </div>
);

export default ExpenseVoucherTableElement;
