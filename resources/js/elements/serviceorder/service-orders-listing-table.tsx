import { Badge } from '@/components/ui/badge';
import { ServiceOrder } from '@/types';

type ServiceOrdersListingTableProps = {
    serviceOrders: ServiceOrder[];
    onSelect?: (serviceOrder: ServiceOrder) => void;
    emptyMessage?: string;
};

const ServiceOrdersListingTable: React.FC<ServiceOrdersListingTableProps> = ({
    serviceOrders,
    onSelect,
    emptyMessage = 'No service orders found.',
}) => {
    if (!serviceOrders.length) {
        return <p className="py-6 text-center text-sm text-muted-foreground">{emptyMessage}</p>;
    }

    return (
        <div className="overflow-x-auto rounded-lg border">
            <table className="w-full text-sm">
                <thead className="bg-muted/50 text-xs uppercase text-muted-foreground">
                    <tr>
                        <th className="px-4 py-2 text-left">SO Number</th>
                        <th className="px-4 py-2 text-left">Type</th>
                        <th className="px-4 py-2 text-left">Department</th>
                        <th className="px-4 py-2 text-left">Date</th>
                    </tr>
                </thead>
                <tbody className="divide-y">
                    {serviceOrders.map((so, i) => (
                        <tr
                            key={so.id ?? i}
                            className={onSelect ? 'cursor-pointer hover:bg-accent transition-colors' : ''}
                            onClick={() => onSelect?.(so)}
                        >
                            <td className="px-4 py-2 font-mono text-xs">{so.so_number}</td>
                            <td className="px-4 py-2">{so.type}</td>
                            <td className="px-4 py-2">
                                <Badge variant="outline" className="text-xs">
                                    {so.so_short ?? so.departmentKey ?? '-'}
                                </Badge>
                            </td>
                            <td className="px-4 py-2 text-xs">{so.created_at?.split('T')[0]}</td>
                        </tr>
                    ))}
                </tbody>
            </table>
        </div>
    );
};

export default ServiceOrdersListingTable;
