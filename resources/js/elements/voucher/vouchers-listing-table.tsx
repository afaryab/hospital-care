import ExpenseVouchersListingTable from '@/elements/expense-voucher/expense-vouchers-listing-table';
import { ExpenseVoucher } from '@/types';

export type VouchersListingTableProps = {
    vouchers: ExpenseVoucher[];
    onSelect?: (voucher: ExpenseVoucher) => void;
    emptyMessage?: string;
};

const VouchersListingTable: React.FC<VouchersListingTableProps> = (props) => (
    <ExpenseVouchersListingTable {...props} />
);

export default VouchersListingTable;
