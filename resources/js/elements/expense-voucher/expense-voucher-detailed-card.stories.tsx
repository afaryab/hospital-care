import type { Meta, StoryObj } from '@storybook/react-vite';
import ExpenseVoucherDetailedCard from '@/elements/expense-voucher/expense-voucher-detailed-card';
import { mockExpenseVoucher, mockExpenseVoucherPending } from '@/storybook-mocks';

const meta: Meta<typeof ExpenseVoucherDetailedCard> = {
    title: 'Elements/ExpenseVoucher/ExpenseVoucherDetailedCard',
    component: ExpenseVoucherDetailedCard,
    tags: ['autodocs'],
    parameters: { layout: 'centered' },
};

export default meta;
type Story = StoryObj<typeof ExpenseVoucherDetailedCard>;

export const Paid: Story = {
    args: { voucher: mockExpenseVoucher },
};

export const Pending: Story = {
    args: { voucher: mockExpenseVoucherPending },
};
