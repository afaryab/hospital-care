import ExpenseVoucherTableElement from '@/elements/expense-voucher/expense-voucher-table-element';
import { ExpenseVoucher } from '@/types';

export type VoucherTableElementProps = {
    voucher: ExpenseVoucher;
    className?: string;
};

const VoucherTableElement: React.FC<VoucherTableElementProps> = (props) => <ExpenseVoucherTableElement {...props} />;

export default VoucherTableElement;
