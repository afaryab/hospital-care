import ExpenseVoucherDetailedCard from '@/elements/expense-voucher/expense-voucher-detailed-card';
import { ExpenseVoucher } from '@/types';

export type VoucherDetailedCardProps = {
    voucher: ExpenseVoucher;
    className?: string;
};

const VoucherDetailedCard: React.FC<VoucherDetailedCardProps> = (props) => (
    <ExpenseVoucherDetailedCard {...props} />
);

export default VoucherDetailedCard;
