import ExpenseVoucherCard from '@/elements/expense-voucher/expense-voucher-card';
import { ExpenseVoucher } from '@/types';

export type VoucherCardProps = {
    voucher: ExpenseVoucher;
    className?: string;
    onClick?: () => void;
};

const VoucherCard: React.FC<VoucherCardProps> = (props) => <ExpenseVoucherCard {...props} />;

export default VoucherCard;
