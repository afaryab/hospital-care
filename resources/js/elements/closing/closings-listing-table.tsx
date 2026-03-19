import { Badge } from '@/components/ui/badge';
import Currency from '@/components/currency';
import { Closing } from '@/types';

type ClosingsListingTableProps = {
    closings: Closing[];
    onSelect?: (closing: Closing) => void;
    emptyMessage?: string;
};

const ClosingsListingTable: React.FC<ClosingsListingTableProps> = ({
    closings,
    onSelect,
    emptyMessage = 'No closings found.',
}) => {
    if (!closings.length) {
        return <p className="py-6 text-center text-sm text-muted-foreground">{emptyMessage}</p>;
    }

    return (
        <div className="overflow-x-auto rounded-lg border">
            <table className="w-full text-sm">
                <thead className="bg-muted/50 text-xs uppercase text-muted-foreground">
                    <tr>
                        <th className="px-4 py-2 text-left">CT Number</th>
                        <th className="px-4 py-2 text-left">Reception</th>
                        <th className="px-4 py-2 text-right">Opening</th>
                        <th className="px-4 py-2 text-right">Closing</th>
                        <th className="px-4 py-2 text-left">Status</th>
                        <th className="px-4 py-2 text-left">Date</th>
                    </tr>
                </thead>
                <tbody className="divide-y">
                    {closings.map((closing) => (
                        <tr
                            key={closing.id}
                            className={onSelect ? 'cursor-pointer hover:bg-accent transition-colors' : ''}
                            onClick={() => onSelect?.(closing)}
                        >
                            <td className="px-4 py-2 font-mono text-xs">{closing.ct_number}</td>
                            <td className="px-4 py-2">{closing.reception?.name ?? '—'}</td>
                            <td className="px-4 py-2 text-right font-semibold">
                                <Currency value={closing.opening_amount} />
                            </td>
                            <td className="px-4 py-2 text-right">
                                {closing.closing_amount != null ? <Currency value={closing.closing_amount} /> : '—'}
                            </td>
                            <td className="px-4 py-2">
                                <Badge
                                    variant={closing.status === 'OPEN' ? 'default' : 'secondary'}
                                    className="text-xs"
                                >
                                    {closing.status}
                                </Badge>
                            </td>
                            <td className="px-4 py-2 text-xs">{closing.created_at?.split('T')[0]}</td>
                        </tr>
                    ))}
                </tbody>
            </table>
        </div>
    );
};

export default ClosingsListingTable;
