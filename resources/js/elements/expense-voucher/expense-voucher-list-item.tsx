import Currency from '@/components/currency';
import { Badge } from '@/components/ui/badge';
import { cn } from '@/lib/utils';
import { ExpenseVoucher } from '@/types';

type ExpenseVoucherListItemProps = {
    voucher: ExpenseVoucher;
    className?: string;
    onClick?: () => void;
    selected?: boolean;
};

const ExpenseVoucherListItem: React.FC<ExpenseVoucherListItemProps> = ({
    voucher,
    className,
    onClick,
    selected,
}) => (
    <div
        className={cn(
            'flex items-center justify-between gap-3 rounded-md px-3 py-2',
            'transition-colors hover:bg-accent',
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
            <p className="font-mono text-xs text-muted-foreground">
                {voucher.vc_number}
            </p>
            <div className="flex items-baseline gap-1.5">
                <span className="text-sm font-semibold">
                    <Currency value={voucher.amount} />
                </span>
                {voucher.payed_to_name && (
                    <span className="truncate text-xs text-muted-foreground">
                        {'-> '}
                        {voucher.payed_to_name}
                    </span>
                )}
            </div>
        </div>
        <Badge
            variant={voucher.status === 'payed' ? 'default' : 'secondary'}
            className="shrink-0 text-xs capitalize"
        >
            {voucher.status}
        </Badge>
    </div>
);

export default ExpenseVoucherListItem;
