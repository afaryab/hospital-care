import { Badge } from '@/components/ui/badge';
import Currency from '@/components/currency';
import { ExpenseVoucher } from '@/types';
import { cn } from '@/lib/utils';

type ExpenseVoucherTableElementProps = {
    voucher: ExpenseVoucher;
    className?: string;
};

const ExpenseVoucherTableElement: React.FC<ExpenseVoucherTableElementProps> = ({ voucher, className }) => (
    <div className={cn('flex items-center gap-2 min-w-0', className)}>
        <div className="min-w-0">
            <p className="text-xs font-mono text-muted-foreground">{voucher.vc_number}</p>
            <p className="text-sm font-semibold"><Currency value={voucher.amount} /></p>
        </div>
        <Badge
            variant={voucher.status === 'payed' ? 'default' : 'secondary'}
            className="text-xs shrink-0 capitalize"
        >
            {voucher.status}
        </Badge>
    </div>
);

export default ExpenseVoucherTableElement;
