import ExpenseVoucherListItem from '@/elements/expense-voucher/expense-voucher-list-item';
import { ExpenseVoucher } from '@/types';

export type VoucherListItemProps = {
    voucher: ExpenseVoucher;
    className?: string;
    onClick?: () => void;
    selected?: boolean;
};

const VoucherListItem: React.FC<VoucherListItemProps> = (props) => <ExpenseVoucherListItem {...props} />;

export default VoucherListItem;
