import { Badge } from '@/components/ui/badge';
import Currency from '@/components/currency';
import { Transaction } from '@/types';

type TransactionsListingTableProps = {
    transactions: Transaction[];
    onSelect?: (transaction: Transaction) => void;
    emptyMessage?: string;
};

const TransactionsListingTable: React.FC<TransactionsListingTableProps> = ({
    transactions,
    onSelect,
    emptyMessage = 'No transactions found.',
}) => {
    if (!transactions.length) {
        return <p className="py-6 text-center text-sm text-muted-foreground">{emptyMessage}</p>;
    }

    return (
        <div className="overflow-x-auto rounded-lg border">
            <table className="w-full text-sm">
                <thead className="bg-muted/50 text-xs uppercase text-muted-foreground">
                    <tr>
                        <th className="px-4 py-2 text-left">TR Number</th>
                        <th className="px-4 py-2 text-left">Type</th>
                        <th className="px-4 py-2 text-right">Amount</th>
                        <th className="px-4 py-2 text-right">Paid</th>
                        <th className="px-4 py-2 text-left">Date</th>
                        <th className="px-4 py-2 text-left">Flow</th>
                    </tr>
                </thead>
                <tbody className="divide-y">
                    {transactions.map((tr) => (
                        <tr
                            key={tr.id}
                            className={onSelect ? 'cursor-pointer hover:bg-accent transition-colors' : ''}
                            onClick={() => onSelect?.(tr)}
                        >
                            <td className="px-4 py-2 font-mono text-xs">{tr.tr_number}</td>
                            <td className="px-4 py-2">{tr.type || '—'}</td>
                            <td className="px-4 py-2 text-right font-semibold">
                                <Currency value={tr.amount} />
                            </td>
                            <td className="px-4 py-2 text-right">
                                <Currency value={tr.customer_payed} />
                            </td>
                            <td className="px-4 py-2 text-xs">{tr.created_at?.split('T')[0]}</td>
                            <td className="px-4 py-2">
                                <Badge
                                    variant={tr.income_or_expense === 'INCOME' ? 'default' : 'destructive'}
                                    className="text-xs"
                                >
                                    {tr.income_or_expense}
                                </Badge>
                            </td>
                        </tr>
                    ))}
                </tbody>
            </table>
        </div>
    );
};

export default TransactionsListingTable;
