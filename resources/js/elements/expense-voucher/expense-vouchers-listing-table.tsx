import Currency from '@/components/currency';
import { Badge } from '@/components/ui/badge';
import { ExpenseVoucher } from '@/types';

type ExpenseVouchersListingTableProps = {
    vouchers: ExpenseVoucher[];
    onSelect?: (voucher: ExpenseVoucher) => void;
    emptyMessage?: string;
};

const ExpenseVouchersListingTable: React.FC<
    ExpenseVouchersListingTableProps
> = ({ vouchers, onSelect, emptyMessage = 'No expense vouchers found.' }) => {
    if (!vouchers.length) {
        return (
            <p className="py-6 text-center text-sm text-muted-foreground">
                {emptyMessage}
            </p>
        );
    }

    return (
        <div className="overflow-x-auto rounded-lg border">
            <table className="w-full text-sm">
                <thead className="bg-muted/50 text-xs text-muted-foreground uppercase">
                    <tr>
                        <th className="px-4 py-2 text-left">VC Number</th>
                        <th className="px-4 py-2 text-left">Category</th>
                        <th className="px-4 py-2 text-right">Amount</th>
                        <th className="px-4 py-2 text-left">Paid To</th>
                        <th className="px-4 py-2 text-left">Status</th>
                        <th className="px-4 py-2 text-left">Date</th>
                    </tr>
                </thead>
                <tbody className="divide-y">
                    {vouchers.map((voucher) => (
                        <tr
                            key={voucher.id}
                            className={
                                onSelect
                                    ? 'cursor-pointer transition-colors hover:bg-accent'
                                    : ''
                            }
                            onClick={() => onSelect?.(voucher)}
                        >
                            <td className="px-4 py-2 font-mono text-xs">
                                {voucher.vc_number}
                            </td>
                            <td className="px-4 py-2">
                                {voucher.expenseCategory?.name ?? '-'}
                            </td>
                            <td className="px-4 py-2 text-right font-semibold">
                                <Currency value={voucher.amount} />
                            </td>
                            <td className="px-4 py-2">
                                {voucher.payed_to_name ?? '-'}
                            </td>
                            <td className="px-4 py-2">
                                <Badge
                                    variant={
                                        voucher.status === 'payed'
                                            ? 'default'
                                            : 'secondary'
                                    }
                                    className="text-xs capitalize"
                                >
                                    {voucher.status}
                                </Badge>
                            </td>
                            <td className="px-4 py-2 text-xs">
                                {voucher.created_at?.split('T')[0]}
                            </td>
                        </tr>
                    ))}
                </tbody>
            </table>
        </div>
    );
};

export default ExpenseVouchersListingTable;
