import { Badge } from '@/components/ui/badge';
import { Card, CardContent } from '@/components/ui/card';
import Currency from '@/components/currency';
import { ExpenseVoucher } from '@/types';
import { cn } from '@/lib/utils';

type ExpenseVoucherCardProps = {
    voucher: ExpenseVoucher;
    className?: string;
    onClick?: () => void;
};

const ExpenseVoucherCard: React.FC<ExpenseVoucherCardProps> = ({ voucher, className, onClick }) => (
    <Card
        className={cn('cursor-default', onClick && 'cursor-pointer hover:shadow-md transition-shadow', className)}
        onClick={onClick}
    >
        <CardContent className="p-4 flex items-center justify-between gap-3">
            <div className="min-w-0">
                <p className="text-xs font-mono text-muted-foreground">{voucher.vc_number}</p>
                <p className="font-semibold text-sm mt-0.5">
                    <Currency value={voucher.amount} />
                </p>
                {voucher.payed_to_name && (
                    <p className="text-xs text-muted-foreground truncate">{voucher.payed_to_name}</p>
                )}
            </div>
            <Badge
                variant={voucher.status === 'payed' ? 'default' : 'secondary'}
                className="text-xs shrink-0 capitalize"
            >
                {voucher.status}
            </Badge>
        </CardContent>
    </Card>
);

export default ExpenseVoucherCard;
