import ReceaveablesListingTable from '@/elements/receaveables/receaveables-listing-table';
import { Receivable } from '@/types';

export type ReceivablesListingTableProps = {
    receaveables: Receivable[];
    onSelect?: (receivable: Receivable) => void;
    emptyMessage?: string;
};

const ReceivablesListingTable: React.FC<ReceivablesListingTableProps> = ({
    receaveables,
    ...props
}) => <ReceaveablesListingTable receaveables={receaveables} {...props} />;

export default ReceivablesListingTable;
