import { Badge } from '@/components/ui/badge';
import Currency from '@/components/currency';
import { Receaveable } from '@/types';

type ReceaveablesListingTableProps = {
    receaveables: Receaveable[];
    onSelect?: (receaveable: Receaveable) => void;
    emptyMessage?: string;
};

const ReceaveablesListingTable: React.FC<ReceaveablesListingTableProps> = ({
    receaveables,
    onSelect,
    emptyMessage = 'No receivables found.',
}) => {
    if (!receaveables.length) {
        return <p className="py-6 text-center text-sm text-muted-foreground">{emptyMessage}</p>;
    }

    return (
        <div className="overflow-x-auto rounded-lg border">
            <table className="w-full text-sm">
                <thead className="bg-muted/50 text-xs uppercase text-muted-foreground">
                    <tr>
                        <th className="px-4 py-2 text-left">TR Number</th>
                        <th className="px-4 py-2 text-left">Patient</th>
                        <th className="px-4 py-2 text-right">Original</th>
                        <th className="px-4 py-2 text-right">Remaining</th>
                        <th className="px-4 py-2 text-left">Due Date</th>
                        <th className="px-4 py-2 text-left">Status</th>
                    </tr>
                </thead>
                <tbody className="divide-y">
                    {receaveables.map((receaveable) => (
                        <tr
                            key={receaveable.id}
                            className={onSelect ? 'cursor-pointer hover:bg-accent transition-colors' : ''}
                            onClick={() => onSelect?.(receaveable)}
                        >
                            <td className="px-4 py-2 font-mono text-xs">
                                {receaveable.transaction?.tr_number ?? '-'}
                            </td>
                            <td className="px-4 py-2">{receaveable.patient?.name ?? '-'}</td>
                            <td className="px-4 py-2 text-right font-semibold">
                                <Currency value={receaveable.orignal_amount} />
                            </td>
                            <td className="px-4 py-2 text-right">
                                <Currency value={receaveable.amount} />
                            </td>
                            <td className="px-4 py-2 text-xs">{receaveable.due_date ?? '-'}</td>
                            <td className="px-4 py-2">
                                <Badge variant={receaveable.status === 'paid' ? 'default' : 'secondary'} className="text-xs">
                                    {receaveable.status}
                                </Badge>
                            </td>
                        </tr>
                    ))}
                </tbody>
            </table>
        </div>
    );
};

export default ReceaveablesListingTable;
