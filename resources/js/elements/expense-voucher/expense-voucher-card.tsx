import Currency from '@/components/currency';
import { Badge } from '@/components/ui/badge';
import { Card, CardContent } from '@/components/ui/card';
import { cn } from '@/lib/utils';
import { ExpenseVoucher } from '@/types';

type ExpenseVoucherCardProps = {
    voucher: ExpenseVoucher;
    className?: string;
    onClick?: () => void;
};

const ExpenseVoucherCard: React.FC<ExpenseVoucherCardProps> = ({
    voucher,
    className,
    onClick,
}) => (
    <Card
        className={cn(
            'cursor-default',
            onClick && 'cursor-pointer transition-shadow hover:shadow-md',
            className,
        )}
        onClick={onClick}
    >
        <CardContent className="flex items-center justify-between gap-3 p-4">
            <div className="min-w-0">
                <p className="font-mono text-xs text-muted-foreground">
                    {voucher.vc_number}
                </p>
                <p className="mt-0.5 text-sm font-semibold">
                    <Currency value={voucher.amount} />
                </p>
                {voucher.payed_to_name && (
                    <p className="truncate text-xs text-muted-foreground">
                        {voucher.payed_to_name}
                    </p>
                )}
            </div>
            <Badge
                variant={voucher.status === 'payed' ? 'default' : 'secondary'}
                className="shrink-0 text-xs capitalize"
            >
                {voucher.status}
            </Badge>
        </CardContent>
    </Card>
);

export default ExpenseVoucherCard;
